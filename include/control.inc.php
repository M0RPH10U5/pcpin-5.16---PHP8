<?php
/* This is a general chat control engine.
   It reads new messages from database and shows them in main window.
   It updates userlist and roomlist frames.
   It executes all server commands. */

/* Declaring command line that will be sent to the client */
$command_line = "";

/* SYSTEM MESSAGES */
$systemmessage = new systemmessage();
/* Deleting old system messages */
$systemmessage->deleteOldMessages($session);
/* Reading new system messages */
$systemmessages = $systemmessage->readNewMessages($session);
/* Counting system messages */
$systemmessages_count = count($systemmessages);
/* Processing each system message */
for ($i = 0; $i < $systemmessages_count; $i++) {
  /* Which type the new message of? */
  switch ($systemmessages[$i]['type']) {
    case 1: // User entered room
      $fields = explode("|", $systemmessages[$i]['body']);
      $user_id = $fields[0];
      $room_id = $fields[1];
      if ($room_id == $session->room_id) {
        $new_user = new user();
        $new_user->readUser($session, $user_id);
        $user_name = $new_user->login;
        common::doHtmlEntities($user_name);
        $user_level = $new_user->level;
        $user_sex = $new_user->sex;
        $user_color = $new_user->color;
        common::addCommand("E>$user_id<$user_name<$user_level<$user_sex<$user_color<{$systemmessages[$i]['post_time']}", $command_line, "'");
      } else {
        common::addCommand("u>$room_id<<<+", $command_line, "'");
      }
      break;
    case 2: // User left room
      $fields = explode("|", $systemmessages[$i]['body']);
      $user_id = $fields[0];
      $room_id = $fields[1];
      if ($room_id == $session->room_id) {
        common::addCommand("L>$user_id<{$systemmessages[$i]['post_time']}", $command_line, "'");
      } else {
        common::addCommand("u>$room_id<<<-", $command_line, "'");
      }
      break;
    case 3: // Userinfo changed
      $user = new user();
      $user->readUser($session, $systemmessages[$i]['body']);
      common::addCommand("U>{$user->id}<{$user->login}<{$user->level}<{$user->sex}<{$user->color}", $command_line, "'");
      break;
    case 4: // Room was deleted
      common::addCommand("d>{$systemmessages[$i]['body']}", $command_line, "'");
      break;
    case 5: // Room was created
      $room = new room();
      $room->listRooms($session, $systemmessages[$i]['body']);
      $new_room = $room->roomlist[0];
      common::doHtmlEntities($new_room['name']);
      common::addCommand("n>{$new_room['id']}<{$new_room['name']}<{$new_room['type']}<0", $command_line, "'");
      break;
    case 6: // User was kicked
      common::addCommand("K>{$systemmessages[$i]['body']}<{$systemmessages[$i]['post_time']}", $command_line, "'");
      break;
    case 7: // Show invitation
      $tmp = explode("|", $systemmessages[$i]['body']);
      if ($tmp[1] == $session->user_id) {
        common::addCommand("I>{$tmp[0]}<{$tmp[2]}", $command_line, "'");
      }
      break;
    case 8: // Show invitation response
      $tmp = explode("|", $systemmessages[$i]['body']);
      if ($tmp[1] == $session->user_id) {
        common::addCommand("i>{$tmp[0]}<{$tmp[2]}", $command_line, "'");
      }
      break;
    case 9: // Change room
      $tmp = explode("|", $systemmessages[$i]['body']);
      if ($tmp[0] == $session->user_id) {
        common::addCommand("C>{$tmp[1]}", $command_line, "'");
      }
      break;
    case 10: // Restart room
      if (empty($systemmessages[$i]['body']) || $systemmessages[$i]['body'] == $session->room_id) {
        common::addCommand("R", $command_line, "'");
      }
      break;
    case 11: // Show advertisement
      $fields = explode("|", $systemmessages[$i]['body']);
      $room_id = array_shift($fields);
      $systemmessages[$i]['body'] = addslashes(implode('|', $fields));
      if ($room_id == $session->room_id) {
        common::addCommand("A>{$systemmessages[$i]['body']}", $command_line, "'");
      }
      break;
    case 12: // Clear room
      if (empty($systemmessages[$i]['body']) || $systemmessages[$i]['body'] == $session->room_id) {
        common::addCommand("c", $command_line, "'");
      }
      break;
  }
}

