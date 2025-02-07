<?php
/* This is a PCPIN Chat main file */

// Check "/install" directory
if (file_exists('./install')){
  die('<html><body><center><h3>Chat locked</h3><br />Delete directory <b>install</b> in order to continue.</center></body></html>');
}

/* Offset */
define('OFFSET', './');

/* Load configuration */
require './config/config.inc.php';

/* Execute global actions and load classes */
require './config/prepend.inc.php';

/* Load database connection settings */
include './config/db.inc.php';

/* Creating session handle */
if (empty($session_id)){
  $session_id = '';
}
$session = new session($session_id);

/* Deleting old sessions */
$session -> cleanUp();

/* Update max users online counters */
$maxusers = new maxusers($session);

if (!empty($session -> kicked)) {
  /* User was kicked */
  // Delete session
  $session -> logout($session_id);
  // Redirect user
?>
<html>
<head>
    <script>
        parent.window.location.href = "<?php echo htmlspecialchars($session->config->kick_url, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
</head>
</html>
<?php
  die();
} elseif (empty($session->user_id)) {
  /* Session is timed out or does not exists. Loading login page. */
  $include = 2;
} else {
  /* Session is OK. Loading user. */
  $current_user = new user();
  $current_user->readUser($session, $session->user_id);
}

if (empty($include)){
  $include = 2;
}

/* Check language */
if (empty($language)){
  if (!empty($session->language)){
    $language = $session->language;
  } else {
    $language = '';
  }
}

/* Looking for language file */
$lng_files_realpath = str_replace("\\", '/', strtolower(realpath(LANGUAGEPATH)));
$lng_path = LANGUAGEPATH . '/' . $language . '.lng.php';
$lng_realpath = str_replace("\\", '/', strtolower(realpath(dirname($lng_path))));
if (   $lng_realpath == $lng_files_realpath
   && file_exists($lng_path)
   && is_file($lng_path)
   && is_readable($lng_path)){
  // Language file found. Loading it
  $ISO_639_LNG = NULL;
  include $lng_path;
}
if (empty($ISO_639_LNG)){
  if ($session->config->standard_language){
    $lng_path = LANGUAGEPATH . '/' . $session->config->standard_language . '.lng.php';
    $lng_realpath = str_replace("\\", '/', strtolower(realpath(dirname($lng_path))));
    if ($lng_realpath == $lng_files_realpath
       && file_exists($lng_path)
       && is_file($lng_path)
       && is_readable($lng_path)){
      include $lng_path;
    }
  }
}
if (empty($ISO_639_LNG)){
  /* No language selected or language file not found or language file is invalid.
     Redirecting to language selection page. */
  $include = 1;
}

$cssurl = new cssURL($session->db);
if (!empty($cssurl->cssurl)){
  // Use external CSS
  $css = '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($cssurl->cssurl, ENT_QUOTES, 'UTF-8') . '">';
  $css_short = '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($cssurl->cssurl, ENT_QUOTES, 'UTF-8') . '">';
} else {
  // Read CSS from database
  $cssclass = new cssClass();
  $css = $cssclass->generateCSS($session->db);
  // Short CSS for refreshers (Body background color only)
  $css_short = $cssclass->generateCSSBodyBGColor($session->db);
}

