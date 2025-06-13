<?php
function t(string $key): string {
    static $lang = null;
    if ($lang === null) {
        $selected = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'zh';
        $file = __DIR__ . '/../lang/' . $selected . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../lang/zh.php';
        }
        $lang = include $file;
    }
    return $lang[$key] ?? $key;
}
?>
