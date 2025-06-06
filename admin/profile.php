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

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
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
            <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up">
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

<?php require_once '../includes/footer.php'; ?>
