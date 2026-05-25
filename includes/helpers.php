<?php
function flash(string $message, string $category = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'category' => $category];
}

function get_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('Please sign in to continue.', 'error');
        redirect('login.php');
    }
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function current_user(): array
{
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
    ];
}
