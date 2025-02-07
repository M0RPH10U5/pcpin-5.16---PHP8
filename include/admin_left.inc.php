<?php
/* This is an Admin navication frame. */
$user = new user();
$user->readUser($session, $session->user_id);
if (!$user->level) {
    die("Hack?");
}

$cssurl = new cssURL($session->db);

require TEMPLATEPATH . "/admin_left.tpl.php";
?>