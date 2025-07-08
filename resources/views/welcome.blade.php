<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-white px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
            <img src="https://img.freepik.com/free-vector/hand-drawn-sales-representative-illustration_23-2149571276.jpg"
                alt="Welcome Image" class="w-40 mx-auto mb-6 rounded-xl shadow-sm" />

            <h1 class="text-3xl font-bold text-blue-700 mb-2">Selamat Datang!</h1>
            <p class="text-gray-600 mb-6">
                Aplikasi <span class="font-semibold text-blue-600">Sales Canvaser</span> siap membantumu mencatat,
                melacak, dan mengelola kunjungan penjualan harian.
            </p>

            <div class="flex flex-col gap-3">
                <!-- <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Mulai Sekarang
                </button> -->
                <a href="/admin/"
                    class="border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-2 rounded-lg transition">
                    Masuk
                </a>
            </div>

            <div class="mt-6 text-xs text-gray-400">
                © 2025 Sales Canvaser App Provided by haifahrul@gmail.com
            </div>
        </div>
    </div>
</body>

</html>