<?php
require_once __DIR__ . '/config.php';
require_login();

$status = $_GET['status'] ?? 'all';
$uid = $_SESSION['user_id'];
$params = [$uid];

$sql = "SELECT w.wl_id, w.status, w.added_at, m.movie_id, m.title, m.release_year, m.poster_url, m.duration_mins, r.score AS my_score FROM Watchlist w JOIN Movies m ON m.movie_id = w.movie_id LEFT JOIN Ratings r ON r.movie_id = w.movie_id AND r.user_id = w.user_id WHERE w.user_id = ?";
if ($status !== 'all') {
    $sql .= ' AND w.status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY w.added_at DESC';

$items = db_fetch_all($sql, $params);
$counts = db_fetch_all('SELECT status, COUNT(*) AS cnt FROM Watchlist WHERE user_id = ? GROUP BY status', [$uid]);
$countMap = [];
foreach ($counts as $row) {
    $countMap[$row['status']] = $row['cnt'];
}

$pageTitle = 'My Watchlist - ' . APP_NAME;
$pageHeading = 'My Watchlist';
include __DIR__ . '/partials/header.php';
?>
<div class="space-y-5">
  <div class="flex items-center gap-1 border-b border-[#1e1e2a] pb-0">
    <?php $tabs = [ ['all','All'], ['want_to_watch','Want to Watch'], ['watched','Watched'], ['dropped','Dropped'] ]; ?>
    <?php foreach ($tabs as $tab): ?>
      <a href="watchlist.php?status=<?= $tab[0] ?>" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px <?= $status === $tab[0] ? 'border-[#f59e0b] text-[#f59e0b]' : 'border-transparent text-[#4a4a5e] hover:text-[#c8c8d8]' ?>">
        <?= htmlspecialchars($tab[1]) ?>
        <?php if ($tab[0] === 'all'): ?>
          <span class="ml-1 text-xs bg-[#1e1e2a] px-1.5 py-0.5 rounded"><?= array_sum($countMap) ?></span>
        <?php elseif (isset($countMap[$tab[0]])): ?>
          <span class="ml-1 text-xs bg-[#1e1e2a] px-1.5 py-0.5 rounded"><?= $countMap[$tab[0]] ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (count($items)): ?>
    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-[#1e1e2a]">
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Film</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Year</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">My Score</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Status</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Added</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr class="border-b border-[#1e1e2a] last:border-0 hover:bg-[#16161f] transition-colors">
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-11 bg-[#16161f] rounded overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <?php if ($item['poster_url']): ?>
                      <img src="<?= htmlspecialchars($item['poster_url']) ?>" class="w-full h-full object-cover" onerror="this.style.display='none'" />
                    <?php else: ?>
                      <span class="text-[#4a4a5e]">🎬</span>
                    <?php endif; ?>
                  </div>
                  <div>
                    <a href="movie.php?id=<?= $item['movie_id'] ?>" class="text-sm font-medium text-[#f0f0f8] hover:text-[#f59e0b] transition-colors"><?= htmlspecialchars($item['title']) ?></a>
                    <p class="text-xs text-[#4a4a5e]"><?= $item['duration_mins'] ?: '—' ?> min</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-3 text-sm text-[#c8c8d8]"><?= $item['release_year'] ?: '—' ?></td>
              <td class="px-5 py-3 text-sm"><?= $item['my_score'] ? '<span class="text-[#f59e0b]">⭐ ' . htmlspecialchars($item['my_score']) . '/10</span>' : '<span class="text-[#4a4a5e]">—</span>' ?></td>
              <td class="px-5 py-3"><span class="text-xs px-2 py-0.5 rounded-full <?= $item['status'] === 'watched' ? 'badge-watched' : ($item['status'] === 'want_to_watch' ? 'badge-want' : 'badge-dropped') ?>"><?= htmlspecialchars(str_replace('_', ' ', $item['status'])) ?></span></td>
              <td class="px-5 py-3 text-xs text-[#4a4a5e]"><?= date('M d', strtotime($item['added_at'])) ?></td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  <div x-data="{ open: false }" class="relative">
                    <button @click="open=!open" class="text-xs text-[#4a4a5e] hover:text-[#f59e0b] transition-colors">Edit</button>
                    <div x-show="open" @click.outside="open=false" class="absolute right-0 top-6 z-10 bg-[#16161f] border border-[#1e1e2a] rounded-lg shadow-xl w-40">
                      <?php foreach (['watched','want_to_watch','dropped'] as $opt): ?>
                        <form action="watchlist_update.php" method="post">
                          <input type="hidden" name="movie_id" value="<?= $item['movie_id'] ?>" />
                          <input type="hidden" name="status" value="<?= $opt ?>" />
                          <button type="submit" class="w-full text-left text-xs px-3 py-2 text-[#c8c8d8] hover:bg-[#1e1e2a] hover:text-[#f59e0b] transition-colors <?= $item['status'] === $opt ? 'text-[#f59e0b]' : '' ?>"><?= htmlspecialchars(str_replace('_', ' ', $opt)) ?></button>
                        </form>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <form action="watchlist_remove.php" method="post" onsubmit="return confirm('Remove from watchlist?');">
                    <input type="hidden" name="wl_id" value="<?= $item['wl_id'] ?>" />
                    <button type="submit" class="text-xs text-[#4a4a5e] hover:text-red-400 transition-colors">✕</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center py-20">
      <span class="text-5xl block mb-3">📋</span>
      <p class="text-[#4a4a5e] text-sm">No films in this list yet. <a href="movies.php" class="text-[#f59e0b] hover:underline">Browse films</a>.</p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
