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