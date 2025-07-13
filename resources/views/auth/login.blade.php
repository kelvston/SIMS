<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - PhoneStore Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #a2ad29 0%, #f5e6da 40%, #AD5D29 75%, #6e3618 100%);
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
<div id="blob-container" class="absolute inset-0 z-10 pointer-events-none">
    <div class="blob bg-yellow-400 opacity-30 blur-xl rounded-full w-60 h-60 absolute" id="blob1"></div>
    <div class="blob bg-white opacity-20 blur-xl rounded-full w-72 h-52 absolute" id="blob2"></div>
    <div class="blob bg-purple-500 opacity-30 blur-xl rounded-full w-52 h-52 absolute" id="blob3"></div>
</div>
<div class="flex flex-col items-center justify-center w-full max-w-6xl mx-auto p-4 gap-8 z-20">
    <div class="text-center">
        <svg width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="#facc15" stroke-width="1.5" class="mx-auto mb-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 3C6.477 3 2 7.477 2 13s4.477 10 10 10 10-4.477 10-10S17.523 3 12 3z"/>
        </svg>
        <h1 class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-br from-yellow-200 via-orange-500 to-yellow-800">
            Enrich Your Digital Transformation
        </h1>
        <p class="text-white/80 mt-1 text-sm">Powered by Cosmetics Pro</p>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-center gap-6 w-full">
        <div class="glass p-6 rounded-xl shadow-md w-full max-w-sm">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-white/80 text-sm">Email</label>
                    <input type="email" name="email" id="email"
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label for="password" class="block text-white/80 text-sm">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md focus:ring-2 focus:ring-yellow-900 text-white placeholder-white/60 text-sm pr-10"
                               placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3 text-xs text-white/60 hover:text-white">
                            Show
                        </button>
                    </div>
                </div>

                <button id="loginBtn" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md text-base font-medium transition duration-300 bg-yellow-900 hover:bg-yellow-800 text-white">
                    <span id="btnText">Sign In</span>
                    <svg id="spinner" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4" fill="none" />
                        <path d="M12 2a10 10 0 0110 10" stroke="#facc15" stroke-width="4" fill="none"/>
                    </svg>
                </button>
            </form>
        </div>
        <div class="w-full max-w-md">
            <img src="images/phonepro1.png" alt="PhoneStore Dashboard Preview"
                 class="rounded-2xl shadow-lg w-full object-cover border-2 border-white/30">
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

    const blobs = [
        { el: document.getElementById("blob1"), x: 50, y: 100, dx: 1.2, dy: 0.9 },
        { el: document.getElementById("blob2"), x: 300, y: 200, dx: -1.1, dy: 1 },
        { el: document.getElementById("blob3"), x: 600, y: 150, dx: 1, dy: -1.3 }
    ];

    function animateBlobs() {
        const winW = window.innerWidth;
        const winH = window.innerHeight;

        blobs.forEach(b => {
            const w = b.el.offsetWidth;
            const h = b.el.offsetHeight;

            b.x += b.dx;
            b.y += b.dy;

            if (b.x <= 0 || b.x + w >= winW) b.dx *= -1;
            if (b.y <= 0 || b.y + h >= winH) b.dy *= -1;

            b.el.style.transform = `translate3d(${b.x}px, ${b.y}px, 0)`;
        });

        requestAnimationFrame(animateBlobs);
    }

    animateBlobs();
</script>

</body>
</html>
