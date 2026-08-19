<?php
require_once dirname(__DIR__) . '/config/database.php';

$instructors = [
    [
        'id' => 1,
        'name' => 'Bunga Lestari, S.Pd., C.MUA',
        'category' => 'Kecantikan',
        'specialization' => 'Make Up Artist & Bridal Specialist',
        'bio' => 'Praktisi MUA profesional lebih dari 10 tahun pengalaman merias wedding tradisional, modern, editorial, dan panggung hiburan.',
        'certifications' => 'BNSP Level 4 Tata Rias, Certified International MUA, Asesor Kompetensi LSP',
        'image' => 'uploads/instructors/bunga.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Jessica Angelina, C.E.',
        'category' => 'Kecantikan',
        'specialization' => 'Senior Esthetician & Skin Care Specialist',
        'bio' => 'Pakar estetika kulit dan perawatan wajah medis ringan dengan sertifikasi internasional CIDESCO.',
        'certifications' => 'CIDESCO International Diploma, BNSP Perawatan Kulit',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 3,
        'name' => 'Maya Kartika',
        'category' => 'Kecantikan',
        'specialization' => 'Professional Hair Stylist & Colorist',
        'bio' => 'Hair stylist salon ternama di Bandung, spesialis teknik rebonding, smoothing, coloring balayage, dan blow styling modern.',
        'certifications' => 'Certified Pivot Point International, Asesor Tata Kecantikan Rambut',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 4,
        'name' => 'Rani Oktavia',
        'category' => 'Kecantikan',
        'specialization' => 'Nail Art & Eyelash Extension Artist',
        'bio' => 'Trainer kecantikan kuku dan bulu mata dengan teknik semi-permanen higienis dan standar salon kecantikan Jepang.',
        'certifications' => 'Certified Russian Volume Lash, Professional Nail Art Specialist',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 5,
        'name' => 'H. Supardi, A.Md.Fis',
        'category' => 'Kesehatan',
        'specialization' => 'Fisioterapi & Pijat Pengobatan Tradisional',
        'bio' => 'Praktisi terapi pijat dan rehabilitasi fisik berpengalaman lebih dari 18 tahun, menguasai titik saraf motorik dan relaksasi.',
        'certifications' => 'Surat Izin Praktik Fisioterapi (SIPF), BNSP Pijat Tradisional Indonesia',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 6,
        'name' => 'dr. Hendra Gunawan, Sp.Ak',
        'category' => 'Kesehatan',
        'specialization' => 'Akupunktur Medis & Titik Refleksi Tubuh',
        'bio' => 'Dokter spesialis akupunktur yang memadukan teknik refleksi timur kuno dengan sains kedokteran modern untuk kebugaran tubuh.',
        'certifications' => 'Spesialis Akupunktur Medik IDI, Asesor Kesehatan Tradisional Kemenkes',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 7,
        'name' => 'Siti Nurhaliza, S.Tr.Kes',
        'category' => 'Kesehatan',
        'specialization' => 'Spa Therapy & Aromaterapi Herbal',
        'bio' => 'Konsultan spa hotel berbintang dan instruktur teknik massage lulur, body scrub, serta peracikan aromaterapi alami.',
        'certifications' => 'BNSP Spa Therapist Level 3, Certified Wellness Herbalist',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 8,
        'name' => 'Bambang Prakoso, S.Or',
        'category' => 'Kesehatan',
        'specialization' => 'Sports Massage & Cedera Otot Atlit',
        'bio' => 'Mantan fisioterapis tim olahraga profesional, ahli dalam penanganan ketegangan otot, trigger point, dan pemulihan performa tubuh.',
        'certifications' => 'Certified Sports Massage Specialist, Lisensi Pelatih Kebugaran',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 9,
        'name' => 'Dr. Ir. H. Ahmad Jaelani, M.Pd',
        'category' => 'Metodologi',
        'specialization' => 'Master Trainer BNSP & Microteaching',
        'bio' => 'Dosen dan instruktur senior bidang pedagogik vokasi, spesialis penyusunan silabus kompetensi kerja dan teknik pengajaran interaktif.',
        'certifications' => 'Master Asesor BNSP, Certified Master Trainer of Trainer (ToT)',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 10,
        'name' => 'Dra. Ratna Kusuma, M.M.',
        'category' => 'Metodologi',
        'specialization' => 'Pengembangan Kurikulum Vokasi Berbasis SKKNI',
        'bio' => 'Konsultan kurikulum pendidikan non-formal dan pelatihan kerja dengan pengalaman membina puluhan LPK terakreditasi nasional.',
        'certifications' => 'Asesor SKKNI Kemnaker, Certified Instructional Designer',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 11,
        'name' => 'Fajar Nugraha, S.Psi., C.HRM',
        'category' => 'Metodologi',
        'specialization' => 'Komunikasi Publik & Manajemen Kelas Pelatihan',
        'bio' => 'Praktisi psikologi industri yang berpengalaman melatih ribuan fasilitator dalam public speaking, Ice Breaking, dan dinamika kelompok.',
        'certifications' => 'Certified Facilitator BNSP, Certified NLP Practitioner',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 12,
        'name' => 'Dewi Anggraini, S.I.Kom',
        'category' => 'Digital',
        'specialization' => 'Digital Strategist & Meta Ads Performance',
        'bio' => 'Spesialis periklanan berbayar Facebook & Instagram Ads dengan rekam jejak mengelola budget iklan digital brand nasional.',
        'certifications' => 'Meta Certified Media Buying Professional, Google Ads Search Certification',
        'image' => 'uploads/instructors/6a7ec15a2ba99.jpeg'
    ],
    [
        'id' => 13,
        'name' => 'Rizky Ramadhan',
        'category' => 'Digital',
        'specialization' => 'Senior Video Creator & CapCut Specialist',
        'bio' => 'Content creator aktif dengan 500k+ followers, spesialis short-form storytelling video, color grading smartphone, dan audio mixing.',
        'certifications' => 'Certified Digital Content Producer, Adobe Premiere Certified Professional',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 14,
        'name' => 'Dimas Pratama, S.Kom',
        'category' => 'Digital',
        'specialization' => 'SEO Specialist & Google Analytics Consultant',
        'bio' => 'Konsultan optimasi mesin pencari (SEO On-Page & Off-Page) dan data analytics untuk mendongkrak penjualan organik bisnis online.',
        'certifications' => 'Google Analytics Individual Qualification (GA4), Hubspot Inbound Marketing',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 15,
        'name' => 'Amanda Putri, B.A.',
        'category' => 'Digital',
        'specialization' => 'Social Media Organic & TikTok Live Strategy',
        'bio' => 'Social media manager dan strategist live commerce TikTok Shop yang sukses meningkatkan traffic dan engagement akun UMKM.',
        'certifications' => 'Certified Social Media Strategist, TikTok E-commerce Specialist',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 16,
        'name' => 'Chef Agus Wijaya',
        'category' => 'Kuliner',
        'specialization' => 'Executive Pastry Chef & Artisan Bakery',
        'bio' => 'Head Baker bakery ternama, menguasai teknik pembuatan sourdough, croissant lamination, pastry klasik Perancis, dan aneka roti komersial.',
        'certifications' => 'BNSP Baker Profesional Level 4, Certified French Pastry Chef',
        'image' => 'uploads/instructors/6a7ecd58b66ce.jpeg'
    ],
    [
        'id' => 17,
        'name' => 'Chef Nadia Salsabila',
        'category' => 'Kuliner',
        'specialization' => 'Modern Cake Decoration & Fondant Art',
        'bio' => 'Spesialis wedding cake 3 dimensi, butter cream sculpture, serta dekorasi kue ulang tahun tematik tingkat mahir.',
        'certifications' => 'Certified Cake Decorator Wilton, Juri Kompetisi Kuliner Kreatif',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 18,
        'name' => 'Chef Rudi Hartono, S.Tr.Par',
        'category' => 'Kuliner',
        'specialization' => 'Manajemen Usaha Catering & Sanitasi Dapur',
        'bio' => 'Konsultan bisnis kuliner dan pemilik jasa catering event besar, ahli dalam standardisasi resep massal dan keamanan pangan.',
        'certifications' => 'Sertifikasi HACCP & Higiene Pangan, Asesor BNSP Tata Boga',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 19,
        'name' => 'Bayu Samudra, S.Par',
        'category' => 'Pariwisata',
        'specialization' => 'Pemandu Wisata & Tour Leader Internasional',
        'bio' => 'Pemandu wisata berpengalaman memimpin grup tur mancanegara dan domestik, menguasai teknik guiding, storytelling sejarah, dan etika wisata.',
        'certifications' => 'Lisensi HPI (Himpunan Pramuwisata Indonesia), Certified Eco-Tourism Guide',
        'image' => 'assets/img/logo.png'
    ],
    [
        'id' => 20,
        'name' => 'Citra Kirana, M.Par',
        'category' => 'Pariwisata',
        'specialization' => 'Hospitality & Manajemen Layanan Homestay',
        'bio' => 'Praktisi industri perhotelan bintang 5 dan trainer pelayanan prima (Service Excellence) serta pengelolaan desa wisata berkelanjutan.',
        'certifications' => 'Certified Hospitality Educator (CHE), BNSP Front Office & Housekeeping',
        'image' => 'assets/img/logo.png'
    ]
];

try {
    $pdo->exec("TRUNCATE TABLE `instructors`;");
    $stmt = $pdo->prepare("INSERT INTO `instructors` (`id`, `name`, `category`, `specialization`, `bio`, `certifications`, `image`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($instructors as $ins) {
        $stmt->execute([
            $ins['id'],
            $ins['name'],
            $ins['category'],
            $ins['specialization'],
            $ins['bio'],
            $ins['certifications'],
            $ins['image']
        ]);
    }

    echo "SUCCESS: 20 instructors seeded successfully into database!\n";
    $list = $pdo->query("SELECT id, name, category, specialization FROM instructors ORDER BY id ASC")->fetchAll();
    foreach ($list as $row) {
        echo sprintf("[%02d] %-30s | %-12s | %s\n", $row['id'], $row['name'], $row['category'], $row['specialization']);
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
