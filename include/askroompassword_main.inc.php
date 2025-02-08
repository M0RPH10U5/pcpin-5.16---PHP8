<?php
/* Password prompt for password-protected rooms */

$room = new room();
$room->listRooms($session, $m);
$roomname = $room->roomlist[0]['name'];
common::doHtmlEntities($roomname);

if ($submitted) {
  // Check password
  if (md5($t) == $room->roomlist[0]['password']) {
    // Password is correct
?>
<html>
<body onload="document.enterroom.submit();">
<form name="enterroom" action="main.php" method="post" target="_parent">
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="include" value="4">
  <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="room_password" value="<?php echo htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); ?>">
</form>
</body>
</html>
<?php
    die();
  } else {
    $errortext = $lng["wrongpassword"];
  }
}

/* Load login page template */
require TEMPLATEPATH . "/askroompassword.tpl.php";
?>
