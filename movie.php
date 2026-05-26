<?php
require_once __DIR__ . '/config.php';
require_login();

$mid = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($mid <= 0) {
    flash('Movie not found.', 'error');
    redirect('movies.php');
}

$movie = db_fetch_one(
    'SELECT m.*, ROUND(AVG(r.score),1) AS avg_score, COUNT(DISTINCT r.rating_id) AS rating_count FROM Movies m LEFT JOIN Ratings r ON r.movie_id = m.movie_id WHERE m.movie_id = ? GROUP BY m.movie_id',
    [$mid]
);
if (!$movie) {
    flash('Movie not found.', 'error');
    redirect('movies.php');
}

$genres = db_fetch_all('SELECT g.genre_name FROM Genres g JOIN MovieGenres mg ON mg.genre_id = g.genre_id WHERE mg.movie_id = ?', [$mid]);
$reviews = db_fetch_all('SELECT rv.review_text, rv.created_at, u.username FROM Reviews rv JOIN Users u ON u.user_id = rv.user_id WHERE rv.movie_id = ? ORDER BY rv.created_at DESC', [$mid]);
$my_rating = db_fetch_one('SELECT score FROM Ratings WHERE user_id = ? AND movie_id = ?', [$_SESSION['user_id'], $mid]);
$my_status = db_fetch_one('SELECT status FROM Watchlist WHERE user_id = ? AND movie_id = ?', [$_SESSION['user_id'], $mid]);

