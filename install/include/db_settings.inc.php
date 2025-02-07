<?php
$next_include = 1100;


// Set defaults
if (empty($submitted)) {
  if (file_exists('./config/db.inc.php') && is_readable('./config/db.inc.php')) {
    @include_once('./config/db.inc.php');
  }
}

$db_host = defined('DBSERVER') ? DBSERVER : ($db_host ?? 'localhost');
$db_user = defined('DBLOGIN') ? DBLOGIN : ($db_user ?? 'root');
$db_pw = defined('DBPASSWORD') ? DBPASSWORD : ($db_pw ?? '');
$db_db = defined('DBSEGMENT') ? DBSEGMENT : ($db_db ?? 'pcpin');
$db_prefix = defined('PREFIX') ? PREFIX : ($db_prefix ?? 'pcpin_');

if (empty($submitted)) {
  // Get databases list
  $read_only = true;
  $quiet = true;
  require_once(PCPIN_INSTALL_INCLUDES . '/db_connection.inc.php');
} else {
  // Connect to database and check privileges
  $read_only = false;
  $quiet = false;
  require_once(PCPIN_INSTALL_INCLUDES . '/db_connection.inc.php');

  if (empty($errortext) && $db_db !== '') {
    // No errors
    // Create new db.inc.php file
    $db_inc_body = "<?php\r\n";
    $db_inc_body .= "/* This file contains information required to connect to database.\r\n";
    $db_inc_body .= "   This file was created automatically during the installation.\r\n";
    $db_inc_body .= "   EDIT IT ONLY IF YOU KNOW WHAT ARE YOU DOING!\r\n";
    $db_inc_body .= "*/\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* If you don't know your MySQL access data, then ask your webhosting provider */\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* Database server host name or IP address */\r\n";
    $db_inc_body .= "/* Example: 'db.myhost.com' or '127.0.0.1' */\r\n";
    $db_inc_body .= "DEFINE('DBSERVER','$db_host');\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* Database username */\r\n";
    $db_inc_body .= "DEFINE('DBLOGIN','$db_user');\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* Database password */\r\n";
    $db_inc_body .= "DEFINE('DBPASSWORD','$db_pw');\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* Database name */\r\n";
    $db_inc_body .= "DEFINE('DBSEGMENT','$db_db');\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "/* Prefix for all chat table names */\r\n";
    $db_inc_body .= "DEFINE('PREFIX','$db_prefix');\r\n";
    $db_inc_body .= "\r\n";
    $db_inc_body .= "?>";
    if (!empty($do_download)) {
      // "db.inc.php" download requested
      header('Expires: Mon, 01 Jan 2000 11:11:11 GMT');
      header('Content-Disposition: attachment; filename="db.inc.php"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Type: text/plain');
      header('Content-Length: ' . STRLEN($db_inc_body));
      echo $db_inc_body;
      die();
    }
    if (  defined('DBSERVER')   && DBSERVER == $db_host
       && defined('DBLOGIN')    && DBLOGIN == $db_user
       && defined('DBPASSWORD') && DBPASSWORD == $db_pw
       && defined('DBSEGMENT')  && DBSEGMENT == $db_db
       && defined('PREFIX')     && PREFIX == $db_prefix
       ) {
      // db.inc.php already contains correct data
      $db_inc_created = true;
    } else {
      // Create new file
      if (@file_put_contents('./config/db.inc.php', $db_inc_body) !== false) {
        // db.inc.php created
        $db_inc_created=true;
      } else {
        // Failed to open database configuration file for writing
        $db_inc_created = false;
      }
    }
  }
}

if(!empty($db_inc_created)) {
  // Load next page
  header('Location: ./install.php?framed=1&include=' . $next_include . '&timestamp=' . md5(microtime()));
  exit();
}

// Load template
require_once PCPIN_INSTALL_TEMPLATES . '/db_settings.tpl.php';
?>