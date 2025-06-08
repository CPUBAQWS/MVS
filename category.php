<?php
session_start();
if (!isset($_SESSION['user_code'])) {
    header("Location: index.html");
    exit;
}

$userCode = $_SESSION['user_code'];
$folder = $_GET['category'] ?? '';
if (!$folder || !is_dir(__DIR__ . '/Files/' . $folder)) {
    echo "無效的分類";
    exit;
}

$categoryFile = __DIR__ . '/data/categories.json';
$votesFile = __DIR__ . '/data/votes.json';

$categories = json_decode(file_get_contents($categoryFile), true);
$votes = file_exists($votesFile) ? json_decode(file_get_contents($votesFile), true) : [];

$categoryName = $folder;
$rule = 'multi_unique';
$maxVotes = 1;
$enabled = true;
foreach ($categories as $c) {
    if (($c['folder'] ?? '') === $folder) {
        $categoryName = $c['name'];
        $rule = $c['rule'];
        if (isset($c['max_votes'])) {
            $maxVotes = intval($c['max_votes']);
        }
        $rule = $c['rule'] ?? $c['voting_rule'] ?? 'multi_unique';
        $enabled = $c['enabled'] ?? true;
        break;
    }
}

if ($rule === 'single') {
    $maxVotes = 1;
} elseif ($rule === 'multi_unique' && $maxVotes < 1) {
    $maxVotes = 3;
if (!$enabled) {
    echo "此分類目前未開放投票";
    exit;
}

$userVotes = $votes[$userCode][$folder] ?? [];
?>
<!DOCTYPE html>
<html lang="zh">
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
  <a href="voting.php" class="text-blue-600 hover:underline">&larr; 回到投票主頁</a>
</nav>

<section class="p-4 max-w-6xl mx-auto text-center">
  <p class="text-gray-700 mb-2">你已使用 <strong id="usedVotes"><?php echo count($userVotes); ?></strong> / <?php echo $maxVotes; ?> 票。</p>
  <p class="text-sm text-gray-600">規則：<?php echo $rule; ?></p>
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
      <a href="<?php echo $path; ?>" href="javascript:void(0)" onclick="openModal(this.href)" class="text-blue-600 hover:underline text-sm">📄 開啟PDF全文</a>
    <?php else: ?>
      <div class="text-sm text-gray-800 whitespace-pre-line max-h-40 overflow-y-auto"><?php echo htmlspecialchars(file_get_contents($dir . '/' . $file)); ?></div>
    <?php endif; ?>
    <button class="vote-btn mt-2 bg-blue-600 text-white px-3 py-1 rounded w-full text-sm"
            data-file="<?php echo $file; ?>">
      <?php echo $voted ? '取消投票' : '投我一票'; ?>
    </button>
  </div>
<?php endforeach; ?>
</section>

<script>
const voteBtns = document.querySelectorAll(".vote-btn");
let userVotes = <?php echo json_encode($userVotes); ?>;
const rule = "<?php echo $rule; ?>";
const maxVotes = <?php echo $maxVotes; ?>;

function updateUI() {
  document.getElementById("usedVotes").textContent = userVotes.length;
  voteBtns.forEach(btn => {
    const file = btn.dataset.file;
    const isVoted = userVotes.includes(file);
    btn.textContent = isVoted ? "取消投票" : "投我一票";
    const limitReached = userVotes.length >= maxVotes && !isVoted;
    const disable = (rule === "single" && limitReached) || (rule === "multi_unique" && limitReached);
    btn.classList.toggle("opacity-50", disable);
    btn.disabled = disable;
  });
}

voteBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    const file = btn.dataset.file;
    const isVoted = userVotes.includes(file);
    const action = isVoted ? 'cancel' : 'vote';

    if (action === 'vote') {
      if (rule === "single" && userVotes.length >= maxVotes) {
        alert("你只能投一票");
        return;
      }
      if (rule === "multi_unique") {
        if (userVotes.includes(file)) {
          alert("你不能重複投相同作品");
          return;
        }
        if (userVotes.length >= maxVotes) {
          alert("你的票數已用完");
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
        alert(data.message || "操作失敗");
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
  const ext = filePath.split('.').pop().toLowerCase();

  let content = "";
  if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
    content = `<img src="${filePath}" class="max-h-[80vh] w-auto rounded">`;
  } else if (['mp4', 'mov'].includes(ext)) {
    content = '<video src="' + filePath + '" controls autoplay class="max-h-[80vh] w-full rounded"></video>';
  } else if (ext === 'pdf') {
    content = '<iframe src="' + filePath + '#toolbar=0" class="w-full h-[80vh] rounded"></iframe>';
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
