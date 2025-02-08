<?php
/* This is a chat main frameset */

if (!$room_id) {
  $room_id = $m;
}
$room = new room();
$room->readRoom($session, $room_id);

// Check whether the room still exists
if (!$room->id) {
  // Room does not exist (anymore)
?>
<html><body onload="document.i.submit();">
<form name="i" action="main.php" method="post">
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="include" value="3">
</form>
</body></html>
<?php
  die();
} else {
  // Update room ping
  $room->updateRoom($session, $room->id, "last_ping = UNIX_TIMESTAMP()");
}

// Check password for password-protected rooms
if ($room->type == 2 || $room->type == 3) {
  // Room is password-protected
  if (md5($room_password) != $room->password) {
    // Wrong password.
    // Look for pass
    $roompass = new roompass();
    if (!$roompass->checkPass($session, $room_id, $session->user_id, 1)) {
      // Pass does not exist. Ask for password.
?>
<html><body onload="document.i.submit();">
<form name="i" action="main.php" method="post">
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="include" value="10">
  <input type="hidden" name="u" value="<?php echo htmlspecialchars($u, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="m" value="<?php echo htmlspecialchars($room_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="x" value="3">
</form>
</body></html>
<?php
      die();
    }
  }
}

// Load background image, if exists
if ($room->bgimg) {
  $background = "style=\"background-image: url('" . IMAGEPATH . "/rooms/" . htmlspecialchars($room->bgimg, ENT_QUOTES, 'UTF-8') . "'); background-repeat: no-repeat; background-position: center; background-attachment: fixed;\"";
} else {
  $background = "";
}

// Update session
$session->updateSession("last_post_time = UNIX_TIMESTAMP()");

// User entered room.
if ($session->room_id != $room_id) {
  if ($session->room_id > 0) {
    /* User was in other room. Posting a system message into that room. */
    systemmessage::insertMessage($session, $session->user_id . "|" . $session->room_id, 2);
  }
  /* Posting a system message into new room */
  systemmessage::insertMessage($session, $session->user_id . "|" . $room_id, 1);
  /* Updating session */
  $session->updateSession("room_id = $room_id");
  systemmessage::readNewMessages($session);
  usermessage::readNewMessages($session);
}

/* Declaring command line that will be sent to the client */
$command_line = "";

/* Loading userlist */
$sessionlist = $session->listRoomUsers($session->room_id);
$sessionlist_count = count($sessionlist);
$user = new user();
for ($i = 0; $i < $sessionlist_count; $i++) {
  /* Loading data for each user in room */
  $user->readUser($session, $sessionlist[$i]['user_id']);
  $user_id = $user->id;
  $user_name = $user->login;
  common::doHtmlEntities($user_name);
  $user_level = $user->level;
  $user_sex = $user->sex;
  $user_color = $user->color;
  common::addCommand("newUser($user_id,\"$user_name\",$user_level,\"$user_sex\",\"$user_color\");", $command_line, "\n");
}

/* Loading roomlist */
$room = new room();
$room->listRooms($session);
$roomlist = $room->roomlist;
$roomlist_count = count($roomlist);
for ($i = 0; $i < $roomlist_count; $i++) {
  $room_id = $roomlist[$i]['id'];
  $room_name = $roomlist[$i]['name'];
  common::doHtmlEntities($room_name);
  $room_type = $roomlist[$i]['type'];
  $room_userscount = count($session->listRoomUsers($roomlist[$i]['id']));
  common::addCommand("newRoom($room_id,\"$room_name\",$room_type,$room_userscount);", $command_line, "\n");
}

/* Read roomlist frame template into variable */
ob_start();
require INCLUDEPATH . "/roomlist.inc.php";
$roomlist_html = common::convertTextToJavaScriptVar(ob_get_contents());
ob_end_clean();

/* Read userlist frame template into variable */
ob_start();
require INCLUDEPATH . "/userlist.inc.php";
$userlist_html = common::convertTextToJavaScriptVar(ob_get_contents());
ob_end_clean();

/* List smilies */
$smilie = new smilie();
$smilies = $smilie->listSmilies($session);

/* WELCOME MESSAGE */
if ($session->welcome) {
  $welcome_msg = str_replace("<", "|_/&lt;_|", str_replace(">", "|_/&gt;_|", str_replace("\r", '|_/&cr;_|', str_replace("\n", '|_/&lf;_|', addslashes($session->config->welcome_message)))));
  common::addCommand("globalMessage(\"2<" . $welcome_msg . "\");", $command_line, "\n");
  $session->updateSession("welcome = 0");
}

// Show top banner?
if ($session->config->top_banner) {
  $top_banner_height = $session->config->top_banner_height . ",";
  $top_banner_code = "<frame name=\"top_banner\" src=\"" . htmlspecialchars($session->config->top_banner_url, ENT_QUOTES, 'UTF-8') . "\" scrolling=\"auto\" noresize marginwidth=\"0\" marginheight=\"0\">";
} else {
  $top_banner_height = "";
  $top_banner_code = "";
}

// Show bottom banner?
if ($session->config->bottom_banner) {
  $bottom_banner_height = "," . $session->config->bottom_banner_height;
  $bottom_banner_code = "<frame name=\"bottom_banner\" src=\"" . htmlspecialchars($session->config->bottom_banner_url, ENT_QUOTES, 'UTF-8') . "\" scrolling=\"auto\" noresize marginwidth=\"0\" marginheight=\"0\">";
} else {
  $bottom_banner_height = "";
  $bottom_banner_code = "";
}

// Userlist frame position
if ($session->config->userlist_position) {
  $cols = "*, " . $session->config->userlist_width;
} else {
  $cols = $session->config->userlist_width . ", *";
}

/* Load frameset page template */
require TEMPLATEPATH . "/frames_main.tpl.php";
?>