<?php
/* This is a room select page */

/* Read room list from database */
$room = new room();
$main_rooms = [];
$user_rooms = [];

// Main chat rooms without password
$room->listRooms($session, 0, "", 0);
$main_roomlist = [];
$roomlist_count = count($room->roomlist);
for ($i = 0; $i < $roomlist_count; $i++) {
  $users_count = count($session->listRoomUsers($room->roomlist[$i]['id']));
  common::doHtmlEntities($room->roomlist[$i]['name']);
  $main_roomlist[$users_count][] = [
    "id" => $room->roomlist[$i]['id'],
    "name" => $room->roomlist[$i]['name'],
    "type" => $room->roomlist[$i]['type'],
    "userscount" => $users_count
  ];
}
// Sort rooms by users count
krsort($main_roomlist);
reset($main_roomlist);
while (list($key, $val) = each($main_roomlist)) {
  while (list($key2, $val2) = each($val)) {
    $main_rooms[] = $val2;
  }
}

// Main chat rooms with password
$room->listRooms($session, 0, "", 2);
$main_roomlist = [];
$roomlist_count = count($room->roomlist);
for ($i = 0; $i < $roomlist_count; $i++) {
  $users_count = count($session->listRoomUsers($room->roomlist[$i]['id']));
  common::doHtmlEntities($room->roomlist[$i]['name']);
  $main_roomlist[$users_count][] = [
    "id" => $room->roomlist[$i]['id'],
    "name" => $room->roomlist[$i]['name'],
    "type" => $room->roomlist[$i]['type'],
    "userscount" => $users_count
  ];
}
// Sort rooms by users count
krsort($main_roomlist);
reset($main_roomlist);
while (list($key, $val) = each($main_roomlist)) {
  while (list($key2, $val2) = each($val)) {
    $main_rooms[] = $val2;
  }
}

if (!$admin_manage_rooms && !empty($session->config->default_room)) {
  // There is default room. Redirect user into it.
?>
<html>
<body onload="document.redirectform.submit();">
  <form name="redirectform" method="post" target="_parent" action="main.php">
    <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="include" value="4">
    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($session->config->default_room, ENT_QUOTES, 'UTF-8'); ?>">
  </form>
</body>
</html>
<?php
  die();
}

$main_rooms_count = count($main_rooms);
if (!$admin_manage_rooms && $main_rooms_count == 1 && !$session->config->allow_userrooms) {
  // There is only one main room and user rooms are disabled. Redirect to main room
?>
<html>
<body onload="document.redirectform.submit();">
  <form name="redirectform" method="post" target="_parent" action="main.php">
    <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="include" value="4">
    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($main_rooms[0]['id'], ENT_QUOTES, 'UTF-8'); ?>">
  </form>
</body>
</html>
<?php
  die();
}

if ($admin_manage_rooms || $session->config->allow_userrooms) {
  // User rooms
  $room_creator = new user();
  // User rooms without password
  $room->listRooms($session, 0, "", 1);
  $user_roomlist = [];
  $roomlist_count = count($room->roomlist);
  for ($i = 0; $i < $roomlist_count; $i++) {
    $users_count = count($session->listRoomUsers($room->roomlist[$i]['id']));
    common::doHtmlEntities($room->roomlist[$i]['name']);
    $room_creator->readUser($session, $room->roomlist[$i]['creator_id']);
    common::doHtmlEntities($room_creator->login);
    $user_roomlist[$users_count][] = [
      "id" => $room->roomlist[$i]['id'],
      "name" => $room->roomlist[$i]['name'],
      "type" => $room->roomlist[$i]['type'],
      "userscount" => $users_count,
      "creator" => $room_creator->login
    ];
  }
  // Sort rooms by users count
  krsort($user_roomlist);
  reset($user_roomlist);
  while (list($key, $val) = each($user_roomlist)) {
    while (list($key2, $val2) = each($val)) {
      $user_rooms[] = $val2;
    }
  }
  // User rooms with password
  $room->listRooms($session, 0, "", 3);
  $user_roomlist = [];
  $roomlist_count = count($room->roomlist);
  for ($i = 0; $i < $roomlist_count; $i++) {
    $users_count = count($session->listRoomUsers($room->roomlist[$i]['id']));
    common::doHtmlEntities($room->roomlist[$i]['name']);
    $room_creator->readUser($session, $room->roomlist[$i]['creator_id']);
    common::doHtmlEntities($room_creator->login);
    $user_roomlist[$users_count][] = [
      "id" => $room->roomlist[$i]['id'],
      "name" => $room->roomlist[$i]['name'],
      "type" => $room->roomlist[$i]['type'],
      "userscount" => $users_count,
      "creator" => $room_creator->login
    ];
  }
  // Sort rooms by users count
  krsort($user_roomlist);
  reset($user_roomlist);
  while (list($key, $val) = each($user_roomlist)) {
    while (list($key2, $val2) = each($val)) {
      $user_rooms[] = $val2;
    }
  }
  $user_rooms_count = count($user_rooms);
}

/* Loading room select page template */
require TEMPLATEPATH . "/selectroom_list.tpl.php";
?>
