<?php
/* Chat settings */

// Check Rights
if (!($current_user->level & 4)) {
    die("Hack?");
}

if ($settings_submitted) {
    // Save changes
    reset($configuration);
    while (list($key, $val) = each($configuration)) {
        $session->config->changeParameter($session, $key, $val);
    }
    $ession->config = new configuration($session->db);
    $room = new room();
    // Main Rooms
    $room->listRooms($session, 0, "", 2);
    for ($i = 0; $i < count($room->roomlist); $i++) {
        $room_users = $session->listRoomUsers($room->roomlist[$i]['id']);
        for ($ii = 0; $ii < count($room_users); $ii++) {
            $roompass->createPass($session, $room->roomlist[$i]['id'], $room_users[$ii]['user_id']);
        }
    }
    // Restarting all users
    systemMessage::insertMessage($session, "", 10);
}
?>