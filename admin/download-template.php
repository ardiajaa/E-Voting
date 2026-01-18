<?php
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['download'])) {
    // Pastikan tidak ada output sebelum header
    ob_start();
    
    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set judul sheet
    $sheet->setTitle('Template Import User');
    
    // Set header
    $sheet->setCellValue('A1', 'NIS');
    $sheet->setCellValue('B1', 'Nama Lengkap');
    $sheet->setCellValue('C1', 'Kelas');
    $sheet->setCellValue('D1', 'Absen');
    
    // Set lebar kolom
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(10);
    
    // Set contoh data
    $sheet->setCellValue('A2', '123');
    $sheet->setCellValue('B2', 'Ardi');
    $sheet->setCellValue('C2', 'X TKJ 1');
    $sheet->setCellValue('D2', '1');
    
    // Buat file Excel
    $writer = new Xlsx($spreadsheet);
    
    // Bersihkan output buffer
    ob_end_clean();
    
    // Set header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="template_import_user.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output file
    $writer->save('php://output');
    exit;
}

// Jika bukan request download, tampilkan halaman admin
require_once '../includes/admin-header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header dengan gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                    <i class="fas fa-file-excel text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Template Import User</h1>
                <p class="text-blue-100 text-sm sm:text-base">Download template Excel untuk import data user secara massal</p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Template ini sudah berisi format kolom yang diperlukan. Isi data sesuai format yang ada.
                    </p>
                </div>

                <!-- List Kolom -->
                <div class="space-y-3 mb-8">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-id-card text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">NIS</p>
                            <p class="text-sm text-gray-500">Nomor Induk Siswa</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Nama Lengkap</p>
                            <p class="text-sm text-gray-500">Nama lengkap siswa</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chalkboard text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Kelas</p>
                            <p class="text-sm text-gray-500">Contoh: X TKJ 1</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sort-numeric-up text-orange-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Absen</p>
                            <p class="text-sm text-gray-500">Nomor absen siswa</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="?download=1" 
                       class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-center">
                        <i class="fas fa-download mr-2"></i>
                        Download Template Excel
                    </a>
                    
                    <a href="users.php" 
                       class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Manajemen User
                    </a>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="mt-6 bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Tips
            </h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Pastikan format data sesuai dengan contoh yang ada di template</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Jangan menghapus atau mengubah nama kolom header</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Isi semua kolom yang wajib diisi sebelum melakukan import</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>