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