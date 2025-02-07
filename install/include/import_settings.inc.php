<?php
$next_include = 1300;

// Ensure a valid database connection
if (!isset($conn) || !$conn instanceof mysqli) {
    die('Database connection error,');
}

// Create temporary table
mysqli_query('DROP TABLE IF EXISTS `' . PREFIX . '_chat_installdata`', $conn);
mysqli_query('CREATE TABLE `' . PREFIX . '_chat_installdata` (
    `name` varchar(255) NOT NULL default "",
    `value` longtext NOT NULL,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB', $conn);


// Search for existing installations and data
$existsing_data = [
    'Chat settings'   =>false,
    'Chat design'     =>false,
    'Users'           =>false,
    'Rooms'           =>false,
    'Bad words filter'=>false,
    'Advertisements'  =>false,
    'Smilies'         =>false,
    'Bans'            =>false
];

$result = mysqli_query($conn, 'SELECT `version` FROM `' . $db_prefix . 'version` ORDER BY `version` DESC LIMIT 1');
$version = null;

if (!mysqli_errno($conn)) {
  $data = mysqli_fetch_assoc($result) ?: ['version' => '5.06']; // Workaround for PCPIN Chat 5.06 missing version number
  ////////////////////////////////////////////////////////////////
  // Workaround for a problem caused by PCPIN Chat 5.06, which has not stored it's version number
  // if(empty($data)){
  //  $data=array('version'=>'5.06');
  // }
  // END: Workaround
  ////////////////////////////////////////////////////////////////
  if (!empty($data['version'])) {
    $old_version = $data['version'];
    $version_parts = explode('.', $old_version);
    $version = $version_parts[0];
    if ($version == '4' || $version == '5') {
      $tables = [
        'bad words filter' => 'badword',
        'Advertisements'   => 'advertisement',
        'Rooms'            => 'room',
        'Users'            => 'user',
        'Chat Settings'    => 'configuration',
        'Chat design'      => 'cssclass',
        'Smilies'          => 'smilie',
        'Bans'             => 'ban'
      ];

      foreach ($tables as $key => $table) {
        $result = mysqli_query($conn, 'SELECT * FROM `' . $db_prefix . $table . '` LIMIT 1');
        if (!mysqli_errno($conn)) {
            $data = mysqli_fetch_array($result, MYSQLI_NUM);
            $existing_data[$key] = !empty($data[0]);
        }
      }
    }
  }
}
$data_found = array_reduce($existing_data, fn($carry, $item) => $carry || $item, false);

if (!empty($import_submitted)) {
  mysqli_query($conn, 'DELETE FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "keep_settings" LIMIT 1');
  
  $keep_settings_valid = [];
  if (!empty($keep_settings) && is_array($keep_settings)) {
    foreach ($keep_settings as $setting) {
      if (!empty($existsing_data[$setting])) {
        $keep_settings_valid[$setting] = true;
      }
    }
  }
  if (!empty($keep_settings_valid)) {
    $serialized_data = mysqli_real_escape_string($conn, serialize($keep_settings_valid));
    mysqli_query($conn, 'INSERT INTO `' . PREFIX . '_chat_installdata` (`name`, `value`) VALUES ("keep_settings", "' . $serialized_data . '")');
  }
  // Load next page
  header('Location: ./install.php?framed=1&include=' . $next_include . '&timestamp=' . md5(microtime()));
  die();
}

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/import_settings.tpl.php');
?>