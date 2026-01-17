<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

// Buat tabel voting_time jika belum ada
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS voting_time (
        id INT AUTO_INCREMENT PRIMARY KEY,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Handle error jika diperlukan
}

// Ambil statistik
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as voted FROM users WHERE has_voted = 1");
$voted_users = $stmt->fetch()['voted'];

$stmt = $pdo->query("SELECT c.nama, c.foto, COUNT(v.id) as vote_count 
                     FROM candidates c 
                     LEFT JOIN votes v ON c.id = v.candidate_id 
                     GROUP BY c.id");
$candidate_stats = $stmt->fetchAll();

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();

// Ambil data admin yang login
$admin_id = $_SESSION['admin_id'] ?? null;
$admin_data = null;
if ($admin_id) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin_data = $stmt->fetch();
}

// Ambil pengaturan waktu voting
$stmt = $pdo->query("SELECT * FROM voting_time ORDER BY id DESC LIMIT 1");
$voting_time = $stmt->fetch();

// Hitung persentase
$voting_percentage = $total_users > 0 ? ($voted_users / $total_users) * 100 : 0;

// Cek status voting
$now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$voting_status = 'belum_mulai';
$time_left = null;
$start_time = null;
$end_time = null;

if ($voting_time && !empty($voting_time['start_time']) && !empty($voting_time['end_time'])) {
    $start_time = new DateTime($voting_time['start_time'], new DateTimeZone('Asia/Jakarta'));
    $end_time = new DateTime($voting_time['end_time'], new DateTimeZone('Asia/Jakarta'));
    
    // Bandingkan waktu dengan akurat
    if ($now < $start_time) {
        $voting_status = 'belum_mulai';
        $time_left = $now->diff($start_time);
    } elseif ($now >= $start_time && $now <= $end_time) {
        $voting_status = 'sedang_berlangsung';
        $time_left = $now->diff($end_time);
    } else {
        $voting_status = 'selesai';
        $time_left = null;
    }
}
?>

<!-- Tambahkan Chart.js dan AOS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Custom style untuk background */
.min-h-screen {
    background: #f3f4f6;
    position: relative;
}