/* Loading appropriate page */
switch($include){
  CASE 1  :  /* Language selection page */
             require INCLUDEPATH . "/language.inc.php";
             break;
  CASE 2  :  /* Login page */
             require INCLUDEPATH . "/login.inc.php";
             break;
  CASE 3  :  /* Room selection */
             // Save screen height
             $session->updateSession('screen_height = ' . $screen_height);
             require INCLUDEPATH . "/selectroom.inc.php";
             break;
  CASE 4  :  /* Main chat page */
             switch($frame){
               CASE "i" :    /* Input frame */
                             require INCLUDEPATH ."/input.inc.php";
                             break;
               CASE "c" :    /* Control frame */
                             require INCLUDEPATH ."/control.inc.php";
                             break;
               DEFAULT  :    /* Frameset */
                             require INCLUDEPATH ."/frames_main.inc.php";
                             break;
             }
             break;
  CASE 5  :  /* Show/edit user profile */
             require INCLUDEPATH ."/userprofile.inc.php";
             break;
  CASE 6  :  /* Color select box */
             require INCLUDEPATH . "/colorbox.inc.php";
             break;
  CASE 7  :  /* Private message window */
             requireINCLUDEPATH . "/frames_pm.inc.php";
             break;
  CASE 8  :  /* 'Create new room' form */
             require INCLUDEPATH . "/createroom.inc.php";
             break;
  CASE 9  :  /* Exit chat */
             require INCLUDEPATH . "/logout.inc.php" ;
             break;
  CASE 10 :  /* Password promt for password-protected rooms */
             require INCLUDEPATH . "/askroompassword.inc.php";
             break;
  CASE 11 :  /* Memberlist */
             require INCLUDEPATH . "/memberlist.inc.php";
             break;
  CASE 12 :  /* Invite user */
             require INCLUDEPATH . "/invite.inc.php";
             break;
  CASE 13 :  /* Admin frameset */
             require INCLUDEPATH . "/admin_frames.inc.php";
             break;
  CASE 14 :  /* Admin: left frame */
             require INCLUDEPATH . "/admin_left.inc.php";
             break;
  CASE 15 :  /* Admin: chat statistics */
             require INCLUDEPATH . "/admin_statistics.inc.php";
             break;
  CASE 16 :  /* Admin: chat design */
             require INCLUDEPATH . "/admin_design.inc.php";
             break;
  CASE 17 :  /* Admin: chat settings */
             require INCLUDEPATH . "/admin_settings.inc.php";
             break;
  CASE 18 :  /* Admin: edit users */
             require INCLUDEPATH . "/admin_editusers.inc.php";
             break;
  CASE 19 :  /* Admin: kick users */
             require INCLUDEPATH . "/admin_kickusers.inc.php";
             break;
  CASE 20 :  /* Admin: ban users */
             require INCLUDEPATH . "/admin_banusers.inc.php";
             break;
  CASE 22 :  /* Photo upload */
             require INCLUDEPATH . "/photo_upload.inc.php";
             break;
  CASE 23 :  /* Admin: Post global message */
             require INCLUDEPATH . "/admin_globalmsg.inc.php";
             break;
  CASE 24 :  /* Global message Pop-Up */
             require INCLUDEPATH . "/globalmsg_popup.inc.php";
             break;
  CASE 25 :  /* Admin: Advertisement */
             require INCLUDEPATH . "/admin_advertisement.inc.php";
             break;
  CASE 26 :  /* Admin: Smilies */
             require INCLUDEPATH . "/admin_smilies.inc.php";
             break;
  CASE 27 :  /* Smilies */
             require INCLUDEPATH . "/smilies.inc.php";
             break;
  CASE 28 :  /* Admin: Bad words */
             require INCLUDEPATH . "/admin_badwords.inc.php";
             break;
  CASE 29 :  /* Admin: Rooms */
             require INCLUDEPATH . "/admin_rooms.inc.php";
             require INCLUDEPATH . "/selectroom.inc.php";
             break;
  CASE 30 :  /* Dummy form */
             require INCLUDEPATH . "/dummyform.inc.php";
             break;
  CASE 31 :  /* Admin: Export design */
             require INCLUDEPATH . "/admin_export_design.inc.php";
             break;
  CASE 32 :  /* Admin: Edit room */
             require INCLUDEPATH . "/admin_edit_room.inc.php";
             break;
  CASE 33 :  /* Messages frame template */
             require INCLUDEPATH . "/mainframe.inc.php";
             break;
  CASE 40 :  /* Admin: clear screen */
             require INCLUDEPATH . "/admin_clearscreen.inc.php";
             break;
  DEFAULT :  /* Hack? */
             die("Hack?");
             break;
}
die();
?>
