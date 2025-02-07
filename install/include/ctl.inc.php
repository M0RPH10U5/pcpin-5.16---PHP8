<?php
// Load database connection settings
@include('./config/db.inc.php');
// Establish database connection
$quiet = true;
$read_only = true;
require_once(PCPIN_INSTALL_INCLUDES . '/db_connection.inc.php');

if (isset($step)) {

  // Load database connection settings
  require_once('./config/db.inc.php');
  // Establish database connection
  $quiet = true;
  $read_only = true;
  require_once(PCPIN_INSTALL_INCLUDES . '/db_connection.inc.php');
  $in_progress = false;
  switch ($step) {
    case  0   :   // Store import data
                  storeImportData($conn);
                  break;
    case  1   :   // Create database structure
                  makeDatabase($conn);
                  break;
    case  2   :   // Install data
                  installData($conn);
                  break;
    case  3   :   // Import stored data
                  if (restoreImportData($conn)) {
                    $step--;
                    $in_progress = true;
                  }
                  break;
    case  4   :   // Save admin account
                  saveAdminAccount($conn);
                  // Set new version number
                  setVersion($conn);
                  // Save data from "Final configuration" form
                  storeSettings($conn);
                  break;
    case  5   :   // Cleanup
                  doCleanup($conn);
                  break;
    case  6   :   // Empty step
                  $in_progress = true;
                  break;
    case  7   :   // Empty step
                  break;
    default   :   // End
                  $step = -1;
                  break;
  }
  if ($step >= 0) {
    $_body_onload .= ' doStep(' . ($step + 1) . ', ' . ($in_progress ? 'true' : 'false') . '); ';
  }
}

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/ctl.tpl.php');
 
function storeImportData(&$conn) {
  $keep_data = false;
  $stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "keep_settings" LIMIT 1');
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    if (!empty($data['value'])) {
        $keep_data = @unserialize($data['value']);
    } else {
        $keep_data = false;
    }
  }
  if (!empty($keep_data)) {
    $backup_tables = [];
    $keep_data = array_keys($keep_data);
    foreach ($keep_data as $name) {
      switch ($name) {
        case  'Chat settings'   :   // Chat settings
                                    $tables = [
                                        'configuration',
                                        'maxusers'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Chat design'     :   // Chat design
                                    $tables = [
                                        'cssclass',
                                        'cssproperty',
                                        'cssurl',
                                        'fk_cssvalue'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Users'           :   // Users
                                    $tables = [
                                        'user'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Rooms'           :   // Rooms
                                    $tables = [
                                        'room'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Bad words filter':   // Bad words filter
                                    $tables = [
                                        'badword'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Advertisements'  :   // Advertisements
                                    $tables = [
                                        'advertisement',
                                        'fk_advertisement'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Smilies'         :   // Smilies
                                    $tables = [
                                        'smilie'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
        case  'Bans'            :   // Bans
                                    $tables = [
                                        'ban'
                                    ];
                                    storeTables($conn, $backup_tables, $tables);
                                    break;
      }
    }
  }
  $stmt = $conn->prepare('DELETE FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "keep_settings" LIMIT 1');
  $stmt->execute();
  $stmt = $conn->prepare('DELETE FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "stored_tables" LIMIT 1');
  $stmt->execute();
  if (!empty($backup_tables)) {
    $stmt = $conn->prepare('INSERT INTO `' . PREFIX . '_chat_installdata` (`name`, `value`) VALUES ("stored_tables", ?)');
    $stmt->bind_param('s', serialize($backup_tables));
    $stmt->execute();
  }
}


function storeTables(&$conn, &$backup_tables, $tables) {
  foreach ($tables as $table) {
    $stmt = $conn->prepare('ALTER TABLE `' . PREFIX . $table . '` RENAME `' . PREFIX . '_STORED_' . $table . '`');
    $stmt->execute();
    $backup_tables[PREFIX . $table] = PREFIX . '_STORED_' . $table;
  }
}


function storeSettings($conn) {
    $stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "configuration" LIMIT 1');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if ($settings = @unserialize($data['value'])) {
            foreach ($settings as $name => $value) {
                $stmt = $conn->prepare('UPDATE `' . PREFIX . 'configuration` SET `value` = ? WHERE `name` = BINARY ? LIMIT 1');
                $stmt->bind_param('ss', $value, $name);
                $stmt->execute();
            }
        }
    }
}

function makeDatabase($conn) {
    if ($h = fopen(PCPIN_INSTALL_BASEDIR . '/db_structure.dat', 'r')) {
        while (!feof($h) && false !== $line = fgets($h)) {
            $line = trim($line);
            $line = rtrim(rtrim($line, ';'));
            if ($line <> '') {
                $line = str_replace('$$$PREFIX$$$', PREFIX, $line);
                $stmt = $conn->prepare($line);
                $stmt->execute();
            }
        }
        fclose($h);
    }
}

function installData($conn) {
    if ($h = fopen(PCPIN_INSTALL_BASEDIR . '/db_data.dat', 'r')) {
        while (!feof($h) && false !== $line = fgets($h)) {
            $line = trim($line);
            $line = rtrim(rtrim($line, ';'));
            if ($line <> '') {
                $line = str_replace('$$$PREFIX$$$', PREFIX, $line);
                $stmt = $conn->prepare($line);
                $stmt->execute();
            }
        }
        fclose($h);
    }
}


function restoreImportData(&$conn) {
    $done = false;
    $stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "stored_tables" LIMIT 1');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (!empty($data['value'])) {
            $tables = @unserialize($data['value']);
        }
    }
    if (!empty($tables)) {
        foreach ($tables as $tgt => $src) {
            break;
        }
        array_shift($tables);
        $stmt = $conn->prepare('TRUNCATE TABLE `' . $tgt . '`');
        $stmt->execute();
        $stmt = $conn->prepare('INSERT INTO `' . $tgt . '` SELECT * FROM `' . $src . '`');
        $stmt->execute();
        $stmt = $conn->prepare('DROP TABLE `' . $src . '`');
        $stmt->execute();
        if ($done = !empty($tables)) {
            $stmt = $conn->prepare('UPDATE `' . PREFIX . '_chat_installdata` SET `value` = ? WHERE `name` = BINARY "stored_tables" LIMIT 1');
            $stmt->bind_param('s', serialize($tables));
            $stmt->execute();
        }
    }
    return $done;
}


function doCleanup(&$conn) {
    $stmt = $conn->prepare('DROP TABLE `' . PREFIX . '_chat_installdata`');
    $stmt->execute();
}


function setVersion(&$conn) {
    $stmt = $conn->prepare('TRUNCATE TABLE `' . PREFIX . 'version`');
    $stmt->execute();
    $stmt = $conn->prepare('INSERT INTO `' . PREFIX . 'version` VALUES (?)');
    $stmt->bind_param('s', PCPIN_CHAT_VERSION);
    $stmt->execute();
}


function saveAdminAccount(&$conn) {
    $stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "admin_account" LIMIT 1');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (!empty($data['value'])) {
            $admin = @unserialize($data['value']);
            $stmt = $conn->prepare('INSERT INTO `' . PREFIX . 'user` (`login`, `email`, `password`, `level`, `joined`, `activated`, `last_login`, `cookie`) VALUES (?, ?, ?, 131071, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), "")');
            $stmt->bind_param('ssss', $admin['login'], $admin['email'], $admin['password']);
            $stmt->execute();
        }
    }
}
?>