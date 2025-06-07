<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sekolah = $_POST['nama_sekolah'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $visi = $_POST['visi'];
    $misi = $_POST['misi'];
    $default_password = $_POST['default_password'];
    
    // Handle logo upload
    $logo = $settings['logo'] ?? ''; // Keep existing logo by default
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'logo_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                // Delete old logo if exists
                if (!empty($settings['logo']) && file_exists('../uploads/' . $settings['logo'])) {
                    unlink('../uploads/' . $settings['logo']);
                }
                $logo = $new_filename;
            }
        }
    }

    // Handle background upload
    $background = $settings['background'] ?? ''; // Keep existing background by default
    if (isset($_FILES['background']) && $_FILES['background']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['background']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'bg_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['background']['tmp_name'], $upload_path)) {
                // Delete old background if exists
                if (!empty($settings['background']) && file_exists('../uploads/' . $settings['background'])) {
                    unlink('../uploads/' . $settings['background']);
                }
                $background = $new_filename;
            }
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO settings (nama_sekolah, tahun_ajaran, visi, misi, logo, background, default_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama_sekolah, $tahun_ajaran, $visi, $misi, $logo, $background, $default_password]);
    
    $success = "Data berhasil disimpan!";
}

// Ambil data settings terbaru
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8" data-aos="fade-down">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Edit Halaman Utama</h2>
                <p class="text-gray-600 text-sm md:text-base">Atur informasi dan tampilan halaman utama website</p>
            </div>

            <?php if (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" 
                 data-aos="fade-up" 
                 role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-xl mr-2"></i>
                    <p class="font-medium"><?php echo $success; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
                <!-- Background Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 transform transition-all duration-300 hover:shadow-2xl" data-aos="fade-up">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        Background Website
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            <div class="relative w-full h-48 md:h-64 lg:h-80 mb-4 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 hover:border-blue-500 transition-colors duration-300 group">
                                <img id="backgroundPreview" 
                                     src="<?php echo !empty($settings['background']) ? '../uploads/' . htmlspecialchars($settings['background']) : 'https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg'; ?>" 
                                     alt="Background Preview"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="text-white text-sm md:text-base">Klik untuk mengubah</span>
                                </div>
                            </div>
                            <input type="file" 
                                   name="background" 
                                   id="background" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this, 'backgroundPreview')">
                            <button type="button" 
                                    onclick="document.getElementById('background').click()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105 flex items-center">
                                <i class="fas fa-upload mr-2"></i>Pilih Background
                            </button>
                            <p class="text-sm text-gray-500 mt-2 text-center">Format yang didukung: JPG, JPEG, PNG. Ukuran yang disarankan: 1920x1080px</p>
                        </div>
                    </div>
                </div>

                <!-- Logo Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 transform transition-all duration-300 hover:shadow-2xl" data-aos="fade-up">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        Logo OSIS
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            <div class="relative w-32 h-32 md:w-48 md:h-48 mb-4 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 hover:border-blue-500 transition-colors duration-300 group">
                                <img id="logoPreview" 
                                     src="<?php echo !empty($settings['logo']) ? '../uploads/' . htmlspecialchars($settings['logo']) : '../assets/images/placeholder.png'; ?>" 
                                     alt="Logo Preview"
                                     class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="text-white text-sm md:text-base">Klik untuk mengubah</span>
                                </div>
                            </div>
                            <input type="file" 
                                   name="logo" 
                                   id="logo" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this, 'logoPreview')">
                            <button type="button" 
                                    onclick="document.getElementById('logo').click()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105 flex items-center">
                                <i class="fas fa-upload mr-2"></i>Pilih Logo
                            </button>
                            <p class="text-sm text-gray-500 mt-2 text-center">Format yang didukung: JPG, JPEG, PNG, GIF</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Sekolah Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 transform transition-all duration-300 hover:shadow-2xl" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-school text-blue-500 mr-2"></i>
                        Informasi Sekolah
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-medium" for="nama_sekolah">
                                Nama Sekolah
                            </label>
                            <input type="text" 
                                   name="nama_sekolah" 
                                   id="nama_sekolah" 
                                   required
                                   value="<?php echo htmlspecialchars($settings['nama_sekolah'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-medium" for="tahun_ajaran">
                                Tahun Ajaran
                            </label>
                            <input type="text" 
                                   name="tahun_ajaran" 
                                   id="tahun_ajaran" 
                                   required
                                   value="<?php echo htmlspecialchars($settings['tahun_ajaran'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                        </div>
                    </div>
                </div>

                <!-- Visi Misi Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 transform transition-all duration-300 hover:shadow-2xl" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bullseye text-blue-500 mr-2"></i>
                        Visi & Misi OSIS
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-medium" for="visi">
                                Visi
                            </label>
                            <textarea name="visi" 
                                      id="visi" 
                                      rows="6" 
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 resize-none"><?php echo htmlspecialchars($settings['visi'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-medium" for="misi">
                                Misi
                            </label>
                            <textarea name="misi" 
                                      id="misi" 
                                      rows="6" 
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 resize-none"><?php echo htmlspecialchars($settings['misi'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan section baru setelah Visi Misi Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 transform transition-all duration-300 hover:shadow-2xl" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-key text-blue-500 mr-2"></i>
                        Pengaturan Password Default
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-gray-700 text-sm font-medium" for="default_password">
                                Password Default User Baru
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="default_password" 
                                       id="default_password" 
                                       required
                                       value="<?php echo htmlspecialchars($settings['default_password'] ?? 'rahasia'); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                                <button type="button" 
                                        onclick="generatePassword()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-500 transition-colors">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Password ini akan digunakan sebagai password default untuk user baru yang ditambahkan melalui import Excel atau penambahan manual.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end" data-aos="fade-up" data-aos-delay="300">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 md:px-8 rounded-xl transition duration-300 transform hover:scale-105 flex items-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.classList.add('loading');
            preview.src = e.target.result;
            
            preview.onload = function() {
                preview.classList.remove('loading');
                preview.classList.add('image-preview');
            }
        }
        
        reader.readAsDataURL(file);
    }
}

