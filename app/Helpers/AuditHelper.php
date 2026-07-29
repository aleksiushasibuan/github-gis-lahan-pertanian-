<?php
namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditHelper
{
    public static function log($aksi, $modul, $deskripsi = null)
    {
        try {
            // Pastikan user terautentikasi
            $userId = auth()->id();
            $userName = auth()->user()?->name ?? 'System';
            
            // Log ke file untuk debugging
            Log::info('AuditHelper dipanggil', [
                'aksi' => $aksi,
                'modul' => $modul,
                'user_id' => $userId,
                'user_name' => $userName,
                'deskripsi' => $deskripsi
            ]);
            
            $audit = AuditLog::create([
                'user_id' => $userId,
                'user_name' => $userName,
                'aksi' => $aksi,
                'modul' => $modul,
                'deskripsi' => $deskripsi,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            Log::info('Audit log berhasil disimpan', ['id' => $audit->id]);
            
            return $audit;
            
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan audit log: ' . $e->getMessage());
            return null;
        }
    }
}