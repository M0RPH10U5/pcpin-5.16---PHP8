<?php
/* Clear chat screen */

// Check rights
if (($current_user->level && 64)) { // Required: "Post global messages"
  // Post a system message
  systemMessage::insertMessage($session, (int)$clear_room_id, 12);
}

// Load dummy form
header('Location: main.php?include=30&session_id=' . $session_id);
exit();
?>