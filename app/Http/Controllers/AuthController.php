<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;  // ← TAMBAHKAN UNTUK AUDIT LOG
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FORM LOGIN
    |--------------------------------------------------------------------------
    */
    public function loginForm()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN EMAIL + PASSWORD + OTP
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // CARI USER
        $user = User::where('email', $request->email)->first();

        // EMAIL TIDAK ADA
        if (!$user) {
            return back()->with(
                'error',
                'Email tidak terdaftar'
            );
        }

        // PASSWORD SALAH
        if (!Hash::check($request->password, $user->password)) {
            return back()->with(
                'error',
                'Password salah'
            );
        }

        // NOMOR WA BELUM ADA
        if (!$user->phone) {
            return back()->with(
                'error',
                'Nomor WhatsApp belum tersedia'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COOLDOWN OTP 60 DETIK
        |--------------------------------------------------------------------------
        */
        if (
            $user->otp_requested_at &&
            Carbon::parse($user->otp_requested_at)
                ->addSeconds(60)
                ->isFuture()
        ) {

            $remaining = now()->diffInSeconds(
                Carbon::parse($user->otp_requested_at)
                    ->addSeconds(60)
            );

            return back()->with(
                'error',
                "Tunggu {$remaining} detik sebelum meminta OTP lagi"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE OTP
        |--------------------------------------------------------------------------
        */
        $otp = rand(100000, 999999);

        // SIMPAN OTP
        $user->otp = $otp;
        $user->otp_requested_at = now();
        $user->otp_expired_at = now()->addMinutes(5);
        $user->save();

        /*
        |--------------------------------------------------------------------------
        | FORMAT PESAN WA
        |--------------------------------------------------------------------------
        */
        $message =
            "🔐 *Kode Verifikasi Login*\n\n" .
            "Kode OTP Anda:\n" .
            "*{$otp}*\n\n" .
            "OTP berlaku selama 5 menit.\n" .
            "Jangan bagikan kode ini kepada siapa pun.";

        /*
        |--------------------------------------------------------------------------
        | KIRIM OTP KE WHATSAPP
        |--------------------------------------------------------------------------
        */
        Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])
            ->post('https://api.fonnte.com/send', [
                'target' => $user->phone,
                'message' => $message,
                'footer' => false,
            ]);

        // SIMPAN SESSION
        session([
            'otp_user_id' => $user->id
        ]);

        // RESET ATTEMPT
        session()->forget('otp_attempts');

        return redirect('/verify-otp')
            ->with('success', 'OTP berhasil dikirim');
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN OTP
    |--------------------------------------------------------------------------
    */
    public function verifyOtpForm()
    {
        return view('auth.verify-otp');
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI OTP
    |--------------------------------------------------------------------------
    */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // AMBIL USER
        $user = User::find(session('otp_user_id'));

        // SESSION HILANG
        if (!$user) {
            return redirect('/login')
                ->with('error', 'Session OTP habis');
        }

        /*
        |--------------------------------------------------------------------------
        | LIMIT SALAH OTP
        |--------------------------------------------------------------------------
        */
        $attempts = session('otp_attempts', 0);

        // OTP SALAH
        if ($user->otp != $request->otp) {

            $attempts++;

            session([
                'otp_attempts' => $attempts
            ]);

            // JIKA SALAH 3X
            if ($attempts >= 3) {

                // HAPUS OTP
                $user->otp = null;
                $user->otp_expired_at = null;
                $user->save();

                // HAPUS SESSION
                session()->forget([
                    'otp_user_id',
                    'otp_attempts'
                ]);

                return redirect('/login')->with(
                    'error',
                    'Terlalu banyak percobaan OTP. Silakan login ulang.'
                );
            }

            $remaining = 3 - $attempts;

            return back()->with(
                'error',
                "OTP salah. Sisa percobaan {$remaining} kali"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK OTP EXPIRED
        |--------------------------------------------------------------------------
        */
        if (
            !$user->otp_expired_at ||
            Carbon::now()->gt($user->otp_expired_at)
        ) {

            return back()->with(
                'error',
                'OTP sudah expired'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HAPUS OTP
        |--------------------------------------------------------------------------
        */
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        /*
        |--------------------------------------------------------------------------
        | LOGIN USER
        |--------------------------------------------------------------------------
        */
        Auth::login($user);

        // ✅ TAMBAHKAN AUDIT LOG UNTUK LOGIN
        AuditLog::create([
            'user_id'    => $user->id,
            'user_name'  => $user->name ?? $user->email,
            'aksi'       => 'LOGIN',
            'modul'      => 'SYSTEM',
            'deskripsi'  => 'User ' . ($user->name ?? $user->email) . ' login ke sistem',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        request()->session()->regenerate();

        // HAPUS SESSION OTP
        session()->forget([
            'otp_user_id',
            'otp_attempts'
        ]);

        return redirect('/admin')
            ->with('success', 'Login berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | RESEND OTP
    |--------------------------------------------------------------------------
    */
    public function resendOtp(Request $request)
    {
        try {

            // AMBIL USER
            $user = User::find(session('otp_user_id'));

            // SESSION HILANG
            if (!$user) {

                return response()->json([
                    'success' => false,
                    'message' => 'Session OTP habis'
                ], 401);

            }

            /*
            |--------------------------------------------------------------------------
            | COOLDOWN RESEND OTP
            |--------------------------------------------------------------------------
            */
            if (
                $user->otp_requested_at &&
                Carbon::parse($user->otp_requested_at)
                    ->addSeconds(60)
                    ->isFuture()
            ) {

                $remaining = now()->diffInSeconds(
                    Carbon::parse($user->otp_requested_at)
                        ->addSeconds(60)
                );

                return response()->json([
                    'success' => false,
                    'message' => "Tunggu {$remaining} detik"
                ], 429);

            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE OTP BARU
            |--------------------------------------------------------------------------
            */
            $otp = rand(100000, 999999);

            // SIMPAN OTP BARU
            $user->otp = $otp;
            $user->otp_requested_at = now();
            $user->otp_expired_at = now()->addMinutes(5);
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | FORMAT PESAN WA
            |--------------------------------------------------------------------------
            */
            $message =
                "🔄 *Resend OTP*\n\n" .
                "Kode OTP baru Anda:\n" .
                "*{$otp}*\n\n" .
                "OTP berlaku selama 5 menit.\n" .
                "Jangan bagikan kode ini kepada siapa pun.";

            /*
            |--------------------------------------------------------------------------
            | KIRIM OTP KE WHATSAPP
            |--------------------------------------------------------------------------
            */
            Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => env('FONNTE_TOKEN')
                ])
                ->post('https://api.fonnte.com/send', [
                    'target' => $user->phone,
                    'message' => $message,
                    'footer' => false,
                ]);

            // RESET ATTEMPT
            session()->forget('otp_attempts');

            return response()->json([
                'success' => true,
                'message' => 'OTP baru berhasil dikirim'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        // ✅ AMBIL USER SEBELUM LOGOUT
        $user = Auth::user();
        
        // ✅ CATAT LOGOUT KE AUDIT LOG
        if ($user) {
            AuditLog::create([
                'user_id'    => $user->id,
                'user_name'  => $user->name ?? $user->email,
                'aksi'       => 'LOGOUT',
                'modul'      => 'SYSTEM',
                'deskripsi'  => 'User ' . ($user->name ?? $user->email) . ' logout dari sistem',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}