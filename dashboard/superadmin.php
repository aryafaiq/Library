<?php
session_start();
if (!isset($_SESSION['isLogin']) || !isset($_SESSION['role'])) {
    return header("Location: ./landing.php");
}

if ($_SESSION['role'] != 'superadmin') {
    $role = $_SESSION['role'];
    return header("Location: ./$role.php");
}
?>
<h1>
    Superadmin
</h1>