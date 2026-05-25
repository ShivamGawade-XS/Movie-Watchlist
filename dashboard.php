<?php
require_once __DIR__ . '/config.php';
require_login();
$uid = $_SESSION['user_id'];

$stats = db_fetch_one(
    'SELECT total_watched, avg_rating, total_hours, fav_genre FROM user_stats WHERE user_id = ?',
    [$uid]
);
if (!$stats) {
    $stats = ['total_watched' => 0, 'avg_rating' => 0, 'total_hours' => 0, 'fav_genre' => '—'];
}

$recent = db_fetch_all(
    'SELECT w.status, w.added_at, m.title, m.poster_url, m.movie_id FROM Watchlist w JOIN Movies m ON m.movie_id = w.movie_id WHERE w.user_id = ? ORDER BY w.added_at DESC LIMIT 5',
    [$uid]
);

$genre_data = db_fetch_all(
    'SELECT g.genre_name, COUNT(*) AS cnt FROM WatchHistory wh JOIN MovieGenres mg ON mg.movie_id = wh.movie_id JOIN Genres g ON g.genre_id = mg.genre_id WHERE wh.user_id = ? GROUP BY g.genre_name ORDER BY cnt DESC LIMIT 6',
    [$uid]
);

$monthly = db_fetch_all(
    "SELECT DATE_FORMAT(watched_on, '%b %Y') AS month_label, YEAR(watched_on) AS yr, MONTH(watched_on) AS mo, COUNT(*) AS cnt FROM WatchHistory WHERE user_id = ? GROUP BY yr, mo ORDER BY yr DESC, mo DESC LIMIT 6",
    [$uid]
);
$monthly = array_reverse($monthly);

$pageTitle = 'Dashboard - ' . APP_NAME;
$pageHeading = 'Dashboard';
include __DIR__ . '/partials/header.php';
?>
<div class="space-y-6">
  <div>
    <h2 class="text-2xl font-semibold text-[#f0f0f8]">
      Hello, <span class="text-[#f59e0b]"><?= htmlspecialchars($_SESSION['username']) ?></span> 👋
    </h2>
    <p class="text-sm text-[#4a4a5e] mt-0.5">Here's your film universe at a glance.</p>
  </div>

  <div class="grid grid-cols-4 gap-4">
    <?php $cards = [
      ['Films Watched', $stats['total_watched'], 'text-[#f59e0b]', '🎬'],
      ['Avg Rating', $stats['avg_rating'] ?: '—', 'text-emerald-400', '⭐'],
      ['Hours Watched', round($stats['total_hours'], 1), 'text-sky-400', '⏱️'],
      ['Fav Genre', $stats['fav_genre'] ?: '—', 'text-purple-400', '🎭'],
    ]; ?>
    <?php foreach ($cards as $card): ?>
      <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5 hover:border-[#2a2a3a] transition-colors">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs text-[#4a4a5e] uppercase tracking-wider"><?= htmlspecialchars($card[0]) ?></p>
            <p class="text-2xl font-bold <?= $card[2] ?> mt-1.5"><?= htmlspecialchars($card[1]) ?></p>
          </div>
          <span class="text-2xl opacity-60"><?= $card[3] ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5">
      <h3 class="text-sm font-medium text-[#f0f0f8] mb-4">Monthly Watch Activity</h3>
      <?php if (count($monthly)): ?>
        <canvas id="monthlyChart" height="180"></canvas>
      <?php else: ?>
        <div class="h-44 flex items-center justify-center text-[#4a4a5e] text-sm">No watch history yet.</div>
      <?php endif; ?>
    </div>

    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5">
      <h3 class="text-sm font-medium text-[#f0f0f8] mb-4">Genre Breakdown</h3>
      <?php if (count($genre_data)): ?>
        <div class="flex items-center gap-4">
          <canvas id="genreChart" width="160" height="160" style="max-width:160px;max-height:160px;"></canvas>
          <div class="flex-1 space-y-1.5">
            <?php foreach ($genre_data as $row): ?>
              <div class="flex items-center justify-between text-xs">
                <span class="text-[#c8c8d8]"><?= htmlspecialchars($row['genre_name']) ?></span>
                <span class="text-[#4a4a5e]"><?= htmlspecialchars($row['cnt']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="h-44 flex items-center justify-center text-[#4a4a5e] text-sm">Watch some films to see genres.</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-medium text-[#f0f0f8]">Recent Activity</h3>
      <a href="watchlist.php" class="text-xs text-[#f59e0b] hover:underline">View all →</a>
    </div>

    <?php if (count($recent)): ?>
      <div class="space-y-2">
        <?php foreach ($recent as $item): ?>
          <div class="flex items-center gap-4 py-2 border-b border-[#1e1e2a] last:border-0">
            <div class="w-8 h-10 bg-[#16161f] rounded flex items-center justify-center text-[#4a4a5e] text-lg flex-shrink-0">🎬</div>
            <div class="flex-1 min-w-0">
              <a href="movie.php?id=<?= $item['movie_id'] ?>" class="text-sm font-medium text-[#f0f0f8] hover:text-[#f59e0b] truncate block transition-colors"><?= htmlspecialchars($item['title']) ?></a>
              <p class="text-xs text-[#4a4a5e]"><?= date('M d, Y', strtotime($item['added_at'])) ?></p>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full <?= $item['status'] === 'watched' ? 'badge-watched' : ($item['status'] === 'want_to_watch' ? 'badge-want' : 'badge-dropped') ?>">
              <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $item['status']))) ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-sm text-[#4a4a5e] text-center py-8">No activity yet. <a href="movies.php" class="text-[#f59e0b] hover:underline">Browse films</a> to get started.</p>
    <?php endif; ?>
  </div>
</div>

<?php
$monthlyLabels = json_encode(array_column($monthly, 'month_label'));
$monthlyCounts = json_encode(array_column($monthly, 'cnt'));
$genreLabels = json_encode(array_column($genre_data, 'genre_name'));
$genreCounts = json_encode(array_column($genre_data, 'cnt'));
$footerScripts = "<script>
const chartDefaults = {
  color: '#4a4a5e',
  plugins: { legend: { display: false } },
  scales: {
    x: { ticks: { color:'#4a4a5e', font:{ size:10 } }, grid: { color:'#1e1e2a' } },
    y: { ticks: { color:'#4a4a5e', font:{ size:10 } }, grid: { color:'#1e1e2a' } }
  }
};

if ({$monthlyLabels}.length) {
  new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
      labels: {$monthlyLabels},
      datasets: [{
        data: {$monthlyCounts},
        backgroundColor: '#f59e0b33',
        borderColor: '#f59e0b',
        borderWidth: 1.5,
        borderRadius: 4,
      }]
    },
    options: { ...chartDefaults, plugins: { legend: { display: false } } }
  });
}

if ({$genreLabels}.length) {
  new Chart(document.getElementById('genreChart'), {
    type: 'doughnut',
    data: {
      labels: {$genreLabels},
      datasets: [{
        data: {$genreCounts},
        backgroundColor: ['#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#ec4899'],
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: { cutout: '65%', plugins: { legend: { display: false } } }
  });
}
</script>";
include __DIR__ . '/partials/footer.php';
?>
