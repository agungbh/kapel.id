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