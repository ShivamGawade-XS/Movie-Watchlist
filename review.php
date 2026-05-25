<?php
require_once __DIR__ . '/config.php';
require_login();

$movie_id = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
$review_text = trim($_POST['review_text'] ?? '');
if ($movie_id <= 0) {
    flash('Invalid movie selection.', 'error');
    redirect($_SERVER['HTTP_REFERER'] ?? 'movies.php');
}

if ($review_text !== '') {
    db_execute('INSERT INTO Reviews (user_id, movie_id, review_text) VALUES (?, ?, ?)', [$_SESSION['user_id'], $movie_id, $review_text]);
    flash('Review posted!', 'success');
}
redirect('movie.php?id=' . $movie_id);
