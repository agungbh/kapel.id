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