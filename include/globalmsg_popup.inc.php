<?php

// Read message
$message = globalMessage::readMessage($session, $msg_id);

$sender = new user();
$sender->readUser($session, $message['user_id']);

// Load template
require TEMPLATEPATH . "/globalmsg_popup.tpl.php";
?>