<?php
/* Process logout */
// Update cookie
$user = new user();
$user->readUser($session, $session->user_id);
setcookie('pcpin_cookie', '@' . $user->login, time() + COOKIE_LIFETIME);

// Delete session
$session->logout($session_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($session->config->exit_url, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
  <script>
    window.location.href = "<?php echo htmlspecialchars($session->config->exit_url, ENT_QUOTES, 'UTF-8'); ?>";
  </script>
</body>
</html>