<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIMS') }} | Sign in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --ink:#14212d; --muted:#607080; --line:#dce3e8; --orange:#d75a13; --navy:#102638; }
        * { box-sizing:border-box; } body { margin:0; min-height:100vh; background:#f4f6f7; color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,sans-serif; }
        .photo { background:linear-gradient(90deg,rgba(12,27,38,.93) 0%,rgba(12,27,38,.77) 47%,rgba(12,27,38,.22)),url('{{ asset('images/spare.jpg') }}') center/cover; }
        input[type=checkbox] { accent-color:var(--orange); }
        .login-card { box-shadow: 0 24px 60px rgba(21, 36, 48, .10), 0 2px 5px rgba(21, 36, 48, .04); }
        #loader { transition:opacity .25s ease,visibility .25s ease; } #loader.is-hidden { opacity:0; visibility:hidden; }
    </style>
</head>
<body>
    <div id="loader" class="fixed inset-0 z-50 grid place-items-center bg-white"><div class="h-9 w-9 animate-spin rounded-full border-4 border-slate-200 border-t-orange-600"></div></div>
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1.45fr)_minmax(420px,.8fr)]">
        <section class="photo relative hidden min-h-screen p-10 text-white lg:flex lg:flex-col xl:p-14">
            <header>
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-white no-underline">
                    <span class="grid h-10 w-10 place-items-center rounded bg-orange-600"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0-1.4 0l-7 7a1 1 0 0 0 0 1.4l2 2a1 1 0 0 0 1.4 0l7-7a1 1 0 0 0 0-1.4l-2-2Z"/><path d="m5 19 2-2M15 5l2-2M12 8l4 4"/></svg></span>
                    <span><strong class="block text-lg tracking-wide">{{ config('app.name', 'SIMS') }}</strong><small class="text-xs uppercase tracking-[.18em] text-slate-300">Service operations</small></span>
                </a>
            </header>

            <div class="my-auto max-w-2xl py-12">
                <h1 class="max-w-xl text-5xl font-semibold leading-[1.1] tracking-tight xl:text-6xl">GARAGE PRO<br>made simple.</h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-slate-200">Secured access to your workspace.</p>
            </div>
        </section>

        <section class="flex min-h-screen items-center bg-[#f3f6f8] px-5 py-8 sm:px-10 lg:px-14">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ url('/') }}" class="mb-8 flex items-center gap-3 text-[var(--ink)] no-underline lg:hidden"><span class="grid h-10 w-10 place-items-center rounded-xl bg-orange-600 text-white"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0-1.4 0l-7 7a1 1 0 0 0 0 1.4l2 2a1 1 0 0 0 1.4 0l7-7a1 1 0 0 0 0-1.4l-2-2Z"/></svg></span><strong>{{ config('app.name', 'SIMS') }}</strong></a>
                <div class="login-card rounded-2xl border border-slate-200/80 bg-white p-7 sm:p-9">
                    <div class="mb-8">
                        <span class="mb-5 grid h-11 w-11 place-items-center rounded-xl bg-orange-50 text-orange-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
                        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Welcome back</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Sign in to access your workspace.</p>
                    </div>
                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div><label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="name@company.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100"></div>
                    <div><div class="mb-2 flex justify-between"><label for="password" class="text-sm font-medium text-slate-700">Password</label>@if(Route::has('password.request'))<a class="text-xs font-semibold text-orange-600 hover:text-orange-700 hover:underline" href="{{ route('password.request') }}">Forgot password?</a>@endif</div><div class="relative"><input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Enter your password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100"><button id="toggle-password" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-800">Show</button></div></div>
                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600"><input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300"> Remember this device</label>
                    <button id="login-button" type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#d75a13] px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:-translate-y-px hover:bg-[#bd4c0d] focus:outline-none focus:ring-4 focus:ring-orange-200 disabled:opacity-70"><span id="button-text">Sign in</span><svg id="spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M12 3a9 9 0 1 1-9 9"/></svg></button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script>
        window.addEventListener('load', () => document.getElementById('loader').classList.add('is-hidden'));
        document.getElementById('toggle-password').addEventListener('click', function () { const field = document.getElementById('password'); const showing = field.type === 'password'; field.type = showing ? 'text' : 'password'; this.textContent = showing ? 'Hide' : 'Show'; });
        document.querySelector('form').addEventListener('submit', () => { document.getElementById('login-button').disabled = true; document.getElementById('button-text').textContent = 'Signing in…'; document.getElementById('spinner').classList.remove('hidden'); });
    </script>
</body>
</html>
