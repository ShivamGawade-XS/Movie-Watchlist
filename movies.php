<?php
require_once __DIR__ . '/config.php';
require_login();

$q = trim($_GET['q'] ?? '');
$genre = trim($_GET['genre'] ?? '');
$params = [$_SESSION['user_id']];

$sql = "SELECT DISTINCT m.movie_id, m.title, m.release_year, m.duration_mins, m.language, m.poster_url, ROUND(AVG(r.score),1) AS avg_score, COUNT(DISTINCT r.rating_id) AS rating_count, w.status AS my_status FROM Movies m LEFT JOIN Ratings r ON r.movie_id = m.movie_id LEFT JOIN Watchlist w ON w.movie_id = m.movie_id AND w.user_id = ? LEFT JOIN MovieGenres mg ON mg.movie_id = m.movie_id LEFT JOIN Genres g ON g.genre_id = mg.genre_id WHERE 1=1";

if ($q !== '') {
    $sql .= ' AND m.title LIKE ?';
    $params[] = "%$q%";
}
if ($genre !== '') {
    $sql .= ' AND g.genre_name = ?';
    $params[] = $genre;
}
$sql .= ' GROUP BY m.movie_id ORDER BY m.title';

$movies = db_fetch_all($sql, $params);
$genres = db_fetch_all('SELECT genre_name FROM Genres ORDER BY genre_name');

$pageTitle = 'Browse Films - ' . APP_NAME;
$pageHeading = 'Browse Films';
include __DIR__ . '/partials/header.php';
?>
<div class="space-y-5" x-data="{ addModal: false, selectedMovie: null }">
  <div class="flex items-center gap-3">
    <form method="get" class="flex items-center gap-3 flex-1">
      <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by title…"
             class="bg-[#111118] border border-[#1e1e2a] rounded-lg px-4 py-2 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] w-64 transition-colors" />
      <select name="genre"
              class="bg-[#111118] border border-[#1e1e2a] rounded-lg px-3 py-2 text-sm text-[#c8c8d8] focus:outline-none focus:border-[#f59e0b66] transition-colors">
        <option value="">All Genres</option>
        <?php foreach ($genres as $g): ?>
          <option value="<?= htmlspecialchars($g['genre_name']) ?>" <?= $g['genre_name'] === $genre ? 'selected' : '' ?>><?= htmlspecialchars($g['genre_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-[#f59e0b] hover:bg-[#d97706] text-black text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Search</button>
      <?php if ($q !== '' || $genre !== ''): ?>
        <a href="movies.php" class="text-sm text-[#4a4a5e] hover:text-[#f59e0b] transition-colors">Clear</a>
      <?php endif; ?>
    </form>
    <a href="add_movie.php" class="flex items-center gap-2 text-sm text-[#4a4a5e] hover:text-[#f59e0b] transition-colors border border-[#1e1e2a] hover:border-[#f59e0b44] rounded-lg px-3 py-2">+ Add Film</a>
  </div>

  <p class="text-xs text-[#4a4a5e]"><?= count($movies) ?> film<?= count($movies) !== 1 ? 's' : '' ?></p>

  <?php if (count($movies)): ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
      <?php foreach ($movies as $m): ?>
        <div class="poster-hover bg-[#111118] border border-[#1e1e2a] rounded-xl overflow-hidden group">
          <a href="movie.php?id=<?= $m['movie_id'] ?>">
            <div class="aspect-[2/3] bg-[#16161f] flex items-center justify-center overflow-hidden">
              <?php if ($m['poster_url']): ?>
                <img src="<?= htmlspecialchars($m['poster_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="w-full h-full object-cover" onerror="this.style.display='none'" />
              <?php else: ?>
                <div class="flex flex-col items-center gap-2 text-[#4a4a5e] p-4"><span class="text-4xl">🎬</span><span class="text-xs text-center font-medium"><?= htmlspecialchars(mb_strimwidth($m['title'], 0, 20, '...')) ?></span></div>
              <?php endif; ?>
            </div>
          </a>
          <div class="p-3">
            <a href="movie.php?id=<?= $m['movie_id'] ?>" class="text-sm font-medium text-[#f0f0f8] hover:text-[#f59e0b] transition-colors line-clamp-1 block"><?= htmlspecialchars($m['title']) ?></a>
            <p class="text-xs text-[#4a4a5e] mt-0.5"><?= $m['release_year'] ?: '—' ?></p>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-[#f59e0b]"><?= $m['avg_score'] ? '⭐ ' . htmlspecialchars($m['avg_score']) : 'No ratings' ?></span>
              <?php if ($m['my_status']): ?>
                <span class="text-xs px-1.5 py-0.5 rounded <?= $m['my_status'] === 'watched' ? 'badge-watched' : ($m['my_status'] === 'want_to_watch' ? 'badge-want' : 'badge-dropped') ?>"><?= htmlspecialchars(str_replace('_', ' ', $m['my_status'])) ?></span>
              <?php endif; ?>
            </div>
            <form action="watchlist_add.php" method="post" class="mt-2">
              <input type="hidden" name="movie_id" value="<?= $m['movie_id'] ?>" />
              <select name="status" class="w-full bg-[#16161f] border border-[#1e1e2a] text-[#4a4a5e] text-xs rounded px-2 py-1 focus:outline-none focus:border-[#f59e0b44] mb-1">
                <option value="want_to_watch">Want to Watch</option>
                <option value="watched">Watched</option>
                <option value="dropped">Dropped</option>
              </select>
              <button type="submit" class="w-full bg-[#16161f] hover:bg-[#f59e0b22] border border-[#1e1e2a] hover:border-[#f59e0b44] text-[#4a4a5e] hover:text-[#f59e0b] text-xs py-1 rounded transition-colors">+ Watchlist</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-20">
      <span class="text-5xl block mb-3">🎬</span>
      <p class="text-[#4a4a5e]">No films found.
        <?php if ($q !== '' || $genre !== ''): ?><a href="movies.php" class="text-[#f59e0b] hover:underline">Clear filters</a><?php else: ?><a href="add_movie.php" class="text-[#f59e0b] hover:underline">Add the first one</a>.<?php endif; ?>
      </p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
