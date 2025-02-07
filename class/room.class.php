<?php
/****************************************************************************
CLASS room
-----------------------------------------------------------------------------
Task:
  Manage rooms
****************************************************************************/

class room{

  /* Class variables */

  /* Room ID
  *  Type: int
  */
  public $id=0;

  /* Room name
  *  Type: string
  */
  public $name='';

  /* Room type
  *  Type: int
  *  Values:
  *     0:  Main chat room
  *     1:  Room created by user
  *     2:  Main chat room with password
  *     3:  Room created by user with password
  */
  public $type=0;

  /* Time the room was created or visited
  *  Type: int
  */
  public $last_ping=0;

  /* Room password
  *  Type: string
  */
  public $password='';

  /* Background image
  *  Type: string
  */
  public $bgimg='';

  /* Room creator's user ID
  *  Type: int
  */
  public $creator_id=0;

  /* Roomlist
  *  Type: array
  */
  public $roomlist=null;



  /**************************************************************************
  room
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Creates room object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
  }

  /**************************************************************************
  listRooms
  ---------------------------------------------------------------------------
  Task:
    Reads chat rooms
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $id               int           Room ID
    $name             string        Room name
    $type             int           Room type
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function listRooms(&$session, $id = 0, $name = "", $type = -1) {
    $where = "1";
    /* Advanced search parameters */
    if ($id) {
      $where .= " AND id = ?";
    }
    if ($name) {
      $where .= " AND name = ?";
    }
    if ($type >= 0) {
      $where .= " AND type = ?";
    }
    
    $query = "SELECT * FROM " . PREFIX . "room WHERE $where ORDER BY name ASC";
    $stmt = $session->db->prepare($query);
    
    if ($id) {
      $stmt->bind_param('i', $id);
    }
    if ($name) {
      $stmt->bind_param('s', $name);
    }
    if ($type >= 0) {
      $stmt->bind_param('i', $type);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $this->roomlist = $session->db->fetchAll($result);
  }

  /**************************************************************************
  updateRoom
  ---------------------------------------------------------------------------
  Task:
    Updates room
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    id                int           Room ID
    fields            string        Fields to update
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateRoom(&$session, $id = 0, $fields = "") {
    if ($id && $fields) {
      $query = "UPDATE " . PREFIX . "room SET $fields WHERE id = ?";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $id);
      $stmt->execute();
    }
  }

  /**************************************************************************
  createRoom
  ---------------------------------------------------------------------------
  Task:
    Create room
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $name                 string        Room name
    $type                 int           Room type
    $password_protected   string        Is the room password-protected? ('1': yes, '0': no)
    $password             string        Room password
    $bgimg                string        Background image filename
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function createRoom(&$session, $name = "", $type = 1, $password = "", $bgimg = "") {
    if ($name) {
      $query = "INSERT INTO " . PREFIX . "room (name, type, last_ping, password, bgimg, creator_id) VALUES (?, ?, UNIX_TIMESTAMP(), ?, ?, ?)";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('sisss', $name, $type, $password, $bgimg, $session->user_id);
      $stmt->execute();
    }
  }

  /**************************************************************************
  cleanUp
  ---------------------------------------------------------------------------
  Task:
    Delete empty userrooms that are not used anymore
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function cleanUp(&$session) {
    $deadline = time() - $session->config->userroom_life;
    // Read all timed-up user rooms
    $query = "SELECT id FROM " . PREFIX . "room WHERE last_ping < ? AND (type = 1 OR type = 3)";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('i', $deadline);
    $stmt->execute();
    $result = $stmt->get_result();
    $roomlist = $session->db->fetchAll($result);
    $rooms_count = count($roomlist);
    for ($i = 0; $i < $rooms_count; $i++) {
      // Deleting user room
      $this->deleteRoom($session, $roomlist[$i]['id']);
    }
  }

  /**************************************************************************
  readRoom
  ---------------------------------------------------------------------------
  Task:
    Read room from database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $room_id          int           Room ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readRoom(&$session, $room_id = 0) {
    if ($room_id) {
      $query = "SELECT * FROM " . PREFIX . "room WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $room_id);
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

  /**************************************************************************
  deleteRoom
  ---------------------------------------------------------------------------
  Task:
    Delete room from database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $room_id          int           Room ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteRoom(&$session, $room_id = 0) {
    if ($room_id) {
      // Deleting room from all lists
      systemmessage::insertMessage($session, $room_id, 4);
      // Clean up 'fk_advertisement' table
      $query = "DELETE FROM " . PREFIX . "fk_advertisement WHERE room_id = ?";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $room_id);
      $stmt->execute();
      // Deleting background image, if exists
      $tmp = new room();
      $tmp->readRoom($session, $room_id);
      if ($tmp->bgimg) {
        unlink(IMAGEPATH . "/rooms/" . $tmp->bgimg);
      }
      // Deleting passes, if any
      $roompass = new roompass();
      $roompass->deletePass($session, $room_id, 0);
      // Deleting room
      $query = "DELETE FROM " . PREFIX . "room WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $room_id);
      $stmt->execute();
    }
  }
}
?>