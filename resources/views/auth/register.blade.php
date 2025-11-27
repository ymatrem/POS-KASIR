<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - POS Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-orange-400 to-orange-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-8 rounded-t-lg text-white">
            <div class="flex items-center justify-center mb-2">
                <i class="fas fa-user-plus text-3xl mr-3"></i>
            </div>
            <h1 class="text-2xl font-bold text-center">Create Account</h1>
            <p class="text-center text-orange-100 text-sm mt-1">Join POS Kasir System</p>
        </div>

        <!-- Form -->
        <div class="px-6 py-8">
            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition @error('name') border-red-500 @enderror" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
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

                <!-- Password Confirmation -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition @error('password_confirmation') border-red-500 @enderror" required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Register Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition mb-4">
                    <i class="fas fa-user-check mr-2"></i> Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">Already have an account? 
                    <a href="{{ route('login') }}" class="text-orange-600 font-semibold hover:text-orange-700">Login here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
