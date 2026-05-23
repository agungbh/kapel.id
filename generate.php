<?php
/**
 * KAPEL.ID - Ultimate System Installer & Generator
 * File: generate.php
 * Tahun: 2026
 * Deskripsi: Mengotomatiskan inisialisasi basis data MySQL dan ekstraksi 
 * arsitektur file web keuangan lengkap (CRUD, Multi-User Auth, Cetak PDF Laporan).
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>KAPEL.ID - Automated System Installer</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-slate-900 text-slate-100 font-sans p-10 flex justify-center items-center min-h-screen'>
    <div class='bg-slate-800 p-8 rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-700'>
        <h1 class='text-3xl font-black text-indigo-400 mb-2 tracking-tight'>KAPEL.ID System Installer</h1>
        <p class='text-sm text-slate-400 mb-6'>Mengonfigurasi pangkalan data dan mengurai kode aplikasi finansial terintegrasi.</p>
        <div class='bg-slate-950 p-5 rounded-xl font-mono text-xs text-green-400 space-y-2 overflow-y-auto max-h-96 border border-slate-900'>";

// =========================================================================
// SEKTOR 1: INISIALISASI DATABASE & STRUKTUR TABEL SAMPAI RECORD SAMPEL
// =========================================================================
$host = "localhost";
$user = "root";
$pass = "";

echo "<div>[>] Menghubungkan ke layanan MySQL Engine...</div>";
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("<div class='text-red-500'>[!] Instansiasi Gagal: " . $conn->connect_error . "</div></div></div></body></html>");
}
echo "<div class='text-blue-400'>[✓] Sukses terhubung ke server MySQL.</div>";

$sql_init = "
CREATE DATABASE IF NOT EXISTS kapel_id_keuangan;
USE kapel_id_keuangan;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'operator') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jurnal_umum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    akun_debit VARCHAR(100) NOT NULL,
    akun_kredit VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL
);

CREATE TABLE IF NOT EXISTS kas_retail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NULL,
    keterangan VARCHAR(255) NOT NULL,
    qty VARCHAR(50) NOT NULL,
    debet_kas INT NOT NULL,
    kredit_penjualan INT NOT NULL,
    subtotal_harian INT NULL
);

CREATE TABLE IF NOT EXISTS kas_custom (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NULL,
    keterangan VARCHAR(255) NOT NULL,
    debet_kas INT NOT NULL DEFAULT 0,
    debet_dp INT NOT NULL DEFAULT 0,
    kredit_piutang INT NOT NULL DEFAULT 0,
    kredit_penjualan INT NOT NULL DEFAULT 0,
    keterangan_dp VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NULL,
    keterangan VARCHAR(255) NOT NULL,
    jumlah INT NOT NULL
);

CREATE TABLE IF NOT EXISTS kas_kecil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NULL,
    keterangan VARCHAR(255) NOT NULL,
    debit INT NOT NULL DEFAULT 0,
    kredit INT NOT NULL DEFAULT 0,
    saldo INT NOT NULL
);

CREATE TABLE IF NOT EXISTS arus_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL UNIQUE,
    brankas INT NOT NULL,
    bank_bca INT NOT NULL,
    shopeepay INT NOT NULL,
    isian_uang INT NOT NULL,
    saldo_akhir INT NOT NULL
);
";

echo "<div>[>] Menyusun rancang bangun skema relasi tabel...</div>";
if ($conn->multi_query($sql_init)) {
    do {
        if ($result = $conn->store_result()) { $result->free(); }
    } while ($conn->next_result());
    echo "<div class='text-blue-400'>[✓] Skema database 'kapel_id_keuangan' berhasil diwujudkan.</div>";
} else {
    echo "<div class='text-red-500'>[!] Kegagalan pembuatan tabel: " . $conn->error . "</div>";
}

$conn->select_db("kapel_id_keuangan");

// Verifikasi ketersediaan record agar tidak terjadi redudansi duplikasi saat skrip dimuat ulang
$check_user = $conn->query("SELECT id FROM users LIMIT 1");
if ($check_user && $check_user->num_rows == 0) {
    echo "<div>[>] Menginjeksikan kumpulan data transaksi pembukuan Mei 2026...</div>";
    
    $conn->query("INSERT INTO users (username, password, nama_lengkap, role) VALUES
    ('admin', '\$2y\$10\$yI28L.rQ7mH2q9K29f7GDuHwLzE9y8Kz9p8b8A.gYt8I2W7R3gZ2.', 'Agung Baitul Hikmah (Admin)', 'admin'),
    ('operator', '\$2y\$10\$7vR7D2M3mD7Y8G2G2f7GDu8zE9y8Kz9p8b8A.gYt8I2W7R3gZ2.', 'Staff Keuangan (Operator)', 'operator')");

    $conn->query("INSERT INTO jurnal_umum (tanggal, keterangan, akun_debit, akun_kredit, jumlah) VALUES 
    ('2026-05-01', 'Penambahan modal ke shopeepay', 'BCA', 'modal usaha', 1000000),
    ('2026-05-06', 'Penambahan modal ke shopeepay', 'BCA', 'modal usaha', 2000000)");

    $conn->query("INSERT INTO kas_retail (tanggal, keterangan, qty, debet_kas, kredit_penjualan, subtotal_harian) VALUES 
    ('2026-05-01', 'soft handle bag', '3pcs', 3000, 1000, NULL),
    ('2026-05-01', 'kartu ucapan request', '4pcs', 28000, 28000, NULL),
    ('2026-05-01', 'STDXLL biru muda boneka', '1pcs', 175000, 175000, NULL),
    ('2026-05-01', 'thumbelina L coklat', '1pcs', 85000, 85000, 973000),
    ('2026-05-02', 'RB 7 tropis biru', '1pcs', 125000, 125000, NULL),
    ('2026-05-02', 'stiker', '19pcs', 9500, 9500, 1323500)");

    $conn->query("INSERT INTO kas_custom (tanggal, keterangan, debet_kas, debet_dp, kredit_piutang, kredit_penjualan, keterangan_dp) VALUES 
    ('2026-05-01', 'RB 10 satin pink polos a.n teh linda', 0, 100000, 0, 0, 'baru masuk dp'),
    ('2026-05-01', 'buket coklat hijau sage', 145000, 0, 0, 145000, ''),
    ('2026-05-05', 'Pelunasan pesanan andi bellaris, rb, tmb', 350000, 150000, 0, 500000, '')");

    $conn->query("INSERT INTO pengeluaran (tanggal, keterangan, jumlah) VALUES 
    ('2026-05-01', 'Kain tile polos kaku', 76575),
    ('2026-05-01', 'pembayaran salary karyawan', 5560000),
    ('2026-05-07', 'lem lilin', 328156)");

    $conn->query("INSERT INTO kas_kecil (tanggal, keterangan, debit, kredit, saldo) VALUES 
    ('2026-05-01', 'saldo brankas akhir bulan April', 9479000, 0, 9479000),
    ('2026-05-01', 'pembayaran salary karyawan', 0, 7582000, 1897000),
    ('2026-05-01', 'pendapatan retail', 1765000, 0, 3662000)");

    $conn->query("INSERT INTO arus_kas (tanggal, brankas, bank_bca, shopeepay, isian_uang, saldo_akhir) VALUES 
    ('2026-05-02', 3560000, 721244, 14487, 1920000, 6215731),
    ('2026-05-05', 8661200, 4247938, 14487, 1920000, 14843625)");
    
    echo "<div class='text-blue-400'>[✓] Database seeding record awal berhasil disuntikkan.</div>";
}
$conn->close();


// =========================================================================
// SEKTOR 2: PRODUKSI BERKAS APLIKASI WEB SECARA MODULAR
// =========================================================================
$files = [];

// 2.1 - config.php
$files['config.php'] = <<<'EOD'
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost"; $user = "root"; $pass = ""; $db = "kapel_id_keuangan";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi Database Gagal: " . $conn->connect_error); }

function rupiah($angka) { return "Rp " . number_format($angka, 0, ',', '.'); }

function proteksi_halaman($role_diizinkan = []) {
    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
    if (!empty($role_diizinkan) && !in_array($_SESSION['role'], $role_diizinkan)) { header("Location: index.php?error=akses_ditolak"); exit(); }
}

function render_sidebar($active_page) {
    $role = $_SESSION['role'] ?? 'operator';
    $menus = [
        'dashboard'           => ['Dashboard', 'index.php'],
        'jurnal'              => ['Jurnal Umum', 'jurnal_umum.php'],
        'retail'              => ['Kas Retail', 'kas_retail.php'],
        'custom'              => ['Kas Custom', 'kas_custom.php'],
        'pengeluaran'         => ['Pengeluaran Kas', 'pengeluaran.php'],
        'laporan_keseluruhan' => ['Laporan Bulanan', 'laporan_keseluruhan.php'],
    ];
    if ($role === 'admin') {
        $menus['kas_kecil']      = ['Kas Kecil (Brankas)', 'kas_kecil.php'];
        $menus['arus_kas']       = ['Arus Kas Multi', 'arus_kas.php'];
        $menus['manajemen_user'] = ['Manajemen User', 'manajemen_user.php'];
    }
    echo '<div class="w-64 bg-slate-800 text-white min-h-screen p-5 flex flex-col justify-between shadow-xl fixed left-0 top-0 h-full z-50">';
    echo '<div><div class="text-2xl font-black mb-1 text-center text-indigo-400 tracking-wider">KAPEL.ID</div>';
    echo '<div class="text-[10px] text-center text-slate-400 mb-8 uppercase font-bold tracking-widest bg-slate-900/50 py-1 rounded">Role: ' . $role . '</div>';
    echo '<nav class="space-y-1.5">';
    foreach ($menus as $key => $val) {
        $active_class = ($active_page == $key) ? 'bg-indigo-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-700/60';
        echo "<a href='{$val[1]}' class='block py-2.5 px-4 rounded-xl text-sm transition {$active_class}'>{$val[0]}</a>";
    }
    echo '</nav></div>';
    echo '<div class="space-y-4"><div class="border-t border-slate-700 pt-4 text-xs text-slate-400 px-2">Masuk sebagai:<br><span class="font-bold text-sm text-white block mt-0.5">'.htmlspecialchars($_SESSION['nama_lengkap']).'</span></div>';
    echo '<a href="logout.php" class="block py-2.5 px-4 text-center rounded-xl bg-red-600 hover:bg-red-700 transition text-sm font-bold">Keluar Sistem</a></div></div>';
    echo '<div class="w-64 min-h-screen shrink-0"></div>';
}
?>
EOD;

// 2.2 - login.php
$files['login.php'] = <<<'EOD'
<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); $password = trim($_POST['password']);
    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username); $stmt->execute(); $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap']; $_SESSION['role'] = $user['role'];
                header("Location: index.php"); exit();
            } else { $error = "Kombinasi password tidak cocok."; }
        } else { $error = "Identitas pengguna tidak ditemukan."; }
        $stmt->close();
    } else { $error = "Harap masukkan data lengkap."; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Masuk Sistem - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-900 h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-2xl">
        <div class="text-center mb-8"><h1 class="text-4xl font-black text-slate-800 tracking-tight">KAPEL.ID</h1><p class="text-xs text-slate-400 font-medium uppercase mt-1.5">Sistem Finansial Pembukuan Toko</p></div>
        <?php if(!empty($error)): ?><div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl"><p class="text-xs text-red-700 font-semibold"><?= $error; ?></p></div><?php endif; ?>
        <form action="login.php" method="POST" class="space-y-5">
            <div><label class="block text-xs font-bold text-slate-600 uppercase mb-2">Username</label><input type="text" name="username" required class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500" placeholder="admin / operator"></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase mb-2">Password</label><input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-indigo-500" placeholder="••••••••"></div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg text-sm transition">Akses Masuk</button>
        </form>
    </div>
</body>
</html>
EOD;

// 2.3 - index.php
$files['index.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();

$total_retail = $conn->query("SELECT SUM(kredit_penjualan) as total FROM kas_retail")->fetch_assoc()['total'] ?? 0;
$total_custom = $conn->query("SELECT SUM(kredit_penjualan) as total FROM kas_custom")->fetch_assoc()['total'] ?? 0;
$total_pengeluaran = $conn->query("SELECT SUM(jumlah) as total FROM pengeluaran")->fetch_assoc()['total'] ?? 0;
$saldo_arus_kas = $conn->query("SELECT saldo_akhir FROM arus_kas ORDER BY tanggal DESC LIMIT 1")->fetch_assoc()['saldo_akhir'] ?? 0;

$chart_query = $conn->query("
    SELECT tanggal, SUM(total_jual) as total FROM (
        SELECT tanggal, SUM(kredit_penjualan) as total_jual FROM kas_retail WHERE tanggal IS NOT NULL GROUP BY tanggal
        UNION ALL
        SELECT tanggal, SUM(kredit_penjualan) as total_jual FROM kas_custom WHERE tanggal IS NOT NULL GROUP BY tanggal
    ) as gabungan GROUP BY tanggal ORDER BY tanggal ASC
");
$labels = []; $chart_data = [];
while($row = $chart_query->fetch_assoc()) { $labels[] = $row['tanggal']; $chart_data[] = $row['total']; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Dashboard - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('dashboard'); ?>
    <div class="flex-1 p-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Panel Utama Keuangan</h1><p class="text-sm text-slate-500 mb-8">Informasi eksekutif pembukuan berjalan.</p>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'akses_ditolak'): ?><div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl"><p class="text-xs text-amber-800 font-bold">Akses Ditolak: Anda tidak memliki hak akses Administrator untuk menu rahasia tersebut.</p></div><?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border"><div class="text-[11px] font-bold text-slate-400 uppercase">Omset Retail</div><div class="text-xl font-black mt-1"><?= rupiah($total_retail); ?></div></div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border"><div class="text-[11px] font-bold text-slate-400 uppercase">Omset Custom</div><div class="text-xl font-black mt-1"><?= rupiah($total_custom); ?></div></div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border"><div class="text-[11px] font-bold text-slate-400 uppercase">Total Pengeluaran</div><div class="text-xl font-black text-red-600 mt-1"><?= rupiah($total_pengeluaran); ?></div></div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border bg-indigo-50/30"><div class="text-[11px] font-bold text-indigo-500 uppercase">Arus Saldo Akhir</div><div class="text-xl font-black text-indigo-700 mt-1"><?= rupiah($saldo_arus_kas); ?></div></div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border"><h2 class="text-base font-bold mb-4">Tren Gabungan Omset Harian</h2><div class="h-80"><canvas id="salesChart"></canvas></div></div>
    </div>
    <script>
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: { labels: <?= json_encode($labels); ?>, datasets: [{ label: 'Total Omset', data: <?= json_encode($chart_data); ?>, borderColor: 'rgb(79, 70, 229)', backgroundColor: 'rgba(79, 70, 229, 0.05)', fill: true, tension: 0.15, borderWidth: 3 }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>
</html>
EOD;

// 2.4 - jurnal_umum.php
$files['jurnal_umum.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM jurnal_umum WHERE id = ?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: jurnal_umum.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tanggal = $_POST['tanggal']; $keterangan = $_POST['keterangan']; $akun_debit = $_POST['akun_debit']; $akun_kredit = $_POST['akun_kredit']; $jumlah = intval($_POST['jumlah']);
    if ($id > 0) { $stmt = $conn->prepare("UPDATE jurnal_umum SET tanggal=?, keterangan=?, akun_debit=?, akun_kredit=?, jumlah=? WHERE id=?"); $stmt->bind_param("ssssii", $tanggal, $keterangan, $akun_debit, $akun_kredit, $jumlah, $id); }
    else { $stmt = $conn->prepare("INSERT INTO jurnal_umum (tanggal, keterangan, akun_debit, akun_kredit, jumlah) VALUES (?, ?, ?, ?, ?)"); $stmt->bind_param("ssssi", $tanggal, $keterangan, $akun_debit, $akun_kredit, $jumlah); }
    $stmt->execute(); $stmt->close(); header("Location: jurnal_umum.php"); exit();
}
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM jurnal_umum WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT * FROM jurnal_umum ORDER BY tanggal ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Jurnal Umum - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('jurnal'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Buku Jurnal Umum</h1><a href="jurnal_umum.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Transaksi</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="jurnal_umum.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Keterangan</label><input type="text" name="keterangan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : ''; ?>"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Akun Debit</label><input type="text" name="akun_debit" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['akun_debit']) : ''; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Akun Kredit</label><input type="text" name="akun_kredit" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['akun_kredit']) : ''; ?>"></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Jumlah</label><input type="number" name="jumlah" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['jumlah'] : ''; ?>"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="jurnal_umum.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4">Keterangan</th><th class="p-4">Debit</th><th class="p-4">Kredit</th><th class="p-4 text-right">Jumlah</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4"><?= $row['tanggal']; ?></td><td class="p-4 font-medium"><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td class="p-4"><span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-md"><?= htmlspecialchars($row['akun_debit']); ?></span></td>
                        <td class="p-4"><span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[11px] font-bold rounded-md"><?= htmlspecialchars($row['akun_kredit']); ?></span></td>
                        <td class="p-4 text-right font-bold"><?= rupiah($row['jumlah']); ?></td>
                        <td class="p-4 text-center space-x-2"><a href="jurnal_umum.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 text-xs font-semibold">Ubah</a><a href="jurnal_umum.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus data?')" class="text-red-600 text-xs font-semibold">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.5 - kas_retail.php
$files['kas_retail.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM kas_retail WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: kas_retail.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : NULL;
    $keterangan = $_POST['keterangan']; $qty = $_POST['qty']; $debet_kas = intval($_POST['debet_kas']); $kredit_penjualan = intval($_POST['kredit_penjualan']); $subtotal_harian = !empty($_POST['subtotal_harian']) ? intval($_POST['subtotal_harian']) : NULL;
    if ($id > 0) { $stmt = $conn->prepare("UPDATE kas_retail SET tanggal=?, keterangan=?, qty=?, debet_kas=?, kredit_penjualan=?, subtotal_harian=? WHERE id=?"); $stmt->bind_param("ssisiii", $tanggal, $keterangan, $qty, $debet_kas, $kredit_penjualan, $subtotal_harian, $id); }
    else { $stmt = $conn->prepare("INSERT INTO kas_retail (tanggal, keterangan, qty, debet_kas, kredit_penjualan, subtotal_harian) VALUES (?, ?, ?, ?, ?, ?)"); $stmt->bind_param("sssiii", $tanggal, $keterangan, $qty, $debet_kas, $kredit_penjualan, $subtotal_harian); }
    $stmt->execute(); $stmt->close(); header("Location: kas_retail.php"); exit();
}
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM kas_retail WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT * FROM kas_retail ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kas Retail - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('retail'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Penerimaan Kas Retail</h1><a href="kas_retail.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Retail</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="kas_retail.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">QTY</label><input type="text" name="qty" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['qty']) : ''; ?>"></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Keterangan Produk</label><input type="text" name="keterangan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : ''; ?>"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Debet Kas</label><input type="number" name="debet_kas" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['debet_kas'] : ''; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Kredit Penjualan</label><input type="number" name="kredit_penjualan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['kredit_penjualan'] : ''; ?>"></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Subtotal Harian (Opsional)</label><input type="number" name="subtotal_harian" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['subtotal_harian'] : ''; ?>"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="kas_retail.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4">Keterangan</th><th class="p-4 text-center">QTY</th><th class="p-4 text-right">Kas (D)</th><th class="p-4 text-right">Jual (K)</th><th class="p-4 text-right">Subtotal</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 text-slate-400"><?= $row['tanggal'] ?: '↳'; ?></td><td class="p-4 font-medium"><?= htmlspecialchars($row['keterangan']); ?></td><td class="p-4 text-center"><?= htmlspecialchars($row['qty']); ?></td>
                        <td class="p-4 text-right text-emerald-600"><?= rupiah($row['debet_kas']); ?></td><td class="p-4 text-right text-slate-600"><?= rupiah($row['kredit_penjualan']); ?></td>
                        <td class="p-4 text-right font-black text-indigo-600"><?= $row['subtotal_harian'] ? rupiah($row['subtotal_harian']) : ''; ?></td>
                        <td class="p-4 text-center space-x-2"><a href="kas_retail.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="kas_retail.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 font-semibold text-xs">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.6 - kas_custom.php
$files['kas_custom.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM kas_custom WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: kas_custom.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : NULL;
    $keterangan = $_POST['keterangan']; $debet_kas = intval($_POST['debet_kas']); $debet_dp = intval($_POST['debet_dp']); $kredit_piutang = intval($_POST['kredit_piutang']); $kredit_penjualan = intval($_POST['kredit_penjualan']); $keterangan_dp = $_POST['keterangan_dp'];
    if ($id > 0) { $stmt = $conn->prepare("UPDATE kas_custom SET tanggal=?, keterangan=?, debet_kas=?, debet_dp=?, kredit_piutang=?, kredit_penjualan=?, keterangan_dp=? WHERE id=?"); $stmt->bind_param("ssiiiisi", $tanggal, $keterangan, $debet_kas, $debet_dp, $kredit_piutang, $kredit_penjualan, $keterangan_dp, $id); }
    else { $stmt = $conn->prepare("INSERT INTO kas_custom (tanggal, keterangan, debet_kas, debet_dp, kredit_piutang, kredit_penjualan, keterangan_dp) VALUES (?, ?, ?, ?, ?, ?, ?)"); $stmt->bind_param("ssiiiis", $tanggal, $keterangan, $debet_kas, $debet_dp, $kredit_piutang, $kredit_penjualan, $keterangan_dp); }
    $stmt->execute(); $stmt->close(); header("Location: kas_custom.php"); exit();
}
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM kas_custom WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT * FROM kas_custom ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kas Custom - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('custom'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Kas Custom Order</h1><a href="kas_custom.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Pesanan</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-2xl">
            <form action="kas_custom.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Keterangan / Klien</label><input type="text" name="keterangan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : ''; ?>"></div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Kas (D)</label><input type="number" name="debet_kas" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['debet_kas'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">DP (D)</label><input type="number" name="debet_dp" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['debet_dp'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Piutang (K)</label><input type="number" name="kredit_piutang" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['kredit_piutang'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Jual (K)</label><input type="number" name="kredit_penjualan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['kredit_penjualan'] : '0'; ?>"></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Memo Status</label><input type="text" name="keterangan_dp" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan_dp']) : ''; ?>"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="kas_custom.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4">Keterangan</th><th class="p-4 text-right">Kas</th><th class="p-4 text-right">DP</th><th class="p-4 text-right">Piutang</th><th class="p-4 text-right">Jual</th><th class="p-4">Memo</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 text-slate-400"><?= $row['tanggal'] ?: '↳'; ?></td><td class="p-4 font-medium"><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td class="p-4 text-right text-emerald-600 font-semibold"><?= rupiah($row['debet_kas']); ?></td><td class="p-4 text-right text-blue-600"><?= rupiah($row['debet_dp']); ?></td>
                        <td class="p-4 text-right text-amber-600"><?= rupiah($row['kredit_piutang']); ?></td><td class="p-4 text-right font-bold"><?= rupiah($row['kredit_penjualan']); ?></td>
                        <td class="p-4 text-xs italic text-slate-400"><?= htmlspecialchars($row['keterangan_dp']); ?></td>
                        <td class="p-4 text-center space-x-2"><a href="kas_custom.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="kas_custom.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 font-semibold text-xs">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.7 - pengeluaran.php
$files['pengeluaran.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM pengeluaran WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: pengeluaran.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : NULL; $keterangan = $_POST['keterangan']; $jumlah = intval($_POST['jumlah']);
    if ($id > 0) { $stmt = $conn->prepare("UPDATE pengeluaran SET tanggal=?, keterangan=?, jumlah=? WHERE id=?"); $stmt->bind_param("ssii", $tanggal, $keterangan, $jumlah, $id); }
    else { $stmt = $conn->prepare("INSERT INTO pengeluaran (tanggal, keterangan, jumlah) VALUES (?, ?, ?)"); $stmt->bind_param("ssi", $tanggal, $keterangan, $jumlah); }
    $stmt->execute(); $stmt->close(); header("Location: pengeluaran.php"); exit();
}
$result = $conn->query("SELECT * FROM pengeluaran ORDER BY id ASC");
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM pengeluaran WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Pengeluaran - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('pengeluaran'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Jurnal Pengeluaran Kas</h1><a href="pengeluaran.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Pengeluaran</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="pengeluaran.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Keterangan Alokasi</label><input type="text" name="keterangan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : ''; ?>"></div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Jumlah Pengeluaran</label><input type="number" name="jumlah" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['jumlah'] : ''; ?>"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="pengeluaran.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4">Keterangan Alokasi</th><th class="p-4 text-right">Debit (Beban)</th><th class="p-4 text-right">Kredit (Kas)</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 text-slate-400"><?= $row['tanggal'] ?: '↳'; ?></td><td class="p-4 font-medium"><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td class="p-4 text-right font-semibold"><?= rupiah($row['jumlah']); ?></td><td class="p-4 text-right text-rose-600 font-bold"><?= rupiah($row['jumlah']); ?></td>
                        <td class="p-4 text-center space-x-2"><a href="pengeluaran.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="pengeluaran.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 font-semibold text-xs">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.8 - kas_kecil.php (Admin)
$files['kas_kecil.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman(['admin']);
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM kas_kecil WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: kas_kecil.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : NULL;
    $keterangan = $_POST['keterangan']; $debit = intval($_POST['debit']); $kredit = intval($_POST['kredit']); $saldo = intval($_POST['saldo']);
    if ($id > 0) { $stmt = $conn->prepare("UPDATE kas_kecil SET tanggal=?, keterangan=?, debit=?, kredit=?, saldo=? WHERE id=?"); $stmt->bind_param("ssiiii", $tanggal, $keterangan, $debit, $kredit, $saldo, $id); }
    else { $stmt = $conn->prepare("INSERT INTO kas_kecil (tanggal, keterangan, debit, kredit, saldo) VALUES (?, ?, ?, ?, ?)"); $stmt->bind_param("ssiii", $tanggal, $keterangan, $debit, $kredit, $saldo); }
    $stmt->execute(); $stmt->close(); header("Location: kas_kecil.php"); exit();
}
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM kas_kecil WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT * FROM kas_kecil ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kas Kecil - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('kas_kecil'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Kas Kecil (Brankas Fisik)</h1><a href="kas_kecil.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Mutasi</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="kas_kecil.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Keterangan Aktivitas</label><input type="text" name="keterangan" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : ''; ?>"></div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Debit (+)</label><input type="number" name="debit" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['debit'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Kredit (-)</label><input type="number" name="kredit" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['kredit'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Saldo Akumulatif</label><input type="number" name="saldo" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['saldo'] : '0'; ?>"></div>
                </div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="kas_kecil.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4">Keterangan Aktivitas</th><th class="p-4 text-right">Debit (+)</th><th class="p-4 text-right">Kredit (-)</th><th class="p-4 text-right bg-indigo-50 text-indigo-700">Saldo Akhir</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 text-slate-400"><?= $row['tanggal'] ?: '↳'; ?></td><td class="p-4 font-medium"><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td class="p-4 text-right text-emerald-600"><?= $row['debit'] ? rupiah($row['debit']) : '-'; ?></td><td class="p-4 text-right text-rose-600"><?= $row['kredit'] ? rupiah($row['kredit']) : '-'; ?></td>
                        <td class="p-4 text-right font-black bg-indigo-50/30 text-indigo-900"><?= rupiah($row['saldo']); ?></td>
                        <td class="p-4 text-center space-x-2"><a href="kas_kecil.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="kas_kecil.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 font-semibold text-xs">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.9 - arus_kas.php (Admin)
$files['arus_kas.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman(['admin']);
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("DELETE FROM arus_kas WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close(); header("Location: arus_kas.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $tanggal = $_POST['tanggal'];
    $brankas = intval($_POST['brankas']); $bank_bca = intval($_POST['bank_bca']); $shopeepay = intval($_POST['shopeepay']); $isian_uang = intval($_POST['isian_uang']); $saldo_akhir = intval($_POST['saldo_akhir']);
    if ($id > 0) { $stmt = $conn->prepare("UPDATE arus_kas SET tanggal=?, brankas=?, bank_bca=?, shopeepay=?, isian_uang=?, saldo_akhir=? WHERE id=?"); $stmt->bind_param("siiiiii", $tanggal, $brankas, $bank_bca, $shopeepay, $isian_uang, $saldo_akhir, $id); }
    else { $stmt = $conn->prepare("INSERT INTO arus_kas (tanggal, brankas, bank_bca, shopeepay, isian_uang, saldo_akhir) VALUES (?, ?, ?, ?, ?, ?)"); $stmt->bind_param("siiiii", $tanggal, $brankas, $bank_bca, $shopeepay, $isian_uang, $saldo_akhir); }
    $stmt->execute(); $stmt->close(); header("Location: arus_kas.php"); exit();
}
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT * FROM arus_kas WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT * FROM arus_kas ORDER BY tanggal ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Arus Kas - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('arus_kas'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Rekonsiliasi Arus Kas Posisi Saldo</h1><a href="arus_kas.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl">Tambah Arus</a></div>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="arus_kas.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label><input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Brankas</label><input type="number" name="brankas" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['brankas'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Bank BCA</label><input type="number" name="bank_bca" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['bank_bca'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">ShopeePay</label><input type="number" name="shopeepay" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['shopeepay'] : '0'; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Isian Uang</label><input type="number" name="isian_uang" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['isian_uang'] : '0'; ?>"></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Total Konsolidasi</label><input type="number" name="saldo_akhir" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? $edit_data['saldo_akhir'] : '0'; ?>"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="arus_kas.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Tanggal</th><th class="p-4 text-right">Brankas</th><th class="p-4 text-right">BCA</th><th class="p-4 text-right">ShopeePay</th><th class="p-4 text-right">Isian Uang</th><th class="p-4 text-right bg-violet-600 text-white shadow">Konsolidasi</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 font-bold"><?= $row['tanggal']; ?></td><td class="p-4 text-right"><?= rupiah($row['brankas']); ?></td><td class="p-4 text-right"><?= rupiah($row['bank_bca']); ?></td><td class="p-4 text-right"><?= rupiah($row['shopeepay']); ?></td><td class="p-4 text-right"><?= rupiah($row['isian_uang']); ?></td>
                        <td class="p-4 text-right font-black bg-violet-50 text-violet-900"><?= rupiah($row['saldo_akhir']); ?></td>
                        <td class="p-4 text-center space-x-2"><a href="arus_kas.php?action=edit&id=<?= $row['id']; ?>" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="arus_kas.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-600 font-semibold text-xs">Hapus</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.10 - manajemen_user.php (Admin)
$files['manajemen_user.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman(['admin']);
$error = ''; $success = '';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id === $_SESSION['user_id']) { header("Location: manajemen_user.php?error=self_delete"); exit(); }
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    header("Location: manajemen_user.php?success=delete"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; $username = trim($_POST['username']); $nama_lengkap = trim($_POST['nama_lengkap']); $role = $_POST['role']; $password = trim($_POST['password']);
    if ($id > 0) {
        if (!empty($password)) { $hash = password_hash($password, PASSWORD_BCRYPT); $stmt = $conn->prepare("UPDATE users SET username=?, password=?, nama_lengkap=?, role=? WHERE id=?"); $stmt->bind_param("ssssi", $username, $hash, $nama_lengkap, $role, $id); }
        else { $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, role=? WHERE id=?"); $stmt->bind_param("sssi", $username, $nama_lengkap, $role, $id); }
        if ($stmt->execute()) { $success = "Data pengguna berhasil diperbarui."; } else { $error = "Username sudah terpakai."; } $stmt->close();
    } else {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT); $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)"); $stmt->bind_param("ssss", $username, $hash, $nama_lengkap, $role);
            if ($stmt->execute()) { $success = "User baru berhasil ditambahkan."; } else { $error = "Username sudah terdaftar."; } $stmt->close();
        } else { $error = "Password wajib diisi untuk user baru."; }
    }
}
if (isset($_GET['success']) && $_GET['success'] == 'delete') { $success = "User berhasil dihapus."; }
if (isset($_GET['error']) && $_GET['error'] == 'self_delete') { $error = "Aksi ditolak: Tidak bisa menghapus akun sendiri."; }

$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']); $stmt = $conn->prepare("SELECT id, username, nama_lengkap, role FROM users WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
$result = $conn->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY role ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Manajemen User - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <?php render_sidebar('manajemen_user'); ?>
    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold tracking-tight">Manajemen Pengguna</h1><a href="manajemen_user.php?action=add" class="bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-xl shadow">Tambah User Baru</a></div>
        <?php if(!empty($error)): ?><div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-xl"><p class="text-xs text-red-700 font-semibold"><?= $error; ?></p></div><?php endif; ?>
        <?php if(!empty($success)): ?><div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-r-xl"><p class="text-xs text-green-700 font-semibold"><?= $success; ?></p></div><?php endif; ?>
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
        <div class="bg-white p-6 rounded-2xl border mb-6 max-w-xl">
            <form action="manajemen_user.php" method="POST" class="space-y-4">
                <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id']; ?>"><?php endif; ?>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Username</label><input type="text" name="username" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['username']) : ''; ?>"></div>
                    <div><label class="block text-xs font-bold text-slate-600 mb-1">Role</label><select name="role" class="w-full px-3 py-2 border rounded-xl text-sm"><option value="operator" <?= ($edit_data && $edit_data['role'] == 'operator')?'selected':''; ?>>Operator</option><option value="admin" <?= ($edit_data && $edit_data['role'] == 'admin')?'selected':''; ?>>Admin</option></select></div>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label><input type="text" name="nama_lengkap" required class="w-full px-3 py-2 border rounded-xl text-sm" value="<?= $edit_data ? htmlspecialchars($edit_data['nama_lengkap']) : ''; ?>"></div>
                <div><label class="block text-xs font-bold text-slate-600 mb-1">Password</label><input type="password" name="password" <?= $_GET['action']=='add'?'required':''; ?> class="w-full px-3 py-2 border rounded-xl text-sm" placeholder="Kosongkan jika tidak diubah"></div>
                <div class="flex space-x-2"><button type="submit" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-lg">Simpan</button><a href="manajemen_user.php" class="bg-slate-100 text-slate-600 text-xs font-bold py-2 px-4 rounded-lg">Batal</a></div>
            </form>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Nama Lengkap</th><th class="p-4">Username</th><th class="p-4">Role</th><th class="p-4 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 font-bold"><?= htmlspecialchars($row['nama_lengkap']); ?></td><td class="p-4">@<?= htmlspecialchars($row['username']); ?></td>
                        <td class="p-4"><span class="px-2 py-0.5 text-xs font-bold rounded-full <?= $row['role'] == 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' ?>"><?= $row['role']; ?></span></td>
                        <td class="p-4 text-center space-x-2"><?= ($row['id'] !== $_SESSION['user_id']) ? '<a href="manajemen_user.php?action=edit&id='.$row['id'].'" class="text-indigo-600 font-semibold text-xs">Ubah</a><a href="manajemen_user.php?action=delete&id='.$row['id'].'" onclick="return confirm(\'Hapus?\')" class="text-red-600 font-semibold text-xs">Hapus</a>' : '<a href="manajemen_user.php?action=edit&id='.$row['id'].'" class="text-indigo-600 font-semibold text-xs">Ubah</a>'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
EOD;

// 2.11 - laporan_keseluruhan.php (Semua Hak Akses + Cetak Lembar Nota Kerja PDF Bawaan)
$files['laporan_keseluruhan.php'] = <<<'EOD'
<?php
require_once 'config.php'; proteksi_halaman();
$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : '05'; $tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : '2026';
$periode_string = "$tahun_pilihan-$bulan_pilihan"; $like_periode = $periode_string . "%";

$stmt_retail = $conn->prepare("SELECT SUM(kredit_penjualan) as total FROM kas_retail WHERE (tanggal LIKE ? OR (tanggal IS NULL AND '05'=?))");
$stmt_retail->bind_param("ss", $like_periode, $bulan_pilihan); $stmt_retail->execute(); $total_retail = $stmt_retail->get_result()->fetch_assoc()['total'] ?? 0; $stmt_retail->close();

$stmt_custom = $conn->prepare("SELECT SUM(kredit_penjualan) as total FROM kas_custom WHERE tanggal LIKE ?");
$stmt_custom->bind_param("s", $like_periode); $stmt_custom->execute(); $total_custom = $stmt_custom->get_result()->fetch_assoc()['total'] ?? 0; $stmt_custom->close();

$stmt_pengeluaran = $conn->prepare("SELECT SUM(jumlah) as total FROM pengeluaran WHERE tanggal LIKE ?");
$stmt_pengeluaran->bind_param("s", $like_periode); $stmt_pengeluaran->execute(); $total_pengeluaran = $stmt_pengeluaran->get_result()->fetch_assoc()['total'] ?? 0; $stmt_pengeluaran->close();

$total_pendapatan = $total_retail + $total_custom; $saldo_bersih = $total_pendapatan - $total_pengeluaran;
$daftar_bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Laporan - KAPEL.ID</title><script src="https://cdn.tailwindcss.com"></script>
    <style>@media print { .no-print { display: none !important; } body { background: #fff; color: #000; } .print-container { width: 100%!important; padding: 0!important; margin: 0!important; } }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">
    <div class="no-print"><?php render_sidebar('laporan_keseluruhan'); ?></div>
    <div class="flex-1 p-8 print-container">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-5 mb-6 no-print gap-4">
            <div><h1 class="text-2xl font-bold tracking-tight">Laporan Rekapitulasi Keseluruhan</h1></div>
            <form action="laporan_keseluruhan.php" method="GET" class="flex items-center gap-2">
                <select name="bulan" class="px-3 py-2 border rounded-xl text-xs font-semibold bg-white">
                    <?php foreach($daftar_bulan as $k => $v): ?><option value="<?= $k; ?>" <?= $bulan_pilihan == $k ? 'selected' : ''; ?>><?= $v; ?></option><?php endforeach; ?>
                </select>
                <select name="tahun" class="px-3 py-2 border rounded-xl text-xs font-semibold bg-white">
                    <?php for($t=2025;$t<=2030;$t++): ?><option value="<?= $t; ?>" <?= $tahun_pilihan == $t ? 'selected' : ''; ?>><?= $t; ?></option><?php endfor; ?>
                </select>
                <button type="submit" class="bg-slate-800 text-white text-xs font-bold py-2 px-4 rounded-xl">Filter</button>
                <button type="button" onclick="window.print()" class="bg-indigo-600 text-white text-xs font-bold py-2 px-4 rounded-xl shadow">Cetak PDF</button>
            </form>
        </div>
        <div class="bg-white p-8 rounded-2xl border max-w-4xl mx-auto shadow-sm">
            <div class="text-center border-b-2 border-slate-900 pb-5 mb-8">
                <h2 class="text-3xl font-black text-slate-900">KAPEL.ID</h2><p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mt-0.5">Laporan Aktivitas Finansial Bulanan</p>
                <p class="text-sm mt-3">Periode Pembukuan: <span class="font-bold text-slate-900"><?= $daftar_bulan[$bulan_pilihan] . ' ' . $tahun_pilihan; ?></span></p>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="border p-4 rounded-xl bg-slate-50/50"><div class="text-[10px] font-bold text-slate-400 uppercase">Total Omset Pendapatan</div><div class="text-lg font-bold mt-0.5"><?= rupiah($total_pendapatan); ?></div></div>
                <div class="border p-4 rounded-xl bg-slate-50/50"><div class="text-[10px] font-bold text-slate-400 uppercase">Total Beban Operasional</div><div class="text-lg font-bold text-rose-600 mt-0.5"><?= rupiah($total_pengeluaran); ?></div></div>
                <div class="border p-4 rounded-xl <?= $saldo_bersih>=0?'bg-emerald-50/20':'bg-red-50/20' ?>"><div class="text-[10px] font-bold uppercase">Laba Bersih Sementara</div><div class="text-lg font-black mt-0.5"><?= rupiah($saldo_bersih); ?></div></div>
            </div>
            <div class="overflow-hidden border rounded-xl">
                <table class="w-full text-left text-sm">
                    <thead><tr class="bg-slate-50 border-b text-slate-500 font-bold"><th class="p-4">Nama Pos Akun Finansial</th><th class="p-4 text-right">Arus Masuk (Kredit)</th><th class="p-4 text-right">Arus Keluar (Debet)</th></tr></thead>
                    <tbody class="divide-y text-slate-700">
                        <tr><td class="p-4 font-medium">Penjualan Produk Retail</td><td class="p-4 text-right text-emerald-600 font-medium"><?= rupiah($total_retail); ?></td><td class="p-4 text-right text-slate-400">-</td></tr>
                        <tr><td class="p-4 font-medium">Penjualan Buket Custom Order</td><td class="p-4 text-right text-emerald-600 font-medium"><?= rupiah($total_custom); ?></td><td class="p-4 text-right text-slate-400">-</td></tr>
                        <tr><td class="p-4 font-medium">Beban Pengeluaran Bahan & Operasional Toko</td><td class="p-4 text-right text-slate-400">-</td><td class="p-4 text-right text-rose-600 font-medium"><?= rupiah($total_pengeluaran); ?></td></tr>
                        <tr class="bg-slate-50 font-bold text-slate-900 border-t-2"><td class="p-4">Total Kompilasi Arus</td><td class="p-4 text-right text-emerald-700"><?= rupiah($total_pendapatan); ?></td><td class="p-4 text-right text-rose-700"><?= rupiah($total_pengeluaran); ?></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-12 flex justify-between text-xs font-semibold text-slate-500 px-4">
                <div><p>Disiapkan Oleh,</p><div class="h-14"></div><p class="border-t pt-1 w-32 text-center font-bold text-slate-800">Operator Kasir</p></div>
                <div class="text-right"><p>Tasikmalaya, <?= date('d').' '.$daftar_bulan[date('m')].' '.date('Y'); ?></p><div class="h-14"></div><p class="border-t pt-1 w-44 text-center font-bold text-slate-800 ml-auto"><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></p></div>
            </div>
        </div>
    </div>
</body>
</html>
EOD;

// 2.12 - logout.php
$files['logout.php'] = <<<'EOD'
<?php
require_once 'config.php'; $_SESSION = array();
if (ini_get("session.use_cookies")) { $params = session_get_cookie_params(); setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]); }
session_destroy(); header("Location: login.php?pesan=logout"); exit();
?>
EOD;

// Loop penulisan berkas modular ke piringan harddisk server lokal
foreach ($files as $filename => $content) {
    echo "<div>[>] Menyusun berkas fisik: <span class='text-yellow-400'>$filename</span>...</div>";
    if (file_put_contents($filename, $content) !== false) {
        echo "<div class='text-blue-400'>--- [✓] Berkas $filename sukses dikomparasi dan diekstrak.</div>";
    } else {
        echo "<div class='text-red-500'>--- [!] Kegagalan fatal penulisan file fisik $filename. Periksa hak izin (CHMOD) penulisan direktori.</div>";
    }
}

echo "</div>
        <div class='mt-6 bg-indigo-950/40 p-4 rounded-xl border border-indigo-500/30 text-center'>
            <p class='text-sm text-indigo-300 font-semibold'>🚀 Selesai! Seluruh struktur aplikasi pembukuan KAPEL.ID telah terpasang sempurna.</p>
            <a href='login.php' class='inline-block mt-3 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition uppercase tracking-wider shadow-lg shadow-indigo-600/30'>
                Buka Halaman Login
            </a>
        </div>
    </div>
</body>
</html>";