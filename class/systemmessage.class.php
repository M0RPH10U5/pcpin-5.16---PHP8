<?php
/****************************************************************************
CLASS systemMessage
-----------------------------------------------------------------------------
Task:
  Manage system messages
****************************************************************************/

class systemMessage{

  /* Class variables */

  /* Message type
  *  Type: int
  *  Values:
  *     1   :     User 'xxx' entered room 'yyy'
  *                 message body: "user_id|room_id"
  *     2   :     User 'xxx' left room 'yyy'
  *                 message body: "user_id|room_id"
  *     3   :     Userinfo changed
  *                 message body: "user_id"
  *     4   :     Room was deleted
  *                 message body: "room_id"
  *     5   :     Room was created
  *                 message body: "room_id"
  *     6   :     User 'xxx' was kicked out
  *                 message body: "user_id"
  *     7   :     Invite user
  *                 message body: "user_id|target_user_id|room_id"
  *     8   :     Invitation accepted / rejected
  *                 message body: "user_id|responser_user_id|status"
  *     9   :     Change room
  *                 message body: "user_id|new_room_id"
  *     10  :     Restart room
  *                 message body: "room_id"
  *     11  :     Show advertisement
  *                 message body: "room_id|advertisement_html"
  *     12  :     Clear room screen
  *                 message body: "room_id"
  *
  */
  public $type = 0;

  /* Message post time (TIMESTAMP)
  *  Type: int
  */
  public $post_time = 0;

  /* Message body
  *  Type: string
  */
  public $body = '';

  /* Messages list
  *  Type: array
  */
  public $messagelist = null;



  /**************************************************************************
  Constructor
  ---------------------------------------------------------------------------
  Task:
    Creates systemMessage object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    // Constructor logic, if needed
  }

  /**************************************************************************
  deleteOldMessages
  ---------------------------------------------------------------------------
  Task:
    Deletes old system messages from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Sessionhandle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteOldMessages($session) {
    $delete_time = $session->config->main_refresh + 1;
    $query = "DELETE FROM " . PREFIX . "systemmessage WHERE post_time + ? < UNIX_TIMESTAMP()";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('i', $delete_time);
    $stmt->execute();
  }

  /**************************************************************************
  insertMessage
  ---------------------------------------------------------------------------
  Task:
    Insert system message into database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Sessionhandle
    $message_body     string        Message body
    $type             int           Message type
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function insertMessage($session, $message_body = "", $type = 0) {
    $query = "INSERT INTO " . PREFIX . "systemmessage (type, body, post_time) VALUES (?, ?, UNIX_TIMESTAMP())";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('is', $type, $message_body);
    $stmt->execute();
  }

  /**************************************************************************
  readNewMessages
  ---------------------------------------------------------------------------
  Task:
    Read system messages from database
  ---------------------------------------------------------------------------
  Parameters:
    $session      Object      Sessionhandle
  ---------------------------------------------------------------------------
  Return:
                  Array       Messages list
  **************************************************************************/
  public function readNewMessages($session) {
    $query = "SELECT * FROM " . PREFIX . "systemmessage WHERE id > ? ORDER BY id ASC";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('i', $session->last_systemmessage);
    $stmt->execute();
    $result = $stmt->get_result();

    $messagelist = [];
    while ($data = $result->fetch_assoc()) {
      // Adding message to list
      $messagelist[] = $data;
    }

    // Updating session
    if (count($messagelist) > 0) {
      $session->updateSession("last_systemmessage = {$messagelist[count($messagelist) - 1]['id']}");
    }
    
    return $messagelist;
  }
}
?>