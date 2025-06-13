<?php
session_start();
require_once __DIR__ . '/inc/i18n.php';
if (!isset($_SESSION['user_code'])) {
    header("Location: index.php");
    exit;
}

$userCode = $_SESSION['user_code'];
$folder = $_GET['category'] ?? '';
if (!$folder || !is_dir(__DIR__ . '/Files/' . $folder)) {
    echo t('invalid_category');
    exit;
}

$categoryFile = __DIR__ . '/data/categories.json';
$votesFile = __DIR__ . '/data/votes.json';

$categories = json_decode(file_get_contents($categoryFile), true);
$votes = file_exists($votesFile) ? json_decode(file_get_contents($votesFile), true) : [];

$categoryName = $folder;
$rule = 'multi_unique';
$maxVotes = 1;
$allowVote = true;
foreach ($categories as $c) {
    if (($c['folder'] ?? '') === $folder) {
        $categoryName = $c['name'];
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

$userVotes = $votes[$userCode][$folder] ?? [];
$langAttr = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'zh';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($langAttr); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($categoryName); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<header class="bg-blue-600 text-white p-4 text-center text-xl font-semibold">
  <?php echo htmlspecialchars($categoryName); ?>
</header>

<nav class="max-w-6xl mx-auto mt-4 px-4">
  <a href="voting.php" class="text-blue-600 hover:underline">&larr; <?php echo t('back_voting_home'); ?></a>
</nav>

<section class="p-4 max-w-6xl mx-auto text-center">
  <p class="text-gray-700 mb-2"><?php echo sprintf(t('used_votes'), count($userVotes), $maxVotes); ?></p>
  <p class="text-sm text-gray-600"><?php echo sprintf(t('rule_colon'), $rule); ?></p>
  <?php if (!$allowVote): ?>
    <p class="text-red-600 text-sm mt-1"><?php echo t('category_view_only'); ?></p>
  <?php endif; ?>
</section>

<section class="p-4 mx-auto grid gap-4 grid-cols-1 sm:max-w-md md:max-w-3xl lg:max-w-6xl sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

<?php
$dir = __DIR__ . '/Files/' . $folder;
$files = array_diff(scandir($dir), ['.', '..']);
foreach ($files as $file):
  $path = "Files/$folder/$file";
  $ext = pathinfo($file, PATHINFO_EXTENSION);
  $voted = in_array($file, $userVotes);
?>
  <div class="bg-white rounded shadow p-2 flex flex-col items-center">
    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
      <img class='cursor-pointer' onclick="openModal(this.src)" src="<?php echo $path; ?>" class="w-full h-40 object-cover rounded mb-2">
    <?php elseif (in_array($ext, ['mp4'])): ?>
      <video class='cursor-pointer' onclick="openModal(this.querySelector('source').src)" class="w-full h-40 rounded mb-2 cursor-pointer" muted autoplay loop playsinline onclick="openModal('Files/B2sIF/trip.mp4')">
        <source src="<?php echo $path; ?>" type="video/mp4">
      </video>
    <?php elseif ($ext === 'pdf'): ?>
      <iframe src="<?php echo $path; ?>#toolbar=0&view=FitH" class="w-full h-40 rounded mb-2"></iframe>
      <a href="<?php echo $path; ?>" href="javascript:void(0)" onclick="openModal(this.href)" class="text-blue-600 hover:underline text-sm"><?php echo t('open_pdf'); ?></a>
    <?php elseif ($ext === 'yt'): ?>
      <?php
        $ytLink = trim(file_get_contents($dir . '/' . $file));
        preg_match('/(?:youtu\.be\/|v=|\/embed\/)([A-Za-z0-9_-]{11})/', $ytLink, $matches);
        $ytId = $matches[1] ?? $ytLink;
      ?>
      <div class="relative w-full h-40 mb-2 rounded cursor-pointer" onclick="openModal('yt:<?php echo $ytId; ?>')">
        <iframe src="https://www.youtube.com/embed/<?php echo $ytId; ?>" class="w-full h-full rounded pointer-events-none"></iframe>
      </div>
    <?php else: ?>
      <div class="text-sm text-gray-800 whitespace-pre-line max-h-40 overflow-y-auto"><?php echo htmlspecialchars(file_get_contents($dir . '/' . $file)); ?></div>
    <?php endif; ?>
    <button class="vote-btn mt-2 bg-blue-600 text-white px-3 py-1 rounded w-full text-sm <?php echo !$allowVote ? 'opacity-50' : ''; ?>"
            data-file="<?php echo $file; ?>" <?php echo !$allowVote ? 'disabled' : ''; ?>>
      <?php echo $voted ? t('cancel_vote') : t('vote_for_me'); ?>
    </button>
  </div>
<?php endforeach; ?>
</section>

<script>
const voteBtns = document.querySelectorAll(".vote-btn");
let userVotes = <?php echo json_encode($userVotes); ?>;
const rule = "<?php echo $rule; ?>";
const maxVotes = <?php echo $maxVotes; ?>;
const allowVote = <?php echo $allowVote ? 'true' : 'false'; ?>;

function updateUI() {
  document.getElementById("usedVotes").textContent = userVotes.length;
  voteBtns.forEach(btn => {
    const file = btn.dataset.file;
    const isVoted = userVotes.includes(file);
    btn.textContent = isVoted ? <?php echo json_encode(t('cancel_vote')); ?> : <?php echo json_encode(t('vote_for_me')); ?>;
    const limitReached = userVotes.length >= maxVotes && !isVoted;
    const disable = !allowVote || (rule === "single" && limitReached) || (rule === "multi_unique" && limitReached);
    btn.classList.toggle("opacity-50", disable);
    btn.disabled = disable;
  });
}

voteBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    if (!allowVote) {
      alert(<?php echo json_encode(t('vote_disabled_msg')); ?>);
      return;
    }
    const file = btn.dataset.file;
    const isVoted = userVotes.includes(file);
    const action = isVoted ? 'cancel' : 'vote';

    if (action === 'vote') {
      if (rule === "single" && userVotes.length >= maxVotes) {
        alert(<?php echo json_encode(t('vote_once_msg')); ?>);
        return;
      }
      if (rule === "multi_unique") {
        if (userVotes.includes(file)) {
          alert(<?php echo json_encode(t('no_repeat_vote_msg')); ?>);
          return;
        }
        if (userVotes.length >= maxVotes) {
          alert(<?php echo json_encode(t('votes_used_up_msg')); ?>);
          return;
        }
      }
    }

    fetch("save_vote.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        user: "<?php echo $userCode; ?>",
        category: "<?php echo $folder; ?>",
        item: file,
        action: action
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        userVotes = data.votes;
        updateUI();
      } else {
        alert(data.message || <?php echo json_encode(t('action_failed')); ?>);
      }
    });
  });
});

