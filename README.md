Cara install Projek ini:

1. PERSYARATAN:
 - PHP v8.2+
 - Node v24.12+
 - Composser
 - MySQL

2. Instalasi
 - Clone projek dari https://github.com/alwisahrul9/test-rs-delta-surya.git
 - Buat file .env (isi environment ada di .env.example)
 - Sesuaikan nama database, username dan password di .env
 - Jalankan perintah:
    - composer install
    - npm install
    - npm run build
    - php artisan key:generate
    - php artisan migrate
    - php artisan db:seed

3. Untuk menjalankan aplikasi, jalankan perintah berikut:
    - php artisan serve
    - npm run dev
    - Secara default, aplikasi akan mengarah ke halaman utama (http://127.0.0.1:8000), namun untuk fungsi aplikasi dimulai dari halaman login berikut:
        - http://127.0.0.1:8000/login
        - Login sebagai admin untuk menambah dokter dan apoteker dengan akun:
        - Email: admin@email.com
        - Password: password
