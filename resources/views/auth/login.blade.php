{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8" />--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0" />--}}
{{--    <title>Login - TARI - CASHEW</title>--}}
{{--    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">--}}
{{--    <script src="{{ asset('assets/js/tailwind.min.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/js/chart.min.js') }}"></script>--}}
{{--    <style>--}}
{{--        body {--}}
{{--            font-family: 'Poppins', sans-serif;--}}
{{--            background: linear-gradient(to bottom right, #AD5D29 0%, #f5e6da 40%, #AD5D29 75%, #6e3618 100%);--}}
{{--            margin: 0;--}}
{{--            padding: 0;--}}
{{--            height: 100vh;--}}
{{--        }--}}

{{--        .glass {--}}
{{--            background: rgba(60, 32, 20, 0.3);--}}
{{--            border: 1px solid rgba(255, 255, 255, 0.15);--}}
{{--            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);--}}
{{--            backdrop-filter: blur(16px);--}}
{{--            -webkit-backdrop-filter: blur(16px);--}}
{{--        }--}}

{{--        ::placeholder {--}}
{{--            color: rgba(255, 255, 255, 0.6);--}}
{{--        }--}}

{{--        html, body {--}}
{{--            overflow: hidden;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body class="flex items-center justify-center relative">--}}

{{--<!-- Main Container -->--}}
{{--<div class="flex flex-col items-center justify-center w-full max-w-6xl mx-auto p-4 gap-8 z-20">--}}

{{--    <!-- Logo and Header -->--}}
{{--    <div class="text-center">--}}
{{--        <!-- Cashew leaf SVG Logo -->--}}
{{--        <svg width="60" height="60" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2">--}}
{{--            <!-- Cashew nut shape -->--}}
{{--            <path d="M20 38 C14 32, 12 22, 18 16 C24 10, 36 10, 42 16 C50 24, 48 36, 40 42 C34 47, 24 46, 20 38Z"--}}
{{--                  fill="#facc15" fill-opacity="0.9" stroke="#fde68a" stroke-width="1.5"/>--}}
{{--            <!-- Stem -->--}}
{{--            <path d="M40 42 C44 46, 46 52, 44 56" stroke="#86efac" stroke-width="2.5" stroke-linecap="round"/>--}}
{{--            <!-- Leaf -->--}}
{{--            <path d="M44 56 C40 50, 34 48, 44 56Z M44 56 C50 52, 52 46, 44 56Z"--}}
{{--                  fill="#4ade80" stroke="#86efac" stroke-width="1"/>--}}
{{--            <!-- Shine on nut -->--}}
{{--            <ellipse cx="27" cy="22" rx="5" ry="3" fill="white" fill-opacity="0.25" transform="rotate(-30 27 22)"/>--}}
{{--        </svg>--}}
{{--        <h1 class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-br from-yellow-200 via-orange-500 to-yellow-800">--}}
{{--            CASHEW MANAGEMENT SYSTEM--}}
{{--        </h1>--}}
{{--        <p class="text-white/80 mt-1 text-sm">Powered by TARI - NALIENDELE</p>--}}
{{--    </div>--}}

{{--    <div class="flex flex-col md:flex-row items-center justify-center gap-6 w-full">--}}

{{--        <!-- Glass Login Card -->--}}
{{--        <div class="glass p-6 rounded-xl shadow-md w-full max-w-sm">--}}
{{--            <form action="{{ route('login') }}" method="POST" class="space-y-4">--}}
{{--                @csrf--}}

{{--                --}}{{-- Error messages --}}
{{--                @if ($errors->any())--}}
{{--                    <div class="bg-red-500/20 border border-red-400/40 rounded-md px-4 py-3">--}}
{{--                        <ul class="text-red-200 text-sm space-y-1 list-disc list-inside">--}}
{{--                            @foreach ($errors->all() as $error)--}}
{{--                                <li>{{ $error }}</li>--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                @endif--}}

{{--                <div>--}}
{{--                    <label for="email" class="block text-white/80 text-sm">Email</label>--}}
{{--                    <input type="email" name="email" id="email"--}}
{{--                           autocomplete="email"--}}
{{--                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm"--}}
{{--                           placeholder="you@example.com" required>--}}
{{--                </div>--}}

{{--                <div>--}}
{{--                    <label for="password" class="block text-white/80 text-sm">Password</label>--}}
{{--                    <div class="relative">--}}
{{--                        <input type="password" name="password" id="password"--}}
{{--                               autocomplete="current-password"--}}
{{--                               class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm pr-10"--}}
{{--                               placeholder="••••••••" required>--}}
{{--                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3 text-xs text-white/60 hover:text-white">--}}
{{--                            Show--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <button type="submit" id="loginBtn" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md text-base font-medium transition duration-300 bg-yellow-900 hover:bg-yellow-800 text-white">--}}
{{--                    <span id="btnText">Sign In</span>--}}
{{--                    <svg id="spinner" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">--}}
{{--                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4" fill="none" />--}}
{{--                        <path d="M12 2a10 10 0 0110 10" stroke="#facc15" stroke-width="4" fill="none"/>--}}
{{--                    </svg>--}}
{{--                </button>--}}
{{--            </form>--}}
{{--        </div>--}}

{{--        <!-- Dashboard Image Preview -->--}}
{{--        <div class="w-full max-w-md">--}}
{{--            <img src="images/img.png" alt="PhoneStore Dashboard Preview"--}}
{{--                 class="rounded-2xl shadow-lg w-full object-cover border-2 border-yellow-200/50">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<script>--}}
{{--    function togglePassword() {--}}
{{--        const passwordInput = document.getElementById("password");--}}
{{--        const toggleBtn = event.currentTarget;--}}
{{--        const isHidden = passwordInput.type === "password";--}}
{{--        passwordInput.type = isHidden ? "text" : "password";--}}
{{--        toggleBtn.textContent = isHidden ? "Hide" : "Show";--}}
{{--    }--}}

{{--    document.getElementById('loginBtn').addEventListener('click', function () {--}}
{{--        document.getElementById('spinner').classList.remove('hidden');--}}
{{--        document.getElementById('btnText').textContent = 'Signing In...';--}}
{{--    });--}}
{{--</script>--}}

{{--</body>--}}
{{--</html>--}}


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - TARI - CASHEW</title>
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <script src="{{ asset('assets/js/tailwind.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #AD5D29 0%, #f5e6da 40%, #AD5D29 75%, #6e3618 100%);
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .glass {
            background: rgba(60, 32, 20, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        ::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        html, body {
            overflow: hidden;
        }
    </style>
</head>
<body class="flex items-center justify-center relative">

<!-- Main Container -->
<div class="flex flex-col items-center justify-center w-full max-w-6xl mx-auto p-4 gap-8 z-20">

    <!-- Logo and Header -->
    <div class="text-center">
        <!-- Cashew leaf SVG Logo -->
        <svg width="60" height="60" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2">
            <!-- Cashew nut shape -->
            <path d="M20 38 C14 32, 12 22, 18 16 C24 10, 36 10, 42 16 C50 24, 48 36, 40 42 C34 47, 24 46, 20 38Z"
                  fill="#facc15" fill-opacity="0.9" stroke="#fde68a" stroke-width="1.5"/>
            <!-- Stem -->
            <path d="M40 42 C44 46, 46 52, 44 56" stroke="#86efac" stroke-width="2.5" stroke-linecap="round"/>
            <!-- Leaf -->
            <path d="M44 56 C40 50, 34 48, 44 56Z M44 56 C50 52, 52 46, 44 56Z"
                  fill="#4ade80" stroke="#86efac" stroke-width="1"/>
            <!-- Shine on nut -->
            <ellipse cx="27" cy="22" rx="5" ry="3" fill="white" fill-opacity="0.25" transform="rotate(-30 27 22)"/>
        </svg>
        <h1 class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-br from-yellow-200 via-orange-500 to-yellow-800">
            CASHEW MANAGEMENT SYSTEM
        </h1>
        <p class="text-white/80 mt-1 text-sm">Powered by TARI - NALIENDELE</p>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-center gap-6 w-full">

        <!-- Glass Login Card -->
        <div class="glass p-6 rounded-xl shadow-md w-full max-w-sm">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Error messages --}}
                @if ($errors->any())
                    <div class="bg-red-500/20 border border-red-400/40 rounded-md px-4 py-3">
                        <ul class="text-red-200 text-sm space-y-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-white/80 text-sm">Email</label>
                    <input type="email" name="email" id="email"
                           autocomplete="email"
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label for="password" class="block text-white/80 text-sm">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               autocomplete="current-password"
                               class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm pr-10"
                               placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3 text-xs text-white/60 hover:text-white">
                            Show
                        </button>
                    </div>
                </div>

                <button type="submit" id="loginBtn" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md text-base font-medium transition duration-300 bg-yellow-900 hover:bg-yellow-800 text-white">
                    <span id="btnText">Sign In</span>
                    <svg id="spinner" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4" fill="none" />
                        <path d="M12 2a10 10 0 0110 10" stroke="#facc15" stroke-width="4" fill="none"/>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Dashboard Image Preview -->
        <div class="w-full max-w-md">
            <img src="images/img.png" alt="PhoneStore Dashboard Preview"
                 class="rounded-2xl shadow-lg w-full object-cover border-2 border-yellow-200/50">
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleBtn = event.currentTarget;
        const isHidden = passwordInput.type === "password";
        passwordInput.type = isHidden ? "text" : "password";
        toggleBtn.textContent = isHidden ? "Hide" : "Show";
    }

    document.getElementById('loginBtn').addEventListener('click', function () {
        document.getElementById('spinner').classList.remove('hidden');
        document.getElementById('btnText').textContent = 'Signing In...';
    });
</script>

</body>
</html>
