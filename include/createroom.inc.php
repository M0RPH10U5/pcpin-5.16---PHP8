<?php
/* Create new room page */

// Check rights
if ($admin_manage_rooms) {
  if (!($current_user->level & 2048)) {
    unset($admin_manage_rooms);
  }
}

if (!($session->config->allow_userrooms == 2 || ($session->config->allow_userrooms == 1 && !$current_user->guest)) && !$admin_manage_rooms) {
  die();
}

if (!$admin_manage_rooms && $session->room_id > 0) {
  // User came here from another room. Posting a system message into that room.
  systemmessage::insertMessage($session, $session->user_id . "|" . $session->room_id, 2);
  // Create pass to make return into password-protected room possible
  $room = new room();
  $room->readRoom($session, $session->room_id);
  if ($room->type == 2 || $room->type == 3) {
    $roompass = new roompass();
    $roompass->createPass($session, $session->room_id, $session->user_id);
  }
  // Updating session
  $session->updateSession("room_id = -2");
}

switch ($frame) {
  case "main":  // Roomlist
    require INCLUDEPATH . "/createroom_main.inc.php";
    break;
  case "refresher":  // Refresher
    require INCLUDEPATH . "/refresher.inc.php";
    break;
  default:  // Frameset
    require INCLUDEPATH . "/frames1.inc.php";
    break;
}
?>