/* GLOBAL MESSAGES */
$globalmessage = new globalMessage();
/* Deleting old global messages */
$globalmessage->deleteOldMessages($session);
/* Reading new global messages */
$globalmessages = $globalmessage->readNewMessages($session);
/* Counting global messages */
$globalmessages_count = count($globalmessages);
/* Processing each global message */
for ($i = 0; $i < $globalmessages_count; $i++) {
  $new_user = new user();
  $new_user->readUser($session, $globalmessages[$i]['user_id']);
  $user_name = $new_user->login;
  common::doHtmlEntities($user_name);
  $user_color = $new_user->color;
  if ($globalmessages[$i]['type']) {
    $globalmessages[$i]['body'] = $globalmessages[$i]['id'];
    $user_name = "";
    $user_color = "";
  }
  common::addCommand("G>{$globalmessages[$i]['type']}<$user_name<$user_color<{$globalmessages[$i]['body']}", $command_line, "'");
}

/* USER MESSAGES */
$usermessage = new usermessage();
/* Deleting old user messages */
$usermessage->deleteOldMessages($session);
/* Reading new user messages */
$usermessages = $usermessage->readNewMessages($session);
/* Counting user messages */
$usermessages_count = count($usermessages);
/* Processing each user message */
for ($i = 0; $i < $usermessages_count; $i++) {
  switch ($usermessages[$i]['type']) {
    case 1: // Normal message
      if ($usermessages[$i]['user_id'] != $session->user_id || $session->config->synchronize_time) {
        common::addCommand("M>{$usermessages[$i]['user_id']}<{$usermessages[$i]['body']}<{$usermessages[$i]['flags']}<{$usermessages[$i]['post_time']}", $command_line, "'");
      }
      break;
    case 2: // Private message
      if ($usermessages[$i]['target_user_id'] == $session->user_id) {
        common::addCommand("P>{$usermessages[$i]['user_id']}<{$usermessages[$i]['body']}<{$usermessages[$i]['id']}<{$usermessages[$i]['flags']}<{$usermessages[$i]['post_time']}", $command_line, "'");
      }
      break;
    case 3: // Whispered message
      if ($usermessages[$i]['target_user_id'] == $session->user_id) {
        common::addCommand("W>{$usermessages[$i]['user_id']}<{$usermessages[$i]['body']}<{$usermessages[$i]['flags']}<{$usermessages[$i]['post_time']}", $command_line, "'");
      } elseif ($usermessages[$i]['user_id'] == $session->user_id && $session->config->synchronize_time) {
        common::addCommand("w>{$usermessages[$i]['target_user_id']}<{$usermessages[$i]['body']}<{$usermessages[$i]['flags']}<{$usermessages[$i]['post_time']}", $command_line, "'");
      }
      break;
    case 4: // 'Said' message
      if ($usermessages[$i]['user_id'] != $session->user_id || $session->config->synchronize_time) {
        common::addCommand("S>{$usermessages[$i]['user_id']}<{$usermessages[$i]['body']}<{$usermessages[$i]['flags']}<{$usermessages[$i]['post_time']}<{$usermessages[$i]['target_user_id']}", $command_line, "'");
      }
      break;
  }
}

/* ADVERTISEMENT */
$advertisement = new advertisement();
$advertisements = $advertisement->listAdvertisements($session, 1);
$advertisements_count = count($advertisements);
for ($i = 0; $i < $advertisements_count; $i++) {
  // Check period
  if (!fk_advertisement::check($session, $session->room_id, $advertisements[$i]['id']) || time() - $advertisements[$i]['period'] * 60 >= fk_advertisement::check($session, $session->room_id, $advertisements[$i]['id'])) {
    // Which room type?
    $room = new room();
    $room->readRoom($session, $session->room_id);
    if (!$room->type || $room->type && $advertisements[$i]['show_private']) {
      // Are there enough users in room?
      if ($advertisements[$i]['min_roomusers'] <= $session->countRoomUsers($session->room_id)) {
        // Show message
        // Convert HTML characters
        $text = str_replace("<", "*_/&lt;_*", str_replace(">", "*_/&gt;_*", str_replace("'", "*_/&quot;_*", str_replace("\r", "*_/CR_*", str_replace("\n", "*_/LF_*", addslashes($advertisements[$i]['text']))))));
        systemMessage::insertMessage($session, $session->room_id . '|' . $text, 11);
        // Update advertisement time
        fk_advertisement::update($session, $session->room_id, $advertisements[$i]['id']);
        // Update advertisement shows count
        $advertisement->updateAdvertisement($session, $advertisements[$i]['id'], "shows_count = shows_count + 1");
      }
    }
  }
}

/* Update room ping */
$room = new room();
$room->updateRoom($session, $session->room_id, "last_ping = UNIX_TIMESTAMP()");

/* Load user control frame page template */
require TEMPLATEPATH . "/control.tpl.php";
?>
