<?php
/* This is a user profile page. */

// Check rights
if (!($current_user->level & 8)) {
  die("HACK?");
}

/* Read userdata from database */
$user = new user();
$user->readUser($session,$profile_user_id);
if ($user->level >= 131071) {
  // Superuser protection
  die('Access denied');
}

/* Prepare nickname */
common::doHtmlEntities($user->login);

if(!empty($delete)) {
  /* Delete user */
  // Get user's session ID
  $session2 = new session($session->getUsersSession($profile_user_id));
  if (!empty($session2->id)) {
    // User is online
    // Deleting user's session
    $session2->logout();
  }
  // Deleting user from database
  $user->deleteUser($session, $profile_user_id);
?>
<html><body onload="document.redirectform.submit();">
<form name="redirectform" action="main.php" method="post">
<input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="include" value="11">
<input type="hidden" name="edit" value="1">
</form>
</body>
</html>
<?php
  exit();
}

if (!empty($delete_photo) && !empty($user->photo) && $user->photo !== 'nophoto.jpg') {
  // Delete photo
  $photoPath = IMAGEPATH . '/userphotos/' . $user->photo;
  if (file_exists($photoPath)) {
    unlink($photoPath);
  }
  $user->updateUser($session, $profile_user_id, "photo = ''");
}

if (!empty($update_profile)) {
  // Validate email address
  if (common::checkEmail($email, $session->config->email_validation_level)) {
    // Calculate privileges
    $level = 0;
    if (is_array($set_rights)) {
      foreach ($set_rights as $key => $val) {
        if ($current_user->level & $key) {
          $level += $key;
        }
      }
    }

    // Update userprofile
    $user->updateUser(
        $session,
        $profile_user_id,
        "color = '" . str_replace("#", "", $color) . "',
        name = '" . addslashes($name) . "',
        sex = '" . $sex . "',
        email = '" . addslashes($email) . "',
        age = '" . (int)$age . "',
        location = '" . addslashes($location) . "',
        about = '" . addslashes($about) . "',
        hide_email = '" . (int)$hide_email . "',
        level = '" . (int)$level . "'"
    );
    // Inserting 'update user in userlist' command
    $systemmessage = new systemmessage();
    $systemmessage->insertMessage($session, $profile_user_id, 3);
  } else {
    $user->email = $email;
    $errortext = $lng["emailinvalid"];
  }
}
${'selected_sex_' . $user->sex} = 'selected';
${'selected_hide_email_' . $user->hide_email} = 'selected';
// Default photo
if (!$user->photo) {
  $user->photo = "nophoto.jpg";
}

// Calculate privileges
$privileges = [];
$privilege_levels = [
    1 => "chatstatistics",
    2 => "chatdesign",
    4 => "chatsettings",
    8 => "editusers",
    16 => "kickusers",
    32 => "banusersip",
    64 => "postglobalmessage",
    128 => "manageadvertisements",
    256 => "managesmilies",
    512 => "managesbadwords",
    1024 => "manageprivileges",
    2048 => "managerooms"
];

foreach ($privilege_levels as $i => $name) {
    if ($current_user->level & $i) {
        $privileges[] = [
            "name" => $lng[$name],
            "value" => $i,
            "checked" => (user->level & $i) ? "checked" : ""
        ];
    }
}

require TEMPLATEPATH . "/admin_editusers.tpl.php";
?>
