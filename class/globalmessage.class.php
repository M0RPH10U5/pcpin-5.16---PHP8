<?php
/****************************************************************************
CLASS globalMessage
-----------------------------------------------------------------------------
Task:
  Manage global messages
****************************************************************************/

class globalMessage{

  /* Class variables */
  public $type = 0;
  public $user_id = 0;
  public $body = '';
  public $post_time = 0;

  /**************************************************************************
  Constructor
  ---------------------------------------------------------------------------
  Task:
    Creates globalMessage object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    // Constructor can be customized if needed
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
    $delete_time = $session->config->main_refresh + 1;
    $query = "DELETE FROM " . PREFIX . "globalmessage WHERE post_time + ? < UNIX_TIMESTAMP()";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('i', $delete_time);
    $stmt->execute();
    $stmt->close();
  }

  /**************************************************************************
  insertMessage
  ---------------------------------------------------------------------------
  Task:
    Inserts new global message into database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Sessionhandle
    $user_id          int           User ID
    $message_body     string        Message body
    $type             int           Message type
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function insertMessage(&$session, $user_id = 0, $message_body = '', $type = 0) {
    if (!empty($message_body)) {
      // Use prepared statements to prevent SQL injection
      $query = "INSERT INTO " . PREFIX . "globalmessage (type, user_id, body, post_time) VALUES (?, ?, ?, UNIX_TIMESTAMP())";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('iis', $type, $user_id, $message_body);
      $stmt->execute();
      $stmt->close();

      // Log message if logging is enabled
      if ($session->config->logfile) {
        log::saveMessage($session, 0, $user_id, $message_body);
      }
    }
  }

  /**************************************************************************
  readNewMessages
  ---------------------------------------------------------------------------
  Task:
    Read new global messages from database
  ---------------------------------------------------------------------------
  Parameters:
    $session      Object      Sessionhandle
  ---------------------------------------------------------------------------
  Return:
                  Array       Messages list
  **************************************************************************/
  public function readNewMessages(&$session) {
    $query = "SELECT * FROM " . PREFIX . "globalmessage WHERE id > ? ORDER BY id ASC";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('i', $session->last_globalmessage);
    $stmt->execute();
    $result = $stmt->get_result();
    $messagelist = [];
    
    while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
      $messagelist[] = $data;
    }
    
    // Updating session with the last global message ID
    if (count($messagelist) > 0) {
      $session->updateSession("last_globalmessage = {$messagelist[count($messagelist) - 1]['id']}");
    }
    
    $stmt->close();
    return $messagelist;
  }

  /**************************************************************************
  readMessage
  ---------------------------------------------------------------------------
  Task:
    Read global message from database
  ---------------------------------------------------------------------------
  Parameters:
    $session      Object      Sessionhandle
    $id           int         Message ID
  ---------------------------------------------------------------------------
  Return:
                  Array       Message
  **************************************************************************/
  public function readMessage(&$session, $id = 0) {
    if ($id) {
      $query = "SELECT * FROM " . PREFIX . "globalmessage WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $message = $result->fetch_array(MYSQLI_ASSOC);
      $stmt->close();
      return $message;
    }
  }
}
?>