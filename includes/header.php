<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit();
}

require_once '../config/database.php';
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan Ketua OSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #3B82F6;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .nav-link.active::after {
            width: 100%;
        }
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
        .mobile-menu.hidden {
            transform: translateY(-10px);
            opacity: 0;
            pointer-events: none;
        }
        .mobile-menu:not(.hidden) {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50 animate-slide-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/user/" class="flex items-center group">
                            <div class="relative">
                                <img src="/uploads/<?php echo htmlspecialchars($settings['logo'] ?? 'logo-osis.png'); ?>" 
                                     alt="Logo OSIS" 
                                     class="h-auto w-auto max-h-10 mr-2 rounded-lg transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-lg transition-opacity duration-300"></div>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 text-lg group-hover:text-blue-600 transition-colors duration-300">
                                    Pemilihan Ketua OSIS
                                </span>
                                <?php if (!empty($settings['nama_sekolah']) && !empty($settings['tahun_ajaran'])): ?>
                                    <span class="text-sm text-gray-500"><?php echo htmlspecialchars($settings['nama_sekolah']); ?> - <?php echo htmlspecialchars($settings['tahun_ajaran']); ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden sm:flex sm:items-center sm:space-x-8">
                    <a href="/user/index.php" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active text-blue-500' : 'text-gray-500 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 text-sm font-medium">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="/user/candidates.php" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'candidates.php' ? 'active text-blue-500' : 'text-gray-500 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 text-sm font-medium">
                        <i class="fas fa-users mr-2"></i>Kandidat
                    </a>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative group">
                        <button class="nav-link text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium focus:outline-none">
                            <i class="fas fa-user-circle text-xl mr-2 text-gray-500"></i>
                            <span>Profil</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 origin-top-right transform opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-200">
                            <div class="py-1">
                                <a href="/user/profile.php" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profil Saya
                                </a>
                                <a href="/auth/logout.php" 
                                   class="block px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" 
                            onclick="toggleMobileMenu()"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-300">
                        <span class="sr-only">Buka menu</span>
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="mobile-menu hidden sm:hidden bg-white border-t border-gray-200">
            <div class="pt-2 pb-3 space-y-1">
                <a href="/user/index.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                <a href="/user/candidates.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'candidates.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-users mr-2"></i>Kandidat
                </a>
                <a href="/user/profile.php" 
                   class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-300">
                    <i class="fas fa-user-circle mr-2"></i>Profile
                </a>
                <a href="/auth/logout.php" 
                   class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-red-500 hover:bg-red-50 hover:border-red-300 hover:text-red-700 transition-colors duration-300">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Spacer untuk fixed navbar -->
    <div class="h-16"></div>

    <script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
        
        // Tambahkan animasi saat menu dibuka/ditutup
        if (!mobileMenu.classList.contains('hidden')) {
            mobileMenu.style.display = 'block';
            setTimeout(() => {
                mobileMenu.style.transform = 'translateY(0)';
                mobileMenu.style.opacity = '1';
            }, 10);
        } else {
            mobileMenu.style.transform = 'translateY(-10px)';
            mobileMenu.style.opacity = '0';
            setTimeout(() => {
                mobileMenu.style.display = 'none';
            }, 300);
        }
    }

    // Tutup menu mobile saat mengklik di luar menu
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuButton = document.querySelector('button[onclick]');
        
        if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
            if (!mobileMenu.classList.contains('hidden')) {
                toggleMobileMenu();
            }
        }
    });

    // Tambahkan animasi saat scroll
    let lastScroll = 0;
    const nav = document.querySelector('nav');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            nav.style.transform = 'translateY(0)';
            return;
        }
        
        if (currentScroll > lastScroll && !nav.classList.contains('scroll-down')) {
            // Scroll ke bawah
            nav.style.transform = 'translateY(-100%)';
        } else if (currentScroll < lastScroll && nav.classList.contains('scroll-down')) {
            // Scroll ke atas
            nav.style.transform = 'translateY(0)';
        }
        
        lastScroll = currentScroll;
    });
    </script>
</body>
</html>