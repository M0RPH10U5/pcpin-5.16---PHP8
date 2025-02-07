<?php
// Check rights
if (!($current_user->level & 1)) {
  die("HACK?");
}

// Check database tables
if ($session->testDB()) {
  // One or more tables need to be optimized.
  $need_optimization = true;
  if ($optimize_db) {
    // Optimize database tables
    $session->optimizeDB();
    $need_optimization = false;
  }
} else {
  // No database tables optimization needed
  $need_optimization = false;
}

// Users
$user = new user();
$registered_users_count = 0;
$registered_users_online_count = 0;
$guests_count = 0;
$total_users_online_count = 0;
$user->countUsers($session, $registered_users_count, $registered_users_online_count, $guests_count, $total_users_online_count);

// Rooms
$room = new room();
// Main rooms without password
$room->listRooms($session, 0, "", 0);
$main_rooms_no_pass_count = count($room->roomlist);
// Main rooms with password
$room->listRooms($session, 0, "", 2);
$main_rooms_pass_count = count($room->roomlist);
// User rooms without password
$room->listRooms($session, 0, "", 1);
$user_rooms_no_pass_count = count($room->roomlist);
// User rooms with password
$room->listRooms($session, 0, "", 3);
$user_rooms_pass_count = count($room->roomlist);
$total_rooms_count = $main_rooms_no_pass_count + $main_rooms_pass_count + $user_rooms_no_pass_count + $user_rooms_pass_count;

// Load template
require TEMPLATEPATH . "/admin_statistics.tpl.php";
?>
