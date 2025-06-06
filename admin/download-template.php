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

<!-- Tambahkan AOS CSS dan JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
.page-container {
    min-height: calc(100vh - 4rem);
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                url('https://smkn1cermegresik.sch.id/wp-content/uploads/2020/11/Lapangan.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 2rem 0;
}

.content-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
    position: relative;
    display: inline-block;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 0;
    width: 3rem;
    height: 0.25rem;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    border-radius: 0.25rem;
}

.description {
    color: #4b5563;
    font-size: 1.125rem;
    line-height: 1.75;
    margin-bottom: 2rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    color: #4b5563;
    font-size: 1.125rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item:hover {
    transform: translateX(0.5rem);
    color: #1e293b;
}

.feature-icon {
    width: 2rem;
    height: 2rem;
    margin-right: 1rem;
    color: #3b82f6;
    transition: all 0.3s ease;
}

.feature-item:hover .feature-icon {
    transform: scale(1.1);
}

.download-button {
    display: inline-flex;
    align-items: center;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    font-weight: 600;
    font-size: 1.125rem;
    border-radius: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    position: relative;
    overflow: hidden;
}

.download-button::before {
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

.download-button:hover::before {
    width: 300px;
    height: 300px;
}

.download-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
}

.download-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.75rem;
    transition: transform 0.3s ease;
}

.download-button:hover .download-icon {
    transform: translateY(2px);
}

/* Responsive styles */
@media (max-width: 768px) {
    .page-container {
        padding: 1rem 0;
    }
    
    .content-card {
        margin: 0 1rem;
        border-radius: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .description {
        font-size: 1rem;
    }
    
    .feature-item {
        font-size: 1rem;
        padding: 0.75rem 0;
    }
    
    .download-button {
        width: 100%;
        justify-content: center;
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .feature-icon {
        width: 1.5rem;
        height: 1.5rem;
    }
}
</style>

<div class="page-container">
    <div class="container mx-auto px-4">
        <div class="content-card p-8" data-aos="fade-up">
            <h2 class="page-title">Download Template Import User</h2>
            
            <p class="description" data-aos="fade-up" data-aos-delay="100">
                Download template Excel untuk import data user. Template ini berisi kolom-kolom yang diperlukan untuk memudahkan proses import data user secara massal.
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
// Inisialisasi AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

// Animasi untuk elemen
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('[data-aos]');
    elements.forEach((element, index) => {
        element.style.animationDelay = `${(index + 1) * 0.1}s`;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>