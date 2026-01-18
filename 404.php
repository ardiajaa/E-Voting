<?php
require_once 'config/database.php';

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo !empty($settings['logo']) ? '/uploads/' . htmlspecialchars($settings['logo']) : '/assets/images/placeholder.png'; ?>" type="image/png">
    <title>404 - Halaman Tidak Ditemukan | <?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'Pemilihan Ketua OSIS'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                 url('<?php echo !empty($settings['background']) ? '/uploads/' . htmlspecialchars($settings['background']) : 'https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg'; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            backdrop-filter: blur(5px);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .btn-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-hover::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        .btn-hover:hover::before {
            width: 300px;
            height: 300px;
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        @media (max-width: 640px) {
            .error-number {
                font-size: 5rem;
            }
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4" data-aos="fade-right">
                    <?php if (!empty($settings['logo'])): ?>
                        <img src="/uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
                             alt="Logo OSIS" 
                             class="h-12 w-auto">
                    <?php endif; ?>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'SMA Negeri 1'); ?></h1>
                        <p class="text-gray-600">Pemilihan Ketua OSIS Periode <?php echo htmlspecialchars($settings['tahun_ajaran'] ?? '2023/2024'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section text-white py-20 min-h-screen flex items-center">
        <div class="container mx-auto px-4 text-center">
            <div class="hero-content max-w-4xl mx-auto" data-aos="fade-up">
                <!-- Animated Lottie Icon -->
                <div class="flex justify-center mb-8" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-52 sm:w-72 mx-auto">
                        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                        <lottie-player 
                            src="https://assets6.lottiefiles.com/packages/lf20_t24tpvcu.json" 
                            background="transparent" 
                            speed="1" 
                            style="width: 100%; height: auto;" 
                            loop 
                            autoplay>
                        </lottie-player>
                    </div>
                </div>

                <!-- Title & Description -->
                <h2 class="text-4xl sm:text-5xl font-bold mb-4" data-aos="fade-up" data-aos-delay="300">
                    Halaman Tidak Ditemukan
                </h2>
                <p class="text-xl sm:text-2xl mb-8 max-w-2xl mx-auto text-gray-200" data-aos="fade-up" data-aos-delay="400">
                    Oops! Sepertinya halaman yang Anda cari tidak dapat ditemukan.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-8" data-aos="fade-up" data-aos-delay="500">
                    <a href="/" 
                       class="btn-hover bg-white text-blue-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg inline-flex items-center">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                    <button onclick="history.back()" 
                            class="btn-hover bg-white/20 hover:bg-white/30 text-white font-bold py-3 px-8 rounded-lg inline-flex items-center border border-white/30">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali Sebelumnya
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <p class="text-center text-gray-600">&copy; <?php echo date('Y'); ?> OSIS <?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'PILKETOS'); ?>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>


