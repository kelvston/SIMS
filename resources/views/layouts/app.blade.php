<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'POS')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* =========================================================
           DASHBOARD WATERMARK
        ========================================================= */

        /* =========================================================
   PREMIUM CENTERED DASHBOARD WATERMARK
========================================================= */

        .dashboard-watermark {
            position: fixed;

            top: 50%;
            left: calc(240px + (100vw - 240px) / 2);

            width: min(520px, 48vw);
            height: min(520px, 48vw);

            transform: translate(-50%, -50%);

            display: flex;
            align-items: center;
            justify-content: center;

            pointer-events: none;
            user-select: none;

            z-index: 1;

            opacity: .065;

            filter:
                saturate(.75)
                contrast(.95)
                drop-shadow(
                    0 18px 35px rgba(122,63,29,.10)
                );
        }


        /* Logo */

        .dashboard-watermark img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;

            transform: scale(.88);

            opacity: .9;

            mix-blend-mode: multiply;
        }


        /* Premium circular frame */

        .dashboard-watermark::before {
            content: "";

            position: absolute;

            width: 82%;
            height: 82%;

            border-radius: 50%;

            border:
                1px solid rgba(173,93,41,.13);

            box-shadow:
                0 0 0 18px rgba(173,93,41,.018),
                0 0 0 1px rgba(255,255,255,.35) inset,
                0 0 70px rgba(173,93,41,.07);

            z-index: -1;
        }


        /* Soft center glow */

        .dashboard-watermark::after {
            content: "";

            position: absolute;

            width: 72%;
            height: 72%;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(173,93,41,.055) 0%,
                    rgba(244,194,122,.025) 42%,
                    transparent 72%
                );

            filter: blur(18px);

            z-index: -2;
        }


        /* Tablet */

        @media (max-width: 1023px) {

            .dashboard-watermark {

                left: 50%;

                width: min(420px, 70vw);
                height: min(420px, 70vw);

                opacity: .055;
            }
        }


        /* Mobile */

        @media (max-width: 640px) {

            .dashboard-watermark {

                width: 290px;
                height: 290px;

                opacity: .045;
            }
        }
        /* =========================================================
           GLOBAL
        ========================================================= */

        body {
            font-family: 'Inter', sans-serif;
            background: #f7f3ef;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(173, 93, 41, .35) transparent;
        }

        *::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        *::-webkit-scrollbar-track {
            background: transparent;
        }

        *::-webkit-scrollbar-thumb {
            background: rgba(173, 93, 41, .35);
            border-radius: 20px;
        }


        /* =========================================================
           PREMIUM DESKTOP SIDEBAR
           TARI / CASHEW BRAND COLORS
        ========================================================= */

        .premium-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 240px;

            display: flex;
            flex-direction: column;

            color: #fff;

            background:
                radial-gradient(
                    circle at 20% 0%,
                    rgba(255,255,255,.12),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 100% 65%,
                    rgba(244,194,122,.10),
                    transparent 35%
                ),
                linear-gradient(
                    160deg,
                    #7A3F1D 0%,
                    #AD5D29 32%,
                    #91491F 68%,
                    #6B3518 100%
                );

            border-right: 1px solid rgba(255,255,255,.12);

            box-shadow:
                12px 0 45px rgba(91, 45, 20, .20),
                inset -1px 0 rgba(255,255,255,.05);

            overflow: hidden;

            z-index: 40;
        }


        /* Ambient sidebar lighting */

        .premium-sidebar::before {
            content: "";
            position: absolute;

            width: 240px;
            height: 240px;

            top: -100px;
            right: -100px;

            border-radius: 50%;

            background: rgba(244,194,122,.18);

            filter: blur(55px);

            pointer-events: none;
        }

        .premium-sidebar::after {
            content: "";
            position: absolute;

            width: 200px;
            height: 200px;

            bottom: -100px;
            left: -100px;

            border-radius: 50%;

            background: rgba(255,255,255,.045);

            filter: blur(50px);

            pointer-events: none;
        }


        /* =========================================================
           BRAND AREA
        ========================================================= */

        .sidebar-brand {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;
            gap: 11px;

            padding: 18px 15px 17px;

            border-bottom: 1px solid rgba(255,255,255,.12);

            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.08),
                    rgba(255,255,255,.02)
                );
        }

        .sidebar-logo {
            width: 43px;
            height: 43px;

            flex: 0 0 43px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 5px;

            border-radius: 14px;

            background:
                linear-gradient(
                    145deg,
                    #ffe3ad,
                    #d99a45 45%,
                    #9b5a20
                );

            border: 1px solid rgba(255,255,255,.30);

            box-shadow:
                inset 2px 2px 4px rgba(255,255,255,.40),
                inset -3px -3px 5px rgba(83,38,13,.30),
                0 8px 22px rgba(67,31,12,.30);
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;

            filter:
                drop-shadow(0 2px 3px rgba(0,0,0,.25));
        }

        .sidebar-brand-text {
            min-width: 0;
        }

        .sidebar-brand-name {
            margin: 0;

            color: #fff;

            font-size: .82rem;
            line-height: 1.15;

            font-weight: 900;

            letter-spacing: .015em;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-brand-subtitle {
            margin-top: 4px;

            color: rgba(255,235,210,.65);

            font-size: .55rem;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .12em;
        }


        /* =========================================================
           NAVIGATION
        ========================================================= */

        .premium-nav {
            position: relative;
            z-index: 2;

            flex: 1;

            padding: 14px 10px;

            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-section-label {
            padding: 7px 10px 6px;

            color: rgba(255,235,210,.50);

            font-size: .54rem;

            font-weight: 900;

            letter-spacing: .13em;

            text-transform: uppercase;
        }


        /* Main menu item */

        .premium-nav-item {
            position: relative;

            display: flex;
            align-items: center;

            min-height: 42px;

            margin: 3px 0;

            padding: 8px 10px;

            border-radius: 12px;

            color: rgba(255,255,255,.78);

            text-decoration: none;

            font-size: .73rem;

            font-weight: 650;

            transition:
                color .25s ease,
                background .25s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        .premium-nav-item::before {
            content: "";

            position: absolute;

            left: 0;
            top: 9px;
            bottom: 9px;

            width: 3px;

            border-radius: 0 5px 5px 0;

            background: #F4C27A;

            opacity: 0;

            transform: scaleY(.4);

            transition:
                opacity .25s ease,
                transform .25s ease;
        }

        .premium-nav-item:hover {
            color: #fff;

            background:
                linear-gradient(
                    90deg,
                    rgba(255,255,255,.13),
                    rgba(255,255,255,.045)
                );

            transform: translateX(3px);

            box-shadow:
                inset 0 1px rgba(255,255,255,.07),
                0 5px 14px rgba(76,34,13,.15);
        }

        .premium-nav-item:hover::before {
            opacity: 1;
            transform: scaleY(1);
        }


        /* Icons */

        .nav-icon {
            width: 30px;
            height: 30px;

            flex: 0 0 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 8px;

            border-radius: 9px;

            color: rgba(255,245,230,.78);

            background: rgba(255,255,255,.065);

            border: 1px solid rgba(255,255,255,.06);

            font-size: 12px;

            transition:
                color .25s ease,
                background .25s ease,
                transform .25s ease;
        }

        .premium-nav-item:hover .nav-icon {
            color: #FFE0A8;

            background: rgba(244,194,122,.15);

            border-color: rgba(244,194,122,.20);

            transform: scale(1.06);
        }

        .nav-label {
            flex: 1;
        }

        .nav-chevron {
            color: rgba(255,235,210,.42);

            font-size: 9px;

            transition:
                transform .3s ease,
                color .3s ease;
        }

        .premium-nav-item:hover .nav-chevron {
            color: rgba(255,255,255,.78);
        }


        /* =========================================================
           DROPDOWN
        ========================================================= */

        .premium-dropdown {
            position: relative;

            margin: 2px 0 5px 15px;

            padding: 4px 0 4px 9px;

            border-left: 1px solid rgba(244,194,122,.25);
        }

        .premium-sub-item {
            display: flex;
            align-items: center;

            min-height: 35px;

            padding: 6px 9px;

            border-radius: 9px;

            color: rgba(255,255,255,.62);

            text-decoration: none;

            font-size: .65rem;

            font-weight: 600;

            transition:
                color .2s ease,
                background .2s ease,
                transform .2s ease;
        }

        .premium-sub-item:hover {
            color: #FFE0A8;

            background: rgba(255,255,255,.06);

            transform: translateX(3px);
        }

        .sub-icon {
            width: 24px;

            color: rgba(255,235,210,.40);

            font-size: 9px;
        }

        .premium-sub-item:hover .sub-icon {
            color: #F4C27A;
        }


        /* =========================================================
           LOGOUT
        ========================================================= */

        .sidebar-logout {
            position: relative;
            z-index: 2;

            margin: 5px 10px 10px;
        }

        .sidebar-logout button {
            position: relative;

            display: flex;
            align-items: center;

            width: 100%;

            min-height: 42px;

            padding: 8px 10px;

            border: 1px solid rgba(239,68,68,.14);

            border-radius: 12px;

            color: rgba(255,255,255,.70);

            background:
                linear-gradient(
                    90deg,
                    rgba(239,68,68,.08),
                    rgba(239,68,68,.025)
                );

            font-size: .7rem;

            font-weight: 700;

            cursor: pointer;

            transition:
                all .25s ease;
        }

        .sidebar-logout button:hover {
            color: #fff;

            background:
                linear-gradient(
                    90deg,
                    rgba(239,68,68,.20),
                    rgba(239,68,68,.07)
                );

            border-color: rgba(239,68,68,.28);

            transform: translateY(-1px);
        }


        /* =========================================================
           USER PROFILE
        ========================================================= */

        .sidebar-user {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;

            gap: 9px;

            margin: 0 10px 12px;

            padding: 10px;

            border-radius: 14px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.10),
                    rgba(255,255,255,.035)
                );

            border: 1px solid rgba(255,255,255,.09);

            box-shadow:
                inset 0 1px rgba(255,255,255,.055),
                0 8px 20px rgba(76,34,13,.16);
        }

        .sidebar-avatar {
            position: relative;

            width: 36px;
            height: 36px;

            flex: 0 0 36px;

            border-radius: 11px;

            border: 1px solid rgba(255,255,255,.20);

            box-shadow:
                0 4px 10px rgba(67,31,12,.25);
        }

        .sidebar-user-info {
            min-width: 0;
        }

        .sidebar-user-name {
            color: #fff;

            font-size: .66rem;

            font-weight: 800;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-email {
            margin-top: 2px;

            color: rgba(255,235,210,.50);

            font-size: .53rem;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .online-indicator {
            position: absolute;

            width: 8px;
            height: 8px;

            bottom: -1px;
            right: -1px;

            border-radius: 50%;

            background: #34d399;

            border: 2px solid #8B471F;

            box-shadow:
                0 0 0 3px rgba(52,211,153,.08);
        }


        /* =========================================================
           NOTIFICATION
        ========================================================= */

        .notification-dot {
            position: absolute;

            top: -5px;
            right: -5px;

            width: 20px;
            height: 20px;

            background:
                linear-gradient(
                    145deg,
                    #fb7185,
                    #dc2626
                );

            border: 2px solid #fff;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;

            font-size: .65rem;

            font-weight: bold;

            animation: pulse 1.5s infinite;

            box-shadow:
                0 4px 10px rgba(220,38,38,.3);
        }

        @keyframes pulse {

            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: .7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }


        /* =========================================================
           MOBILE SIDEBAR
        ========================================================= */

        .sidebar-mobile {
            position: fixed;

            top: 0;
            left: -100%;

            height: 100%;
            width: 270px;

            color: white;

            background:
                radial-gradient(
                    circle at 20% 0%,
                    rgba(255,255,255,.10),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 100% 70%,
                    rgba(244,194,122,.10),
                    transparent 35%
                ),
                linear-gradient(
                    160deg,
                    #7A3F1D,
                    #AD5D29 45%,
                    #6B3518
                );

            transition:
                left .35s cubic-bezier(.2,.8,.2,1);

            z-index: 50;

            overflow: hidden;

            box-shadow:
                15px 0 50px rgba(72,32,13,.30);
        }

        .sidebar-mobile.active {
            left: 0;
        }


        /* Mobile overlay */

        .sidebar-overlay {
            position: fixed;

            inset: 0;

            background: rgba(65,32,15,.48);

            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);

            z-index: 45;

            opacity: 0;
            pointer-events: none;

            transition: opacity .3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }


        /* =========================================================
           MOBILE TOP BAR
        ========================================================= */

        .premium-mobile-header {
            background:
                rgba(255,250,246,.92);

            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);

            border-bottom: 1px solid rgba(173,93,41,.10);

            box-shadow:
                0 8px 25px rgba(91,45,20,.07);
        }


        /* =========================================================
           PAGE LOADER
        ========================================================= */

        #page-loader {
            transition: opacity .35s ease;

            background:
                radial-gradient(
                    circle at center,
                    #fff,
                    #f8f2ec
                );
        }

        #page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-bars div {
            border-radius: 4px;

            box-shadow:
                0 5px 12px rgba(173,93,41,.22);

            background: #AD5D29 !important;
        }


        @keyframes loader-bar {

            0%, 100% {
                transform: scaleY(1);
            }

            50% {
                transform: scaleY(2);
            }
        }

        .animate-loader-bar {
            animation:
                loader-bar 1s infinite ease-in-out;
        }

        .delay-100 {
            animation-delay: .1s;
        }

        .delay-200 {
            animation-delay: .2s;
        }

        .delay-300 {
            animation-delay: .3s;
        }

        .delay-400 {
            animation-delay: .4s;
        }


        /* =========================================================
           CHART
        ========================================================= */

        .chart-wrapper {
            height: 320px;
        }


        /* =========================================================
           MAIN CONTENT
        ========================================================= */

        .premium-main {
            background:
                radial-gradient(
                    circle at 90% 0%,
                    rgba(173,93,41,.045),
                    transparent 25%
                ),
                #f7f3ef;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 1023px) {

            .premium-sidebar {
                display: none;
            }

            .premium-main {
                margin-left: 0 !important;
            }
        }

    </style>

    @stack('styles')
