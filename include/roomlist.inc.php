<?php
/* This is a roomlist page. */

// Load user data
$user = new user();
$user->readUser($session);

require TEMPLATEPATH . "/roomlist.tpl.php";
?>
