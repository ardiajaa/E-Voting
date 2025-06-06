<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

// Ambil data user berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: users.php');
        exit();
    }
} else {
    header('Location: users.php');
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $kelas = $_POST['kelas'];
    $absen = $_POST['absen'];
    
    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nis = ?, nama_lengkap = ?, kelas = ?, absen = ?, password = ? WHERE id = ?");
        $stmt->execute([$nis, $nama_lengkap, $kelas, $absen, $password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nis = ?, nama_lengkap = ?, kelas = ?, absen = ? WHERE id = ?");
        $stmt->execute([$nis, $nama_lengkap, $kelas, $absen, $id]);
    }
    
    header('Location: users.php');
    exit();
}
?>

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
.page-container {
    min-height: calc(100vh - 4rem);
    background: linear-gradient(135deg, #f6f8fc 0%, #f1f5f9 100%);
    padding: 2rem 0;
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.content-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #fff;
    color: #1f2937;
    transition: all 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:hover {
    border-color: #cbd5e1;
}

.form-input::placeholder {
    color: #9ca3af;
}

.form-input:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    gap: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.btn-secondary {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-secondary:hover {
    background: #e5e7eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-icon {
    font-size: 1.25rem;
}

/* Animasi untuk form elements */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-group {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }

/* Responsive styles */
@media (max-width: 640px) {
    .page-container {
        padding: 1rem 0;
    }
    
    .content-card {
        margin: 0 -1rem;
        border-radius: 0;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .button-group {
        flex-direction: column;
    }
}
</style>

<div class="page-container">
    <div class="container mx-auto px-4">
        <div class="content-card p-6" data-aos="fade-up">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
                <a href="users.php" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
            
            <form action="" method="POST" class="space-y-6">
                <div class="form-group">
                    <label class="form-label" for="nis">
                        <i class="fas fa-id-card mr-2"></i>
                        NIS
                    </label>
                    <input type="text" name="nis" id="nis" required
                           value="<?php echo htmlspecialchars($user['nis']); ?>"
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="nama_lengkap">
                        <i class="fas fa-user mr-2"></i>
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required
                           value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="kelas">
                        <i class="fas fa-chalkboard mr-2"></i>
                        Kelas
                    </label>
                    <input type="text" name="kelas" id="kelas" required
                           value="<?php echo htmlspecialchars($user['kelas']); ?>"
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="absen">
                        <i class="fas fa-sort-numeric-up mr-2"></i>
                        Absen
                    </label>
                    <input type="number" name="absen" id="absen" required min="1"
                           value="<?php echo htmlspecialchars($user['absen']); ?>"
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-key mr-2"></i>
                        Password Baru
                        <span class="text-sm text-gray-500">(Kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" id="password"
                           class="form-input"
                           placeholder="Masukkan password baru">
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8">
                    <a href="users.php"
                       class="btn btn-secondary">
                        <i class="fas fa-times btn-icon"></i>
                        <span>Batal</span>
                    </a>
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save btn-icon"></i>
                        <span>Simpan Perubahan</span>
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

// Animasi untuk form elements
document.addEventListener('DOMContentLoaded', function() {
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 