<?php
/* This is a message input page */

// Update session
$session->updateSession("last_post_time = UNIX_TIMESTAMP()");

if (isset($m)) {
  $usermessage = new usermessage();
  // Preparing message
  $m = trim($m);
  common::dTrim($m);
  common::doHtmlEntities($m);
  $m = addslashes($m);
  /* Message type */
  switch ($t) {
    case 1: // Normal message (in main frame)
      if ($u > 0) {
        // Message was whispered to user
        $usermessage->insertMessage($session, $u, $m, 3, $x);
      } elseif ($u < 0) {
        // Message was 'said' to user
        $u *= -1;
        $usermessage->insertMessage($session, $u, $m, 4, $x);
      } else {
        // Message for all room users
        $usermessage->insertMessage($session, 0, $m, 1, $x);
      }
      break;
    case 2: // Private message (in popUp)
      $usermessage->insertMessage($session, $u, $m, 2, $x);
      break;
  }
  include INCLUDEPATH . "/dummyform.inc.php";
} else {
  /* First run, load template */
  /* Template type */
  switch ($t) {
    case 1: // Main chat input frame
      require TEMPLATEPATH . "/input_main.tpl.php";
      break;
    case 2: // Private messages input frame
      require TEMPLATEPATH . "/input_pm.tpl.php";
      break;
  }
}
?>
