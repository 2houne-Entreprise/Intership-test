<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TaskFlow') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        TaskFlow
                    </h1>
                </div>

                <!-- Links -->
                <div class="flex space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">Register</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="flex flex-col justify-center items-center h-[calc(100vh-4rem)] text-center px-4">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4">
            Welcome to <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">TaskFlow</span>
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mb-8">
            Streamline your workflow and enhance productivity with TaskFlow. 
            Our cutting-edge platform empowers teams and individuals to manage tasks effortlessly, 
            collaborate effectively, and achieve their goals with precision.
        </p>
        <a href="{{ route('register') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-200 shadow-lg hover:shadow-xl">
            Get Started 
        </a>
    </div>

</body>
</html>