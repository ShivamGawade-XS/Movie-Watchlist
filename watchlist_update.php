<?php
require_once __DIR__ . '/config.php';
require_login();

$movie_id = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
$status = $_POST['status'] ?? 'want_to_watch';
if ($movie_id <= 0) {
    flash('Invalid movie selection.', 'error');
    redirect($_SERVER['HTTP_REFERER'] ?? 'watchlist.php');
}

db_execute('UPDATE Watchlist SET status = ? WHERE user_id = ? AND movie_id = ?', [$status, $_SESSION['user_id'], $movie_id]);
flash("Status updated to '" . htmlspecialchars(str_replace('_', ' ', $status)) . "'.", 'success');
redirect($_SERVER['HTTP_REFERER'] ?? 'watchlist.php');