updateUI();
</script>

<!-- Modal Preview -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden" onclick="closeModal()">
  <div id="modal-content" class="bg-white rounded max-w-3xl w-full max-h-screen overflow-auto p-4 relative" onclick="event.stopPropagation()">
    <button onclick="closeModal()" class="absolute top-2 right-2 text-6xl font-bold text-grey-600 hover:text-red-600">×</button>
    <div id="modal-body" class="flex justify-center items-center"></div>
  </div>
</div>

<script>
function openModal(filePath) {
  const modal = document.getElementById("modal");
  const modalBody = document.getElementById("modal-body");

  let content = "";
  const isYT = filePath.startsWith('yt:') || filePath.includes('youtube.com') || filePath.includes('youtu.be');
  if (isYT) {
    let id = '';
    if (filePath.startsWith('yt:')) {
      id = filePath.slice(3);
    } else {
      try {
        const url = new URL(filePath);
        if (url.hostname.includes('youtu.be')) {
          id = url.pathname.slice(1);
        } else if (url.searchParams.get('v')) {
          id = url.searchParams.get('v');
        } else {
          id = url.pathname.split('/').pop();
        }
      } catch (e) {
        id = filePath;
      }
    }
    content = `<iframe src="https://www.youtube.com/embed/${id}" class="w-full h-[80vh] rounded" allowfullscreen></iframe>`;
  } else {
    const ext = filePath.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
      content = `<img src="${filePath}" class="max-h-[80vh] w-auto rounded">`;
    } else if (['mp4', 'mov'].includes(ext)) {
      content = '<video src="' + filePath + '" controls autoplay class="max-h-[80vh] w-full rounded"></video>';
    } else if (ext === 'pdf') {
      content = '<iframe src="' + filePath + '#toolbar=0" class="w-full h-[80vh] rounded"></iframe>';
    }
  }

  modalBody.innerHTML = content;
  modal.classList.remove("hidden");
}

function closeModal() {
  document.getElementById("modal").classList.add("hidden");
  document.getElementById("modal-body").innerHTML = "";
}
</script>
</body>
</html>
