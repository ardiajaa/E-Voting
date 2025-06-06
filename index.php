<?php
require_once 'config/database.php';

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();

// Ambil data kandidat
$stmt = $pdo->query("SELECT * FROM candidates ORDER BY id DESC");
$candidates = $stmt->fetchAll();

// Ambil pengaturan waktu voting
$stmt = $pdo->query("SELECT * FROM voting_time ORDER BY id DESC LIMIT 1");
$voting_time = $stmt->fetch();

// Cek status voting
$now = new DateTime();
$voting_status = 'belum_mulai';
$time_left = '';
$can_vote = false;

if ($voting_time) {
    $start_time = new DateTime($voting_time['start_time']);
    $end_time = new DateTime($voting_time['end_time']);
    
    if ($now < $start_time) {
        $voting_status = 'belum_mulai';
        $time_left = $now->diff($start_time);
    } elseif ($now >= $start_time && $now <= $end_time) {
        $voting_status = 'sedang_berlangsung';
        $time_left = $now->diff($end_time);
        $can_vote = true;
    } else {
        $voting_status = 'selesai';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan OSIS <?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'SMA Negeri 1'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg');
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
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .candidate-image {
            aspect-ratio: 1/1;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        .candidate-card:hover .candidate-image {
            transform: scale(1.05);
        }
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.875rem;
            backdrop-filter: blur(4px);
        }
        .status-badge.belum-mulai {
            background: rgba(234, 179, 8, 0.9);
            color: #fff;
        }
        .status-badge.berlangsung {
            background: rgba(34, 197, 94, 0.9);
            color: #fff;
        }
        .status-badge.selesai {
            background: rgba(239, 68, 68, 0.9);
            color: #fff;
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
                        <img src="uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
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

    <!-- Status Voting -->
    <div class="bg-white shadow-md border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4" data-aos="fade-down">
                <div class="flex items-center gap-4">
                    <?php if ($voting_status == 'belum_mulai'): ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-yellow-500 text-xl"></i>
                            <span class="text-yellow-600 font-medium">Pemilihan Belum Dimulai</span>
                        </div>
                        <?php if ($time_left): ?>
                            <span class="text-gray-600">
                                Dimulai dalam: <?php echo $time_left->format('%d hari %h jam %i menit'); ?>
                            </span>
                        <?php endif; ?>
                    <?php elseif ($voting_status == 'sedang_berlangsung'): ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-green-600 font-medium">Pemilihan Sedang Berlangsung</span>
                        </div>
                        <?php if ($time_left): ?>
                            <span class="text-gray-600">
                                Sisa waktu: <?php echo $time_left->format('%d hari %h jam %i menit'); ?>
                            </span>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                            <span class="text-red-600 font-medium">Pemilihan Telah Selesai</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($can_vote): ?>
                    <a href="auth/login.php" 
                       class="btn-hover bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">
                        <i class="fas fa-vote-yea mr-2"></i>Mulai Memilih
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <div class="hero-content max-w-4xl mx-auto" data-aos="fade-up">
                <?php if (!empty($settings['logo'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
                         alt="Logo OSIS" 
                         class="h-32 w-auto mx-auto mb-8">
                <?php endif; ?>
                <h2 class="text-4xl font-bold mb-4">Pemilihan Ketua OSIS</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Pilih pemimpin OSIS yang akan membawa perubahan positif untuk sekolah kita
                </p>
                <?php if ($can_vote): ?>
                    <a href="auth/login.php" 
                       class="btn-hover bg-white text-blue-500 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg inline-block">
                        <i class="fas fa-vote-yea mr-2"></i>Mulai Memilih
                    </a>
                <?php else: ?>
                    <div class="inline-block bg-gray-100 text-gray-500 font-bold py-3 px-8 rounded-lg">
                        <?php if ($voting_status == 'belum_mulai'): ?>
                            <i class="fas fa-clock mr-2"></i>Pemilihan Belum Dimulai
                        <?php else: ?>
                            <i class="fas fa-times-circle mr-2"></i>Pemilihan Telah Selesai
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Visi Misi OSIS -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12" data-aos="fade-up">Visi & Misi OSIS</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Visi -->
                <div class="glass-card rounded-2xl shadow-xl p-8 card-hover" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-2xl font-bold text-blue-600 mb-4 flex items-center">
                        <i class="fas fa-bullseye mr-3"></i>Visi
                    </h3>
                    <div class="prose prose-lg">
                        <?php echo nl2br(htmlspecialchars($settings['visi'] ?? 'Visi OSIS akan ditampilkan di sini')); ?>
                    </div>
                </div>
                
                <!-- Misi -->
                <div class="glass-card rounded-2xl shadow-xl p-8 card-hover" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-2xl font-bold text-blue-600 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-3"></i>Misi
                    </h3>
                    <div class="prose prose-lg">
                        <?php echo nl2br(htmlspecialchars($settings['misi'] ?? 'Misi OSIS akan ditampilkan di sini')); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kandidat Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12" data-aos="fade-up">Kandidat Ketua OSIS</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($candidates as $index => $candidate): ?>
                <div class="card-hover glass-card rounded-2xl shadow-xl overflow-hidden" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="relative">
                        <img src="assets/images/candidates/<?php echo htmlspecialchars($candidate['foto']); ?>" 
                             alt="<?php echo htmlspecialchars($candidate['nama']); ?>"
                             class="w-full candidate-image">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($candidate['nama']); ?></h3>
                            <p class="text-gray-200"><?php echo htmlspecialchars($candidate['kelas']); ?> - Absen <?php echo htmlspecialchars($candidate['absen']); ?></p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-bullseye text-blue-500 mr-2"></i>Visi:
                            </h4>
                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($candidate['visi'])); ?></p>
                        </div>
                        
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-tasks text-blue-500 mr-2"></i>Misi:
                            </h4>
                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($candidate['misi'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>
