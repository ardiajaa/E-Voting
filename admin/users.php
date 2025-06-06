<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

// Proses hapus user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    try {
        // Cek apakah user sudah memilih
        $stmt = $pdo->prepare("SELECT has_voted FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if ($user && $user['has_voted']) {
            $error = "Tidak dapat menghapus user yang sudah memilih!";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
            $stmt->execute([$id]);
            $success = "User berhasil dihapus!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Proses tambah user
if (isset($_POST['add_user'])) {
    $nis = $_POST['nis'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $kelas = $_POST['kelas'];
    $absen = $_POST['absen'];
    
    try {
        // Cek apakah NIS sudah ada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nis = ?");
        $stmt->execute([$nis]);
        if ($stmt->fetchColumn() > 0) {
            $error = "NIS sudah terdaftar!";
        } else {
            // Generate password default
            $password = password_hash('rahasia', PASSWORD_DEFAULT);
            
            // Insert user baru
            $stmt = $pdo->prepare("INSERT INTO users (nis, nama_lengkap, kelas, absen, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->execute([$nis, $nama_lengkap, $kelas, $absen, $password]);
            
            $success = "User berhasil ditambahkan!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Proses import Excel
if (isset($_POST['import'])) {
    require '../vendor/autoload.php'; // Mengubah path ke vendor/autoload.php
    
    try {
        $inputFileName = $_FILES['excel_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $stmt = $pdo->prepare("INSERT INTO users (nis, nama_lengkap, kelas, absen, password) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            
            if (!empty($data[0])) {
                $password = password_hash('rahasia', PASSWORD_DEFAULT);
                $stmt->execute([$data[0], $data[1], $data[2], $data[3], $password]);
            }
        }
        
        $success = "Data berhasil diimport!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Ambil data users
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
/* Custom style untuk halaman users */
.page-container {
    min-height: calc(100vh - 4rem);
    background: linear-gradient(135deg, #f6f8fc 0%, #f1f5f9 100%);
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.content-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.action-button {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.action-button::before {
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

.action-button:hover::before {
    width: 300px;
    height: 300px;
}

.action-button:hover {
    transform: translateY(-2px);
}

.table-container {
    overflow-x: auto;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    background: white;
    margin: 1rem 0;
    width: 100%;
}

.custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 100%;
}

.custom-table th {
    background: #f8fafc;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    padding: 1rem;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    text-align: left;
}

.custom-table td {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    vertical-align: middle;
}

.custom-table tr:last-child td {
    border-bottom: none;
}

.custom-table tr:hover td {
    background: #f8fafc;
}

.custom-table td:first-child {
    padding-left: 1.5rem;
}

.custom-table td:last-child {
    padding-right: 1.5rem;
}

.custom-table th:first-child {
    padding-left: 1.5rem;
}

.custom-table th:last-child {
    padding-right: 1.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.status-badge.voted {
    background: #dcfce7;
    color: #166534;
}

.status-badge.not-voted {
    background: #fee2e2;
    color: #991b1b;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-start;
    align-items: center;
}

.action-buttons a {
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.action-buttons a:hover {
    background: #f1f5f9;
}

.action-buttons .edit-btn {
    color: #4f46e5;
}

.action-buttons .delete-btn {
    color: #ef4444;
}

.action-buttons .edit-btn:hover {
    color: #4338ca;
}

.action-buttons .delete-btn:hover {
    color: #dc2626;
}

.modal-overlay {
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: scale(0.95);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-content.show {
    transform: scale(1);
    opacity: 1;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.form-label {
    font-weight: 500;
    color: #475569;
    margin-bottom: 0.5rem;
    display: block;
}

/* Animasi untuk tabel */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-row {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.table-row:nth-child(1) { animation-delay: 0.1s; }
.table-row:nth-child(2) { animation-delay: 0.2s; }
.table-row:nth-child(3) { animation-delay: 0.3s; }
.table-row:nth-child(4) { animation-delay: 0.4s; }
.table-row:nth-child(5) { animation-delay: 0.5s; }

/* Responsive table columns */
@media (min-width: 1024px) {
    .custom-table th:nth-child(1),
    .custom-table td:nth-child(1) {
        width: 15%;
    }
    
    .custom-table th:nth-child(2),
    .custom-table td:nth-child(2) {
        width: 25%;
    }
    
    .custom-table th:nth-child(3),
    .custom-table td:nth-child(3) {
        width: 15%;
    }
    
    .custom-table th:nth-child(4),
    .custom-table td:nth-child(4) {
        width: 10%;
    }
    
    .custom-table th:nth-child(5),
    .custom-table td:nth-child(5) {
        width: 20%;
    }
    
    .custom-table th:nth-child(6),
    .custom-table td:nth-child(6) {
        width: 15%;
    }
}

/* Responsive header */
@media (min-width: 1024px) {
    .content-card .flex {
        padding: 0 1rem;
    }
    
    .content-card h2 {
        font-size: 1.5rem;
        line-height: 2rem;
    }
    
    .action-button {
        padding: 0.5rem 1rem;
    }
}

/* Responsive table container */
@media (max-width: 1023px) {
    .table-container {
        margin: 1rem -1rem;
        border-radius: 0;
        width: calc(100% + 2rem);
    }
    
    .custom-table {
        min-width: 800px;
    }
}

/* Responsive action buttons */
@media (max-width: 640px) {
    .action-buttons {
        gap: 0.5rem;
    }
    
    .action-buttons a {
        padding: 0.375rem;
    }
}

/* Header section styles */
.header-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .header-section {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.header-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.action-buttons-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.action-button i {
    font-size: 1rem;
}

.action-button span {
    font-size: 0.875rem;
}
</style>

<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <div class="content-card rounded-xl p-6" data-aos="fade-up">
            <div class="header-section">
                <h2 class="header-title">Manajemen User</h2>
                
                <div class="action-buttons-group">
                    <a href="download-template.php" 
                       class="action-button bg-yellow-500 hover:bg-yellow-600 text-white">
                        <i class="fas fa-download"></i>
                        <span>Download Template</span>
                    </a>
                    <button onclick="openModal('importModal')" 
                            class="action-button bg-green-500 hover:bg-green-600 text-white">
                        <i class="fas fa-file-import"></i>
                        <span>Import Excel</span>
                    </button>
                    <button onclick="openModal('addUserModal')"
                            class="action-button bg-blue-500 hover:bg-blue-600 text-white">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah User</span>
                    </button>
                </div>
            </div>

            <?php if (isset($success)): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4" role="alert" data-aos="fade-right">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p><?php echo $success; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4" role="alert" data-aos="fade-right">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p><?php echo $error; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Absen</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                        <tr class="table-row">
                            <td><?php echo htmlspecialchars($user['nis']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($user['kelas']); ?></td>
                            <td><?php echo htmlspecialchars($user['absen']); ?></td>
                            <td>
                                <?php if ($user['has_voted']): ?>
                                    <span class="status-badge voted">
                                        <i class="fas fa-check-circle"></i>
                                        Sudah Memilih
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge not-voted">
                                        <i class="fas fa-times-circle"></i>
                                        Belum Memilih
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                       class="edit-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $user['id']; ?>" 
                                       class="delete-btn" title="Hapus"
                                       onclick="event.preventDefault();
                                       if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                                           window.location.href = this.href;
                                       }">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
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

<!-- Modal Import Excel -->
<div id="importModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute inset-0"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Data User</h3>
                <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label" for="excel_file">
                        File Excel
                    </label>
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required
                           class="form-input">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('importModal')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" name="import"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute inset-0"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="modal-content w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah User Baru</h3>
                <button onclick="closeModal('addUserModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="form-label" for="nis">NIS</label>
                    <input type="text" name="nis" id="nis" required class="form-input">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required class="form-input">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="kelas">Kelas</label>
                    <input type="text" name="kelas" id="kelas" required class="form-input">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="absen">Absen</label>
                    <input type="number" name="absen" id="absen" required class="form-input">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addUserModal')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" name="add_user"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
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

// Fungsi untuk modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.modal-content').classList.add('show');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.modal-content');
    modalContent.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Konfirmasi hapus
function confirmDelete() {
    return Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus user ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Animasi untuk tabel
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row');
    rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 