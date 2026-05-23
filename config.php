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