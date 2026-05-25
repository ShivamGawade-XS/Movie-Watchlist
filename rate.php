<?php
require_once __DIR__ . '/config.php';
require_login();

$movie_id = isset($_POST['movie_id']) ? (int) $_POST['movie_id'] : 0;
$score = isset($_POST['score']) ? (int) $_POST['score'] : 0;
if ($movie_id <= 0 || $score < 1 || $score > 10) {
    flash('Invalid rating submission.', 'error');
    redirect($_SERVER['HTTP_REFERER'] ?? 'movies.php');
}

$existing = db_fetch_one('SELECT rating_id FROM Ratings WHERE user_id = ? AND movie_id = ?', [$_SESSION['user_id'], $movie_id]);
if ($existing) {
    db_execute('UPDATE Ratings SET score = ?, rated_at = NOW() WHERE user_id = ? AND movie_id = ?', [$score, $_SESSION['user_id'], $movie_id]);
} else {
    db_execute('INSERT INTO Ratings (user_id, movie_id, score) VALUES (?, ?, ?)', [$_SESSION['user_id'], $movie_id, $score]);
}
flash('Rating saved!', 'success');
redirect('movie.php?id=' . $movie_id);
