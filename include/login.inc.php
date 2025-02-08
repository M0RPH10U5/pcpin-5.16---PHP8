<?php
if (!empty($confirm)) {
  // Account activation
  if ($submitted) {
    // Changing password
    $user = new user();
    $result = $user->generatePassword($session, $id, $a, $new_password_1, $new_password_2);
    switch ($result) {
      case 0: // Password generated
        $password_changed = true;
        // Activate user (if needed)
        $user->updateUser($session, $user->id, "activated = 1");
        break;
      case 2: // New passwords are not identical
        $errortext[] = $lng["passwordsnotident"];
        break;
      case 3: // Password length incorrect
        $errortext[] = str_replace("{MIN}", $session->config->password_length_min, str_replace("{MAX}", $session->config->password_length_max, $lng["passwordlengthwrong"]));
        break;
      case 4: // Illegal characters in password
        $errortext[] = $lng["passwordillegalchars"];
        break;
      default:
        die("HACK ?");
        break;
    }
  }
  // Load template
  require(TEMPLATEPATH . "/accountactivation.tpl.php");
} elseif ($register) {
  // Register user
  if ($submitted) {
    // Check login
    unset($errortext);
    $login = trim($login);
    $email = trim($email);
    $user = new user();
    if (strlen($login) < $session->config->login_length_min || strlen($login) > $session->config->login_length_max) {
      // Login length wrong
      $errortext[] = str_replace("{MIN}", $session->config->login_length_min, str_replace("{MAX}", $session->config->login_length_max, $lng["loginlengthwrong"]));
    } else {
      if (is_array($user->listUsers($session, $login))) {
        // Username already taken
        $errortext[] = str_replace("{USERNAME}", $login, $lng["usernametaken"]);
      } else {
        $badword = new badword();
        $badwords = $badword->listBadWords($session);
        foreach ($badwords as $badword_data) {
          if (false !== strpos(strtolower($login), strtolower($badword_data['word']))) {
            $errortext[] = $lng['invalidcharsinlogin'];
            break;
          }
        }
      }
    }
    // Check email address
    if (empty($email)) {
      $errortext[] = $lng["emailempty"];
    } elseif (!common::checkEmail($email, $session->config->email_validation_level)) {
      $errortext[] = $lng["emailinvalid"];
    } else {
      // Check whether email address already exists in database
      $user_tmp = new user();
      $user_tmp->findUser($session, '', $email);
      if (!empty($user_tmp->login)) {
        $errortext[] = str_replace('{EMAIL}', $email, $lng["emailtaken"]);
      }
    }
    if (!$session->config->require_activation) {
      // Check password
      // Compare new passwords
      if ($new_password_1 == $new_password_2) {
        // Check new password length
        if (strlen($new_password_1) >= $session->config->password_length_min && strlen($new_password_1) <= $session->config->password_length_max) {
          // Check characters in new password
          if (preg_replace("/[^0-9a-zA-Z]/", "", $new_password_1) != $new_password_1) {
            // Illegal characters in new password
            $errortext[] = $lng["passwordillegalchars"];
          }
        } else {
          // Illegal password length
          $errortext[] = str_replace("{MIN}", $session->config->password_length_min, str_replace("{MAX}", $session->config->password_length_max, $lng["passwordlengthwrong"]));
        }
      } else {
        // New passwords are not identical
        $errortext[] = $lng["passwordillegalchars"];
      }
    }
    if (!is_array($errortext)) {
      // Save user
      $user = new user();
      $user->login = $login;
      $user->level = 0;
      $user->email = $email;
      $user->hide_email = 1;
      $user->color = ltrim($session->config->guest_color, '#');
      $user->addUser($session);
      if ($session->config->require_activation) {
        // Generate activation code
        $passcode = $user->generatePassCode($session, $user->id, 12);
        // Email template
        $email_template = str_replace("{USER}", $login,
          str_replace("{ACTIVATIONURL}", $session->config->homepage . "/main.php?confirm=1&language=" . $language . "&a=" . $passcode . "&id=" . $user->id,
            str_replace("{CHATURL}", $session->config->homepage,
              str_replace("{CHATOWNER}", $session->config->sender_name,
                $lng["activateregistration"]))));
      } else {
        $user->updateUser($session, $user->id, "password = '" . md5($new_password_1) . "', activated = '1'");
        // Email template
        $email_template = str_replace("{USER}", $login,
          str_replace("{PASSWORD}", $new_password_1,
            str_replace("{CHATURL}", $session->config->homepage,
              str_replace("{CHATOWNER}", $session->config->sender_name,
                $lng["instantregistration"]))));
      }
      $user_saved = true;
      // Send email
      email::send($session->config->sender_email, $session->config->sender_name, $user->email, $lng["registration"], $email_template);
    }
  }
  // Load template
  require(TEMPLATEPATH . "/register.tpl.php");
} elseif ($lostpassword) {
  // Lost password
  if ($submitted) {
    if (!$login) {
      $errortext[] = $lng["loginempty"];
    }
    if (!$email) {
      $errortext[] = $lng["emailempty"];
    }
    if (!is_array($errortext)) {
      // Look for user
      $user = new user();
      $user->findUser($session, $login, $email);
      if ($user->id) {
        // User found
        // Generate new password
        $passcode = $user->generatePassCode($session, $user->id, 12);
        // Send email
        $body = str_replace("{USER}", $user->login,
          str_replace("{URL}", $session->config->homepage . "/main.php?confirm=1&type=1&language=" . $language . "&a=" . $passcode . "&id=" . $user->id,
            str_replace("{CHATOWNER}", $session->config->sender_name,
              $lng["email_lostpassword"])));
        email::send($session->config->sender_email, $session->config->sender_name, $user->email, $lng["lostpassword"], $body);
        $statustext = str_replace("{EMAIL}", $user->email, $lng["activationsent"]);
        common::doHtmlEntities($statustext);
      } else {
        // User not found
        $tmp = str_replace("{USER}", $login, str_replace("{EMAIL}", $email, $lng["usernotfound"]));
        common::doHtmlEntities($tmp);
        $errortext[] = $tmp;
      }
    }
  }
  // Load template
  require(TEMPLATEPATH . "/lostpassword.tpl.php");
} else {
  /* Check login and password or cookie (if any) */
  $login = trim($login);
  common::dTrim($login);
  if (!empty($login) || (!empty($pcpin_cookie) && $pcpin_cookie[0] != '@')) {
    // Check that IP address is not in the banlist
    if (!ban::checkIP($session, IP)) {
      // IP address is banned
      $errortext = $lng["ipbanned"];
    } else {
      $user = new user();
      $user->login = $login;
      $user->cookie = (!empty($pcpin_cookie)) ? $pcpin_cookie : '';
      $user->password = md5($password);
      $user->checkLogin($session);
      if ($user->id) {
        /* Login and password are OK */
        /* Ensure that user not already logged in */
        if ($session->checkUserUnique($user->id, true)) {
          /* Check that user is not in the banlist */
          if (ban::checkUser($session, $user->id)) {
            // If user has Admin level and has been logged in directly, update user's session
            $session_id = $session->getUsersSession($user->id);
            if (!empty($session_id)) {
              $session->id = $session_id;
              $session->readSession();
              if (!empty($session->user_id) && !empty($session->direct_login)) {
                $session->updateSession('direct_login = 0');
                $next_include = '3';
              } else {
                $session_id = '';
              }
            }
            if (empty($session_id)) {
              // Creating new session
              $session->newSession($user->id);
              $session_id = $session->id;
              /* Updating user's language */
              $session->updateSession('language = "' . $language . '"');
              $session->updateSession('direct_login = ' . (!empty($admin) ? '"1"' : '"0"'));
              // Next include
              $next_include = (!empty($admin) && $user->level > 0) ? '13' : '3';
            }
            /* Save cookie */
            setcookie('pcpin_cookie', $user->cookie, time() + COOKIE_LIFETIME);
          } else {
            // User is banned
            $errortext = $lng["youarebanned"];
          }
        } else {
          // User already logged in
          if (!empty($user->login)) {
            $login = $user->login;
          }
          $errortext = str_replace("{USER}", $login, $lng["useralreadyloggedin"]);
        }
      } elseif (isset($login)) {
        $login = trim($login);
        if (strlen($login) < $session->config->login_length_min || strlen($login) > $session->config->login_length_max) {
          $errortext = str_replace("{MIN}", $session->config->login_length_min, str_replace("{MAX}", $session->config->login_length_max, $lng["loginlengthwrong"]));
        } elseif (empty($password) && $session->config->allow_guests) {
          // Guests are allowed
          // Check username
          $login = addslashes($login);
          $user_list = $user->listUsers($session, $login);
          if (empty($user_list)) {
            // Username is free
            $next_include = 3;
            // Inserting guest into database
            $user = new user();
            $user->login = $login;
            $user->password = md5(mt_rand(-time(), time()));
            $user->guest = 1;
            $user->color = $session->config->guest_color;
            $user->addUser($session);
            $user->checkLogin($session);
            $session->newSession($user->id);
            $session->updateSession("language = '$language'");
            $session_id = $session->id;
          } else {
            $errortext = str_replace("{USERNAME}", $login, $lng["usernametaken"]);
          }
        } else {
          // Wrong login/password
          $errortext = $lng["loginincorrect"];
        }
      }
    }
  }
  if (!empty($errortext) || (empty($login) && empty($user->id))) {
    /* Load login page template */
    if (empty($login) && !empty($pcpin_cookie) && $pcpin_cookie[0] == '@') {
      $login = substr($pcpin_cookie, 1);
    }
    require(TEMPLATEPATH . "/login.tpl.php");
  } else {
    /* Proceed to room selection */
?>
<HTML>
<BODY onload="document.loginform.submit();">
  <FORM name="loginform" action="main.php" method="post">
    <INPUT type="hidden" name="include" value="<?php echo $next_include ?>">
    <INPUT type="hidden" name="session_id" value="<?php echo $session_id ?>">
    <INPUT type="hidden" name="guest" value="<?php echo $guest ?>">
    <INPUT type="hidden" name="admin" value="<?php echo $admin ?>">
    <INPUT type="hidden" name="screen_height" value="">
  </FORM>
</BODY>
</HTML>
<?php
  }
}
?>
