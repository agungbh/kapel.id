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