<?php
session_start();
require_once __DIR__ . '/inc/i18n.php';
header('Content-Type: application/json');

$user = $_POST['user'] ?? '';
$category = $_POST['category'] ?? '';
$item = $_POST['item'] ?? '';
$action = $_POST['action'] ?? 'vote';

if (!$user || !$category || !$item) {
    echo json_encode(['success' => false, 'message' => t('missing_fields')]);
    exit;
}

$catFile = __DIR__ . '/data/categories.json';
$voteFile = __DIR__ . '/data/votes.json';

$categories = file_exists($catFile) ? json_decode(file_get_contents($catFile), true) : [];
$votes = file_exists($voteFile) ? json_decode(file_get_contents($voteFile), true) : [];

$rule = 'multi_unique'; // fallback
$maxVotes = 1;
$allowVote = true;
foreach ($categories as $c) {
    if ($c['folder'] === $category) {
        $rule = $c['rule'];
        if (isset($c['allow_vote'])) {
            $allowVote = (bool)$c['allow_vote'];
        }
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

if (!$allowVote) {
    echo json_encode(['success' => false, 'message' => t('voting_disabled')]);
    exit;
}

if (!isset($votes[$user])) {
    $votes[$user] = [];
}
if (!isset($votes[$user][$category])) {
    $votes[$user][$category] = [];
}

$userVotes = $votes[$user][$category];

if ($action === 'cancel') {
    if ($rule === 'multi_repeat') {
        $index = array_search($item, $userVotes);
        if ($index !== false) {
            unset($userVotes[$index]);
            $votes[$user][$category] = array_values($userVotes);
        }
    } else {
        $votes[$user][$category] = array_values(array_filter($userVotes, fn($v) => $v !== $item));
    }
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
        echo json_encode(['success' => false, 'message' => t('voting_rule_violated')]);
        exit;
    }
}

file_put_contents($voteFile, json_encode($votes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['success' => true, 'votes' => $votes[$user][$category]]);
exit;
?>
