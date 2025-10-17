<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Espay VA Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Espay VA Portal</h1>
            <p class="text-gray-500 text-sm mt-1">Masuk ke akun Anda</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-400">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-400">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2">
                    Ingat saya
                </label>
                <a href="#" class="text-sm text-blue-500 hover:underline">Lupa password?</a>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun? <a href="#" class="text-blue-500 hover:underline">Hubungi admin</a>
        </p>
    </div>
</body>

</html>
