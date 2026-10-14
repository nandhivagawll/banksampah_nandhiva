<?php
require 'config.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id_user,nama,username,password,role,saldo FROM user WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data && hash_equals($data['password'], $password)) {
        unset($data['password']);
        $_SESSION['user'] = $data;
        header("Location: dashboard.php");
        exit;
    }
    $error = "Username atau password salah.";
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bank Sampah - Login</title>
<link rel="stylesheet" href="style.css?v=3">
</head>
<body class="login-page">
<div class="login-card">
    <div class="logo">♻</div>
    <h1>Bank Sampah</h1>
    <p class="muted">Sistem pengelolaan setoran sampah</p>
    <?php if ($error): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn primary full">Masuk</button>
    </form>
    <div class="demo">
        <b>Akun demo</b><br>
        admin / admin123<br>
        siswa / siswa123<br>
        guru / guru123<br>
        pengepul / pengepul123
    </div>
</div>
</body>
</html>