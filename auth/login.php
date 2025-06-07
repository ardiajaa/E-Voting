<?php
// Set pengaturan session sebelum session_start
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Lax');

// Mulai session
session_start();

// Cek jika user sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/index.php');
    exit();
}

// Cek jika admin sudah login
if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin/index.php');
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $location = getLocationFromIP($ip_address);
    $device = getDeviceInfo($user_agent);
    
    // Cek login admin
    if (strpos($email, '@') !== false) {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['role'] = 'admin';

            // Catat login berhasil
            $stmt = $pdo->prepare("INSERT INTO login_history (user_id, user_type, ip_address, location, device, user_agent, attempted_email, status) VALUES (?, 'admin', ?, ?, ?, ?, ?, 'success')");
            $stmt->execute([$user['id'], $ip_address, $location, $device, $user_agent, $email]);

            header('Location: ./../admin/index.php');
            exit();
        } else {
            // Catat login gagal
            $reason = !$user ? "Email tidak ditemukan" : "Password salah";
            $stmt = $pdo->prepare("INSERT INTO login_history (user_type, ip_address, location, device, user_agent, attempted_email, status, reason) VALUES ('admin', ?, ?, ?, ?, ?, 'failed', ?)");
            $stmt->execute([$ip_address, $location, $device, $user_agent, $email, $reason]);
        }
    } else {
        // Cek login user biasa
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nis = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'user';

            // Catat login berhasil
            $stmt = $pdo->prepare("INSERT INTO login_history (user_id, user_type, ip_address, location, device, user_agent, attempted_email, status) VALUES (?, 'user', ?, ?, ?, ?, ?, 'success')");
            $stmt->execute([$user['id'], $ip_address, $location, $device, $user_agent, $email]);

            header('Location: ./../user/index.php');
            exit();
        } else {
            // Catat login gagal
            $reason = !$user ? "NIS tidak ditemukan" : "Password salah";
            $stmt = $pdo->prepare("INSERT INTO login_history (user_type, ip_address, location, device, user_agent, attempted_email, status, reason) VALUES ('user', ?, ?, ?, ?, ?, 'failed', ?)");
            $stmt->execute([$ip_address, $location, $device, $user_agent, $email, $reason]);
        }
    }
    
    $error = "Email/NIS atau password salah!";
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

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo !empty($settings['logo']) ? '../uploads/' . htmlspecialchars($settings['logo']) : '../assets/images/placeholder.png'; ?>" type="image/png">
    <title>Login - Pemilihan Ketua OSIS <?php echo htmlspecialchars($settings['nama_sekolah'] ?? ''); ?> <?php echo htmlspecialchars($settings['tahun_ajaran'] ?? ''); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-image {
            background-image: url('<?php echo !empty($settings['background']) ? '../uploads/' . htmlspecialchars($settings['background']) : 'https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg'; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .bg-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        .login-container {
            position: relative;
            z-index: 1;
        }
        .input-group {
            position: relative;
        }
        .input-group i.input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
        }
        .input-group input {
            padding-left: 2.5rem;
            padding-right: 2.5rem;
        }
        .password-toggle {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: none;
            border: none;
        }
        .password-toggle:hover {
            color: #3B82F6;
            background-color: rgba(59, 130, 246, 0.1);
        }
        .animate-fade-up {
            animation: fadeUp 0.5s ease-out;
        }
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #3B82F6;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        .back-button:hover {
            background-color: rgba(59, 130, 246, 0.1);
            transform: translateX(-4px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen bg-image flex items-center justify-center p-4">
        <div class="login-container w-full max-w-md">
            <div class="glass-effect p-8 rounded-2xl shadow-2xl animate-fade-up">
                <!-- Logo -->
                <?php if (!empty($settings['logo'])): ?>
                <div class="text-center mb-8">
                    <img src="../uploads/<?php echo htmlspecialchars($settings['logo']); ?>" 
                         alt="Logo OSIS" 
                         class="h-24 w-auto mx-auto mb-4">
                </div>
                <?php endif; ?>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Pemilihan Ketua OSIS<h1>
                    <p class="text-gray-600"><?php echo htmlspecialchars($settings['nama_sekolah'] ?? 'SMA Negeri 1'); ?> - <?php echo htmlspecialchars($settings['tahun_ajaran'] ?? '2023/2024'); ?></p>
                </div>
                
                <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 animate__animated animate__fadeIn" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-2"></i>
                        <p class="font-medium"><?php echo $error; ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" 
                               id="email" 
                               type="text" 
                               name="email" 
                               placeholder="Masukkan NIS"
                               required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" 
                               id="password" 
                               type="password" 
                               name="password" 
                               placeholder="Masukkan Password"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center" 
                                type="submit">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        &copy; <?php echo date('Y'); ?> <a href="https://github.com/ardiajaa" target="_blank" class="hover:text-blue-600 transition-colors duration-200">Achmad Rizky Putra Ardianto</a>.
                    </p>
                    <a href="../" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animasi untuk input fields
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('animate__animated', 'animate__pulse');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('animate__animated', 'animate__pulse');
            });
        });

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 