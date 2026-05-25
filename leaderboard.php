<?php
require_once __DIR__ . '/config.php';
require_login();

$movies = db_fetch_all(
    'SELECT m.movie_id, m.title, m.release_year, m.poster_url, ROUND(AVG(r.score),2) AS avg_score, COUNT(r.rating_id) AS votes, RANK() OVER (ORDER BY AVG(r.score) DESC) AS rnk FROM Movies m JOIN Ratings r ON r.movie_id = m.movie_id GROUP BY m.movie_id HAVING votes >= 1 ORDER BY avg_score DESC LIMIT 20'
);

$pageTitle = 'Top Rated Films - ' . APP_NAME;
$pageHeading = 'Top Rated Films';
include __DIR__ . '/partials/header.php';
?>
<div class="space-y-5">
  <p class="text-sm text-[#4a4a5e]">Community rankings based on all user ratings.</p>

  <?php if (count($movies)): ?>
    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-[#1e1e2a]">
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3 w-12">Rank</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Film</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Year</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Avg Score</th>
            <th class="text-left text-xs text-[#4a4a5e] uppercase tracking-wider px-5 py-3">Votes</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($movies as $m): ?>
            <tr class="border-b border-[#1e1e2a] last:border-0 hover:bg-[#16161f] transition-colors">
              <td class="px-5 py-3">
                <?php if ($m['rnk'] == 1): ?>
                  <span class="font-display text-xl text-[#f59e0b]">01</span>
                <?php elseif ($m['rnk'] == 2): ?>
                  <span class="font-display text-xl text-[#9ca3af]">02</span>
                <?php elseif ($m['rnk'] == 3): ?>
                  <span class="font-display text-xl text-[#b45309]">03</span>
                <?php else: ?>
                  <span class="font-display text-lg text-[#4a4a5e]"><?= sprintf('%02d', $m['rnk']) ?></span>
                <?php endif; ?>
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-11 bg-[#16161f] rounded overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <?php if ($m['poster_url']): ?>
                      <img src="<?= htmlspecialchars($m['poster_url']) ?>" class="w-full h-full object-cover" onerror="this.style.display='none'" />
                    <?php else: ?>
                      <span class="text-[#4a4a5e] text-xs">🎬</span>
                    <?php endif; ?>
                  </div>
                  <a href="movie.php?id=<?= $m['movie_id'] ?>" class="text-sm font-medium text-[#f0f0f8] hover:text-[#f59e0b] transition-colors"><?= htmlspecialchars($m['title']) ?></a>
                </div>
              </td>
              <td class="px-5 py-3 text-sm text-[#c8c8d8]"><?= $m['release_year'] ?: '—' ?></td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  <div class="w-24 bg-[#1e1e2a] rounded-full h-1.5">
                    <div class="bg-[#f59e0b] h-1.5 rounded-full" style="width: <?= round(($m['avg_score'] ?? 0) / 10 * 100) ?>%"></div>
                  </div>
                  <span class="text-sm font-bold text-[#f59e0b]"><?= $m['avg_score'] ?></span>
                </div>
              </td>
              <td class="px-5 py-3 text-sm text-[#4a4a5e]"><?= $m['votes'] ?></td>
              <td class="px-5 py-3"><a href="movie.php?id=<?= $m['movie_id'] ?>" class="text-xs text-[#4a4a5e] hover:text-[#f59e0b] transition-colors">View →</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center py-20">
      <span class="text-5xl block mb-3">🏆</span>
      <p class="text-[#4a4a5e] text-sm">No ratings yet. <a href="movies.php" class="text-[#f59e0b] hover:underline">Rate some films</a> to build the leaderboard.</p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
