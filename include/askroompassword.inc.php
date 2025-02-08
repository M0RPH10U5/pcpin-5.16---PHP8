<?php
/* Password prompt for password-protected rooms */

// Check whether the room still exists
$room = new room();
$room->listRooms($session, $m);
if (!$room->roomlist[0]['id']) {
  // Room does not exist anymore
?>
<html>
<body onload="document.i.submit();">
<form name="i" action="main.php" method="post" target="_parent">
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="include" value="3">
</form>
</body>
</html>
<?php
  die();
}

if ($session->room_id > 0) {
  /* User came here from another room. Posting a system message into that room. */
  systemmessage::insertMessage($session, $session->user_id . "|" . $session->room_id, 2);
  $session->updateSession("room_id = -3");
}

// Look for pass
$roompass = new roompass();
if ($roompass->checkPass($session, $m, $session->user_id)) {
  // A pass exists. Redirecting user into room.
?>
<html>
<body onload="document.loginform.submit();">
<form name="loginform" action="main.php" method="post" target="_parent">
  <input type="hidden" name="include" value="4">
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="u" value="<?php echo htmlspecialchars($u, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="x" value="<?php echo htmlspecialchars($x, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="submitted" value="1">
</form>
</body>
</html>
<?php
  die();
}

switch ($frame) {
  case "main": // Roomlist
    require INCLUDEPATH . "/askroompassword_main.inc.php";
    break;
  case "refresher": // Refresher
    require INCLUDEPATH . "/refresher.inc.php";
    break;
  default: // Frameset
    require INCLUDEPATH . "/frames1.inc.php";
    break;
}
?>