</head>


<body class="bg-gray-100 font-sans">

@php
    // dd($settings['organization_name']);
@endphp


    <!-- =========================================================
     PAGE LOADER
========================================================= -->

<div id="page-loader"
     class="fixed inset-0 z-50 bg-white flex items-center justify-center">

    <div class="loader-bars flex space-x-1">

        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-100"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-200"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-300"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-400"></div>

    </div>

</div>


<!-- =========================================================
     MOBILE SIDEBAR OVERLAY
========================================================= -->

<div id="sidebarOverlay"
     class="sidebar-overlay lg:hidden"></div>


<div class="flex min-h-screen overflow-hidden">


    <!-- =====================================================
         DESKTOP SIDEBAR
    ====================================================== -->

    <aside
        class="premium-sidebar hidden lg:flex"
        x-data="{ manageOpen: false, reportsOpen: false }">


        <!-- BRAND -->

        <div class="sidebar-brand">

            <div class="sidebar-logo">

                @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])

                    <img
                        src="{{ asset('storage/' . $settings['organization_logo_path']) }}"
                        alt="Logo">

                @else
a
                    <i class="fas fa-leaf text-[#3d220e]"></i>

                @endif

            </div>


            <div class="sidebar-brand-text">

                <h2 class="sidebar-brand-name">
                    {{ $settings['organization_name'] ?? 'TARI - CASHEW' }}
                </h2>

                <div class="sidebar-brand-subtitle">
                    Management System
                </div>

            </div>

        </div>


        <!-- NAVIGATION -->

        <nav class="premium-nav">

            <div class="sidebar-section-label">
                Main Menu
            </div>


            @can('view dashboard')

                <a href="{{ route('dashboard') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-chart-pie"></i>
                    </span>

                    <span class="nav-label">
                        Dashboard
                    </span>

                </a>

            @endcan


            @can('view cashews')

                <a href="{{ route('cashews.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-boxes-stacked"></i>
                    </span>

                    <span class="nav-label">
                        Inventory
                    </span>

                </a>

            @endcan


            @can('view sales')

                <a href="{{ route('sales.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-cash-register"></i>
                    </span>

                    <span class="nav-label">
                        Sales
                    </span>

                </a>

            @endcan


            @can('view installments')

                <a href="{{ route('installments.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-calendar-check"></i>
                    </span>

                    <span class="nav-label">
                        Installments
                    </span>

                </a>

            @endcan


            <!-- REPORTS -->

            @canany([
                'view sales reports',
                'view stock reports',
                'view profit loss reports'
            ])

                <div class="sidebar-section-label mt-3">
                    Analytics
                </div>

                <button
                    @click="reportsOpen = !reportsOpen"
                    class="premium-nav-item w-full text-left border-0 bg-transparent cursor-pointer"
                    :aria-expanded="reportsOpen.toString()">

                    <span class="nav-icon">
                        <i class="fas fa-chart-column"></i>
                    </span>

                    <span class="nav-label">
                        Reports
                    </span>

                    <i
                        class="fas fa-chevron-down nav-chevron"
                        :class="{'rotate-180': reportsOpen}">
                    </i>

                </button>


                <div
                    x-show="reportsOpen"
                    x-collapse
                    class="premium-dropdown">

                    @can('manage users')

                        <a href="{{ route('reports.sales') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-chart-line"></i>
                            </span>

                            Sales Report

                        </a>

                    @endcan


                    @can('manage roles')

                        <a href="{{ route('reports.stock') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-box-open"></i>
                            </span>

                            Stock Report

                        </a>


                        <a href="{{ route('reports.general') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-chart-simple"></i>
                            </span>

                            General Report

                        </a>

                    @endcan


                    @can('manage products')

                        <a href="{{ route('reports.profit_loss') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-arrow-trend-up"></i>
                            </span>

                            Profit / Loss

                        </a>

                    @endcan

                </div>

            @endcanany


            <!-- MANAGE -->

            @canany([
                'manage users',
                'manage roles',
                'manage products'
            ])

                <div class="sidebar-section-label mt-3">
                    Administration
                </div>


                <button
                    @click="manageOpen = !manageOpen"
                    class="premium-nav-item w-full text-left border-0 bg-transparent cursor-pointer"
                    :aria-expanded="manageOpen.toString()">

                    <span class="nav-icon">
                        <i class="fas fa-sliders"></i>
                    </span>

                    <span class="nav-label">
                        Manage
                    </span>

                    <i
                        class="fas fa-chevron-down nav-chevron"
                        :class="{'rotate-180': manageOpen}">
                    </i>

                </button>


                <div
                    x-show="manageOpen"
                    x-collapse
                    class="premium-dropdown">


                    @can('manage users')

                        <a href="{{ route('users.index') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-users"></i>
                            </span>

                            Manage Users

                        </a>


                        <a href="{{ route('settings.edit') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-gear"></i>
                            </span>

                            Manage Settings

                        </a>

                    @endcan


                    @can('manage roles')

                        <a href="{{ route('roles.index') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-shield-halved"></i>
                            </span>

                            Manage Roles

                        </a>

                    @endcan


                    @can('manage products')

                        <a href="{{ route('products.index') }}"
                           class="premium-sub-item">

                            <span class="sub-icon">
                                <i class="fas fa-tags"></i>
                            </span>

                            Manage Products

                        </a>

                    @endcan

                </div>

            @endcanany


            @can('view expenses')

                <div class="sidebar-section-label mt-3">
                    Finance
                </div>

                <a href="{{ route('expenses.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-money-bill-transfer"></i>
                    </span>

                    <span class="nav-label">
                        Expenses
                    </span>

                </a>

            @endcan


        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-logout">

            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit">

                    <span class="nav-icon">

                        <i class="fas fa-right-from-bracket"></i>

                    </span>

                    <span class="nav-label">
                        Log Out
                    </span>

                </button>

            </form>

        </div>


        <!-- USER PROFILE -->

        <div class="sidebar-user">

            <div class="relative">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=AD5D29&color=fff&size=40"
                    alt="Avatar"
                    class="sidebar-avatar">

                <span class="online-indicator"></span>

            </div>


            <div class="sidebar-user-info">

                <div class="sidebar-user-name">
                    {{ Auth::user()->name }}
                </div>

                <div class="sidebar-user-email">
                    {{ Auth::user()->email }}
                </div>

            </div>

        </div>

    </aside>


    <!-- =====================================================
         MOBILE SIDEBAR
    ====================================================== -->

    <aside
        id="mobileSidebar"
        class="sidebar-mobile p-4 lg:hidden">


        <!-- Mobile brand -->

        <div class="flex items-center gap-3 pb-5 mb-4 border-b border-white/10">

            <div class="sidebar-logo">

                @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])

                    <img
                        src="{{ asset('storage/' . $settings['organization_logo_path']) }}"
                        alt="Logo">

                @else

                    <i class="fas fa-leaf text-[#3d220e]"></i>

                @endif

            </div>


            <div>

                <div class="font-black text-sm">
                    {{ $settings['organization_name'] ?? 'TARI - CASHEW' }}
                </div>

                <div class="text-[9px] uppercase tracking-widest text-white/40 mt-1">
                    Management System
                </div>

            </div>


            <button
                id="mobileSidebarClose"
                class="ml-auto w-8 h-8 rounded-lg bg-white/5 text-white/60 hover:text-white">

                <i class="fas fa-xmark"></i>

            </button>

        </div>


        <nav class="premium-nav p-0">


            @can('view dashboard')

                <a href="{{ route('dashboard') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-chart-pie"></i>
                    </span>

                    <span class="nav-label">
                        Dashboard
                    </span>

                </a>

            @endcan


            @can('view cashews')

                <a href="{{ route('cashews.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-boxes-stacked"></i>
                    </span>

                    <span class="nav-label">
                        Inventory
                    </span>

                </a>

            @endcan


            @can('view sales')

                <a href="{{ route('sales.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-cash-register"></i>
                    </span>

                    <span class="nav-label">
                        Sales
                    </span>

                </a>

            @endcan


            @can('view installments')

                <a href="{{ route('installments.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-calendar-check"></i>
                    </span>

                    <span class="nav-label">
                        Installments
                    </span>

                </a>

            @endcan


            @canany([
                'view sales reports',
                'view stock reports',
                'view profit loss reports'
            ])

                <a href="{{ route('reports.sales') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-chart-column"></i>
                    </span>

                    <span class="nav-label">
                        Reports
                    </span>

                </a>

            @endcanany


            @can('manage users')

                <a href="{{ route('users.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-users"></i>
                    </span>

                    <span class="nav-label">
                        Manage Users
                    </span>

                </a>

            @endcan


            @can('manage roles')

                <a href="{{ route('roles.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-shield-halved"></i>
                    </span>

                    <span class="nav-label">
                        Manage Roles
                    </span>

                </a>

            @endcan


            @can('manage products')

                <a href="{{ route('products.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-tags"></i>
                    </span>

                    <span class="nav-label">
                        Manage Products
                    </span>

                </a>

            @endcan


            @can('view expenses')

                <a href="{{ route('expenses.index') }}"
                   class="premium-nav-item">

                    <span class="nav-icon">
                        <i class="fas fa-money-bill-transfer"></i>
                    </span>

                    <span class="nav-label">
                        Expenses
                    </span>

                </a>

            @endcan


            <form method="POST"
                  action="{{ route('logout') }}"
                  class="mt-3">

                @csrf

                <button
                    type="submit"
                    class="premium-nav-item w-full text-left border-0 bg-transparent">

                    <span class="nav-icon">
                        <i class="fas fa-right-from-bracket"></i>
                    </span>

                    <span class="nav-label">
                        Log Out
                    </span>

                </button>

            </form>


        </nav>


        <!-- Mobile user -->

        <div class="sidebar-user mt-auto">

            <div class="relative">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=AD5D29&color=fff&size=40"
                    alt="Avatar"
                    class="sidebar-avatar">

                <span class="online-indicator"></span>

            </div>


            <div class="sidebar-user-info">

                <div class="sidebar-user-name">
                    {{ Auth::user()->name }}
                </div>

                <div class="sidebar-user-email">
                    {{ Auth::user()->email }}
                </div>

            </div>

        </div>

    </aside>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <div class="flex-1 flex flex-col lg:ml-60 premium-main">


        <!-- TOP BAR -->

        <header
            class="premium-mobile-header flex justify-between items-center px-5 py-4 shadow lg:hidden">

            <button
                id="menuToggle"
                class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-600 bg-gray-50 hover:bg-gray-100">

                <i class="fas fa-bars text-lg"></i>

            </button>


            <h1 class="text-lg font-black text-gray-800">
                @yield('title', 'Dashboard')
            </h1>


            <div class="relative">

                <button
                    class="w-10 h-10 flex items-center justify-center p-2 text-gray-600 hover:bg-gray-100 rounded-xl">

                    <i class="fas fa-bell"></i>

                </button>

            </div>

        </header>


        <!-- PAGE CONTENT -->

        <main class="flex-1 p-6 space-y-8 overflow-y-auto relative">

            <!-- =====================================================
                 CENTERED DASHBOARD WATERMARK
            ====================================================== -->

            @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])

                <div class="dashboard-watermark" aria-hidden="true">

                    <img
                        src="{{ asset('storage/' . $settings['organization_logo_path']) }}"
                        alt="">

                </div>

            @endif

            <div class="relative z-10 space-y-8">

                @yield('content')

            </div>

        </main>

    </div>

