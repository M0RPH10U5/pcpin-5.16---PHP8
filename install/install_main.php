<?php
/*
 * ---------------------------------------------
 * This file installs/updates PCPIN Chat
 * ---------------------------------------------
 * Author: Kanstantin Reznichak <k.reznichak@pcpin.com>
 * Homepage: http://www.pcpin.com/
 * Support forum: http://community.pcpin.com/
 * ----------------------------------------------
 */

define('PCPIN_CHAT_VERSION', '5.16.8');

// Includes directory
define('PCPIN_INSTALL_INCLUDES', PCPIN_INSTALL_BASEDIR . '/include');

// Templates directory
define('PCPIN_INSTALL_TEMPLATES', PCPIN_INSTALL_BASEDIR . '/template');

// Images directory
define('PCPIN_INSTALL_IMAGES', PCPIN_INSTALL_BASEDIR . '/image');


// Check lock file
if (file_exists('./install_lock.php')) {
?>
<html>
  <body>
    <div align="center">
      <h3>Installation locked</h3>
      <br />
      Delete file <b>install_lock.php</b> in order to continue.
    </div>
  </body>
</html>
<?php
  die();
}

// Defaults
if (!isset($include) || !is_scalar($include)) {
  $include = 0;
}
$_body_onload = '';
$errortext = [];


$_progress = '';

if (empty($framed)) {
  // Load frameset
  require_once(PCPIN_INSTALL_INCLUDES . '/frameset.inc.php');
} else {
  // Load contents
  ob_start();
  if (!empty($include)) {
    $_progress = '';
    // Load database connection settings
    @include('./config/db.inc.php');
    if ($include >= 1000) {
      // Load progress line
      require_once(PCPIN_INSTALL_INCLUDES . '/progress.inc.php');
      if ($include > 1000) {
        // Establish database connection
        require_once(PCPIN_INSTALL_INCLUDES . '/db_connection.inc.php');
        if (!empty($errortext)) {
          // Something wrong with database connection
          $include = 1000;
        }
      }
    }
  }
  // Check data
  switch ($include) {
    default   :
    case    0 :   // Welcome page
                  require_once(PCPIN_INSTALL_INCLUDES . '/welcome.inc.php');
                  break;
    case   10 :   // Control frame
                  require_once(PCPIN_INSTALL_INCLUDES . '/ctl.inc.php');
                  break;
    case 1000 :   // Database connection settings
                  require_once(PCPIN_INSTALL_INCLUDES . '/db_settings.inc.php');
                  break;
    case 1100 :   // Files, directories and permissions
                  require_once(PCPIN_INSTALL_INCLUDES . '/filesystem.inc.php');
                  break;
    case 1200 :   // Import settings
                  require_once(PCPIN_INSTALL_INCLUDES . '/import_settings.inc.php');
                  break;
    case 1300 :   // Create Administrator account
                  require_once(PCPIN_INSTALL_INCLUDES . '/admin_account.inc.php');
                  break;
    case 1400 :   // Final configuration
                  require_once(PCPIN_INSTALL_INCLUDES . '/chat_settings.inc.php');
                  break;
    case 1500 :   // Install chat
                  require_once(PCPIN_INSTALL_INCLUDES . '/install_chat.inc.php');
                  break;
  }
  $_contents = ob_get_clean();
  require_once(PCPIN_INSTALL_INCLUDES . '/main.inc.php');
}
die();
?>