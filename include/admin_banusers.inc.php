<?php
/* Ban user */

// Check rights
if (!($current_user->level & 32)) {
  die("HACK?");
}

$user = new user();

// Superuser protection
if (!empty($profile_user_id)) {
  $target_user = new user();
  $target_user->readUser($session, $profile_user_id);
  if ($target_user->level >= 131071) {
    die('Access denied');
  }
}

if ($list) {
  if ($removefromlist) {
    // Remove users / IP addresses from banlist
    if (is_array($ban_id)) {
        foreach ($ban_id as $id => $dummy) {
            ban::unBan($session, $id);
        }
    }
  }
  // Show banlist
  $usr_sortby = $usr_sortby ?? 0;
  $usr_sortdir = $usr_sortdir ?? 0;
  $ip_sortby = $ip_sortby ?? 0;
  $ip_sortdir = $ip_sortdir ?? 0;

  $banlist = ban::banList($session, $usr_sortby, $usr_sortdir, $ip_sortby, $ip_sortdir);
  $banlist_count = count($banlist);
  $banlist_users = [];
  $banlist_ips = [];

  for ($i=0; $i < $banlist_count; $i++) {
    if ($banlist[$i][user_id]) {
      // Banned user
      common::doHtmlEntities($banlist[$i]['login']);
      $banlist_users[] = [
        "name" => $banlist[$i]['login'],
        "bandate" => common::convertDateFromTimestamp($session, $banlist[$i]['bandate']),
        "id" => $banlist[$i]['id']
      ];
    } else {
      // Banned IP address
      $banlist_ips[] = [
        "ip" => $banlist[$i]['ip'],
        "bandate" => common::convertDateFromTimestamp($session, $banlist[$i]['bandate']),
        "id" => $banlist[$i]['id']
      ];
    }
  }
  $banlist_users_count = count($banlist_users);
  $banlist_ips_count = count($banlist_ips);
  // Load template
  require(TEMPLATEPATH . "/admin_banlist.tpl.php");
} elseif ($profile_user_id && $profile_user_id !== $session->user_id) {
  if ($do_ban && ($user_id || $ip)) {
    // First, kick user if he is online
    if ($session->isOnline($user_id)) {
      // Post a control message
      $session2 = new session($session->getUsersSession($user_id));
      if ($session2->id) {
        // Update user's session
        $session2->updateSession('kicked = 1');
        // Post a system message
        systemMessage::insertMessage($session, $user_id, 6);
      }
    }
    ban::banUser($session, $user_id, $ip);
    // Show banlist
    header("Location: main.php?session_id=$session_id&include=$include&list=1");
  } else {
    $user = new user();
    $user->readUser($session, $profile_user_id);
    common::doHtmlEntities($user->login);
    // Load template
    require(TEMPLATEPATH . "/admin_banuser.tpl.php");
  }
} else {
  header("Location: main.php?session_id=$session_id&include=$include&list=1");
}
?>