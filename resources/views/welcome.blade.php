<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Phage Pro</title>

    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <script src="{{ asset('assets/js/tailwind.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 193, 7, 0.22), transparent 28%),
                radial-gradient(circle at 85% 20%, rgba(255, 145, 0, 0.18), transparent 28%),
                radial-gradient(circle at 50% 100%, rgba(104, 52, 20, 0.55), transparent 45%),
                linear-gradient(135deg, #2b1308 0%, #6e3215 28%, #ad5d29 58%, #4b210f 100%);
            color: white;
        }

        /* Premium ambient background */
        .ambient {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .ambient::before {
            content: "";
            position: absolute;
            width: 700px;
            height: 700px;
            left: -250px;
            top: -300px;
            border-radius: 50%;
            background: rgba(255, 196, 87, 0.13);
            filter: blur(100px);
        }

        .ambient::after {
            content: "";
            position: absolute;
            width: 650px;
            height: 650px;
            right: -250px;
            bottom: -300px;
            border-radius: 50%;
            background: rgba(255, 119, 0, 0.15);
            filter: blur(100px);
        }

        /* Glass panel */
        .glass {
            position: relative;
            background:
                linear-gradient(
                    145deg,
                    rgba(255, 255, 255, 0.16),
                    rgba(255, 255, 255, 0.055)
                );
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow:
                0 35px 80px rgba(0, 0, 0, 0.38),
                inset 0 1px 0 rgba(255, 255, 255, 0.22),
                inset 0 -1px 0 rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
        }

        .glass::before {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: inherit;
            background: linear-gradient(
                120deg,
                rgba(255, 255, 255, 0.10),
                transparent 35%,
                transparent 65%,
                rgba(255, 255, 255, 0.05)
            );
            pointer-events: none;
        }

        /* 3D logo */
        .logo-3d {
            width: 78px;
            height: 78px;
            margin: auto;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(145deg, #ffd66b, #d97706 48%, #78350f);
            border: 1px solid rgba(255,255,255,0.35);
            box-shadow:
                0 20px 40px rgba(0,0,0,0.38),
                inset 0 2px 2px rgba(255,255,255,0.5),
                inset 0 -7px 15px rgba(87, 38, 8, 0.35);
            transform: perspective(600px) rotateX(8deg);
        }

        .logo-3d svg {
            filter: drop-shadow(0 5px 5px rgba(0,0,0,0.35));
        }

        /* Header typography */
        .premium-title {
            background: linear-gradient(
                100deg,
                #fff7d6 0%,
                #ffd76a 28%,
                #fff3c4 50%,
                #f59e0b 78%,
                #fff0bd 100%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shine 5s linear infinite;
            text-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        /* Login card */
        .login-card {
            transform: perspective(1200px) rotateX(1deg);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .login-card:hover {
            transform: perspective(1200px) rotateX(0deg) translateY(-4px);
            box-shadow:
                0 45px 100px rgba(0,0,0,0.45),
                inset 0 1px 0 rgba(255,255,255,0.25);
        }

        /* Inputs */
        .premium-input {
            background: rgba(0, 0, 0, 0.18);
            border: 1px solid rgba(255,255,255,0.16);
            box-shadow:
                inset 0 2px 8px rgba(0,0,0,0.16),
                0 1px 0 rgba(255,255,255,0.05);
            transition: all 0.25s ease;
        }

        .premium-input:hover {
            border-color: rgba(255, 210, 100, 0.35);
        }

        .premium-input:focus {
            outline: none;
            border-color: rgba(255, 205, 85, 0.75);
            background: rgba(0,0,0,0.23);
            box-shadow:
                0 0 0 3px rgba(250, 204, 21, 0.10),
                inset 0 2px 8px rgba(0,0,0,0.18),
                0 8px 20px rgba(0,0,0,0.15);
        }

        ::placeholder {
            color: rgba(255,255,255,0.43);
        }

        /* 3D Sign in button */
        .premium-button {
            position: relative;
            overflow: hidden;
            background: linear-gradient(
                145deg,
                #facc15,
                #d97706 55%,
                #92400e
            );
            border: 1px solid rgba(255,255,255,0.30);
            box-shadow:
                0 12px 25px rgba(0,0,0,0.30),
                inset 0 1px 0 rgba(255,255,255,0.45),
                inset 0 -5px 10px rgba(95,42,5,0.28);
            transition: all 0.25s ease;
        }

        .premium-button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,0.45),
                transparent
            );
            transform: skewX(-20deg);
            transition: left 0.6s ease;
        }

        .premium-button:hover::before {
            left: 140%;
        }

        .premium-button:hover {
            transform: translateY(-2px);
            box-shadow:
                0 18px 32px rgba(0,0,0,0.35),
                inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .premium-button:active {
            transform: translateY(1px);
        }

        /* Image preview */
        .preview-wrap {
            position: relative;
            perspective: 1400px;
        }

        .preview-card {
            position: relative;
            transform:
                perspective(1400px)
                rotateY(-8deg)
                rotateX(3deg)
                rotateZ(1deg);
            transition: all 0.5s ease;
            box-shadow:
                35px 40px 70px rgba(0,0,0,0.42),
                -5px 5px 25px rgba(255,180,50,0.08);
        }

        .preview-card:hover {
            transform:
                perspective(1400px)
                rotateY(0deg)
                rotateX(0deg)
                rotateZ(0deg)
                translateY(-8px);
        }

        .preview-card::before {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 19px;
            background: linear-gradient(
                135deg,
                rgba(255,255,255,0.45),
                transparent 30%,
                transparent 70%,
                rgba(255,180,50,0.35)
            );
            z-index: -1;
        }

        .preview-card img {
            display: block;
            border-radius: 18px;
        }

        .preview-glow {
            position: absolute;
            inset: 10% 5% -10%;
            background: rgba(255, 180, 50, 0.22);
            filter: blur(55px);
            z-index: -2;
        }

        /* Floating objects */
        .floating-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(1px);
            box-shadow:
                inset -10px -12px 20px rgba(0,0,0,0.25),
                inset 8px 8px 15px rgba(255,255,255,0.22),
                0 20px 45px rgba(0,0,0,0.20);
        }

        .orb-one {
            width: 90px;
            height: 90px;
            top: 12%;
            left: 8%;
            background: linear-gradient(145deg, #facc15, #92400e);
            animation: floatOne 7s ease-in-out infinite;
        }

        .orb-two {
            width: 55px;
            height: 55px;
            right: 13%;
            top: 16%;
            background: linear-gradient(145deg, #fde68a, #b45309);
            animation: floatTwo 6s ease-in-out infinite;
        }

        .orb-three {
            width: 75px;
            height: 75px;
            left: 18%;
            bottom: 10%;
            background: linear-gradient(145deg, #d97706, #451a03);
            animation: floatThree 8s ease-in-out infinite;
        }

        @keyframes floatOne {
            0%,100% { transform: translate3d(0,0,0); }
            50% { transform: translate3d(30px,-35px,0); }
        }

        @keyframes floatTwo {
            0%,100% { transform: translate3d(0,0,0); }
            50% { transform: translate3d(-20px,30px,0); }
        }

        @keyframes floatThree {
            0%,100% { transform: translate3d(0,0,0); }
            50% { transform: translate3d(35px,-25px,0); }
        }

        /* Loader */
        #page-loader {
            background:
                radial-gradient(circle at center, #3b1b0d, #160903);
        }

        #page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-bars div {
            border-radius: 999px;
            box-shadow: 0 0 15px rgba(250,204,21,0.55);
        }

        @keyframes loader-bar {
            0%, 100% {
                transform: scaleY(1);
                opacity: 0.45;
            }
            50% {
                transform: scaleY(2.2);
                opacity: 1;
            }
        }

        .animate-loader-bar {
            animation: loader-bar 1s infinite ease-in-out;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* Error */
        .error-box {
            background: rgba(127, 29, 29, 0.45);
            border: 1px solid rgba(252,165,165,0.25);
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.20);
        }

        /* Mobile */
        @media (max-width: 768px) {
            html,
            body {
                overflow-y: auto;
            }

            .preview-wrap {
                display: none;
            }

            .orb-one {
                width: 60px;
                height: 60px;
            }

            .orb-two {
                width: 40px;
                height: 40px;
            }

            .orb-three {
                width: 50px;
                height: 50px;
            }

            .premium-title {
                font-size: 1.65rem;
                line-height: 1.2;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center relative">

@if ($errors->any())
    <div class="error-box fixed top-5 left-1/2 -translate-x-1/2 z-[10000] text-white px-5 py-4 rounded-xl text-sm max-w-md w-[90%]">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Page Loader -->
<div id="page-loader"
     class="fixed inset-0 z-[9999] flex items-center justify-center transition-opacity duration-700">

    <div class="text-center">
        <div class="loader-bars flex justify-center items-center space-x-1.5 h-10">
            <div class="w-2 h-6 bg-yellow-400 animate-loader-bar"></div>
            <div class="w-2 h-6 bg-yellow-400 animate-loader-bar delay-100"></div>
            <div class="w-2 h-6 bg-yellow-400 animate-loader-bar delay-200"></div>
            <div class="w-2 h-6 bg-yellow-400 animate-loader-bar delay-300"></div>
            <div class="w-2 h-6 bg-yellow-400 animate-loader-bar delay-400"></div>
        </div>

        <p class="mt-4 text-xs tracking-[0.35em] uppercase text-yellow-200/70">
            TARI
        </p>
    </div>
</div>

<!-- Ambient Background -->
<div class="ambient"></div>

<!-- Floating 3D Objects -->
<div class="floating-orb orb-one"></div>
<div class="floating-orb orb-two"></div>
<div class="floating-orb orb-three"></div>

<!-- Main Container -->
<div class="relative z-20 w-full max-w-6xl mx-auto px-5 py-8">

    <!-- Header -->
    <div class="text-center mb-7">

        <div class="logo-3d mb-4">
            <svg width="44"
                 height="44"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="#fff7d6"
                 stroke-width="1.4">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 6v6l4 2M12 3C6.477 3 2 7.477 2 13s4.477 10 10 10 10-4.477 10-10S17.523 3 12 3z"/>
            </svg>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                    bg-white/10 border border-white/15 text-yellow-100/80
                    text-[10px] uppercase tracking-[0.25em] mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 shadow-[0_0_10px_#4ade80]"></span>
            Secure Management Platform
        </div>

        <h1 class="premium-title text-3xl md:text-4xl lg:text-5xl font-black tracking-tight">
            CASHEW MANAGEMENT SYSTEM - TARI
        </h1>

        <p class="text-white/65 mt-2 text-sm tracking-wide">
            Powered by TARI
        </p>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row items-center justify-center gap-8 lg:gap-12 w-full">

        <!-- Login Card -->
        <div class="login-card glass rounded-3xl p-7 md:p-8 w-full max-w-sm">

            <!-- Card heading -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                bg-yellow-400/15 border border-yellow-300/20">
                        <i class="fas fa-lock text-yellow-300 text-sm"></i>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Welcome Back
                        </h2>

                        <p class="text-xs text-white/45">
                            Sign in to continue
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email"
                           class="block text-white/70 text-xs font-semibold uppercase tracking-wider mb-2">
                        Email Address
                    </label>

                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/35">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>

                        <input type="email"
                               name="email"
                               id="email"
                               autocomplete="email"
                               class="premium-input w-full pl-10 pr-3 py-3 rounded-xl
                                      text-white text-sm"
                               placeholder="you@example.com"
                               required>
                    </div>

                    @error('email')
                    <p class="text-red-300 text-xs mt-2">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password"
                           class="block text-white/70 text-xs font-semibold uppercase tracking-wider mb-2">
                        Password
                    </label>

                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/35">
                            <i class="fas fa-key text-sm"></i>
                        </span>

                        <input type="password"
                               name="password"
                               id="password"
                               autocomplete="current-password"
                               class="premium-input w-full pl-10 pr-16 py-3 rounded-xl
                                      text-white text-sm"
                               placeholder="••••••••"
                               required>

                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-[11px] font-semibold text-yellow-300/70
                                       hover:text-yellow-200 transition">
                            Show
                        </button>
                    </div>

                    @error('password')
                    <p class="text-red-300 text-xs mt-2">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Sign In -->
                <button id="loginBtn"
                        type="submit"
                        class="premium-button w-full flex items-center justify-center
                               gap-2 py-3 px-4 rounded-xl text-sm font-bold
                               text-white tracking-wide">

                    <span id="btnText">
                        Sign In
                    </span>

                    <svg id="spinner"
                         class="hidden w-4 h-4 animate-spin"
                         viewBox="0 0 24 24">

                        <circle cx="12"
                                cy="12"
                                r="10"
                                stroke="rgba(255,255,255,0.35)"
                                stroke-width="4"
                                fill="none"/>

                        <path d="M12 2a10 10 0 0110 10"
                              stroke="white"
                              stroke-width="4"
                              fill="none"/>
                    </svg>
                </button>

                <!-- Security indicator -->
                <div class="flex items-center justify-center gap-2 pt-1 text-[10px] text-white/35">
                    <i class="fas fa-shield-alt text-green-400/70"></i>
                    Secure authentication
                </div>

            </form>
        </div>

        <!-- Dashboard Preview -->
        <div class="preview-wrap w-full max-w-md">

            <div class="preview-glow"></div>

            <div class="preview-card">
                <img src="images/phonepro1.png"
                     alt="Cashew Dashboard Preview"
                     class="w-full object-cover border border-white/25">

                <!-- Floating label -->
                <div class="absolute -bottom-5 left-1/2 -translate-x-1/2
                            glass rounded-2xl px-5 py-3
                            flex items-center gap-3 whitespace-nowrap">

                    <div class="w-9 h-9 rounded-xl bg-yellow-400/15
                                flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-300"></i>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-white">
                            Smart Dashboard
                        </p>
                        <p class="text-[10px] text-white/45">
                            Inventory • Sales • Reports
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    /* Page loader */
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('page-loader').classList.add('hidden');
        }, 350);
    });

    /* Password toggle */
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleBtn = event.currentTarget;

        const isHidden = passwordInput.type === "password";

        passwordInput.type = isHidden ? "text" : "password";
        toggleBtn.textContent = isHidden ? "Hide" : "Show";
    }

    /* Login loading state */
    document.getElementById('loginBtn').addEventListener('click', function () {
        const form = this.closest('form');

        if (!form.checkValidity()) {
            return;
        }

        document.getElementById('spinner').classList.remove('hidden');
        document.getElementById('btnText').textContent = 'Signing In...';

        this.classList.add('opacity-90');
    });

    /* Subtle mouse-based 3D movement */
    const preview = document.querySelector('.preview-card');

    if (preview && window.innerWidth > 768) {
        document.addEventListener('mousemove', (event) => {

            const x = (event.clientX / window.innerWidth - 0.5);
            const y = (event.clientY / window.innerHeight - 0.5);

            preview.style.transform = `
                perspective(1400px)
                rotateY(${x * -7}deg)
                rotateX(${y * 5}deg)
                rotateZ(1deg)
            `;
        });
    }
</script>

</body>
</html>
