<?php
/* Kick users */

// Check rights
if (!(($current_user->level) & 16)) {
    die("Access Denied");
}

// Superuser Protection
$target_user = new user();
$target_user->readUser($session, $profile_user_id);
if ($target_user->level >= 131071) {
    die("Access Denied");
}

// Get user's session ID
$session2 = new session($session->getUsersSession($profile_user_id));
if ($session2->session_id) {
    // Update user's session
    $session2->updateSession("kicked = 1");
    // Post a system message
    systemMessage::insertMessage($session, $session->user_id, 6);
}

if (!empty($dummy)) {
    // Load dummy form
    header("Location: main.php?include=30&sessionid=$session_id");
} else {
    // Return to userlist
    header("Location: main.php?include=11&kick=1&session_id=$session_id");
}
?>