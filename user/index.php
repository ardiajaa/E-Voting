<?php
require_once '../includes/header.php';
require_once '../config/database.php';

// Ambil data settings dengan pengecekan error
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch() ?? [
    'nama_sekolah' => 'SMA Negeri 1',
    'tahun_ajaran' => '2023/2024',
    'visi_misi' => 'Visi dan Misi OSIS akan ditampilkan di sini'
];
?>

<!-- Tambahkan AOS CSS dan JS -->
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
</style>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="hero-section text-white py-20 px-4 sm:px-6 lg:px-8" data-aos="fade-down">
        <div class="max-w-4xl mx-auto">
            <div class="hero-content">
                <?php if (!empty($settings['logo'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
                         alt="Logo OSIS" 
                         class="h-24 w-auto mx-auto mb-6 animate__animated animate__fadeInDown">
                <?php endif; ?>
                <h1 class="text-4xl sm:text-5xl font-bold mb-4 animate__animated animate__fadeInUp text-center">
                    OSIS <?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'SMA Negeri 1'); ?>
                </h1>
                <p class="text-xl text-gray-200 mb-8 animate__animated animate__fadeInUp animate__delay-1s text-center">
                    Tahun Ajaran <?php echo htmlspecialchars($settings['tahun_ajaran'] ?? '2023/2024'); ?>
                </p>
                <div class="text-center">
                    <a href="candidates.php" 
                       class="btn-hover inline-block bg-white text-gray-800 font-bold py-3 px-8 rounded-full text-lg shadow-lg animate__animated animate__fadeInUp animate__delay-2s">
                        <i class="fas fa-users mr-2"></i>Lihat Kandidat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi & Misi Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Visi & Misi OSIS <?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'SMA Negeri 1'); ?></h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Visi dan Misi OSIS merupakan pedoman dan arah dalam menjalankan program kerja untuk mewujudkan siswa yang berkarakter, berprestasi, dan bertanggung jawab.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Visi Card -->
            <div class="card-hover glass-card rounded-2xl shadow-xl p-8" data-aos="fade-right">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-blue-100 rounded-xl mr-4">
                        <i class="fas fa-bullseye text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Visi</h2>
                </div>
                <div class="prose max-w-none text-gray-600">
                    <?php echo nl2br(htmlspecialchars($settings['visi'] ?? 'Visi OSIS akan ditampilkan di sini')); ?>
                </div>
            </div>

            <!-- Misi Card -->
            <div class="card-hover glass-card rounded-2xl shadow-xl p-8" data-aos="fade-left">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-green-100 rounded-xl mr-4">
                        <i class="fas fa-tasks text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Misi</h2>
                </div>
                <div class="prose max-w-none text-gray-600">
                    <?php echo nl2br(htmlspecialchars($settings['misi'] ?? 'Misi OSIS akan ditampilkan di sini')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Inisialisasi AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

// Animasi untuk elemen saat scroll
document.addEventListener('scroll', function() {
    const cards = document.querySelectorAll('.card-hover');
    cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const isVisible = (rect.top <= window.innerHeight && rect.bottom >= 0);
        
        if (isVisible) {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>