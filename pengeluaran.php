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