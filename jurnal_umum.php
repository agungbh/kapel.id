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