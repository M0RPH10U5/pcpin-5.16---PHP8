<?php
/* This is a userlist page. */

// Get room name
$room = new room();
$room->readRoom($session, $session->room_id);
$roomname = $room->name;
common::doHtmlEntities($roomname);

require TEMPLATEPATH . "/userlist.tpl.php";
?>
