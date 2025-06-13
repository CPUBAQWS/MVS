<?php
session_start();
$lang = $_GET['lang'] ?? '';
$available = array_map(function($f){return basename($f,'.php');}, glob(__DIR__.'/lang/*.php'));
if ($lang && in_array($lang, $available, true)) {
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time()+60*60*24*30, '/');
    echo 'ok';
    exit;
}
echo 'invalid';
?>
