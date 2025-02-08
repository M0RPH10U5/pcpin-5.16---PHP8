<?php
/* This is a room selection page */

if (!$admin_manage_rooms && $session->room_id > 0 && $x == "-") {
  /* User came here from another room. Posting a system message into that room. */
  $systemmessage = new systemmessage();
  $systemmessage->insertMessage($session, $session->user_id . "|" . $session->room_id, 2);
  $session->updateSession("room_id = -1");
}

switch ($frame) {
  case "main":
    /* Roomlist */
    require INCLUDEPATH . "/selectroom_list.inc.php";
    break;
  case "refresher":
    /* Refresher */
    require INCLUDEPATH . "/refresher.inc.php";
    break;
  default:
    /* Frameset */
    require INCLUDEPATH . "/frames1.inc.php";
    break;
}
?>
