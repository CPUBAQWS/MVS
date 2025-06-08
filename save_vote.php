<?php
session_start();
header('Content-Type: application/json');

$user = $_POST['user'] ?? '';
$category = $_POST['category'] ?? '';
$item = $_POST['item'] ?? '';
$action = $_POST['action'] ?? 'vote';

if (!$user || !$category || !$item) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$catFile = __DIR__ . '/data/categories.json';
$voteFile = __DIR__ . '/data/votes.json';

$categories = file_exists($catFile) ? json_decode(file_get_contents($catFile), true) : [];
$votes = file_exists($voteFile) ? json_decode(file_get_contents($voteFile), true) : [];

$rule = 'multi_unique'; // fallback
$maxVotes = 1;
foreach ($categories as $c) {
    if ($c['folder'] === $category) {
        $rule = $c['rule'];
        if (isset($c['max_votes'])) {
            $maxVotes = intval($c['max_votes']);
        }
        break;
    }
}

if ($rule === 'single') {
    $maxVotes = 1;
} elseif ($rule === 'multi_unique' && $maxVotes < 1) {
    $maxVotes = 3;
}

if (!isset($votes[$user])) {
    $votes[$user] = [];
}
if (!isset($votes[$user][$category])) {
    $votes[$user][$category] = [];
}

$userVotes = $votes[$user][$category];

if ($action === 'cancel') {
    $votes[$user][$category] = array_values(array_filter($userVotes, fn($v) => $v !== $item));
} elseif ($action === 'vote') {
    $canVote = true;
    if ($rule === 'single' && count($userVotes) >= $maxVotes) {
        $canVote = false;
    } elseif ($rule === 'multi_unique') {
        if (in_array($item, $userVotes) || count($userVotes) >= $maxVotes) {
            $canVote = false;
        }
    }

    if ($canVote) {
        $votes[$user][$category][] = $item;
    } else {
        echo json_encode(['success' => false, 'message' => 'Voting rule violated']);
        exit;
    }
}

file_put_contents($voteFile, json_encode($votes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['success' => true, 'votes' => $votes[$user][$category]]);
exit;
?>
