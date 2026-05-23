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