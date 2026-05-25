<?php
require_once __DIR__ . '/config.php';
require_login();

$movie_id = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
$status = $_POST['status'] ?? 'want_to_watch';
if ($movie_id <= 0) {
    flash('Invalid movie selection.', 'error');
    redirect($_SERVER['HTTP_REFERER'] ?? 'movies.php');
}

$existing = db_fetch_one('SELECT wl_id FROM Watchlist WHERE user_id = ? AND movie_id = ?', [$_SESSION['user_id'], $movie_id]);
if ($existing) {
    db_execute('UPDATE Watchlist SET status = ? WHERE user_id = ? AND movie_id = ?', [$status, $_SESSION['user_id'], $movie_id]);
    flash('Watchlist updated!', 'success');
} else {
    db_execute('INSERT INTO Watchlist (user_id, movie_id, status) VALUES (?, ?, ?)', [$_SESSION['user_id'], $movie_id, $status]);
    flash('Added to watchlist!', 'success');
}
redirect($_SERVER['HTTP_REFERER'] ?? 'movies.php');
