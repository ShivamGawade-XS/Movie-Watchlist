<?php
require_once __DIR__ . '/config.php';
require_login();

$genres = db_fetch_all('SELECT * FROM Genres ORDER BY genre_name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $year = trim($_POST['release_year'] ?? '');
    $duration = trim($_POST['duration_mins'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $poster = trim($_POST['poster_url'] ?? '');
    $genreIds = $_POST['genres'] ?? [];

    if ($title === '') {
        flash('Title is required.', 'error');
    } else {
        $mid = db_execute('INSERT INTO Movies (title, release_year, duration_mins, language, poster_url) VALUES (?, ?, ?, ?, ?)', [$title, $year ?: null, $duration ?: null, $language ?: null, $poster ?: null]);
        foreach ((array) $genreIds as $gid) {
            $gid = (int) $gid;
            if ($gid > 0) {
                db_execute('INSERT IGNORE INTO MovieGenres (movie_id, genre_id) VALUES (?, ?)', [$mid, $gid]);
            }
        }
        flash('"' . htmlspecialchars($title) . '" added!', 'success');
        redirect('movies.php');
    }
}

$pageTitle = 'Add Film - ' . APP_NAME;
$pageHeading = 'Add Film';
include __DIR__ . '/partials/header.php';
?>
<div class="max-w-xl">
  <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-6">
    <h2 class="text-[#f0f0f8] font-semibold mb-5">Add a new film to the database</h2>
    <form method="post" class="space-y-4">
      <div>
        <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Title *</label>
        <input name="title" type="text" required class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" placeholder="e.g. The Godfather" />
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Release Year</label>
          <input name="release_year" type="number" min="1888" max="2030" class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" placeholder="2024" />
        </div>
        <div>
          <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Duration (mins)</label>
          <input name="duration_mins" type="number" min="1" class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" placeholder="120" />
        </div>
      </div>
      <div>
        <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Language</label>
        <input name="language" type="text" class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" placeholder="English" />
      </div>
      <div>
        <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Poster URL</label>
        <input name="poster_url" type="url" class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" placeholder="https://…" />
      </div>
      <div>
        <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-2">Genres</label>
        <div class="grid grid-cols-3 gap-2">
          <?php foreach ($genres as $g): ?>
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="checkbox" name="genres[]" value="<?= $g['genre_id'] ?>" class="w-3.5 h-3.5 accent-[#f59e0b]" />
              <span class="text-sm text-[#c8c8d8] group-hover:text-[#f0f0f8] transition-colors"><?= htmlspecialchars($g['genre_name']) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-[#f59e0b] hover:bg-[#d97706] text-black font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors">Add Film</button>
        <a href="movies.php" class="bg-[#16161f] border border-[#1e1e2a] text-[#c8c8d8] hover:text-[#f0f0f8] px-5 py-2.5 rounded-lg text-sm transition-colors">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
