<?php
/****************************************************************************
CLASS userMessage
-----------------------------------------------------------------------------
Task:
  Manage user messages
****************************************************************************/

class userMessage{

  /* Class variables */

  /* Message type
  *  Type: int
  *  Values:
  *     1:      Normal message
  *     2:      Private message
  *     3:      Whispered message
  *
  */
  public $type = 0;

  /* User ID
  *  Type: int
  */
  public $user_id = 0;

  /* Target user ID (for private messages)
  *  Type: int
  */
  public $target_user_id = 0;

  /* Message body
  *  Type: string
  */
  public $body = '';

  /* Binary coded message flags
  *   Values:
  *     bit#          Description (if bit set)
  *      0 (1)          Bold
  *      1 (2)          Italic
  *      2 (3)          Underlined
  *  Type: integer
  */
  public $flags = 0;

  /* Message post time (TIMESTAMP)
  *  Type: int
  */
  public $post_time = 0;

  /* Messages list
  *  Type: array
  */
  public $messagelist = null;



  /**************************************************************************
  Constructor
  ---------------------------------------------------------------------------
  Task:
    Creates message object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    // Initialize the message object (if necessary)
  }

  /**************************************************************************
  deleteOldMessages
  ---------------------------------------------------------------------------
  Task:
    Deletes old user messages from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Sessionhandle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteOldMessages(&$session) {
    $delete_time = $session->config->main_refresh + 60;
    $query = "DELETE FROM " . PREFIX . "usermessage WHERE post_time + ? < UNIX_TIMESTAMP()";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param("i", $delete_time);
    $stmt->execute();
  }

  /**************************************************************************
  insertMessage
  ---------------------------------------------------------------------------
  Task:
    Inserts user message into database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Sessionhandle
    $target_user_id   int           Target user ID (for private messages)
    $message_body     string        Message body
    $type             int           Message type
    $flags            int           Message flags
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function insertMessage(&$session, $target_user_id = 0, $message_body, $type = 0, $flags = 0) {
    // 'Flood' protection
    if ($session->last_message == $message_body) {
      $session->message_repeat++;
      $session->updateSession("message_repeat = {$session->message_repeat}");
      if ($session->message_repeat > $session->config->flood_max) {
        // User will be kicked due to flooding
        // Superuser protection
        $tmp_user = new user();
        $tmp_user->readUser($session, $session->user_id);
        if ($tmp_user->level < 131071) {
          systemMessage::insertMessage($session, $session->user_id, 6);
          // Update user's session
          $session->updateSession("kicked = 1");
        }
      }
    } else {
      $session->updateSession("last_message = ?, message_repeat = 1", $message_body);
    }

    if ($message_body != '') {
      // Inserting a message using prepared statements
      $query = "INSERT INTO " . PREFIX . "usermessage (type, user_id, target_user_id, body, post_time, flags) 
                VALUES (?, ?, ?, ?, UNIX_TIMESTAMP(), ?)";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param("iiisi", $type, $session->user_id, $target_user_id, $message_body, $flags);
      $stmt->execute();

      // Logging the message if necessary
      if ($session->config->logfile) {
        if ($session->config->logfile == 2 || $type == 1) {
          // Log message
          for ($i = 0; $i < 255; $i++) {
            $i_str = ($i < 100) ? '0' . (string)$i : (string)$i;
            $message_body = str_replace("\\\\\\\\", "\\", str_replace("\\\\&#" . $i_str . ';', chr($i), $message_body));
          }
          log::saveMessage($session, $type, $session->room_id, $session->user_id, $target_user_id, urldecode($message_body));
        }
      }
    }
  }

  /**************************************************************************
  readNewMessages
  ---------------------------------------------------------------------------
  Task:
    Read new user messages from database
  ---------------------------------------------------------------------------
  Parameters:
    $session      Object      Sessionhandle
  ---------------------------------------------------------------------------
  Return:
                  Array       Messages list
  **************************************************************************/
  public function readNewMessages(&$session) {
    $query = "SELECT um.* FROM " . PREFIX . "usermessage um
              JOIN " . PREFIX . "session se ON se.user_id = um.user_id
              WHERE se.room_id = ? AND um.id > ?
              ORDER BY um.id ASC";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param("ii", $session->room_id, $session->last_usermessage);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messagelist = [];
    while ($data = $result->fetch_assoc()) {
      $messagelist[] = $data;
    }

    // Updating session with the last message ID
    if (count($messagelist) > 0) {
      $session->updateSession("last_usermessage = ?", $messagelist[count($messagelist) - 1]['id']);
    }

    return $messagelist;
  }

  /**************************************************************************
  readMessage
  ---------------------------------------------------------------------------
  Task:
    Read user message from database
  ---------------------------------------------------------------------------
  Parameters:
    $session        Object      Sessionhandle
    $message_id     int         Message ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readMessage(&$session, $message_id = 0) {
    if ($message_id) {
      $query = "SELECT * FROM " . PREFIX . "usermessage WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param("i", $message_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($data = $result->fetch_assoc()) {
        foreach ($data as $key => $val) {
          if (!preg_match("/^\d+$/", $key)) {
            $this->$key = $val;
          }
        }
      }
    }
  }
}
?>