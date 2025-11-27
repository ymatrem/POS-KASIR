<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-6">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">POS Kasir</h1>
                        <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>
            </div>

            <nav class="mt-8">
                @if(Auth::user()->role === 'admin')
                    <!-- Admin Navigation -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dashboard*') ? 'bg-orange-100 border-l-4 border-orange-500' : '' }} text-gray-700 hover:bg-orange-100">
                        <i class="fas fa-chart-line mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('orders*') ? 'bg-orange-100 border-l-4 border-orange-500' : '' }} text-gray-700 hover:bg-orange-100">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        <span>Pesanan</span>
                    </a>
                    <a href="{{ route('menus.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('menus*') ? 'bg-orange-100 border-l-4 border-orange-500' : '' }} text-gray-700 hover:bg-orange-100">
                        <i class="fas fa-list mr-3"></i>
                        <span>Item Menu</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('categories*') ? 'bg-orange-100 border-l-4 border-orange-500' : '' }} text-gray-700 hover:bg-orange-100">
                        <i class="fas fa-tag mr-3"></i>
                        <span>Kategori</span>
                    </a>
                @else
                    <!-- Cashier Navigation -->
                    <a href="{{ route('cashier.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('cashier*') ? 'bg-orange-100 border-l-4 border-orange-500' : '' }} text-gray-700 hover:bg-orange-100">
                        <i class="fas fa-cash-register mr-3"></i>
                        <span>Kasir</span>
                    </a>
                @endif
            </nav>

            <div class="absolute bottom-0 left-0 w-64 p-6 border-t">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center px-6 py-3 text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Header -->
            <div class="bg-white shadow-sm border-b">
                <div class="px-8 py-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title')</h2>
                        @yield('page-subtitle')
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell text-gray-600"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="px-8 py-4">
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <div class="px-8 pb-8">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
