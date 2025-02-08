<?php
// Current user
$current_user = new user();
$current_user->readUser($session, $session->user_id);

// Defaults
if (!$orderby) {
  $orderby = "us.login";
}
if (!$orderdir) {
  $orderdir = "ASC";
}
if (empty($page) || !is_scalar($page) || $page < 1) {
  $page = 1;
} else {
  $page = round($page);
}

// Admin?
if ($current_user->level & 8 || $current_user->level & 16 || $current_user->level & 32) {
  // Show IP addresses
  $show_ip = true;
} else {
  // Don't show IP addresses
  $show_ip = false;
}
// Check rights
if ($edit) {
  if (!($current_user->level & 8)) {
    unset($edit);
  }
}
if ($kick) {
  if (!($current_user->level & 16)) {
    unset($kick);
  }
}
if ($ban) {
  if (!($current_user->level & 32)) {
    unset($ban);
  }
}

// Create userlist
$users_per_page = 20;
$registered_users_count = 0;
$dummy = 0;
$guests_count = 0;
$dummy2 = 0;
user::countUsers($session, $registered_users_count, $dummy, $guests_count, $dummy2);
$total_users = $registered_users_count + $guests_count;
if (!empty($kick)) {
  // No need page splitting for online userlist
  $total_pages = 1;
  $userlist = user::listUsers($session, $username, $orderby, 0, 0, 1);
} else {
  $total_pages = ceil($total_users / $users_per_page);
  $userlist = user::listUsers($session, $username, $orderby, ($page - 1) * $users_per_page, $users_per_page);
}
$userlist_count = count($userlist);

// Prepare list
$new_list = [];
for ($i = 0; $i < $userlist_count; $i++) {
  if (!$kick || $userlist[$i]['online']) {
    common::doHtmlEntities($userlist[$i]['login']);
    common::doHtmlEntities($userlist[$i]['name']);
    if (empty($userlist[$i]['email'])) {
      $userlist[$i]['email'] = "&nbsp;";
    }
    if (empty($userlist[$i]['name'])) {
      $userlist[$i]['name'] = "&nbsp;";
    }
    $new_list[] = $userlist[$i];
  }
}
$userlist = $new_list;
$userlist_count = count($userlist);

require TEMPLATEPATH . "/memberlist.tpl.php";
?>