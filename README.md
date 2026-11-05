# The Citrus Cafe

Sistem pesanan dalam talian untuk kafe, dibina dengan PHP, MySQL dan Bootstrap.
Pelanggan melihat menu, membuat pesanan dine-in atau take-away, dan menerima resit melalui email.
Admin mengurus menu dan pesanan melalui dashboard.

Perlukan **PHP 8.0 atau lebih baru**, serta MySQL 5.7+ atau MariaDB.

## Jalankan di Laragon

1. Dalam Laragon, tukar PHP kepada versi 8: **Menu → PHP → Version → php-8.3**.
2. Buka `http://localhost/citruscafe-secure/home.php`.

Fail `.env` dalam folder ini dah tersedia untuk ujian tempatan. Ia guna database `citruscafe_secure_test`.
Akaun admin ujian ialah `admin@citrus.test`, dan passwordnya ada pada baris `TEST_ADMIN_PASSWORD` dalam `.env`.

## Pasang pada server baru

1. Upload semua fail kecuali `.env`.
2. Buat database kosong, kemudian import `database/schema.sql`. Fail ini hanya ada struktur jadual dan menu.
3. Salin `.env.example` kepada `.env` dan isikan butiran database. Biarkan `APP_DEBUG=false`.
4. Untuk email resit, isi `SENDGRID_API_KEY` dan `MAIL_FROM_ADDRESS`.
5. Buat akaun admin dari terminal:

   ```bash
   php database/create_admin.php "Nama Anda" email@anda.com 0123456789
   ```

6. Aktifkan HTTPS pada hosting, dan semak bahawa `https://domain-anda/.env` memberi ralat 404.

Fail `.htaccess` melindungi `.env`, `config/`, `dao/` dan `database/`, tetapi ia hanya berfungsi pada Apache.
Kalau hosting guna Nginx, tetapan yang sama perlu dibuat pada server.

## Apa yang dibaiki

| Masalah asal | Sekarang |
| --- | --- |
| SendGrid API key dalam kod | Disimpan dalam `.env`, yang tak masuk Git |
| Data akaun dan pesanan dalam fail SQL | `schema.sql` hanya ada struktur dan menu |
| Sesiapa boleh daftar sebagai admin melalui API | Pendaftaran awam sentiasa jadi akaun pelanggan |
| Page admin dan API padam atau kemas kini terbuka | Perlu login sebagai admin |
| API profil dan password menerima `user_id` orang lain | Hanya guna akaun yang sedang login |
| Harga dan status bayaran dihantar dari browser | Harga diambil dari database, dan status ditetapkan server |
| Resit boleh dibuka dengan menukar nombor pada URL | Hanya pemilik pesanan atau admin |
| SQL injection pada `invoice.php` | Guna prepared statement |
| Nama pelanggan boleh menyuntik skrip (XSS) | Semua output di-escape |
| Upload boleh menerima sebarang jenis fail | Hanya imej JPG, PNG atau WebP sehingga 5 MB, dengan nama rawak |
| Padam produk melalui pautan biasa | Perlu POST dengan token CSRF |
| Password teks biasa diterima semasa login | Hanya password hash |
| Sambungan `localhost` ditulis dalam banyak fail | Semua setting dalam `.env` |
| Gambar 25 MB | Kira-kira 6 MB |

Folder `PHPMailer` dibuang sebab tak digunakan. Kod Chart.js pada dashboard juga dibuang sebab tiada carta yang dipaparkan.

## Had yang masih ada

- **Bayaran:** tiada payment gateway. Semua pesanan, termasuk Online Banking dan E-Wallet, kekal `Pending` sehingga disambungkan kepada gateway sebenar.
- **Stok:** stok disemak semasa pesanan, tetapi tidak dikurangkan selepas pesanan dibuat.
- **Spam:** pesanan tak perlukan login, jadi email resit boleh dihantar ke mana-mana alamat. Tambah CAPTCHA atau had kadar sebelum guna secara komersial.
- **Had cubaan login:** kunci selepas 5 kali gagal hanya mengikut sesi browser. Had pada peringkat server lebih kuat.
- **Email:** penghantaran sebenar melalui SendGrid belum diuji, sebab key baru belum ada.
