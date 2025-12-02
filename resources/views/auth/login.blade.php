<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-orange-400 to-orange-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-8 rounded-t-lg text-white">
            <div class="flex items-center justify-center mb-2">
                <i class="fas fa-cash-register text-3xl mr-3"></i>
            </div>
            <h1 class="text-2xl font-bold text-center">POS Kasir</h1>
            <p class="text-center text-orange-100 text-sm mt-1">Point of Sale System</p>
        </div>

        <!-- Form -->
        <div class="px-6 py-8">
            <form action="{{ route('authenticate') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition @error('email') border-red-500 @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition @error('password') border-red-500 @enderror" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition mb-4">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">Don't have an account?
                    <a href="{{ route('register') }}" class="text-orange-600 font-semibold hover:text-orange-700">Register here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
