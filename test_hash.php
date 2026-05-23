<?php
$password_kamu = "admin123";

// Generate hash baru secara langsung di server kamu
$hash_baru = password_hash($password_kamu, PASSWORD_DEFAULT);

echo "<h3>Hasil Diagnosis Enkripsi Server Anda:</h3>";
echo "1. Teks Asli: <b>" . $password_kamu . "</b><br>";
echo "2. Hasil Hash Baru di Server Anda: <input type='text' value='" . $hash_baru . "' style='width:450px;' readonly><br><br>";
echo "<i>*Salin kode di atas dan masukkan ke kolom 'password' pada tabel 'users' di phpMyAdmin untuk mengganti kode yang lama, lalu coba login kembali melalui halaman utama.*</i>";
?>