# DESIGN.md — Mitra Cipta Mandiri (MCM)

> **Catatan jujur (Path 2 — Agent-supplied direction):** Arah di bawah ini disusun oleh agent berdasarkan identitas dan kode yang sudah ada di repo (palet `#0c4a6e`/`#0ea5e9`/`#f59e0b`, tipografi `Inter`, dan konteks vokasi Bandung). Selera agent cenderung ke default AI (gradient biru-ungu, glass berlebihan, dsb) — itulah slop yang antislop filter. Hasil dengan arah ini kemungkinan masih monoton dibanding arah yang kamu tulis sendiri. Ini adalah versi yang disesuaikan dari yang ada, bukan visi brand baru.

## Identity
Mitra Cipta Mandiri — lembaga pelatihan vokasi premium yang membumi di Bandung. Fokus pada keterampilan praktis yang langsung pakai: Make Up Artist, Tata Kecantikan, Pijat Terapis, dan program umum. Posisi: terpercaya, dekat dengan peserta, hasil terlihat.

## Personality
Hangat, profesional, jernih. Tidak techy, tidak playful berlebihan. Bahasa visual: rapi, dokumenter, memberi ruang pada foto kegiatan (manusia di tengah), bukan dekorasi.

## Palette
- **Primary:** `#0c4a6e` (Deep Navy) — kepercayaan, struktur
- **Secondary:** `#0ea5e9` (Sky Blue) — aksen hierarki, CTA filter aktif
- **Accent:** `#f59e0b` (Amber) — momen penting saja (1 accent, tidak di mana-mana)
- **Neutrals:** `#ffffff`, `#f8fafc` (bg), `#1e293b` (text), `#64748b` (muted), `#e2e8f0` (border)
- **Alasan:** palet sudah hidup di `assets/css/style.css` dan konsisten di header/footer. Dipertahankan, tidak menambah warna baru (R-29: 2-3 core +1 accent).

## Typography
- **Sans:** `Inter` (300/400/500/600/700) — sudah di-load via Google Fonts. Alasan: legibilitas tinggi di dokumenter, netral tapi hangat, cocok untuk vokasi yang butuh kejelasan. Tidak pakai monospace besar atau uppercase tracking lebar (R-06).
- **Scale:** judul `display-5` bold, body `Inter` normal, caption `0.68rem` uppercase untuk kategori.

## Mood — Galeri
Galeri adalah **arsip dokumenter**: foto kegiatan nyata peserta & instruktur. Mood: *clean premium, hangat, terang*. Foto yang bicara, UI menghilang. Tidak ada grid bento penuh, tidak ada terminal, tidak ada glow.

## Dials
`Dial: ENERGY 2 / RHYTHM 2 / MOTION 1`
- **ENERGY 2 (Balanced — Stripe/Vercel):** menyapa dengan jelas tapi tidak berisik. Featured pertama sedikit lebih besar sebagai jangkar, sisanya tenang.
- **RHYTHM 2 (Balanced — konsisten dengan beberapa break):** satu break (featured) di antara grid yang otherwise uniform. Tidak mosaic penuh (hindari R-05 bento).
- **MOTION 1 (Calm — hover only):** hanya hover `scale(1.03)` + fade overlay, tidak ada parallax atau loop.

## Motif Identitas
`rounded 1rem` + `shadow soft 0 4px 14px` + overlay bawah tipis — diulang di galeri dan kartu lain sebagai jejak MCM. Satu aksen (cat putih di navy) di momen kategori.
- **Rounded 1rem (R-11):** hierarki — kartu galeri 1rem, badge 999px, modal 1.25rem; variasi deliberate, bukan semua pill.
- **Shadow 0 4px 14px → 0 12px 28px (R-12):** elevation marker hanya saat hover, bukan default mengambang; flat saat idle, lift saat fokus.

## Galeri — Alasan Keputusan (R-31)
- **Kenapa featured 1 besar:** hierarki — kegiatan flagship dapat bobot lebih, sisanya equal.
- **Kenapa overlay bawah tipis:** legibilitas judul di atas foto tanpa menutup foto.
- **Kenapa +N pakai foto blur + overlay gelap 52%:** tetap bagian galeri, bukan banner solid terpisah.
- **Kenapa filter fade 320ms:** transisi premium, tidak instan, sesuai MOTION 1.
