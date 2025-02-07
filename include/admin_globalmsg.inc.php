<?php
/* Post global message */

// Check rights
if (!($current_user->level & 64)) {
  die("HACK?");
}

if ($message) {
  $message = trim($message);
  common::dTrim($message);
  common::doHtmlEntities($message);
  /* Posting message */
  globalmessage::insertMessage($session, $session->user_id, $message, $type);
}


// Load template
require(TEMPLATEPATH . "/admin_globalmsg.tpl.php");
?>