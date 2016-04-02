<?php

if (isset($_POST['login'])) {
    $kNumber = trim($_POST['kNumber']);
    $pwd = trim($_POST['pwd']);
    $stmt = $db->prepare('SELECT pwd FROM user WHERE kNumber = :kNumber');
    $stmt->bindParam(':kNumber', $kNumber);
    $stmt->execute();
    $stored = $stmt->fetchColumn();
    if (password_verify($pwd, $stored)) {
        session_start();
        session_regenerate_id(true);
        $_SESSION['username'] = $kNumber;
        $_SESSION['authenticated'] = true;
        header('Location: home.php');
        exit;
    } else {
        $error = 'Login failed. Check kNumber and password.';
    }
}