<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">

        <!-- HEADER -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">
                Verifikasi OTP
            </h1>

            <p class="text-slate-500">
                Masukkan kode OTP yang dikirim ke WhatsApp Anda
            </p>
        </div>

        <!-- ERROR -->
        @if(session('error'))
            <div id="errorAlert"
                class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- SUCCESS -->
        @if(session('success'))
            <div id="successAlert"
                class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- SUCCESS AJAX -->
        <div id="successBox"
            class="hidden mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            OTP baru berhasil dikirim
        </div>

        <!-- INFO OTP -->
        <div id="infoBox"
            class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            OTP sedang dikirim ke WhatsApp Anda. Mohon tunggu beberapa detik...
        </div>

        <!-- COUNTDOWN -->
        <div class="mb-5 text-center">

            <p class="text-sm text-slate-500">
                Anda bisa meminta OTP baru dalam
            </p>

            <p id="countdown"
                class="text-4xl font-bold text-emerald-700 mt-2">
                60
            </p>

        </div>

        <!-- FORM OTP -->
        <form method="POST" action="/verify-otp" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Kode OTP
                </label>

                <input 
                    type="text"
                    name="otp"
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="Masukkan 6 digit OTP"
                    required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <button 
                type="submit"
                class="w-full rounded-xl bg-emerald-700 py-3 text-white font-semibold hover:bg-emerald-800 transition"
            >
                Verifikasi OTP
            </button>
        </form>

        <!-- RESEND OTP -->
        <button
            id="resendBtn"
            disabled
            class="w-full mt-4 rounded-xl bg-slate-300 py-3 text-white font-semibold cursor-not-allowed transition"
        >
            Kirim Ulang OTP
        </button>

    </div>

    <!-- SCRIPT -->
    <script>

        let timeLeft = 60;

        const countdown = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        const successBox = document.getElementById('successBox');

        let timer;

        /*
        |--------------------------------------------------------------------------
        | AUTO HIDE ALERT
        |--------------------------------------------------------------------------
        */
        function autoHide(elementId) {

            const element = document.getElementById(elementId);

            if (element) {

                setTimeout(() => {

                    element.classList.add('fade-out');

                    setTimeout(() => {
                        element.remove();
                    }, 500);

                }, 15000);

            }

        }

        autoHide('errorAlert');
        autoHide('successAlert');
        autoHide('successBox');

        /*
        |--------------------------------------------------------------------------
        | COUNTDOWN
        |--------------------------------------------------------------------------
        */
        function startCountdown() {

            resendBtn.disabled = true;

            resendBtn.classList.remove(
                'bg-emerald-700',
                'hover:bg-emerald-800',
                'cursor-pointer'
            );

            resendBtn.classList.add(
                'bg-slate-300',
                'cursor-not-allowed'
            );

            timer = setInterval(() => {

                timeLeft--;

                countdown.innerText = timeLeft;

                if (timeLeft <= 0) {

                    clearInterval(timer);

                    countdown.innerHTML = `
                        <span class="text-green-600 text-lg font-bold">
                            Anda bisa meminta OTP baru
                        </span>
                    `;

                    resendBtn.disabled = false;

                    resendBtn.classList.remove(
                        'bg-slate-300',
                        'cursor-not-allowed'
                    );

                    resendBtn.classList.add(
                        'bg-emerald-700',
                        'hover:bg-emerald-800',
                        'cursor-pointer'
                    );

                }

            }, 1000);

        }

        startCountdown();

        /*
        |--------------------------------------------------------------------------
        | RESEND OTP
        |--------------------------------------------------------------------------
        */
        resendBtn.addEventListener('click', async function () {

            resendBtn.disabled = true;
            resendBtn.innerText = 'Mengirim...';

            try {

                const response = await fetch('/resend-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    alert(data.message);
                    return;
                }

                successBox.classList.remove('hidden');

                successBox.innerText = data.message;

                autoHide('successBox');

                timeLeft = 60;

                countdown.innerText = timeLeft;

                resendBtn.innerText = 'Kirim Ulang OTP';

                clearInterval(timer);

                startCountdown();

            } catch (error) {

                alert('Gagal mengirim OTP');

                resendBtn.disabled = false;
                resendBtn.innerText = 'Kirim Ulang OTP';

            }

        });

    </script>

</body>
</html>