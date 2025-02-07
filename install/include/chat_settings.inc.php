<?php
$next_include = 1500;

// Which data shall be imported?
$keep_settings = false;
$stmt = $conn->prepare('SELECT `value` FROM `' . PREFIX . '_chat-installdata` WHERE `name` = BINARY "keep_settings" LIMIT 1');
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if ($data && !empty($data['value']) && false !== $keep_settings = unserialize($data['value'])) {
    $keep_settings = !empty($keep_settings['Chat settings']);
}

// Determine chat URL
$port = '';
$protocol = 'http://';
$host = '';
$uri = '';
if (!empty($_SERVER)) {
  // Port
  if (!empty($_SERVER['SERVER_PORT'])) {
    $port = $_SERVER['SERVER_PORT'];
  }
  $port_string = ($port <> 80 && $port <> 443) ? ':' . $port : '';
  // Protocol
  $protocol = ($port <> 443) ? 'http://' : 'https://';
  // Hostname
  $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
  $host = trim($host, '/');
  // URI
  $uri = $_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'];
  $uri = dirname($uri);
}
// Create URL
$chat_dir = $protocol . str_replace('//', '/', $host . $port_string . '/' . $uri . '/');
$url = $protocol . str_replace('//', '/', $host.$port_string . '/' . $uri . '/main.php');


if (empty($submitted)) {
  // Configuration defaults
  $settings = [
    'title'=> [
        'value' => 'My Chat',
        'description1' => 'What is the name of your chat?',
        'description2' => 'This appears in the document title of all pages.'],
        ###############################################################################################
    'sender_name' => [
        'value' => 'Chat Administrator',
        'description1' => 'Chat owner\'s name.',
        'description2' => 'Will be displayed as sender name in all system emails sent by this chat.'],
        ###############################################################################################
    'sender_email' => [
        'value' => 'chat_admin@' . ltrim($host, 'www.'),
        'description1' => 'Chat owner\'s email address.',
        'description2' => 'Will be displayed as sender email address in all system emails sent by this chat.'],
        ###############################################################################################
    'homepage'=> [
        'value' => $chat_dir,
        'description1' => 'Your chat directory.',
        'description2' => ''],
        ###############################################################################################
    'exit_url'=> [
        'value' => $url,
        'description1' => '"Logged out" page address.',
        'description2' => 'All logged out users will be redirected to that page.'],
        ###############################################################################################
    'kick_url'=> [
        'value' => $url,
        'description1' => '"Kicked out" page address.',
        'description2' => 'All kicked out users will be redirected to that page.'],
        ###############################################################################################
  ];
}

// Import settings?
$old_config = [];
if (!empty($keep_settings)) {
  // Load existing configuration
  $stmt = $conn->prepare('SELECT `name`, `value` FROM `' . PREFIX . 'configuration`');
  $stmt->execute();
  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $old_config[$data['name']] = $data['value'];
  }
}

if (empty($submitted) && !empty($keep_settings) && !empty($old_config)) {
  // Apply existing configuration
  foreach ($old_config as $name => $value) {
    if (array_key_exists($name, $settings)) {
      $settings[$name]['value'] = $value;
    }
  }
}

if (!empty($submitted)) {
  // Save data
  $settings_new = [];
  // Apply existing configuration
  foreach ($old_config as $name => $value) {
    if (!array_key_exists($name, $settings)) {
      $settings_new[$name] = $value;
    }
  }
  foreach ($settings as $key => $val) {
    $settings_new[$key] = $val;
  }
  if (empty($keep_settings)) {
    $settings_new['logdir'] = common::randomString(12);
  }
  $stmt = $conn->prepare('DELETE FROM `' . PREFIX . '_chat_installdata` WHERE `name` = BINARY "configuration" LIMIT1');
  $stmt->execute();

  $stmt = $conn->prepare('INSERT INTO `' . PREFIX . '_chat_installdata` (`name`, `value`) VALUES ("configuration", :value)');
  $stmt->bindParam(':value', serialize($settings_new), PDO::PARAM_STR);
  $stmt->execute();

  // Load next page
  header('Location: ./install.php?framed=1&include=' . $next_include . '&timestamp=' . md5(microtime()));
  die();
}

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/chat_settings.tpl.php');
?>