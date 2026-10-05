<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->user()->hasAnyRole(['mechanic', 'mechanics'])) {
            return redirect()->route('motor-services.my-pending');
        }

        $user = $request->user();

        if ($user->can('view dashboard')) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        foreach ([
            'view motor services' => 'motor-services.index',
            'view sales' => 'sales.index',
            'view phones' => 'phones.index',
            'view installments' => 'installments.index',
            'view expenses' => 'expenses.index',
        ] as $permission => $route) {
            if ($user->can($permission)) {
                return redirect()->route($route);
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Your account has no assigned access role. Please contact an administrator.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
