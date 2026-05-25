<?php
require_once __DIR__ . '/config.php';
require_login();

$wl_id = isset($_POST['wl_id']) ? (int) $_POST['wl_id'] : 0;
if ($wl_id <= 0) {
    flash('Invalid selection.', 'error');
    redirect('watchlist.php');
}

db_execute('DELETE FROM Watchlist WHERE wl_id = ? AND user_id = ?', [$wl_id, $_SESSION['user_id']]);
flash('Removed from watchlist.', 'success');
redirect($_SERVER['HTTP_REFERER'] ?? 'watchlist.php');
