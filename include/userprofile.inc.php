<?php
/* This is a user profile page. */

/* Read userdata from database */
$user = new user();
$user->readUser($session, $profile_user_id);
/* Prepare nickname */
common::doHtmlEntities($user->login);

// Default photo
if (!$user->photo) {
  $user->photo = "nophoto.jpg";
}

if ($session->user_id == $profile_user_id) {
  /* Own profile */
  if ($update_password) {
    // Update password
    if ($submitted) {
      // Changing password
      $result = $user->changePassword($session, $old_password, $new_password_1, $new_password_2);
      switch ($result) {
        case 0: // Password changed
          $password_changed = true;
          break;
        case 1: // Old password incorrect
          $errortext = $lng["oldpasswordincorrect"];
          break;
        case 2: // New passwords are not identical
          $errortext = $lng["passwordsnotident"];
          break;
        case 3: // Password length incorrect
          $errortext = str_replace("{MIN}", $session->config->password_length_min, str_replace("{MAX}", $session->config->password_length_max, $lng["passwordlengthwrong"]));
          break;
        case 4: // Illegal characters in password
          $errortext = $lng["passwordillegalchars"];
          break;
      }
    }
  } else {
    if ($update_profile) {
      // Validate email address
      if ((empty($email) && $user->guest) || common::checkEmail($email, $session->config->email_validation_level)) {
        // Update user profile
        $user->updateUser($session, $session->user_id, "color = '" . str_replace("#", "", $color) . "', name = '$name', sex = '$sex', email = '$email', age = '$age', location = '$location', about = '$about', hide_email = '$hide_email'");
        // Inserting 'update user in userlist' command
        systemmessage::insertMessage($session, $session->user_id, 3);
      } else {
        $user->email = $email;
        $errortext = $lng["emailinvalid"];
      }
    } elseif ($delete_photo) {
      // Delete user photo
      if ($user->photo != '' && $user->photo != 'nophoto.jpg') {
        unlink(IMAGEPATH . '/userphotos/' . $user->photo);
      }
      $user->updateUser($session, $session->user_id, "photo = ''");
    }
    // Default photo
    if (!$user->photo) {
      $user->photo = "nophoto.jpg";
    }
  }
  ${'selected_sex_' . $user->sex} = 'selected';
  ${'selected_hide_email_' . $user->hide_email} = 'selected';
  require TEMPLATEPATH . "/edit_profile.tpl.php";
} else {
  /* Other user's profile */
  require TEMPLATEPATH . "/view_profile.tpl.php";
}
?>