$pageTitle = htmlspecialchars($movie['title']) . ' - ' . APP_NAME;
$pageHeading = 'Film Detail';
include __DIR__ . '/partials/header.php';
?>
<div class="max-w-4xl space-y-6" x-data="{ ratingVal: <?= $my_rating['score'] ?? 0 ?> }">
  <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl overflow-hidden">
    <div class="flex gap-6 p-6">
      <div class="w-36 flex-shrink-0">
        <div class="aspect-[2/3] bg-[#16161f] rounded-lg overflow-hidden flex items-center justify-center">
          <?php if ($movie['poster_url']): ?>
            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="w-full h-full object-cover" onerror="this.style.display='none'" />
          <?php else: ?>
            <span class="text-5xl">🎬</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex-1 min-w-0">
        <h2 class="font-display text-4xl text-[#f0f0f8] tracking-wide leading-none"><?= htmlspecialchars($movie['title']) ?></h2>
        <div class="flex items-center gap-4 mt-2 text-sm text-[#4a4a5e]">
          <?php if ($movie['release_year']): ?><span><?= htmlspecialchars($movie['release_year']) ?></span><?php endif; ?>
          <?php if ($movie['duration_mins']): ?><span><?= htmlspecialchars($movie['duration_mins']) ?> min</span><?php endif; ?>
          <?php if ($movie['language']): ?><span><?= htmlspecialchars($movie['language']) ?></span><?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-2 mt-3">
          <?php foreach ($genres as $g): ?>
            <span class="text-xs px-2 py-0.5 bg-[#16161f] border border-[#1e1e2a] rounded-full text-[#c8c8d8]"><?= htmlspecialchars($g['genre_name']) ?></span>
          <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-2 mt-4">
          <span class="text-3xl font-bold text-[#f59e0b]"><?= $movie['avg_score'] ?: '—' ?></span>
          <div>
            <p class="text-xs text-[#4a4a5e]">Average score</p>
            <p class="text-xs text-[#4a4a5e]"><?= htmlspecialchars($movie['rating_count']) ?> rating<?= $movie['rating_count'] != 1 ? 's' : '' ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5">
      <h3 class="text-sm font-medium text-[#f0f0f8] mb-3">My Watchlist Status</h3>
      <form action="watchlist_add.php" method="post" class="flex items-center gap-3">
        <input type="hidden" name="movie_id" value="<?= $mid ?>" />
        <select name="status" class="flex-1 bg-[#16161f] border border-[#1e1e2a] rounded-lg px-3 py-2 text-sm text-[#c8c8d8] focus:outline-none focus:border-[#f59e0b66] transition-colors">
          <option value="want_to_watch" <?= ($my_status['status'] ?? '') === 'want_to_watch' ? 'selected' : '' ?>>Want to Watch</option>
          <option value="watched" <?= ($my_status['status'] ?? '') === 'watched' ? 'selected' : '' ?>>Watched</option>
          <option value="dropped" <?= ($my_status['status'] ?? '') === 'dropped' ? 'selected' : '' ?>>Dropped</option>
        </select>
        <button type="submit" class="bg-[#f59e0b] hover:bg-[#d97706] text-black text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Save</button>
      </form>
      <?php if (!empty($my_status['status'])): ?>
        <p class="text-xs text-[#4a4a5e] mt-2">Current: <span class="text-[#f59e0b]"><?= htmlspecialchars(str_replace('_', ' ', $my_status['status'])) ?></span></p>
      <?php endif; ?>
    </div>

    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5">
      <h3 class="text-sm font-medium text-[#f0f0f8] mb-3">My Rating</h3>
      <form action="rate.php" method="post" class="space-y-3" x-data="{ selected: <?= (int)($my_rating['score'] ?? 0) ?>, hovered: 0 }">
        <input type="hidden" name="movie_id" value="<?= $mid ?>" />
        <div class="flex items-center gap-1">
          <?php for ($i = 1; $i <= 10; $i++): ?>
            <label class="cursor-pointer" @mouseenter="hovered = <?= $i ?>" @mouseleave="hovered = 0">
              <input type="radio" name="score" value="<?= $i ?>" class="hidden" x-model="selected" :value="<?= $i ?>" <?= (!empty($my_rating['score']) && $my_rating['score'] == $i) ? 'checked' : '' ?> />
              <span class="text-lg transition-colors" :class="(hovered ? hovered >= <?= $i ?> : selected >= <?= $i ?>) ? 'text-[#f59e0b]' : 'text-[#2a2a3a]'">★</span>
            </label>
          <?php endfor; ?>
          <span class="text-xs text-[#4a4a5e] ml-2" x-text="selected ? selected + '/10' : 'Not rated'"></span>
        </div>
        <button type="submit" class="bg-[#16161f] hover:bg-[#f59e0b22] border border-[#1e1e2a] hover:border-[#f59e0b44] text-[#c8c8d8] hover:text-[#f59e0b] text-sm px-4 py-1.5 rounded-lg transition-colors">Submit Rating</button>
      </form>
    </div>
  </div>

  <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-5" x-data="{ showForm: false }">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-medium text-[#f0f0f8]">Reviews (<?= count($reviews) ?>)</h3>
      <button @click="showForm = !showForm" class="text-xs text-[#f59e0b] hover:underline">+ Write a review</button>
    </div>
    <div x-show="showForm" x-transition class="mb-5">
      <form action="review.php" method="post" class="space-y-3">
        <input type="hidden" name="movie_id" value="<?= $mid ?>" />
        <textarea name="review_text" rows="3" placeholder="Share your thoughts on this film…" class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-3 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] resize-none transition-colors"></textarea>
        <button type="submit" class="bg-[#f59e0b] hover:bg-[#d97706] text-black text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Post Review</button>
      </form>
    </div>
    <?php if (count($reviews)): ?>
      <div class="space-y-4">
        <?php foreach ($reviews as $rv): ?>
          <div class="border-b border-[#1e1e2a] pb-4 last:border-0 last:pb-0">
            <div class="flex items-center gap-2 mb-1.5">
              <div class="w-6 h-6 rounded-full bg-[#f59e0b22] border border-[#f59e0b44] flex items-center justify-center">
                <span class="text-[#f59e0b] text-xs font-bold"><?= strtoupper(substr($rv['username'], 0, 1)) ?></span>
              </div>
              <span class="text-sm font-medium text-[#f0f0f8]"><?= htmlspecialchars($rv['username']) ?></span>
              <span class="text-xs text-[#4a4a5e]">· <?= date('M d, Y', strtotime($rv['created_at'])) ?></span>
            </div>
            <p class="text-sm text-[#c8c8d8] leading-relaxed"><?= nl2br(htmlspecialchars($rv['review_text'])) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-sm text-[#4a4a5e] text-center py-6">No reviews yet. Be the first!</p>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
