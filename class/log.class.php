<?php
/****************************************************************************
CLASS log
-----------------------------------------------------------------------------
Task:
  Manage chat logs
****************************************************************************/

class log{

  /**************************************************************************
  log
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create log object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    // Constructor can be customized if needed
  }

  /**************************************************************************
  saveMessage
  ---------------------------------------------------------------------------
  Task:
    Save chat message
  ---------------------------------------------------------------------------
  Parameters:
    $session          object        Session handle
    $type             int           Message type
    $room_id          int           Room id
    $from_user_id     int           Message sender
    $to_user_id       int           Message receiver
    $body             string        Message body
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function saveMessage(&$session, $type = 0, $room_id = 0, $sender_id = 0, $receiver_id = 0, $body = '') {
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $room_name = '';
    $sender_ip = '';
    $sender_login = '';
    $receiver_ip = '';
    $receiver_login = '';

    if ($room_id) {
      $room = new room();
      $room->readRoom($session, $room_id);
      $room_name = $room->name;
    }

    // Sender's data
    if ($sender_id > 0) {
      $user = new user();
      $user->readUser($session, $sender_id);
      $sender_login = $user->login;
      $sender_ip = $session->ip;
    } else {
      $sender_login = '';
      $sender_ip = '';
    }

    // Receiver's data
    if ($receiver_id > 0) {
      $user = new user();
      $user->readUser($session, $receiver_id);
      $receiver_login = $user->login;
      $session_tmp = new session($session->getUsersSession($receiver_id));
      $receiver_ip = $session_tmp->ip;
    } else {
      $receiver_id = '';
      $receiver_ip = '';
    }

    // Prepare record
    $record = array(
      $date,
      $time,
      $type,
      $room_id,
      $room_name,
      $sender_ip,
      $sender_id,
      $sender_login,
      $receiver_ip,
      $receiver_id,
      $receiver_login,
      $body
    );

    // Escape problematic characters (like quotes and semicolons)
    foreach ($record as $i => $field) {
      if (strpos($field, '"') !== false || strpos($field, ';') !== false) {
        $record[$i] = '"' . str_replace('"', '""', $field) . '"';
      }
    }

    // Convert the record array into a string for logging
    $record_string = implode(';', $record);

    // Check directory
    clearstatcache();
    if (!is_dir('logs/' . $session->config->logdir)) {
      // Directory does not exist, attempting to create it
      mkdir(LOGSPATH . '/' . $session->config->logdir);
      // Creating index.php in new directory (to avoid directory listing)
      $handle = fopen(LOGSPATH . '/' . $session->config->logdir . '/index.php', 'w');
      fclose($handle);
    }

    // Open (create) log file
    $handle = fopen(LOGSPATH . '/' . $session->config->logdir . '/' . date('Ymd') . '_log.csv', 'a');

    // Write line into file
    fwrite($handle, $record_string . "\r\n");

    // Close file
    fclose($handle);
  }
}
?>