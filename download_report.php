<?php
session_start();
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

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="vote_report.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Category', 'File', 'Votes']);
foreach ($result as $catName => $files) {
    foreach ($files as $file => $count) {
        fputcsv($out, [$catName, $file, $count]);
    }
}
fclose($out);
exit;
?>
