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

// Ambil data user yang login
$user_id = $_SESSION['user_id'] ?? null;
$user_data = null;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
}
?>

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        overflow-x: hidden;
    }
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                    url('<?php echo !empty($settings['background']) ? '../uploads/' . htmlspecialchars($settings['background']) : 'https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg'; ?>');
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

// Popup selamat datang
<?php if ($user_data && isset($_SESSION['show_welcome']) && $_SESSION['show_welcome']): ?>
Swal.fire({
    title: 'Selamat Datang!',
    html: `
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-user-circle text-6xl text-blue-500"></i>
            </div>
            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h3>
            <p class="text-gray-600 mb-1">
                <span class="font-medium">Kelas:</span> <?php echo htmlspecialchars($user_data['kelas']); ?>
            </p>
            <p class="text-gray-600">
                <span class="font-medium">Absen:</span> <?php echo htmlspecialchars($user_data['absen']); ?>
            </p>
        </div>
    `,
    icon: 'success',
    showConfirmButton: true,
    confirmButtonText: 'Lanjutkan',
    confirmButtonColor: '#3B82F6',
    timer: 5000,
    timerProgressBar: true,
    toast: false,
    position: 'center',
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    },
    customClass: {
        popup: 'swal2-popup-custom',
        title: 'swal2-title-custom',
        htmlContainer: 'swal2-html-container-custom',
        confirmButton: 'swal2-confirm-button-custom'
    }
});
<?php 
// Hapus flag setelah menampilkan popup
unset($_SESSION['show_welcome']);
endif; 
?>
</script>

<style>
/* Custom styles untuk SweetAlert2 */
.swal2-popup-custom {
    border-radius: 1rem !important;
    padding: 2rem !important;
    max-width: 90% !important;
    width: 400px !important;
}

.swal2-title-custom {
    font-size: 1.5rem !important;
    color: #1F2937 !important;
    margin-bottom: 1rem !important;
}

.swal2-html-container-custom {
    margin: 1rem 0 !important;
}

.swal2-confirm-button-custom {
    padding: 0.75rem 2rem !important;
    font-size: 1rem !important;
    border-radius: 0.5rem !important;
    background-color: #3B82F6 !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm-button-custom:hover {
    background-color: #2563EB !important;
    transform: translateY(-2px) !important;
}

@media (max-width: 640px) {
    .swal2-popup-custom {
        width: 90% !important;
        padding: 1.5rem !important;
    }
    
    .swal2-title-custom {
        font-size: 1.25rem !important;
    }
    
    .swal2-html-container-custom {
        font-size: 0.875rem !important;
    }
    
    .swal2-confirm-button-custom {
        padding: 0.5rem 1.5rem !important;
        font-size: 0.875rem !important;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>