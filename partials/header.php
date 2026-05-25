<?php
$pageTitle = $pageTitle ?? APP_NAME;
$currentPage = basename($_SERVER['PHP_SELF']);
$user = current_user();
$flashMessages = get_flash();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'DM Sans', sans-serif; }
    .font-display { font-family: 'Bebas Neue', sans-serif; }
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: #0a0a0f; }
    ::-webkit-scrollbar-thumb { background: #f59e0b44; border-radius: 2px; }
    .nav-link.active { color: #f59e0b; border-left: 2px solid #f59e0b; background: #f59e0b11; }
    .badge-watched      { background:#064e3b; color:#6ee7b7; }
    .badge-want         { background:#1e3a5f; color:#93c5fd; }
    .badge-dropped      { background:#3b1111; color:#fca5a5; }
    .toast-enter        { animation: slideIn .3s ease; }
    @keyframes slideIn  { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    .film-grain::before {
      content:''; position:fixed; inset:0; pointer-events:none; z-index:999;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
      opacity: 0.4;
    }
    .poster-hover { transition: transform .2s ease, box-shadow .2s ease; }
    .poster-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 40px #f59e0b22; }
    .star-btn { cursor:pointer; transition: color .15s; }
    .star-btn:hover ~ .star-btn,
    .star-btn:hover { color: #f59e0b !important; }
  </style>
</head>
<body class="film-grain bg-[#0a0a0f] text-[#c8c8d8] h-full font-body" x-data="{ sidebarOpen: true }">

<?php if (is_logged_in()): ?>
<div class="flex h-screen overflow-hidden">
  <aside class="w-56 flex-shrink-0 bg-[#111118] border-r border-[#1e1e2a] flex flex-col">
    <div class="px-5 py-5 border-b border-[#1e1e2a]">
      <span class="font-display text-3xl text-[#f59e0b] tracking-widest">CINELOG</span>
      <p class="text-xs text-[#4a4a5e] mt-0.5">Movie Tracker</p>
    </div>
    <nav class="flex-1 px-2 py-4 space-y-0.5">
      <?php $links = [
        ['href' => 'dashboard.php',   'label' => 'Dashboard'],
        ['href' => 'movies.php',      'label' => 'Browse Films'],
        ['href' => 'watchlist.php',   'label' => 'My Watchlist'],
        ['href' => 'leaderboard.php', 'label' => 'Top Rated'],
        ['href' => 'add_movie.php',   'label' => 'Add Film'],
      ]; ?>
      <?php foreach ($links as $link): ?>
        <a href="<?= $link['href'] ?>"
           class="nav-link <?= $currentPage === basename($link['href']) ? 'active' : '' ?> flex items-center gap-3 px-3 py-2.5 rounded text-sm text-[#c8c8d8] hover:text-[#f59e0b] hover:bg-[#f59e0b11] transition-colors border-l-2 border-transparent">
          <?= htmlspecialchars($link['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="px-4 py-4 border-t border-[#1e1e2a]">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-[#f59e0b22] border border-[#f59e0b44] flex items-center justify-center">
          <span class="text-[#f59e0b] text-xs font-bold"><?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?></span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-medium text-[#f0f0f8] truncate"><?= htmlspecialchars($user['username'] ?? '') ?></p>
          <a href="logout.php" class="text-xs text-[#4a4a5e] hover:text-[#f59e0b] transition-colors">Sign out</a>
        </div>
      </div>
    </div>
  </aside>
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <header class="flex-shrink-0 h-14 bg-[#111118] border-b border-[#1e1e2a] flex items-center justify-between px-6">
      <h1 class="font-display text-xl tracking-wider text-[#f0f0f8]"><?= htmlspecialchars($pageHeading ?? '') ?></h1>
      <div class="flex items-center gap-4">
        <form action="movies.php" method="get" class="flex items-center">
          <input name="q" placeholder="Search films…" class="bg-[#16161f] border border-[#1e1e2a] rounded px-3 py-1.5 text-xs text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b44] w-44 transition-colors" />
        </form>
        <div class="w-8 h-8 rounded-full bg-[#f59e0b22] border border-[#f59e0b44] flex items-center justify-center">
          <span class="text-[#f59e0b] text-xs font-bold"><?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?></span>
        </div>
      </div>
    </header>
    <?php if (!empty($flashMessages)): ?>
      <div class="fixed top-4 right-4 z-50 space-y-2" x-data="{ show: true }" x-show="show">
        <?php foreach ($flashMessages as $flash): ?>
          <div class="toast-enter flex items-center gap-3 px-4 py-3 rounded-lg text-sm shadow-xl <?= $flash['category'] === 'error' ? 'bg-red-950 border border-red-800 text-red-300' : 'bg-emerald-950 border border-emerald-800 text-emerald-300' ?>">
            <?php if ($flash['category'] === 'error'): ?>
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php else: ?>
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <?php endif; ?>
            <?= htmlspecialchars($flash['message']) ?>
            <button @click="show=false" class="ml-2 opacity-60 hover:opacity-100">✕</button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <main class="flex-1 overflow-y-auto p-6">
<?php else: ?>
    <?php if (!empty($flashMessages)): ?>
      <div class="fixed top-4 right-4 z-50 space-y-2">
        <?php foreach ($flashMessages as $flash): ?>
          <div class="toast-enter flex items-center gap-3 px-4 py-3 rounded-lg text-sm shadow-xl <?= $flash['category'] === 'error' ? 'bg-red-950 border border-red-800 text-red-300' : 'bg-emerald-950 border border-emerald-800 text-emerald-300' ?>">
            <?= htmlspecialchars($flash['message']) ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <main class="min-h-screen">
<?php endif; ?>
