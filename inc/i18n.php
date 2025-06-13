<?php
function get_lang(): string {
    $selected = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? '';
    if ($selected === '') {
        $browser = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
        $selected = $browser ?: 'zh';
    }
    $file = __DIR__ . '/../lang/' . $selected . '.php';
    if (!file_exists($file)) {
        $selected = 'zh';
    }
    return $selected;
}

function t(string $key): string {
    static $lang = null;
    if ($lang === null) {
        $selected = get_lang();
        $file = __DIR__ . '/../lang/' . $selected . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../lang/zh.php';
        }
        $lang = include $file;
    }
    return $lang[$key] ?? $key;
}
?>
