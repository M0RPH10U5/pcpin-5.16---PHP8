<?php
// Check Rights
if (!($current_user->level & 2048)) {
  die("HACK?");  
}

$admin_manage_rooms = true;

if ($delete) {
    // Delete Room
    $room = new room();
    // Are there any users in room?
    $roomusers = $session->listRoomUsers($room_id);
    $roomusers_count =  count($roomusers);
    if ($roomusers_count) {
        // There are users in room. Redirect all users to room selection page.
        for ($i =0; $i < $roomusers_count; $i++) {
            // Posting control message
            systemmessage::insertMessage($session, $roomusers[$i]['user_id'] . "|-1", 9);
        }
    }
    $room->deleteRoom($session, $room_id);
}
?>