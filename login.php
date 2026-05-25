<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        flash('Please enter both email and password.', 'error');
    } else {
        $user = db_fetch_one('SELECT user_id, username, password_hash FROM Users WHERE email = ?', [$email]);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            redirect('dashboard.php');
        }
        flash('Invalid credentials.', 'error');
    }
}

$pageTitle = 'Sign In - ' . APP_NAME;
$pageHeading = '';
include __DIR__ . '/partials/header.php';
?>
<div class="min-h-screen flex items-center justify-center bg-[#0a0a0f]">
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#f59e0b08] rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#f59e0b05] rounded-full blur-3xl"></div>
  </div>

  <div class="relative w-full max-w-sm px-4">
    <div class="text-center mb-8">
      <span class="font-display text-5xl text-[#f59e0b] tracking-widest">CINELOG</span>
      <p class="text-[#4a4a5e] text-sm mt-1">Your personal film universe</p>
    </div>

    <div class="bg-[#111118] border border-[#1e1e2a] rounded-xl p-8">
      <h2 class="text-[#f0f0f8] font-semibold text-lg mb-6">Welcome back</h2>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Email</label>
          <input name="email" type="email" required autocomplete="email" value="<?= htmlspecialchars($email) ?>"
                 class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" />
        </div>
        <div>
          <label class="block text-xs text-[#4a4a5e] uppercase tracking-wider mb-1.5">Password</label>
          <input name="password" type="password" required autocomplete="current-password"
                 class="w-full bg-[#16161f] border border-[#1e1e2a] rounded-lg px-4 py-2.5 text-sm text-[#c8c8d8] placeholder-[#4a4a5e] focus:outline-none focus:border-[#f59e0b66] transition-colors" />
        </div>
        <button type="submit"
                class="w-full bg-[#f59e0b] hover:bg-[#d97706] text-black font-semibold rounded-lg py-2.5 text-sm transition-colors mt-2">Sign In</button>
      </form>

      <p class="text-center text-xs text-[#4a4a5e] mt-6">
        No account?
        <a href="register.php" class="text-[#f59e0b] hover:underline">Create one</a>
      </p>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
