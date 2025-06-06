<?php
session_start();

$code = $_POST['code'] ?? '';
$userFile = __DIR__ . '/data/users.json';
$adminFile = __DIR__ . '/data/admin.json';

if (file_exists($adminFile)) {
    $admins = json_decode(file_get_contents($adminFile), true);
    if (isset($_POST['username']) && isset($_POST['password']) &&
        isset($admins[$_POST['username']]) &&
        $admins[$_POST['username']] === $_POST['password']) {
        $_SESSION['admin_logged_in'] = true;
        echo 'admin';
        exit;
    }
}

if (file_exists($userFile)) {
    $users = json_decode(file_get_contents($userFile), true);
    if (isset($users[$code])) {
        $_SESSION['user_code'] = $code;
        echo $code;
        exit;
    }
}

echo 'invalid';
?>
