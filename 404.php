<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        .animation-container {
            max-width: 100%;
            width: 300px;
            margin: 0 auto;
        }
        @media (min-width: 640px) {
            .animation-container {
                width: 500px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">
    <div class="text-center w-full">
        <div class="max-w-md mx-auto p-6 sm:p-8 bg-white rounded-2xl shadow-2xl transform transition-all hover:scale-105">
            <div class="animation-container mb-6 sm:mb-8">
                <lottie-player
                    src="https://assets10.lottiefiles.com/packages/lf20_kcsr6fcp.json"
                    background="transparent"
                    speed="1"
                    style="width: 100%; height: auto;"
                    loop
                    autoplay>
                </lottie-player>
            </div>
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-700 mb-4 sm:mb-6">Halaman Tidak Ditemukan</h2>
            <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">
                Oops! Sepertinya halaman yang Anda cari telah hilang di sedot oleh BlackHole.
            </p>
            <a href="/" 
               class="inline-block px-6 py-2 sm:px-8 sm:py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:-translate-y-1 text-sm sm:text-base">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>


