<?php

if ($session->room_id < 0 || !$user_id) {
?>
<html><head><script>window.close();</script></head></html>
<?php
  die("Hack?");
}

// Load userdata
$user = new user();
$user->readUser($session, $user_id);
$login = $user->login;
common::doHtmlEntities($login);
$color = $user->color;

if (isset($answer)) {
  if ($answer) {
    $message = $lng["invitationaccepted"];
  } else {
    $message = $lng["invitationrejected"];
    // Delete 'one-time' pass for user to enter password-protected room, if any
    $roompass = new roompass();
    $roompass->deletePass($session, $session->room_id, $user_id);
  }
} elseif ($invited) {
  if (isset($response)) {
    // Send response
    systemmessage::insertMessage($session, $session->user_id . "|" . $user_id . "|" . $response, 8);
    if ($response) {
      // Invitation accepted. Change room.
      systemmessage::insertMessage($session, $session->user_id . "|" . $room_id, 9);
    }
?>
<html><body onload="window.close();"></body></html>
<?php
    die();
  } else {
    // Show invitation
    // Get room name
    $room = new room();
    $room->readRoom($session, $room_id);
    $roomname = $room->name;
    common::doHtmlEntities($roomname);
  }
} elseif ($do_invite) {
  // Create 'one-time' pass for user to enter password-protected room
  $room = new room();
  $room->readRoom($session, $session->room_id);
  if ($room->type == 2 || $room->type == 3) {
    $roompass = new roompass();
    $roompass->createPass($session, $session->room_id, $user_id);
  }
  // Invite user
  systemmessage::insertMessage($session, $session->user_id . "|" . $user_id . "|" . $session->room_id, 7);
}

// Load template
require TEMPLATEPATH . "/invite.tpl.php";
?>
