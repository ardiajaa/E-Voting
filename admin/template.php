<?php
require_once '../includes/admin-header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Download Template Import User</h2>
        
        <div class="mb-6">
            <p class="text-gray-600 mb-4">
                Download template Excel untuk import data user. Template ini berisi kolom-kolom yang diperlukan:
            </p>
            <ul class="list-disc list-inside text-gray-600 mb-4">
                <li>NIS - Nomor Induk Siswa</li>
                <li>Nama Lengkap - Nama lengkap siswa</li>
                <li>Kelas - Kelas siswa (contoh: X IPA 1)</li>
                <li>Absen - Nomor absen siswa</li>
            </ul>
        </div>
        
        <a href="download-template.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Template Excel
        </a>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?> 