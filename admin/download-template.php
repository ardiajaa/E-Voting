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

<style>
.page-container {
    min-height: calc(100vh - 4rem);
    background-color: #f3f4f6;
    padding: 1rem;
}

@media (min-width: 640px) {
    .page-container {
        padding: 2rem;
    }
}

.content-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    padding: 1.5rem;
    margin: 0 auto;
    max-width: 100%;
}

@media (min-width: 640px) {
    .content-card {
        padding: 2rem;
    }
}

@media (min-width: 768px) {
    .content-card {
        padding: 2.5rem;
    }
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
    position: relative;
    display: inline-block;
    text-align: center;
    width: 100%;
}

@media (min-width: 640px) {
    .page-title {
        font-size: 1.75rem;
        text-align: left;
        width: auto;
    }
}

@media (min-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 3rem;
    height: 0.25rem;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    border-radius: 0.25rem;
}

@media (min-width: 640px) {
    .page-title::after {
        left: 0;
        transform: none;
    }
}

.description {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    text-align: center;
}

@media (min-width: 640px) {
    .description {
        font-size: 1rem;
        line-height: 1.75;
        margin-bottom: 2rem;
        text-align: left;
    }
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    color: #4b5563;
    font-size: 0.875rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

@media (min-width: 640px) {
    .feature-item {
        font-size: 1rem;
        padding: 1rem 0;
    }
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item:hover {
    transform: translateX(0.5rem);
    color: #1e293b;
}

.feature-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.75rem;
    color: #3b82f6;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

@media (min-width: 640px) {
    .feature-icon {
        width: 2rem;
        height: 2rem;
        margin-right: 1rem;
    }
}

.feature-item:hover .feature-icon {
    transform: scale(1.1);
}

.download-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
}

@media (min-width: 640px) {
    .download-button {
        width: auto;
        padding: 1rem 2rem;
        font-size: 1rem;
        border-radius: 1rem;
    }
}

.download-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
}

.download-icon {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
    transition: transform 0.3s ease;
}

@media (min-width: 640px) {
    .download-icon {
        width: 1.5rem;
        height: 1.5rem;
        margin-right: 0.75rem;
    }
}

.download-button:hover .download-icon {
    transform: translateY(2px);
}

/* Container responsive */
.container {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1rem;
    padding-right: 1rem;
}

@media (min-width: 640px) {
    .container {
        max-width: 640px;
    }
}

@media (min-width: 768px) {
    .container {
        max-width: 768px;
    }
}

@media (min-width: 1024px) {
    .container {
        max-width: 1024px;
    }
}

@media (min-width: 1280px) {
    .container {
        max-width: 1280px;
    }
}

/* Touch device optimizations */
@media (hover: none) {
    .content-card:hover {
        transform: none;
    }
    
    .feature-item:hover {
        transform: none;
    }
    
    .download-button:hover {
        transform: none;
    }
    
    .download-button:active {
        transform: translateY(1px);
    }
}
</style>
        
<div class="page-container">
    <div class="container">
        <div class="content-card" data-aos="fade-up">
            <h2 class="page-title">Download Template Import User</h2>

            <p class="description" data-aos="fade-up" data-aos-delay="100">
                Download template Excel untuk import data user. Template ini berisi kolom-kolom yang diperlukan untuk
                memudahkan proses import data user secara massal.
            </p>

            <ul class="feature-list">
                <li class="feature-item" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-id-card feature-icon"></i>
                    <span>NIS - Nomor Induk Siswa</span>
                </li>
                <li class="feature-item" data-aos="fade-up" data-aos-delay="300">
                    <i class="fas fa-user feature-icon"></i>
                    <span>Nama Lengkap - Nama lengkap siswa</span>
                </li>
                <li class="feature-item" data-aos="fade-up" data-aos-delay="400">
                    <i class="fas fa-chalkboard feature-icon"></i>
                    <span>Kelas - Kelas siswa (contoh: X TKJ 1)</span>
                </li>
                <li class="feature-item" data-aos="fade-up" data-aos-delay="500">
                    <i class="fas fa-sort-numeric-up feature-icon"></i>
                    <span>Absen - Nomor absen siswa</span>
                </li>
            </ul>

            <a href="?download=1" class="download-button" data-aos="fade-up" data-aos-delay="600">
                <i class="fas fa-download download-icon"></i>
                <span>Download Template Excel</span>
            </a>
        </div>
    </div>
</div>

<script>
// Inisialisasi AOS dengan konfigurasi yang lebih baik untuk mobile
AOS.init({
    duration: 800,
    once: true,
    offset: 50,
    disable: window.innerWidth < 640 ? true : false,
    startEvent: 'DOMContentLoaded'
});

// Optimasi untuk touch devices
document.addEventListener('DOMContentLoaded', function () {
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    if (isTouchDevice) {
        document.querySelectorAll('.feature-item, .download-button').forEach(element => {
            element.style.cursor = 'pointer';
        });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>