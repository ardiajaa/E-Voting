<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sekolah = $_POST['nama_sekolah'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $visi = $_POST['visi'];
    $misi = $_POST['misi'];
    
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
    
    $stmt = $pdo->prepare("INSERT INTO settings (nama_sekolah, tahun_ajaran, visi, misi, logo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nama_sekolah, $tahun_ajaran, $visi, $misi, $logo]);
    
    $success = "Data berhasil disimpan!";
}

// Ambil data settings terbaru
$stmt = $pdo->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1");
$settings = $stmt->fetch();
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8" data-aos="fade-down">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Edit Halaman Utama</h2>
                <p class="text-gray-600">Atur informasi dan tampilan halaman utama website</p>
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

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                <!-- Logo Section -->
                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        Logo OSIS
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col items-center">
                            <div class="relative w-48 h-48 mb-4 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 hover:border-blue-500 transition-colors duration-300">
                                <img id="logoPreview" 
                                     src="<?php echo !empty($settings['logo']) ? '../uploads/' . htmlspecialchars($settings['logo']) : '../assets/images/placeholder.png'; ?>" 
                                     alt="Logo Preview"
                                     class="w-full h-full object-contain">
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                    <span class="text-white text-sm">Klik untuk mengubah</span>
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
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                                <i class="fas fa-upload mr-2"></i>Pilih Logo
                            </button>
                            <p class="text-sm text-gray-500 mt-2">Format yang didukung: JPG, JPEG, PNG, GIF</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Sekolah Section -->
                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-school text-blue-500 mr-2"></i>
                        Informasi Sekolah
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="nama_sekolah">
                                Nama Sekolah
                            </label>
                            <input type="text" 
                                   name="nama_sekolah" 
                                   id="nama_sekolah" 
                                   required
                                   value="<?php echo htmlspecialchars($settings['nama_sekolah'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="tahun_ajaran">
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
                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bullseye text-blue-500 mr-2"></i>
                        Visi & Misi OSIS
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="visi">
                                Visi
                            </label>
                            <textarea name="visi" 
                                      id="visi" 
                                      rows="6" 
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"><?php echo htmlspecialchars($settings['visi'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="misi">
                                Misi
                            </label>
                            <textarea name="misi" 
                                      id="misi" 
                                      rows="6" 
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"><?php echo htmlspecialchars($settings['misi'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end" data-aos="fade-up" data-aos-delay="300">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition duration-300 transform hover:scale-105 flex items-center">
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
            preview.src = e.target.result;
            preview.classList.add('animate-fade-in');
        }
        
        reader.readAsDataURL(file);
    }
}

// Inisialisasi AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Hover effect untuk input fields */
input:focus, textarea:focus {
    transform: translateY(-1px);
}

/* Transisi untuk semua elemen */
* {
    transition: all 0.3s ease;
}
</style>

<?php require_once '../includes/footer.php'; ?> 