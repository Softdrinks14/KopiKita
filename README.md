
# Kopi Kita

Sebuah project pemesanan kopi online yang dibuat untuk memenuhi tugas besar Mata Kuliah Praktikum Website dengan basis Frontend  dan Backend

<p align="center">
    <img src="assets/Vuejs.svg" width="100" > 
    <img src="assets/Laravel.svg" width="100>
</p>



## Environment Variables

- Step Pertama :
Buka file .env lalu modifikasi file yang ada di dalam sesuai localhost atau database 

`DB_CONNECTION=mysql`

`DB_HOST=127.0.0.1`

`DB_PORT=3306`

`DB_DATABASE=projectfinal`

`DB_USERNAME=root`

`DB_PASSWORD=`

`DB_COLLATION=utf8mb4_unicode_ci`

# Folder
Folder terdiri dari frontend dan backend untuk mengaktifkan harus melalui localhost 

# Backend

Lalu buka jalankan code dibawah
```bash
php artisan migrate
```

- Step Kedua : 
Jalankan code dibawah ini
```bash
php artisan serve
```

Sebelum step kedua silahkan jalankan command dibawah
```bash
php artisan storage:link
```

# Frontend
Langsung jalankan code dibawah
```bash
npm run dev
```

Jika code diatas tidak bisa maka harus build terlebih dahulu menggunakan 
```bash
npm run build
```

Lalu coba kunjungi localhost dari Frontend yang sudah aktif 
Untuk penambahan data user atau role silahkan menggunakan command
```bash
php artisan db:seed RoleSeeder
php artisan db:seed UserSeeder
```
Setelah itu coba untuk login menggunakan user yang sudah dibuat, saya menyarankan untuk membuat user admin terlebih dahulu agar bisa menambahkan item untuk ditampilkan

# CONTRIBUTING

[Rahmatullah Akbar Prima](https://github.com/Arabmeme) sebagai FRONTEND

[Hisyam Bima Ekhsantama](https://github.com/Softdrinks14) Sebagai BACKEND