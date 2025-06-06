<?php
require_once '../includes/admin-header.php';
require_once '../config/database.php';

// Proses tambah/edit kandidat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
        $absen = $_POST['absen'];
        $visi = $_POST['visi'];
        $misi = $_POST['misi'];
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = $_FILES['foto']['name'];
            move_uploaded_file($_FILES['foto']['tmp_name'], '../assets/images/candidates/' . $foto);
            
            $stmt = $pdo->prepare("UPDATE candidates SET nama = ?, kelas = ?, absen = ?, visi = ?, misi = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nama, $kelas, $absen, $visi, $misi, $foto, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE candidates SET nama = ?, kelas = ?, absen = ?, visi = ?, misi = ? WHERE id = ?");
            $stmt->execute([$nama, $kelas, $absen, $visi, $misi, $id]);
        }
    } else {
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
        $absen = $_POST['absen'];
        $visi = $_POST['visi'];
        $misi = $_POST['misi'];
        $foto = $_FILES['foto']['name'];
        
        move_uploaded_file($_FILES['foto']['tmp_name'], '../assets/images/candidates/' . $foto);
        
        $stmt = $pdo->prepare("INSERT INTO candidates (nama, kelas, absen, visi, misi, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $kelas, $absen, $visi, $misi, $foto]);
    }
    
    header('Location: edit-candidates.php');
    exit();
}

// Ambil data kandidat
$stmt = $pdo->query("SELECT * FROM candidates ORDER BY id DESC");
$candidates = $stmt->fetchAll();
?>

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    .candidate-card {
        transition: all 0.3s ease;
    }
    .candidate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .candidate-image {
        aspect-ratio: 1/1;
        object-fit: cover;
    }
    .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-animate {
        animation: slideIn 0.3s ease-out;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-xl p-8" data-aos="fade-up">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-bold text-gray-800">Edit Kandidat</h2>
            
            <button onclick="openAddModal()"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-xl transition duration-300 transform hover:scale-105 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Kandidat
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($candidates as $index => $candidate): ?>
            <div class="candidate-card bg-gray-50 rounded-2xl p-6" 
                 data-aos="fade-up" 
                 data-aos-delay="<?php echo $index * 100; ?>">
                <div class="relative mb-6">
                    <img src="../assets/images/candidates/<?php echo htmlspecialchars($candidate['foto']); ?>" 
                         alt="<?php echo htmlspecialchars($candidate['nama']); ?>"
                         class="w-full candidate-image rounded-xl shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent rounded-xl"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                        <h3 class="text-2xl font-bold"><?php echo htmlspecialchars($candidate['nama']); ?></h3>
                        <p class="text-gray-200">Kelas <?php echo htmlspecialchars($candidate['kelas']); ?> - Absen <?php echo htmlspecialchars($candidate['absen']); ?></p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-bullseye text-blue-500"></i>Visi:
                        </h4>
                        <p class="text-gray-600 bg-white p-3 rounded-lg shadow-sm">
                            <?php echo nl2br(htmlspecialchars($candidate['visi'])); ?>
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-tasks text-blue-500"></i>Misi:
                        </h4>
                        <p class="text-gray-600 bg-white p-3 rounded-lg shadow-sm">
                            <?php echo nl2br(htmlspecialchars($candidate['misi'])); ?>
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="editCandidate(<?php echo htmlspecialchars(json_encode($candidate)); ?>)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <a href="delete-candidate.php?id=<?php echo $candidate['id']; ?>"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus kandidat ini?')"
                       class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Hapus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Kandidat -->
<div id="addCandidateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-2xl rounded-2xl bg-white modal-animate">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Tambah Kandidat Baru</h3>
            <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">
                    Nama
                </label>
                <input type="text" name="nama" id="nama" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="kelas">
                        Kelas
                    </label>
                    <input type="text" name="kelas" id="kelas" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="absen">
                        Absen
                    </label>
                    <input type="number" name="absen" id="absen" required min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="visi">
                    Visi
                </label>
                <textarea name="visi" id="visi" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="misi">
                    Misi
                </label>
                <textarea name="misi" id="misi" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="foto">
                    Foto
                </label>
                <input type="file" name="foto" id="foto" accept="image/*" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format yang didukung: JPG, PNG. Ukuran maksimal: 2MB</p>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeAddModal()"
                        class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-300">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300 transform hover:scale-105">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kandidat -->
<div id="editCandidateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-2xl rounded-2xl bg-white modal-animate">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit Kandidat</h3>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="edit" value="1">
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nama">
                    Nama
                </label>
                <input type="text" name="nama" id="edit_nama" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_kelas">
                        Kelas
                    </label>
                    <input type="text" name="kelas" id="edit_kelas" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_absen">
                        Absen
                    </label>
                    <input type="number" name="absen" id="edit_absen" required min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_visi">
                    Visi
                </label>
                <textarea name="visi" id="edit_visi" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_misi">
                    Misi
                </label>
                <textarea name="misi" id="edit_misi" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_foto">
                    Foto (Opsional)
                </label>
                <input type="file" name="foto" id="edit_foto" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format yang didukung: JPG, PNG. Ukuran maksimal: 2MB</p>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeEditModal()"
                        class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-300">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300 transform hover:scale-105">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Inisialisasi AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

function openAddModal() {
    document.getElementById('addCandidateModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addCandidateModal').classList.add('hidden');
}

function editCandidate(candidate) {
    document.getElementById('edit_id').value = candidate.id;
    document.getElementById('edit_nama').value = candidate.nama;
    document.getElementById('edit_kelas').value = candidate.kelas;
    document.getElementById('edit_absen').value = candidate.absen;
    document.getElementById('edit_visi').value = candidate.visi;
    document.getElementById('edit_misi').value = candidate.misi;
    document.getElementById('editCandidateModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCandidateModal').classList.add('hidden');
}

// Tutup modal saat klik di luar modal
window.onclick = function(event) {
    if (event.target == document.getElementById('addCandidateModal')) {
        closeAddModal();
    }
    if (event.target == document.getElementById('editCandidateModal')) {
        closeEditModal();
    }
}
</script>

<?php require_once '../includes/footer.php'; ?> 