function generatePassword() {
    const length = 8;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let password = "";
    
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * charset.length);
        password += charset[randomIndex];
    }
    
    document.getElementById('default_password').value = password;
}

// Inisialisasi AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50,
    easing: 'ease-out-cubic'
});

// Tambahkan validasi ukuran file
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 5MB');
                this.value = '';
                const preview = document.getElementById(this.id + 'Preview');
                if (preview) {
                    preview.src = preview.dataset.default || '';
                }
            }
        }
    });
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Loading state untuk form submit
document.querySelector('form').addEventListener('submit', function() {
    this.classList.add('loading-state');
});

// Tooltip initialization
document.querySelectorAll('[data-tooltip]').forEach(element => {
    element.classList.add('tooltip');
});
</script>

<style>
/* Base styles */
.min-h-screen {
    background: linear-gradient(135deg, #f6f8fc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Card styles */
.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Image upload styles */
.image-upload-container {
    position: relative;
    border: 2px dashed #e2e8f0;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-upload-container:hover {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
}

.image-upload-container img {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.image-upload-container:hover img {
    transform: scale(1.05);
}

.image-upload-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.image-upload-container:hover .image-upload-overlay {
    opacity: 1;
}

/* Form styles */
.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    background: #fff;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
    transform: translateY(-1px);
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #4b5563;
}

/* Button styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
}

.btn-upload {
    background: #3b82f6;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-upload:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
}

/* Alert styles */
.alert {
    padding: 1rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    animation: slideIn 0.5s ease-out;
}

.alert-success {
    background: #dcfce7;
    border-left: 4px solid #22c55e;
    color: #166534;
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive styles */
@media (max-width: 1024px) {
    .container {
        padding: 1.5rem 1rem;
    }
    
    .card {
        border-radius: 1rem;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    .btn {
        width: 100%;
    }
    
    .image-upload-container {
        height: 200px;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0.75rem;
    }
    
    .card {
        padding: 1rem;
    }
    
    .form-input {
        padding: 0.625rem 0.875rem;
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Loading animation */
.loading {
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: inherit;
}

.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 24px;
    height: 24px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 1;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Image preview animation */
.image-preview {
    animation: scaleIn 0.5s ease-out;
}

/* Form validation styles */
.form-input.error {
    border-color: #ef4444;
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Success message animation */
.success-message {
    animation: slideIn 0.5s ease-out;
}

/* Hover effects */
.hover-lift {
    transition: transform 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

/* Focus styles */
.focus-ring {
    transition: box-shadow 0.3s ease;
}

.focus-ring:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Disabled state */
.disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Loading state */
.loading-state {
    position: relative;
    pointer-events: none;
}

.loading-state::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: inherit;
}

/* Tooltip styles */
.tooltip {
    position: relative;
}

.tooltip::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.5rem;
    background: #1f2937;
    color: white;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.tooltip:hover::before {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(-0.5rem);
}
</style>

<?php require_once '../includes/footer.php'; ?> 