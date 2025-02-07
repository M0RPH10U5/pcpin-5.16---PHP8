<?php
$next_include = 1400;

// Defaults
if (!isset($admin_login)) $admin_login = '';
if (!isset($admin_pw)) $admin_pw = '';
if (!isset($admin_email)) $admin_email = '';

// Which data shall be imported?
$keep_users = false;
$stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "keep_settings" LIMIT 1');
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $data = $result->fetch_array(MYSQLI_ASSOC);
    if (!empty($data['value']) && false !== ($keep_settings = unserialize($data['value']))) {
        $keep_users = !empty($keep_settings['Users']);
    }
}

if (!empty($submitted)) {
  if (empty($do_skip)) {
    // Validate form
    $admin_login = trim($admin_login);
    $admin_email = trim($admin_email);
    if ($admin_login == '') {
      // Empty username
      $errortext[] = 'Administrator username cannot be empty';
    } elseif ($keep_users) {
      // User accounts shall be imported. Check them.
      $stmt = $conn->prepare('SELECT * FROM `' . PREFIX . 'user` WHERE `login` = ? LIMIT 1');
      $stmt->bind_param('s', $admin_login);
      $stmt->execute();
      $result = $stmt ->get_result();
      if ($result && $data = $result->fetch_array(MYSQLI_ASSOC)) {
        if (!empty($data['id'])) {
            // User already exists
            $errortext[] = 'User "' . $admin_login . '" already exists!';
        }
      }
    }
    if ($admin_email == '') {
      $errortext[] = 'Administrator Email address cannot be empty';
    } elseif (!common::checkEmail($admin_email, 1)) {
      $errortext[] = 'Administrator Email address appears to be invalid';
    }
    if ($admin_pw == ''){
      $errortext[] = 'Administrator password cannot be empty';
    } elseif (strlen($admin_pw) < 8) {
      $errortext[] = 'Administrator password is too short';
    }
  }

  if (empty($errortext)) {
    $stmt = $conn->prepare('DELETE FROM `' . PREFIX . '_chat_installdata` WHERE `name` = Binary "admin_account" LIMIT 1');
    $stmt->execute();

    if (empty($do_skip)) {
      // Save data
      $admindata = [
        'login' => $admin_login,
        'email' => $admin_email,
        'password' => password_hash($admin_pw, PASSWORD_DEFAULT) // Use Password_hash instead of md5
      ];
      $stmt = $conn->prepare('INSERT INTO `' . PREFIX . '_chat_installdata` (`name`, `value`) VALUES ("admin_account", ?)');
      $stmt->bind_param('s', serialize($admindata));
      $stmt->execute();
    }
    // Load next page
    header('Location: ./install.php?framed=1&include=' . $next_include . '&timestamp=' . md5(microtime()));
    die();
  }
}

$_body_onload .= ' checkChkBox(); ';

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/admin_account.tpl.php');
?>