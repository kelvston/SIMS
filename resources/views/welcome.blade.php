
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
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: white;
            background:
                radial-gradient(circle at 12% 15%, rgba(250, 204, 21, .20), transparent 28%),
                radial-gradient(circle at 88% 18%, rgba(74, 222, 128, .12), transparent 25%),
                radial-gradient(circle at 50% 100%, rgba(120, 53, 15, .55), transparent 45%),
                linear-gradient(135deg, #241006 0%, #52240f 30%, #ad5d29 60%, #3f1c0c 100%);
        }

        /* Background atmosphere */
        .background {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .background::before {
            content: "";
            position: absolute;
            width: 650px;
            height: 650px;
            left: -280px;
            top: -280px;
            border-radius: 50%;
            background: rgba(250, 204, 21, .12);
            filter: blur(100px);
        }

        .background::after {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            right: -250px;
            bottom: -280px;
            border-radius: 50%;
            background: rgba(74, 222, 128, .09);
            filter: blur(110px);
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            background: linear-gradient(145deg, #fde68a, #d97706);
            box-shadow:
                inset 4px 4px 8px rgba(255,255,255,.25),
                inset -5px -6px 10px rgba(0,0,0,.25),
                0 15px 35px rgba(0,0,0,.25);
        }

        .particle-one {
            width: 72px;
            height: 72px;
            left: 7%;
            top: 14%;
            animation: floatOne 7s ease-in-out infinite;
        }

        .particle-two {
            width: 42px;
            height: 42px;
            right: 11%;
            top: 17%;
            background: linear-gradient(145deg, #bbf7d0, #16a34a);
            animation: floatTwo 6s ease-in-out infinite;
        }

        .particle-three {
            width: 58px;
            height: 58px;
            left: 15%;
            bottom: 9%;
            animation: floatThree 8s ease-in-out infinite;
        }

        @keyframes floatOne {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(28px, -35px, 0);
            }
        }

        @keyframes floatTwo {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(-22px, 30px, 0);
            }
        }

        @keyframes floatThree {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(35px, -20px, 0);
            }
        }

        /* Glass */
        .glass {
            position: relative;
            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.15),
                    rgba(255,255,255,.045)
                );
            border: 1px solid rgba(255,255,255,.20);
            box-shadow:
                0 35px 80px rgba(0,0,0,.40),
                inset 0 1px 0 rgba(255,255,255,.22),
                inset 0 -1px 0 rgba(0,0,0,.15);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
        }

        .glass::before {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: inherit;
            pointer-events: none;
            background:
                linear-gradient(
                    120deg,
                    rgba(255,255,255,.10),
                    transparent 35%,
                    transparent 70%,
                    rgba(255,255,255,.04)
                );
        }

        /* Cashew logo */
        .logo-container {
            width: 86px;
            height: 86px;
            margin: 0 auto;
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    145deg,
                    #fff1a8 0%,
                    #facc15 25%,
                    #d97706 62%,
                    #78350f 100%
                );

            border: 1px solid rgba(255,255,255,.35);

            box-shadow:
                0 25px 45px rgba(0,0,0,.38),
                inset 0 2px 3px rgba(255,255,255,.55),
                inset 0 -8px 15px rgba(92,42,8,.30);

            transform: perspective(700px) rotateX(8deg);
        }

        .logo-container svg {
            filter: drop-shadow(0 6px 5px rgba(0,0,0,.30));
        }

        /* Title */
        .title {
            background:
                linear-gradient(
                    100deg,
                    #fff7d6,
                    #facc15,
                    #fff3c4,
                    #f59e0b,
                    #fff7d6
                );

            background-size: 250% auto;

            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;

            animation: titleShine 6s linear infinite;
            text-shadow: 0 10px 35px rgba(0,0,0,.25);
        }

        @keyframes titleShine {
            to {
                background-position: 250% center;
            }
        }

        /* Login card */
        .login-card {
            transform:
                perspective(1200px)
                rotateX(2deg)
                rotateY(0deg);

            transition:
                transform .45s ease,
                box-shadow .45s ease;
        }

        .login-card:hover {
            transform:
                perspective(1200px)
                rotateX(0deg)
                translateY(-5px);

            box-shadow:
                0 45px 100px rgba(0,0,0,.48),
                inset 0 1px 0 rgba(255,255,255,.28);
        }

        /* Inputs */
        .premium-input {
            background: rgba(0,0,0,.18);
            border: 1px solid rgba(255,255,255,.16);

            box-shadow:
                inset 0 2px 8px rgba(0,0,0,.15),
                0 1px 0 rgba(255,255,255,.04);

            transition: all .25s ease;
        }

        .premium-input:hover {
            border-color: rgba(250,204,21,.35);
        }

        .premium-input:focus {
            outline: none;
            background: rgba(0,0,0,.23);
            border-color: rgba(250,204,21,.75);

            box-shadow:
                0 0 0 3px rgba(250,204,21,.10),
                inset 0 2px 8px rgba(0,0,0,.18),
                0 8px 25px rgba(0,0,0,.15);
        }

        ::placeholder {
            color: rgba(255,255,255,.40);
        }

        /* Button */
        .premium-button {
            position: relative;
            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    #facc15,
                    #d97706 55%,
                    #92400e
                );

            border: 1px solid rgba(255,255,255,.30);

            box-shadow:
                0 14px 28px rgba(0,0,0,.32),
                inset 0 1px 0 rgba(255,255,255,.50),
                inset 0 -6px 12px rgba(90,40,5,.28);

            transition: all .25s ease;
        }

        .premium-button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.50),
                    transparent
                );

            transform: skewX(-20deg);
            transition: left .65s ease;
        }

        .premium-button:hover::before {
            left: 140%;
        }

        .premium-button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 20px 38px rgba(0,0,0,.38),
                inset 0 1px 0 rgba(255,255,255,.55);
        }

        .premium-button:active {
            transform: translateY(1px);
        }

        /* Dashboard */
        .preview-container {
            position: relative;
            perspective: 1400px;
        }

        .preview-glow {
            position: absolute;
            inset: 10% 5% -10%;
            background: rgba(250,204,21,.18);
            filter: blur(55px);
            z-index: -2;
        }

        .preview-card {
            position: relative;

            transform:
                perspective(1400px)
                rotateY(-9deg)
                rotateX(3deg)
                rotateZ(1deg);

            transition: transform .5s ease;

            box-shadow:
                35px 40px 75px rgba(0,0,0,.45),
                -5px 5px 25px rgba(250,204,21,.08);
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
            border-radius: 21px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,.45),
                    transparent 30%,
                    transparent 70%,
                    rgba(250,204,21,.35)
                );

            z-index: -1;
        }

        .preview-card img {
            display: block;
            border-radius: 19px;
        }

        /* Floating preview label */
        .preview-label {
            position: absolute;
            left: 50%;
            bottom: -25px;
            transform: translateX(-50%);

            min-width: 230px;

            display: flex;
            align-items: center;
            gap: 12px;

            padding: 12px 16px;
            border-radius: 18px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.16),
                    rgba(255,255,255,.05)
                );

            border: 1px solid rgba(255,255,255,.18);

            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);

            box-shadow:
                0 20px 45px rgba(0,0,0,.35),
                inset 0 1px 0 rgba(255,255,255,.20);
        }

        /* Loader */
        #page-loader {
            background:
                radial-gradient(circle at center, #4b210e, #170904);
        }

        .loader-bars div {
            border-radius: 999px;
            box-shadow: 0 0 16px rgba(250,204,21,.60);
        }

        @keyframes loader-bar {
            0%, 100% {
                transform: scaleY(1);
                opacity: .45;
            }

            50% {
                transform: scaleY(2.2);
                opacity: 1;
            }
        }

        .animate-loader-bar {
            animation: loader-bar 1s infinite ease-in-out;
        }

        .delay-100 { animation-delay: .1s; }
        .delay-200 { animation-delay: .2s; }
        .delay-300 { animation-delay: .3s; }
        .delay-400 { animation-delay: .4s; }

        /* Error */
        .error-box {
            background: rgba(127,29,29,.42);
            border: 1px solid rgba(252,165,165,.25);
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,.25);
        }

        /* Mobile */
        @media (max-width: 768px) {
            html,
            body {
                overflow-y: auto;
            }

            .preview-container {
                display: none;
            }

            .particle-one {
                width: 50px;
                height: 50px;
            }

            .particle-two {
                width: 30px;
                height: 30px;
            }

            .particle-three {
                width: 42px;
                height: 42px;
            }

            .title {
                font-size: 1.7rem;
                line-height: 1.2;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center relative">

<!-- Background -->
<div class="background"></div>

<div class="particle particle-one"></div>
<div class="particle particle-two"></div>
<div class="particle particle-three"></div>

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

        <p class="mt-4 text-[10px] tracking-[.35em] uppercase text-yellow-200/70">
            TARI - NALIENDELE
        </p>

    </div>
</div>

<!-- Main -->
<div class="relative z-20 w-full max-w-6xl mx-auto px-5 py-8">

    <!-- Header -->
    <div class="text-center mb-7">

        <!-- 3D Cashew Logo -->
        <div class="logo-container mb-4">

            <svg width="55"
                 height="55"
                 viewBox="0 0 64 64"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">

                <!-- Cashew -->
                <path
                    d="M20 38
                       C14 32, 12 22, 18 16
                       C24 10, 36 10, 42 16
                       C50 24, 48 36, 40 42
                       C34 47, 24 46, 20 38Z"
                    fill="#facc15"
                    fill-opacity=".95"
                    stroke="#fff3a3"
                    stroke-width="1.5"/>

                <!-- Stem -->
                <path
                    d="M40 42 C44 46, 46 52, 44 56"
                    stroke="#86efac"
                    stroke-width="2.5"
                    stroke-linecap="round"/>

                <!-- Leaf -->
                <path
                    d="M44 56 C40 50, 34 48, 44 56Z
                       M44 56 C50 52, 52 46, 44 56Z"
                    fill="#4ade80"
                    stroke="#bbf7d0"
                    stroke-width="1"/>

                <!-- Highlight -->
                <ellipse
                    cx="27"
                    cy="22"
                    rx="5"
                    ry="3"
                    fill="white"
                    fill-opacity=".30"
                    transform="rotate(-30 27 22)"/>
            </svg>

        </div>

        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                    bg-white/10 border border-white/15
                    text-yellow-100/80 text-[10px]
                    uppercase tracking-[.25em] mb-3">

            <span class="w-1.5 h-1.5 rounded-full
                         bg-green-400
                         shadow-[0_0_10px_#4ade80]"></span>

            Secure Management Platform
        </div>

        <h1 class="title text-3xl md:text-4xl lg:text-5xl font-black tracking-tight">
            CASHEW MANAGEMENT SYSTEM
        </h1>

        <p class="text-white/65 mt-2 text-sm tracking-wide">
            Powered by TARI - NALIENDELE
        </p>
    </div>

    <!-- Content -->
    <div class="flex flex-col md:flex-row
                items-center justify-center
                gap-8 lg:gap-12 w-full">

        <!-- Login -->
        <div class="login-card glass rounded-3xl p-7 md:p-8
                    w-full max-w-sm">

            <!-- Header -->
            <div class="mb-6">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl
                                flex items-center justify-center
                                bg-yellow-400/15
                                border border-yellow-300/20">

                        <i class="fas fa-seedling text-yellow-300"></i>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold">
                            Welcome Back
                        </h2>

                        <p class="text-xs text-white/45">
                            Access your cashew dashboard
                        </p>
                    </div>

                </div>

            </div>

            <form action="{{ route('login') }}"
                  method="POST"
                  class="space-y-5">

                @csrf

                <!-- Errors -->
                @if ($errors->any())

                    <div class="error-box rounded-xl px-4 py-3">

                        <div class="flex gap-2">

                            <i class="fas fa-circle-exclamation
                                      text-red-300 mt-0.5"></i>

                            <ul class="text-red-200 text-xs space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>

                    </div>

                @endif

                <!-- Email -->
                <div>

                    <label for="email"
                           class="block text-white/70
                                  text-xs font-semibold
                                  uppercase tracking-wider mb-2">

                        Email Address

                    </label>

                    <div class="relative">

                        <span class="absolute left-3.5 top-1/2
                                     -translate-y-1/2
                                     text-white/35">

                            <i class="fas fa-envelope text-sm"></i>

                        </span>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            autocomplete="email"
                            class="premium-input w-full
                                   pl-10 pr-3 py-3 rounded-xl
                                   text-white text-sm"
                            placeholder="you@example.com"
                            required>

                    </div>

                </div>

                <!-- Password -->
                <div>

                    <label for="password"
                           class="block text-white/70
                                  text-xs font-semibold
                                  uppercase tracking-wider mb-2">

                        Password

                    </label>

                    <div class="relative">

                        <span class="absolute left-3.5 top-1/2
                                     -translate-y-1/2
                                     text-white/35">

                            <i class="fas fa-lock text-sm"></i>

                        </span>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            autocomplete="current-password"
                            class="premium-input w-full
                                   pl-10 pr-16 py-3 rounded-xl
                                   text-white text-sm"
                            placeholder="••••••••"
                            required>

                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3
                                   top-1/2 -translate-y-1/2
                                   text-[11px]
                                   font-semibold
                                   text-yellow-300/70
                                   hover:text-yellow-200
                                   transition">

                            Show

                        </button>

                    </div>

                </div>

                <!-- Button -->
                <button
                    type="submit"
                    id="loginBtn"
                    class="premium-button w-full
                           flex items-center justify-center
                           gap-2 py-3 px-4 rounded-xl
                           text-sm font-bold
                           text-white tracking-wide">

                    <span id="btnText">
                        Sign In
                    </span>

                    <svg
                        id="spinner"
                        class="hidden w-4 h-4 animate-spin"
                        viewBox="0 0 24 24">

                        <circle
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="rgba(255,255,255,.35)"
                            stroke-width="4"
                            fill="none"/>

                        <path
                            d="M12 2a10 10 0 0110 10"
                            stroke="white"
                            stroke-width="4"
                            fill="none"/>

                    </svg>

                </button>

                <!-- Security -->
                <div class="flex items-center
                            justify-center gap-2
                            pt-1 text-[10px]
                            text-white/35">

                    <i class="fas fa-shield-halved
                              text-green-400/70"></i>

                    Secure TARI authentication

                </div>

            </form>
        </div>

        <!-- Dashboard -->
        <div class="preview-container w-full max-w-md">

            <div class="preview-glow"></div>

            <div class="preview-card">

                <img
                    src="images/img.png"
                    alt="TARI Cashew Dashboard Preview"
                    class="w-full object-cover
                           border border-yellow-200/30">

                <div class="preview-label">

                    <div class="w-10 h-10 rounded-xl
                                bg-yellow-400/15
                                border border-yellow-300/15
                                flex items-center justify-center">

                        <i class="fas fa-chart-line
                                  text-yellow-300"></i>

                    </div>

                    <div>

                        <p class="text-xs font-bold text-white">
                            Cashew Intelligence
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
    /* Loader */
    window.addEventListener('load', () => {
        setTimeout(() => {
            document
                .getElementById('page-loader')
                .classList.add('opacity-0');

            setTimeout(() => {
                document
                    .getElementById('page-loader')
                    .classList.add('hidden');
            }, 700);

        }, 350);
    });

    /* Password */
    function togglePassword() {

        const passwordInput =
            document.getElementById("password");

        const toggleBtn =
            event.currentTarget;

        const isHidden =
            passwordInput.type === "password";

        passwordInput.type =
            isHidden ? "text" : "password";

        toggleBtn.textContent =
            isHidden ? "Hide" : "Show";
    }

    /* Login loading */
    document
        .getElementById('loginBtn')
        .addEventListener('click', function () {

            const form = this.closest('form');

            if (!form.checkValidity()) {
                return;
            }

            document
                .getElementById('spinner')
                .classList.remove('hidden');

            document
                .getElementById('btnText')
                .textContent = 'Signing In...';

            this.classList.add('opacity-90');
        });

    /* Subtle dashboard 3D interaction */
    const preview =
        document.querySelector('.preview-card');

    if (preview && window.innerWidth > 768) {

        document.addEventListener('mousemove', (event) => {

            const x =
                event.clientX / window.innerWidth - 0.5;

            const y =
                event.clientY / window.innerHeight - 0.5;

            preview.style.transform = `
                perspective(1400px)
                rotateY(${x * -8}deg)
                rotateX(${y * 5}deg)
                rotateZ(1deg)
            `;
        });
    }
</script>

</body>
</html>

