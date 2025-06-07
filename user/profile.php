<?php
require_once '../includes/header.php';
require_once '../config/database.php';

// Ambil data user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Cek apakah user sudah memilih
$stmt = $pdo->prepare("SELECT * FROM votes WHERE user_id = ?");
$stmt->execute([$user_id]);
$vote = $stmt->fetch();

if ($vote) {
    $stmt = $pdo->prepare("SELECT c.* FROM candidates c JOIN votes v ON c.id = v.candidate_id WHERE v.user_id = ?");
    $stmt->execute([$user_id]);
    $selected_candidate = $stmt->fetch();
}

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>

<style>
/* Custom style untuk background */
.min-h-screen {
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
    url('<?php echo !empty($settings['background']) ? '../uploads/' . htmlspecialchars($settings['background']) : 'https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg'; ?>');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
}

.min-h-screen::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

/* Custom style untuk card */
.bg-white {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.profile-card {
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.vote-status {
    position: relative;
    margin-bottom: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-block;
    text-align: center;
    width: fit-content;
}

@media (min-width: 768px) {
    .vote-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        margin-bottom: 0;
    }
}

.vote-status.voted {
    background: #10B981;
    color: white;
}

.vote-status.not-voted {
    background: #EF4444;
    color: white;
}
</style>

<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Profile Card -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden profile-card" data-aos="fade-up">
            <div class="p-8">
                <div class="relative">
                    <div class="vote-status <?php echo isset($user['has_voted']) && $user['has_voted'] ? 'voted' : 'not-voted'; ?>">
                        <?php echo isset($user['has_voted']) && $user['has_voted'] ? 'Sudah Memilih' : 'Belum Memilih'; ?>
                    </div>
                    
                    <div class="text-center mb-8">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo isset($user['nama_lengkap']) ? htmlspecialchars($user['nama_lengkap']) : 'Nama Tidak Tersedia'; ?></h1>
                        <p class="text-gray-600"><?php echo isset($user['kelas']) ? htmlspecialchars($user['kelas']) : 'Kelas Tidak Tersedia'; ?></p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <i class="fas fa-id-card text-blue-500 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-600">NIS</p>
                                <p class="font-medium text-gray-800"><?php echo isset($user['nis']) ? htmlspecialchars($user['nis']) : 'NIS Tidak Tersedia'; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <i class="fas fa-user text-blue-500 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-600">Nama</p>
                                <p class="font-medium text-gray-800"><?php echo isset($user['nama_lengkap']) ? htmlspecialchars($user['nama_lengkap']) : 'Nama Tidak Tersedia'; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <i class="fas fa-graduation-cap text-blue-500 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-600">Kelas</p>
                                <p class="font-medium text-gray-800"><?php echo isset($user['kelas']) ? htmlspecialchars($user['kelas']) : 'Kelas Tidak Tersedia'; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <i class="fas fa-sort-numeric-up text-blue-500 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-600">Absen</p>
                                <p class="font-medium text-gray-800"><?php echo isset($user['absen']) ? htmlspecialchars($user['absen']) : 'Absen Tidak Tersedia'; ?></p>
                            </div>
                        </div>
                        
                        <?php if (isset($user['has_voted']) && $user['has_voted'] && isset($selected_candidate)): ?>
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <i class="fas fa-user-check text-blue-500 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-600">Kandidat Yang Anda Pilih</p>
                                <p class="font-medium text-gray-800"><?php echo isset($selected_candidate['nama']) ? htmlspecialchars($selected_candidate['nama']) : 'Kandidat Tidak Tersedia'; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!isset($user['has_voted']) || !$user['has_voted']): ?>
                    <div class="mt-8 text-center">
                        <a href="candidates.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300">
                            <i class="fas fa-vote-yea mr-2"></i>
                            Pilih Kandidat
                        </a>
                    </div>
                    <?php endif; ?>
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
</script>

<?php require_once '../includes/footer.php'; ?>