</div>


<script src="//unpkg.com/alpinejs" defer></script>


<script>

    /* =========================================================
       MOBILE SIDEBAR
    ========================================================= */

    const menuToggle =
        document.getElementById('menuToggle');

    const mobileSidebar =
        document.getElementById('mobileSidebar');

    const sidebarOverlay =
        document.getElementById('sidebarOverlay');

    const mobileSidebarClose =
        document.getElementById('mobileSidebarClose');


    function openMobileSidebar() {

        if (!mobileSidebar) return;

        mobileSidebar.classList.add('active');

        if (sidebarOverlay) {
            sidebarOverlay.classList.add('active');
        }

        document.body.style.overflow = 'hidden';
    }


    function closeMobileSidebar() {

        if (!mobileSidebar) return;

        mobileSidebar.classList.remove('active');

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }

        document.body.style.overflow = '';
    }


    if (menuToggle) {

        menuToggle.addEventListener(
            'click',
            openMobileSidebar
        );

    }


    if (mobileSidebarClose) {

        mobileSidebarClose.addEventListener(
            'click',
            closeMobileSidebar
        );

    }


    if (sidebarOverlay) {

        sidebarOverlay.addEventListener(
            'click',
            closeMobileSidebar
        );

    }


    /* Close mobile sidebar after navigation */

    if (mobileSidebar) {

        mobileSidebar
            .querySelectorAll('a')
            .forEach(link => {

                link.addEventListener(
                    'click',
                    closeMobileSidebar
                );

            });

    }


    /* =========================================================
       PAGE LOADER
    ========================================================= */

    window.addEventListener('load', () => {

        const loader =
            document.getElementById('page-loader');

        if (loader) {

            setTimeout(() => {

                loader.classList.add('hidden');

            }, 150);

        }

    });

</script>


<!-- Vite JS -->

@vite(['resources/js/app.js'])

@stack('scripts')

</body>
</html>
