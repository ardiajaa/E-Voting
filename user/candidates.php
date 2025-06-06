<?php
require_once '../includes/header.php';
require_once '../config/database.php';

// Cek apakah user sudah memilih
$stmt = $pdo->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Ambil data kandidat
$stmt = $pdo->query("SELECT * FROM candidates");
$candidates = $stmt->fetchAll();
?>

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Tambahkan SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<style>
    .candidate-card {
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .candidate-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .candidate-image-container {
        position: relative;
        width: 100%;
        padding-top: 100%;
        overflow: hidden;
    }
    .candidate-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }
    .candidate-card:hover .candidate-image {
        transform: scale(1.05);
    }
    .vote-button {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .vote-button::before {
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
    .vote-button:hover::before {
        width: 300px;
        height: 300px;
    }
    .vote-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .section-title {
        position: relative;
        display: inline-block;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #3B82F6, #2563EB);
        border-radius: 3px;
    }
    .candidate-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
    }
    
    /* Custom style untuk SweetAlert2 */
    .swal2-popup {
        border-radius: 1rem !important;
        padding: 1.5rem !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(10px) !important;
        max-width: 400px !important;
    }
    .swal2-title {
        color: #1F2937 !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        margin-bottom: 1rem !important;
    }
    .swal2-html-container {
        color: #4B5563 !important;
        font-size: 0.95rem !important;
        line-height: 1.5 !important;
        margin: 0 !important;
    }
    .swal2-confirm {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2) !important;
        color: #FFFFFF !important;
    }
    .swal2-confirm:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3) !important;
        color: #FFFFFF !important;
    }
    .swal2-cancel {
        background: #EF4444 !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2) !important;
        color: #FFFFFF !important;
    }
    .swal2-cancel:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
        color: #FFFFFF !important;
    }
    .swal2-icon {
        border-width: 3px !important;
        margin: 0 auto 1rem !important;
        transform: scale(0.8) !important;
    }
    .swal2-icon.swal2-warning {
        border-color: #F59E0B !important;
        color: #F59E0B !important;
    }
    .warning-box {
        background: #FEF3C7 !important;
        border-left: 3px solid #F59E0B !important;
        padding: 0.75rem !important;
        border-radius: 0.375rem !important;
        margin-top: 0.75rem !important;
    }
    .warning-icon {
        color: #F59E0B !important;
        font-size: 1.25rem !important;
    }
    .swal2-actions {
        margin-top: 1.25rem !important;
        gap: 0.5rem !important;
    }
</style>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-16" data-aos="fade-down">
                <h1 class="text-4xl font-bold text-gray-800 mb-4 section-title">Kandidat Ketua OSIS</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Pilih kandidat yang menurut Anda memiliki visi dan misi terbaik untuk memimpin OSIS
                </p>
            </div>
            
            <!-- Candidates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($candidates as $index => $candidate): ?>
                <div class="candidate-card rounded-2xl shadow-xl overflow-hidden" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <!-- Image Container -->
                    <div class="candidate-image-container">
                        <img src="../assets/images/candidates/<?php echo htmlspecialchars($candidate['foto']); ?>" 
                             alt="<?php echo htmlspecialchars($candidate['nama']); ?>" 
                             class="candidate-image">
                        <div class="candidate-info">
                            <h2 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($candidate['nama']); ?></h2>
                            <p class="text-sm text-gray-200">
                                Kelas <?php echo htmlspecialchars($candidate['kelas']); ?> - Absen <?php echo htmlspecialchars($candidate['absen']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2 flex items-center">
                                <i class="fas fa-bullseye text-blue-500 mr-2"></i>Visi
                            </h3>
                            <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($candidate['visi'])); ?></p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2 flex items-center">
                                <i class="fas fa-tasks text-green-500 mr-2"></i>Misi
                            </h3>
                            <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($candidate['misi'])); ?></p>
                        </div>
                        
                        <?php if (!$user['has_voted']): ?>
                        <form action="vote.php" method="POST" class="text-center" onsubmit="return confirmVote(event)">
                            <input type="hidden" name="candidate_id" value="<?php echo $candidate['id']; ?>">
                            <button type="submit" 
                                    class="vote-button bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 px-6 rounded-lg w-full">
                                <i class="fas fa-vote-yea mr-2"></i>Pilih Kandidat
                            </button>
                        </form>
                        <?php else: ?>
                        <button disabled 
                                class="bg-gray-400 text-white font-bold py-3 px-6 rounded-lg w-full cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>Anda Sudah Memilih
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
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

// Fungsi konfirmasi vote dengan SweetAlert2
function confirmVote(event) {
    event.preventDefault();
    
    const form = event.target;
    const candidateId = form.querySelector('input[name="candidate_id"]').value;
    
    Swal.fire({
        title: 'Konfirmasi Pilihan',
        html: `
            <div class="text-center">
                <p class="mb-3">Apakah Anda yakin ingin memilih kandidat ini?</p>
                <div class="warning-box">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle warning-icon"></i>
                        </div>
                        <div class="ml-2">
                            <p class="text-xs text-yellow-800 font-medium">
                                <strong>PERHATIAN:</strong><br>
                                Anda hanya dapat memilih 1 kali dan tidak dapat mengubah pilihan setelahnya!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Saya Yakin',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'animate__animated animate__fadeInDown',
            confirmButton: 'btn-confirm',
            cancelButton: 'btn-cancel'
        },
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        background: 'rgba(255, 255, 255, 0.98)',
        backdrop: `
            rgba(0,0,0,0.4)
            left top
            no-repeat
        `
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

// Tambahkan event listener untuk semua form vote
document.addEventListener('DOMContentLoaded', function() {
    const voteForms = document.querySelectorAll('form[action="vote.php"]');
    voteForms.forEach(form => {
        form.addEventListener('submit', confirmVote);
    });
});

// Animasi untuk elemen saat scroll
document.addEventListener('scroll', function() {
    const cards = document.querySelectorAll('.candidate-card');
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