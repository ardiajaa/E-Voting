<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Cek session admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./../auth/login.php');
    exit();
}

// Ambil data admin
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: ./../auth/login.php');
    exit();
}

require_once '../includes/admin-header.php';

$success_message = '';
$error_message = '';

// Proses update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validasi email
        if (!$email) {
            $error_message = "Email tidak valid!";
        } else {
            // Cek password lama jika ada perubahan password
            if (!empty($new_password)) {
                if (!password_verify($current_password, $admin['password'])) {
                    $error_message = "Password saat ini tidak sesuai!";
                } elseif ($new_password !== $confirm_password) {
                    $error_message = "Password baru dan konfirmasi password tidak sesuai!";
                } elseif (strlen($new_password) < 6) {
                    $error_message = "Password minimal 6 karakter!";
                } else {
                    // Update email dan password
                    $stmt = $pdo->prepare("UPDATE admin SET email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$email, password_hash($new_password, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
                    $success_message = "Profile berhasil diperbarui!";
                }
            } else {
                // Update email saja
                $stmt = $pdo->prepare("UPDATE admin SET email = ? WHERE id = ?");
                $stmt->execute([$email, $_SESSION['admin_id']]);
                $success_message = "Email berhasil diperbarui!";
            }
        }
    }
}

// Proses hapus riwayat login
if (isset($_GET['delete_history'])) {
    $id = $_GET['delete_history'];
    try {
        $stmt = $pdo->prepare("DELETE FROM login_history WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = "Riwayat login berhasil dihapus!";
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Ambil data riwayat login dengan fitur pencarian dan show entries
$search = isset($_GET['search']) ? $_GET['search'] : '';
$show_entries = isset($_GET['show']) ? (int)$_GET['show'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $show_entries;

$search_condition = '';
$params = [];

if (!empty($search)) {
    $search_condition = "WHERE (lh.attempted_email LIKE ? OR lh.ip_address LIKE ? OR lh.location LIKE ? OR lh.device LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Hitung total data untuk pagination
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM login_history lh 
    $search_condition
");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $show_entries);

// Ambil data riwayat login dengan limit
$stmt = $pdo->prepare("
    SELECT 
        lh.*,
        CASE 
            WHEN lh.user_type = 'admin' THEN a.email
            WHEN lh.user_type = 'user' THEN u.nis
        END as user_identifier
    FROM login_history lh
    LEFT JOIN admin a ON lh.user_id = a.id AND lh.user_type = 'admin'
    LEFT JOIN users u ON lh.user_id = u.id AND lh.user_type = 'user'
    $search_condition
    ORDER BY lh.login_time DESC
    LIMIT $show_entries OFFSET $offset
");
$stmt->execute($params);
$login_history = $stmt->fetchAll();

// Fungsi untuk mendapatkan lokasi dari IP
function getLocationFromIP($ip) {
    if ($ip == '127.0.0.1' || $ip == '::1') {
        return 'Localhost';
    }
    
    $url = "http://ip-api.com/json/" . $ip;
    $response = @file_get_contents($url);
    
    if ($response) {
        $data = json_decode($response);
        if ($data && $data->status == 'success') {
            return $data->city . ', ' . $data->country;
        }
    }
    
    return 'Unknown Location';
}

// Fungsi untuk mendapatkan informasi perangkat
function getDeviceInfo($userAgent) {
    $device = 'Unknown';
    
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
        $device = 'Mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
        $device = 'Tablet';
    } elseif (preg_match('/windows|macintosh|linux/i', $userAgent)) {
        $device = 'Desktop';
    }
    
    return $device;
}
?>

<!-- Tambahkan link Tailwind jika belum ada di header -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <div class="content-card rounded-xl p-6" data-aos="fade-up">
            <!-- Alert Messages -->
            <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" 
                 data-aos="fade-up" 
                 role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-xl mr-2"></i>
                    <p class="font-medium"><?php echo $success_message; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" 
                 data-aos="fade-up" 
                 role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-xl mr-2"></i>
                    <p class="font-medium"><?php echo $error_message; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Profile Form -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8" data-aos="fade-up">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                    Edit Profile
                </h2>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                    </div>

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <div class="relative">
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 focus:outline-none"
                                    onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="relative">
                            <input type="password" 
                                   name="new_password" 
                                   id="new_password"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 focus:outline-none"
                                    onclick="togglePassword('new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" 
                                   name="confirm_password" 
                                   id="confirm_password"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 focus:outline-none"
                                    onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                name="update_profile"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Riwayat Login -->
            <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up" data-aos-delay="100">
                <div class="header-section">
                    <h2 class="header-title flex items-center">
                        <i class="fas fa-history text-blue-500 mr-2"></i>
                        Riwayat Login
                    </h2>
                    
                    <!-- Search Box -->
                    <div class="search-container">
                        <form action="" method="GET" class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Cari berdasarkan email/NIS, IP, lokasi, atau perangkat..."
                                       class="search-input">
                                <?php if (!empty($search)): ?>
                                <a href="profile.php" class="search-clear" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <button type="submit" 
                                    class="action-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-search"></i>
                                <span>Cari</span>
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (empty($login_history)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Belum ada riwayat login</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Email/NIS</th>
                                    <th>IP Address</th>
                                    <th>Lokasi</th>
                                    <th>Perangkat</th>
                                    <th>Status</th>
                                    <th>Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($login_history as $login): ?>
                                    <tr class="table-row hover:bg-blue-50 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('d/m/Y H:i', strtotime($login['login_time'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($login['attempted_email']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($login['ip_address']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($login['location']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <?php
                                                $device_icon = 'fas fa-desktop';
                                                if ($login['device'] == 'Mobile') {
                                                    $device_icon = 'fas fa-mobile-alt';
                                                } elseif ($login['device'] == 'Tablet') {
                                                    $device_icon = 'fas fa-tablet-alt';
                                                }
                                                ?>
                                                <i class="<?= $device_icon ?> mr-2 text-blue-500"></i>
                                                <?= htmlspecialchars($login['device']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?= $login['status'] == 'success' ? 'success' : 'failed' ?>">
                                                <?= $login['status'] == 'success' ? 'Berhasil' : 'Gagal' ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php if ($login['status'] == 'failed'): ?>
                                                <span class="text-red-600"><?= htmlspecialchars($login['reason']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="action-buttons">
                                                <a href="?delete_history=<?= $login['id'] ?>" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat login ini?')"
                                                   class="delete-btn hover:bg-red-50 transition-all duration-300" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Table Footer -->
                        <div class="table-footer">
                            <div class="show-entries">
                                <span class="info-text">Show</span>
                                <form action="" method="GET" class="flex items-center gap-2">
                                    <?php if (!empty($search)): ?>
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                    <?php endif; ?>
                                    <select name="show" onchange="this.form.submit()" class="form-select">
                                        <option value="5" <?php echo $show_entries == 5 ? 'selected' : ''; ?>>5</option>
                                        <option value="10" <?php echo $show_entries == 10 ? 'selected' : ''; ?>>10</option>
                                        <option value="25" <?php echo $show_entries == 25 ? 'selected' : ''; ?>>25</option>
                                        <option value="50" <?php echo $show_entries == 50 ? 'selected' : ''; ?>>50</option>
                                        <option value="100" <?php echo $show_entries == 100 ? 'selected' : ''; ?>>100</option>
                                    </select>
                                    <span class="info-text">entries</span>
                                </form>
                            </div>
                            
                            <div class="info-text">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $show_entries, $total_records); ?> of <?php echo $total_records; ?> entries
                            </div>
                            
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $show_entries != 5 ? '&show='.$show_entries : ''; ?>" 
                                   title="First Page" 
                                   class="pagination-btn hover:bg-blue-50 transition-all duration-300">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $show_entries != 5 ? '&show='.$show_entries : ''; ?>" 
                                   title="Previous Page" 
                                   class="pagination-btn hover:bg-blue-50 transition-all duration-300">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $show_entries != 5 ? '&show='.$show_entries : ''; ?>" 
                                   class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?> hover:bg-blue-50 transition-all duration-300">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $show_entries != 5 ? '&show='.$show_entries : ''; ?>" 
                                   title="Next Page" 
                                   class="pagination-btn hover:bg-blue-50 transition-all duration-300">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $show_entries != 5 ? '&show='.$show_entries : ''; ?>" 
                                   title="Last Page" 
                                   class="pagination-btn hover:bg-blue-50 transition-all duration-300">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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

// Fungsi untuk toggle password visibility
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = passwordInput.nextElementSibling;
    const icon = toggleButton.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
/* Custom style untuk halaman profile */
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

/* Search box styles */
.search-container {
    position: relative;
    margin-bottom: 1.5rem;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
}

.search-clear:hover {
    color: #64748b;
    background: #f1f5f9;
}

/* Table styles */
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

/* Status badge styles */
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

.status-badge.success {
    background: #dcfce7;
    color: #166534;
}

.status-badge.failed {
    background: #fee2e2;
    color: #991b1b;
}

/* Action buttons styles */
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

.action-buttons .delete-btn {
    color: #ef4444;
}

.action-buttons .delete-btn:hover {
    color: #dc2626;
}

/* Table footer styles */
.table-footer {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 0.75rem 0.75rem;
}

@media (min-width: 640px) {
    .table-footer {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.show-entries {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.form-select {
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background: white;
    color: #475569;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 4rem;
}

.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Pagination styles */
.pagination {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination-btn {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    color: #475569;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    min-width: 2.5rem;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.pagination-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.pagination-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination-btn.disabled {
    color: #94a3b8;
    cursor: not-allowed;
    pointer-events: none;
}

/* Responsive styles */
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

@media (max-width: 639px) {
    .table-footer {
        padding: 0.75rem;
    }
    
    .show-entries {
        justify-content: center;
    }
    
    .info-text {
        order: -1;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .pagination {
        width: 100%;
    }
    
    .pagination-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Animation styles */
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

/* Mobile Optimization */
@media (max-width: 768px) {
    .page-container {
        padding: 1rem;
    }

    .content-card {
        padding: 1rem;
        margin: 0;
        border-radius: 1rem;
    }

    /* Header Section Mobile */
    .header-section {
        gap: 0.75rem;
    }

    .header-title {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    /* Search Box Mobile */
    .search-container {
        margin-bottom: 1rem;
    }

    .search-input {
        padding: 0.625rem 1rem 0.625rem 2.25rem;
        font-size: 0.875rem;
    }

    .search-icon {
        left: 0.625rem;
        font-size: 0.875rem;
    }

    .search-clear {
        right: 0.625rem;
        font-size: 0.875rem;
    }

    /* Table Mobile */
    .table-container {
        margin: 0.5rem -0.5rem;
        border-radius: 0.75rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-table {
        min-width: 800px;
    }

    .custom-table th {
        padding: 0.75rem;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 0.75rem;
        font-size: 0.75rem;
    }

    /* Status Badge Mobile */
    .status-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
    }

    /* Action Buttons Mobile */
    .action-buttons {
        gap: 0.5rem;
    }

    .action-buttons a {
        padding: 0.375rem;
    }

    /* Table Footer Mobile */
    .table-footer {
        padding: 0.75rem;
        gap: 0.75rem;
        flex-direction: column;
    }

    .show-entries {
        width: 100%;
        justify-content: space-between;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 0.5rem;
    }

    .form-select {
        padding: 0.375rem;
        font-size: 0.75rem;
        min-width: 3.5rem;
    }

    .info-text {
        font-size: 0.75rem;
        text-align: center;
        width: 100%;
        margin-bottom: 0.5rem;
        color: #64748b;
    }

    /* Pagination Mobile */
    .pagination {
        width: 100%;
        justify-content: center;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .pagination-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
        min-width: 2rem;
    }

    /* Empty State Mobile */
    .text-center {
        padding: 2rem 1rem;
    }

    .text-center i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .text-center p {
        font-size: 0.875rem;
    }
}

/* Small Mobile Optimization */
@media (max-width: 480px) {
    .page-container {
        padding: 0.75rem;
    }

    .content-card {
        padding: 0.75rem;
    }

    .header-title {
        font-size: 1.125rem;
    }

    .search-input {
        font-size: 0.8125rem;
    }

    .custom-table th,
    .custom-table td {
        padding: 0.625rem;
        font-size: 0.7rem;
    }

    .status-badge {
        padding: 0.25rem 0.375rem;
        font-size: 0.65rem;
    }

    .pagination-btn {
        padding: 0.25rem 0.375rem;
        font-size: 0.7rem;
        min-width: 1.75rem;
    }
}

/* Fix untuk iOS Safari */
@supports (-webkit-touch-callout: none) {
    .table-container {
        -webkit-overflow-scrolling: touch;
    }
    
    .search-input,
    .form-select {
        font-size: 16px; /* Mencegah zoom otomatis di iOS */
    }
}

/* Animasi yang lebih smooth untuk mobile */
@media (prefers-reduced-motion: no-preference) {
    .table-row {
        animation: fadeInUp 0.3s ease forwards;
    }
    
    .table-row:nth-child(1) { animation-delay: 0.05s; }
    .table-row:nth-child(2) { animation-delay: 0.1s; }
    .table-row:nth-child(3) { animation-delay: 0.15s; }
    .table-row:nth-child(4) { animation-delay: 0.2s; }
    .table-row:nth-child(5) { animation-delay: 0.25s; }
}

/* Perbaikan untuk touch target */
@media (hover: none) {
    .action-buttons a,
    .pagination-btn,
    .search-clear {
        min-height: 2.5rem;
        min-width: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-select {
        min-height: 2.5rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
