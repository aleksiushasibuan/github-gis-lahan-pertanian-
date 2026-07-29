<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin GIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .page-bg {
            background-image: url('{{ asset('images/bg-sawah-login.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="relative min-h-screen overflow-hidden">

    <!-- Background full layar -->
    <div class="absolute inset-0 page-bg"></div>

    <!-- Overlay supaya tulisan/card tetap jelas -->
    <div class="absolute inset-0 bg-white/35 sm:bg-white/25 backdrop-blur-[2px]"></div>

    <!-- Aksen -->
    <div class="absolute -top-20 -left-20 w-56 h-56 bg-emerald-400/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-teal-400/20 rounded-full blur-3xl"></div>

    <!-- Wrapper -->
    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-6 sm:px-6">
        
        <!-- Card login -->
        <div class="w-full max-w-sm sm:max-w-md rounded-2xl sm:rounded-3xl border border-white/60 bg-white/72 shadow-2xl backdrop-blur-md">
            <div class="px-4 py-5 sm:px-6 sm:py-7 md:px-8 md:py-8">

                <!-- Header -->
                <div class="text-center mb-5 sm:mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-700 shadow-lg mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2m0 18l6-2m-6 2V2m6 16l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-.553-.894L15 2m0 16V2m-6 0l6 2" />
                        </svg>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">
                        Masuk Admin
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xs mx-auto">
                        Login untuk mengakses dashboard Web GIS Pertanian dengan aman dan mudah.
                    </p>
                </div>

                @if(session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50/95 px-4 py-3 text-xs sm:text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="/login" class="space-y-4 sm:space-y-5">
                    @csrf

                    

    <!-- EMAIL -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Alamat Email
        </label>

        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5A2.25 2.25 0 002.25 6.75m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615A2.25 2.25 0 012.25 6.993V6.75" />
                </svg>
            </span>

            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}"
                placeholder="Masukkan email admin"
                required
                class="w-full rounded-xl border border-slate-300 bg-white/90 pl-11 sm:pl-12 pr-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition duration-200 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
            >
        </div>

        @error('email')
            <span class="text-red-500 text-xs mt-1 block">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- PASSWORD -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Password
        </label>

        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5m-.75 0h9a1.5 1.5 0 011.5 1.5v6a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 016 18v-6a1.5 1.5 0 011.5-1.5z" />
                </svg>
            </span>

            <input 
                type="password" 
                name="password"
                placeholder="Masukkan password"
                required
                class="w-full rounded-xl border border-slate-300 bg-white/90 pl-11 sm:pl-12 pr-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition duration-200 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
            >
        </div>

        @error('password')
            <span class="text-red-500 text-xs mt-1 block">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- REMEMBER -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between text-sm">
        <label class="flex items-center gap-2 text-slate-700">
            <input 
                type="checkbox" 
                name="remember" 
                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >
            <span>Ingat saya</span>
        </label>

        <a href="#" class="font-medium text-emerald-700 hover:text-emerald-800 hover:underline">
            Lupa password?
        </a>
    </div>

    <!-- BUTTON -->
    <button 
        type="submit"
        class="w-full rounded-xl bg-emerald-700 px-4 py-3 text-sm sm:text-base font-semibold text-white shadow-lg transition duration-200 hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200"
    >
        Login & Kirim OTP
    </button>
</form>

                <div class="mt-5 sm:mt-7 border-t border-slate-200/70 pt-4">
                    
                </div>

            </div>
        </div>
    </div>

</body>
</html>