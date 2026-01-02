<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <!-- Sidebar Toggle Button (Mobile Only) -->
                <button class="btn btn-outline-dark d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-sidebar" aria-controls="mobile-sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Desktop Sidebar (>= 768px) -->
        <div class="d-none d-md-flex" id="wrapper">
            <div class="bg-dark border-end" id="sidebar-wrapper" style="min-width: 250px; min-height: calc(100vh - 56px);">
                <div class="sidebar-heading text-white p-3">PropertyUp</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="{{ route('customers.index') }}" class="list-group-item list-group-item-action bg-dark text-white">1. ข้อมูลลูกค้า</a>
                    <a href="{{ route('properties.index') }}" class="list-group-item list-group-item-action bg-dark text-white">2. ข้อมูลบ้านเช่า</a>
                    <a href="{{ route('rentals.index') }}" class="list-group-item list-group-item-action bg-dark text-white">3. สัญญาเช่า</a>
                    <a href="{{ route('loans.index') }}" class="list-group-item list-group-item-action bg-dark text-white">4. ขายฝาก/จำนอง</a>
                    <a href="{{ route('finance.index') }}" class="list-group-item list-group-item-action bg-dark text-white">5. การเงิน</a>
                    <a href="{{ route('invoice.index') }}" class="list-group-item list-group-item-action bg-dark text-white">6. ใบแจ้งหนี้</a>
                </div>
            </div>
            <div id="page-content-wrapper" class="w-100 p-3 p-md-4">
                <!-- Success Alert -->
                @if($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Mobile Offcanvas Sidebar (< 768px) -->
        <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="mobile-sidebar" aria-labelledby="sidebar-label">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title text-white" id="sidebar-label">PropertyUp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">📊 Dashboard</a>
                    <a href="{{ route('customers.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">1. ข้อมูลลูกค้า</a>
                    <a href="{{ route('properties.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">2. ข้อมูลบ้านเช่า</a>
                    <a href="{{ route('rentals.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">3. สัญญาเช่า</a>
                    <a href="{{ route('loans.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">4. ขายฝาก/จำนอง</a>
                    <a href="{{ route('finance.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">5. การเงิน</a>
                    <a href="{{ route('invoice.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 sidebar-link">6. ใบแจ้งหนี้</a>
                </div>
            </div>
        </div>

        <!-- Mobile Page Content (< 768px) -->
        <div class="d-md-none p-3 p-md-4">
            <!-- Success Alert -->
            @if($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // Handle sidebar link clicks
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Get the offcanvas element
                const offcanvasElement = document.getElementById('mobile-sidebar');
                if (offcanvasElement) {
                    // Get the Bootstrap offcanvas instance
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                }
                // Let the link navigate normally
            });
        });
    </script>
</body>
</html>
