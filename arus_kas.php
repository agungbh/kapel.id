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