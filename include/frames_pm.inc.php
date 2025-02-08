<?php
/* This is a private message window frameset */

// Update session
$session->updateSession("last_post_time = UNIX_TIMESTAMP()");

// Read userdata
$target_user = new user();
$target_user->readUser($session, $target_user_id);
$target_user_name = $target_user->login;
common::doHtmlEntities($target_user_name);
$current_user_name = $current_user->login;
common::doHtmlEntities($current_user_name);

if ($message_id) {
  // Read message from database
  $usermessage = new userMessage();
  $usermessage->readMessage($session, $message_id);
  // Check user
  if ($usermessage->target_user_id != $session->user_id) {
    die("HACK?");
  }
}

/* Load frameset page template */
require TEMPLATEPATH . "/frames_pm.tpl.php";
?>
