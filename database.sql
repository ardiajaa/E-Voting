CREATE DATABASE voting_osis;
USE voting_osis;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nis VARCHAR(20) UNIQUE,
    nama_lengkap VARCHAR(100),
    kelas VARCHAR(10),
    absen INT,
    password VARCHAR(255),
    has_voted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    visi TEXT,
    misi TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    candidate_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);

CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_sekolah VARCHAR(100),
    tahun_ajaran VARCHAR(20),
    visi_misi TEXT,
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO admin (email, password) VALUES ('admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: rahasia

-- Insert default user for testing
INSERT INTO users (nis, nama_lengkap, kelas, absen, password) 
VALUES ('123', 'User Test', 'X IPA 1', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: rahasia

-- Insert default candidates
INSERT INTO candidates (nama, visi, misi, foto) VALUES 
('Ahmad Rizki', 
'Mewujudkan sekolah yang unggul dalam prestasi akademik dan non-akademik, berkarakter, dan berwawasan global', 
'1. Meningkatkan prestasi akademik melalui program bimbingan belajar dan kompetisi\n2. Mengembangkan bakat dan minat siswa melalui ekstrakurikuler\n3. Menjalin kerjasama dengan sekolah lain untuk pertukaran pelajar\n4. Meningkatkan fasilitas sekolah untuk mendukung kegiatan belajar mengajar', 
'kandidat1.jpg'),

('Siti Nurul', 
'Menciptakan lingkungan sekolah yang nyaman, aman, dan kondusif untuk belajar serta mengembangkan kreativitas siswa', 
'1. Memperbaiki dan merawat fasilitas sekolah\n2. Mengadakan program anti-bullying dan konseling\n3. Mengembangkan program seni dan budaya\n4. Meningkatkan partisipasi siswa dalam kegiatan sekolah', 
'kandidat2.jpg'),

('Muhammad Fajar', 
'Membangun sekolah yang berwawasan lingkungan dan peduli terhadap masyarakat', 
'1. Mengadakan program go green di sekolah\n2. Melakukan kegiatan sosial ke masyarakat sekitar\n3. Mengembangkan program kesehatan dan kebersihan\n4. Meningkatkan kesadaran siswa akan pentingnya menjaga lingkungan', 
'kandidat3.jpg');