/* Custom style untuk card */
.bg-white {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Custom style untuk notifikasi */
.notification-container {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 100%;
    width: 100%;
    padding: 0 1rem;
    pointer-events: none;
}

@media (min-width: 640px) {
    .notification-container {
        width: 400px;
        padding: 0;
    }
}

.notification {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 1rem;
    transform: translateX(120%);
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    pointer-events: auto;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    border-left: 4px solid #10B981;
}

.notification.error {
    border-left: 4px solid #EF4444;
}

.notification-icon {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.notification.success .notification-icon {
    background: #D1FAE5;
    color: #10B981;
}

.notification.error .notification-icon {
    background: #FEE2E2;
    color: #EF4444;
}

.notification-content {
    flex-grow: 1;
}

.notification-title {
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.notification-message {
    color: #4B5563;
    font-size: 0.875rem;
    line-height: 1.4;
}

.notification-close {
    flex-shrink: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9CA3AF;
    cursor: pointer;
    transition: all 0.2s;
}

.notification-close:hover {
    background: #F3F4F6;
    color: #4B5563;
}

.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: #E5E7EB;
    border-radius: 0 0 0 1rem;
}

.notification.success .notification-progress {
    background: #10B981;
}

.notification.error .notification-progress {
    background: #EF4444;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Custom styles untuk SweetAlert2 Admin */
.swal2-popup-custom {
    border-radius: 1rem !important;
    padding: 2rem !important;
    max-width: 90% !important;
    width: 450px !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px) !important;
}

.swal2-title-custom {
    font-size: 1.5rem !important;
    color: #1F2937 !important;
    margin-bottom: 1rem !important;
    font-weight: 700 !important;
}

.swal2-html-container-custom {
    margin: 1rem 0 !important;
}

.swal2-confirm-button-custom {
    padding: 0.75rem 2rem !important;
    font-size: 1rem !important;
    border-radius: 0.5rem !important;
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
    transition: all 0.3s ease !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2) !important;
}

.swal2-confirm-button-custom:hover {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3) !important;
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

<div id="notification-container" class="notification-container"></div>

<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Status Voting -->
        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 mb-8" data-aos="fade-up">
            <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Status Voting</h2>
                    <?php if ($voting_time): ?>
                        <div class="text-sm sm:text-base text-gray-600 space-y-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-play-circle text-blue-500"></i>
                                <span>Mulai: <?php echo date('d M Y H:i', strtotime($voting_time['start_time'])); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-stop-circle text-red-500"></i>
                                <span>Selesai: <?php echo date('d M Y H:i', strtotime($voting_time['end_time'])); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-sm sm:text-base text-gray-600">Belum ada pengaturan waktu voting</p>
                    <?php endif; ?>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <div class="flex items-center gap-3">
                        <?php if ($voting_status == 'belum_mulai'): ?>
                            <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-yellow-100 text-yellow-800 rounded-full text-xs sm:text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-clock"></i>
                                <span>Belum Dimulai</span>
                            </span>
                            <?php if ($time_left && $start_time): ?>
                                <span class="text-xs sm:text-sm text-gray-600" id="countdown-timer">
                                    <span data-target="<?php echo $start_time->format('Y-m-d H:i:s'); ?>">
                                        <?php echo $time_left->format('%d hari %h jam %i menit %s detik'); ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                        <?php elseif ($voting_status == 'sedang_berlangsung'): ?>
                            <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-green-100 text-green-800 rounded-full text-xs sm:text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-spinner animate-spin"></i>
                                <span>Sedang Berlangsung</span>
                            </span>
                            <?php if ($time_left && $end_time): ?>
                                <span class="text-xs sm:text-sm text-gray-600" id="countdown-timer">
                                    <span data-target="<?php echo $end_time->format('Y-m-d H:i:s'); ?>">
                                        <?php echo $time_left->format('%d hari %h jam %i menit %s detik'); ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-red-100 text-red-800 rounded-full text-xs sm:text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span>Selesai</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <button onclick="openVotingTimeModal()" 
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300 flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-clock"></i>
                        <span class="text-sm sm:text-base">Atur Waktu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Informasi OSIS -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 transform hover:scale-[1.02] transition-all duration-300" data-aos="fade-up">
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'Nama Sekolah'); ?></h2>
                    <p class="text-lg text-gray-600"><?php echo htmlspecialchars($settings['tahun_ajaran'] ?? 'Tahun Ajaran'); ?></p>
                </div>
                <?php if (!empty($settings['logo'])): ?>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <img src="../uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
                         alt="Logo OSIS" class="h-24 w-auto object-contain">
                </div>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Visi Card -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl shadow-lg transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <i class="fas fa-bullseye text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-blue-800">Visi OSIS</h3>
                    </div>
                    <div class="text-gray-700 leading-relaxed text-lg space-y-4">
                        <?php if (!empty($settings['visi'])): ?>
                            <?php foreach(explode("\n", $settings['visi']) as $line): ?>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                                    <span><?php echo htmlspecialchars($line); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-gray-500 italic">
                                Visi OSIS akan ditampilkan di sini
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Misi Card -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl shadow-lg transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <i class="fas fa-tasks text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-green-800">Misi OSIS</h3>
                    </div>
                    <div class="text-gray-700 leading-relaxed text-lg space-y-4">
                        <?php if (!empty($settings['misi'])): ?>
                            <?php foreach(explode("\n", $settings['misi']) as $line): ?>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span><?php echo htmlspecialchars($line); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-gray-500 italic">
                                Misi OSIS akan ditampilkan di sini
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Progress Pemilihan</h3>
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                            Progress
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-blue-600">
                            <?php echo number_format($voting_percentage, 1); ?>%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-4 mb-4 text-xs flex rounded-full bg-blue-100">
                    <div style="width: <?php echo $voting_percentage; ?>%" 
                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500 ease-out"></div>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Pemilih</h3>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-4xl font-bold text-blue-600"><?php echo $total_users; ?></p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Sudah Memilih</h3>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-4xl font-bold text-green-600"><?php echo $voted_users; ?></p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Belum Memilih</h3>
                    <div class="p-3 bg-red-100 rounded-xl">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-4xl font-bold text-red-600"><?php echo $total_users - $voted_users; ?></p>
            </div>
        </div>

        <!-- Grafik Statistik -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Grafik Perbandingan Sudah/Belum Memilih -->
            <div class="bg-white rounded-2xl shadow-xl p-8" data-aos="fade-up" data-aos-delay="500">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pemilihan</h2>
                <canvas id="votingStatusChart"></canvas>
            </div>

            <!-- Grafik Hasil Voting -->
            <div class="bg-white rounded-2xl shadow-xl p-8" data-aos="fade-up" data-aos-delay="600">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Hasil Voting</h2>
                <canvas id="votingResultsChart"></canvas>
            </div>
        </div>

        <!-- Tabel Statistik Kandidat -->
        <div class="bg-white rounded-2xl shadow-xl p-8" data-aos="fade-up" data-aos-delay="700">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Statistik Kandidat</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kandidat</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Suara</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($candidate_stats as $stat): 
                            $percentage = $voted_users > 0 ? ($stat['vote_count'] / $voted_users) * 100 : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if (!empty($stat['foto'])): ?>
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                 src="../assets/images/candidates/<?php echo htmlspecialchars($stat['foto']); ?>"
                                                 alt="<?php echo htmlspecialchars($stat['nama']); ?>">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($stat['nama']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $stat['vote_count']; ?> suara</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo number_format($percentage, 1); ?>%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" 
                                         style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Atur Waktu Voting -->
<div id="votingTimeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Atur Waktu Voting</h3>
                <button onclick="closeVotingTimeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="votingTimeForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" required
                           value="<?php echo $voting_time ? date('Y-m-d\TH:i', strtotime($voting_time['start_time'])) : ''; ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                    <input type="datetime-local" name="end_time" required
                           value="<?php echo $voting_time ? date('Y-m-d\TH:i', strtotime($voting_time['end_time'])) : ''; ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeVotingTimeModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-300">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300">
                        Simpan
                    </button>
                </div>
            </form>
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

// Pop-up selamat datang admin
<?php if ($admin_data && isset($_SESSION['show_welcome_admin']) && $_SESSION['show_welcome_admin']): ?>
Swal.fire({
    title: 'Selamat Datang, Admin!',
    html: `
        <div class="text-center">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Anda dapat mengelola pemilihan ketua OSIS, kandidat, dan user dari dashboard ini.
                </p>
            </div>
        </div>
    `,
    icon: 'success',
    showConfirmButton: true,
    confirmButtonText: 'Mulai Mengelola',
    confirmButtonColor: '#3B82F6',
    timer: 6000,
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
unset($_SESSION['show_welcome_admin']);
endif; 
?>

// Countdown Timer
function updateCountdown() {
    const countdownElements = document.querySelectorAll('[data-target]');
    
    countdownElements.forEach(element => {
        const targetStr = element.getAttribute('data-target');
        if (!targetStr) return;
        
        // Parse datetime dengan format Y-m-d H:i:s
        const targetDate = new Date(targetStr.replace(' ', 'T'));
        const now = new Date();
        const distance = targetDate.getTime() - now.getTime();

        if (distance < 0) {
            element.innerHTML = "Waktu Habis";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        element.innerHTML = `${days} hari ${hours} jam ${minutes} menit ${seconds} detik`;
    });
}

// Update countdown setiap detik
setInterval(updateCountdown, 1000);
updateCountdown(); // Panggil sekali untuk inisialisasi

// Data untuk grafik
const votingStatusData = {
    labels: ['Sudah Memilih', 'Belum Memilih'],
    datasets: [{
        data: [<?php echo $voted_users; ?>, <?php echo $total_users - $voted_users; ?>],
        backgroundColor: ['#10B981', '#EF4444'],
        borderWidth: 0
    }]
};

const votingResultsData = {
    labels: [<?php echo implode(',', array_map(function($stat) { 
        return "'" . addslashes($stat['nama']) . "'"; 
    }, $candidate_stats)); ?>],
    datasets: [{
        label: 'Jumlah Suara',
        data: [<?php echo implode(',', array_map(function($stat) { 
            return $stat['vote_count']; 
        }, $candidate_stats)); ?>],
        backgroundColor: [
            '#3B82F6',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6'
        ],
        borderWidth: 0
    }]
};

// Konfigurasi grafik
const chartConfig = {
    type: 'doughnut',
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true,
            duration: 2000,
            easing: 'easeOutQuart'
        },
        cutout: '70%'
    }
};

// Render grafik
new Chart(document.getElementById('votingStatusChart'), {
    ...chartConfig,
    data: votingStatusData
});

new Chart(document.getElementById('votingResultsChart'), {
    ...chartConfig,
    data: votingResultsData
});

// Fungsi untuk modal waktu voting
function openVotingTimeModal() {
    document.getElementById('votingTimeModal').classList.remove('hidden');
}

function closeVotingTimeModal() {
    document.getElementById('votingTimeModal').classList.add('hidden');
}

// Fungsi untuk menampilkan notifikasi yang lebih modern
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    const title = type === 'success' ? 'Berhasil!' : 'Error!';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas ${icon}"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <div class="notification-close">
            <i class="fas fa-times"></i>
        </div>
        <div class="notification-progress"></div>
    `;
    
    container.appendChild(notification);
    
    // Trigger animasi masuk
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Animasi progress bar
    const progress = notification.querySelector('.notification-progress');
    progress.style.animation = 'progress 3s linear forwards';
    
    // Event listener untuk tombol close
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        removeNotification(notification);
    });
    
    // Auto remove setelah 3 detik
    setTimeout(() => {
        removeNotification(notification);
    }, 3000);
}

function removeNotification(notification) {
    notification.classList.remove('show');
    notification.addEventListener('transitionend', () => {
        notification.remove();
    });
}

// Handle form submission
document.getElementById('votingTimeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('set-voting-time', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Waktu voting berhasil diatur! 🎉', 'success');
            setTimeout(() => {
                location.reload();
            }, 5000);
        } else {
            showNotification(result.message || 'Terjadi kesalahan!', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat mengatur waktu voting!', 'error');
    }
});
</script>

<?php require_once '../includes/footer.php'; ?> 