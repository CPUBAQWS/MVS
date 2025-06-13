<?php
session_start();
require_once __DIR__ . '/inc/i18n.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

$catFile = __DIR__ . '/data/categories.json';
$voteFile = __DIR__ . '/data/votes.json';

$categories = file_exists($catFile) ? json_decode(file_get_contents($catFile), true) : [];
$votes = file_exists($voteFile) ? json_decode(file_get_contents($voteFile), true) : [];

$result = [];
foreach ($categories as $cat) {
    $folder = $cat['folder'];
    $name = $cat['name'] ?? $folder;
    $dir = __DIR__ . '/Files/' . $folder;
    $files = is_dir($dir) ? array_diff(scandir($dir), ['.', '..']) : [];
    $counts = array_fill_keys($files, 0);
    foreach ($votes as $userVotes) {
        if (!isset($userVotes[$folder])) continue;
        foreach ($userVotes[$folder] as $f) {
            if (isset($counts[$f])) {
                $counts[$f]++;
            }
        }
    }
    $result[$name] = $counts;
}

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="vote_report.html"');

$langAttr = get_lang();
echo "<!DOCTYPE html><html lang=\"$langAttr\"><head><meta charset=\"UTF-8\"><title>" . t('vote_report_title') . "</title>";
echo "<style>body{font-family:sans-serif;}table{border-collapse:collapse;margin-bottom:20px;}th,td{border:1px solid #ccc;padding:4px 8px;text-align:left;}th{background:#f0f0f0;}</style></head><body>";
echo "<h1>" . t('vote_results_report') . "</h1>";
foreach ($result as $catName => $files) {
    arsort($files); // sort files by vote count descending
    echo '<h2>' . htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<table><thead><tr><th>' . t('file') . '</th><th>' . t('votes') . '</th></tr></thead><tbody>';
    foreach ($files as $file => $count) {
        echo '<tr><td>' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . '</td><td>' . $count . '</td></tr>';
    }
    echo '</tbody></table>';
}
echo '</body></html>';
exit;
?>
