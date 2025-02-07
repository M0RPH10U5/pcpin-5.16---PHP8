<?php
/****************************************************************************
CLASS session
-----------------------------------------------------------------------------
Task:
  Manage Sessions.
****************************************************************************/

class session EXTENDS dbaccess{

  /* Class variables */

  public $db = null; // Database handle
  public $config = null; // Configuration handle
  public $id; // Session ID
  public $user_id = 0; // User ID
  public $room_id = 0; // Room ID
  public $last_ping = 0; // Last ping
  public $last_usermessage = 0; // Last user message read
  public $last_systemmessage = 0; // Last system message read
  public $last_globalmessage = 0; // Last global message read
  public $language = ''; // Used language
  public $ip = ''; // IP address
  public $last_message = ''; // Last posted message
  public $message_repeat = 0; // Message repeat count
  public $welcome = 0; // Flag for welcome message
  public $kicked = 0; // Flag for kicked user
  public $last_post_time = 0; // Last message post time (UNIX TIMESTAMP)
  public $direct_login = 0; // Flag for direct login


  /**************************************************************************
  session
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create session object.
    Load chat configuration from database.
    Update current session (if exists).
  ---------------------------------------------------------------------------
  Parameters:
    id          string        Session ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct($id = "") {
    $this->db = new dbaccess();
    $this->db->connect();
    $this->config = new configuration($this->db);

    if ($id) {
      $this->id = $id;
      $this->updateSession("last_ping = UNIX_TIMESTAMP()");
    }
  }

  /**************************************************************************
  readSession
  ---------------------------------------------------------------------------
  Task:
    Delete old sessions.
    Read session data.
    Update current session last ping.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readSession() {
    if ($this->id) {
      $query = "SELECT * FROM " . PREFIX . "session WHERE id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('s', $this->id);
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
  updateSession
  ---------------------------------------------------------------------------
  Task:
    Updates session data.
  ---------------------------------------------------------------------------
  Parameters:
    fields        string        Fields to update
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateSession($fields = "") {
    if ($fields) {
      $query = "UPDATE " . PREFIX . "session SET $fields WHERE id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('s', $this->id);
      $stmt->execute();
      $this->readSession();
    }
  }

  /**************************************************************************
  newSession
  ---------------------------------------------------------------------------
  Task:
    Creates new session.
  ---------------------------------------------------------------------------
  Parameters:
    user_id         int         User ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function newSession($user_id = 0) {
    if ($user_id) {
      $this->user_id = $user_id;
      $ok = false;

      while (!$ok) {
        $this->id = common::randomString(16);
        $query = "SELECT id FROM " . PREFIX . "session WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->num_rows) {
          $ok = true;
          $this->last_ping = time();
          $query = "INSERT INTO " . PREFIX . "session (id, user_id, room_id, last_ping, ip, last_message) VALUES (?, ?, -1, ?, ?, '')";
          $stmt = $this->db->prepare($query);
          $stmt->bind_param('siis', $this->id, $this->user_id, $this->last_ping, $_SERVER['REMOTE_ADDR']);
          $stmt->execute();
        }
      }
    }
  }

  /**************************************************************************
  listRoomUsers
  ---------------------------------------------------------------------------
  Task:
    List users in one room or in whole chat.
  ---------------------------------------------------------------------------
  Parameters:
    room_id         int           Room ID
  ---------------------------------------------------------------------------
  Return:
                    array         Array with user IDs
  **************************************************************************/
  public function listRoomUsers($room_id = 0) {
    $where = $room_id ? ' AND room_id = ?' : '';
    $query = "SELECT user_id FROM " . PREFIX . "session WHERE direct_login = 0 $where";
    $stmt = $this->db->prepare($query);
    
    if ($room_id) {
      $stmt->bind_param('i', $room_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
      $data[] = $row['user_id'];
    }
    return $data;
  }

  /**************************************************************************
  countRoomUsers
  ---------------------------------------------------------------------------
  Task:
    Count users in one room or in whole chat.
  ---------------------------------------------------------------------------
  Parameters:
    room_id         int           Room ID
  ---------------------------------------------------------------------------
  Return:
    Users count (int)
  **************************************************************************/
  public function countRoomUsers($room_id = 0) {
    $query = $room_id 
      ? 'SELECT COUNT(1) FROM ' . PREFIX . 'session WHERE direct_login = 0 AND room_id = ?'
      : 'SELECT COUNT(1) FROM ' . PREFIX . 'session WHERE direct_login = 0';
    
    $stmt = $this->db->prepare($query);
    
    if ($room_id) {
      $stmt->bind_param('i', $room_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_row();
    return $data[0];
  }

  /**************************************************************************
  cleanUp
  ---------------------------------------------------------------------------
  Task:
    Delete old sessions.
    Delete empty userrooms tah are not used anymore.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function cleanUp() {
    $query = 'SELECT id FROM ' . PREFIX . 'session WHERE direct_login = 0 AND last_ping + ? < UNIX_TIMESTAMP()';
    $stmt = $this->db->prepare($query);
    $stmt->bind_param('i', round($this->config->max_ping + 1.5 * $this->config->main_refresh));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($data = $result->fetch_assoc()) {
      $tmp = new session();
      $tmp->id = $data['id'];
      $tmp->readSession();
      $tmp->logout();
    }

    $room = new room();
    $room->cleanUp($this);
    $user = new user();
    $user->cleanUp($this);
  }

  /**************************************************************************
  checkUserUnique
  ---------------------------------------------------------------------------
  Task:
    Check whether user not logged in
  ---------------------------------------------------------------------------
  Parameters:
    user_id             int           User ID
    skip_direct_login   boolean       Do not check directly logged in sessions
  ---------------------------------------------------------------------------
  Return:
    TRUE if user is NOT logged in
    FALSE if user already logged in
  **************************************************************************/
  public function checkUserUnique($user_id = 0, $skip_direct_login = false) {
    if ($user_id) {
      $query = $skip_direct_login 
        ? "SELECT 1 FROM " . PREFIX . "session WHERE user_id = ? AND direct_login = 0 LIMIT 1"
        : "SELECT 1 FROM " . PREFIX . "session WHERE user_id = ? LIMIT 1";
      
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      
      return !$result->num_rows;
    }
  }

  /**************************************************************************
  logout
  ---------------------------------------------------------------------------
  Task:
    Delete session.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function logout() {
    if ($this->id && $this->user_id) {
      $tmp = new user();
      $tmp->readUser($this);
      
      if ($tmp->guest) {
        // User is a guest. Delete the user.
        $tmp->deleteUser($this, $tmp->id);
      } else {
        // Update user
        $tmp->updateUser($this, $tmp->id, "last_login = UNIX_TIMESTAMP()");
      }

      $roompass = new roompass();
      $roompass->deletePass($this, 0, $this->user_id);

      $query = "DELETE FROM " . PREFIX . "session WHERE id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('s', $this->id);
      $stmt->execute();

      if ($this->room_id) {
        systemmessage::insertMessage($this, $this->user_id . "|" . $this->room_id, 2);
      }
    }
  }

  /**************************************************************************
  isOnline
  ---------------------------------------------------------------------------
  Task:
    Chek whether user is online or not
  ---------------------------------------------------------------------------
  Parameters:
    $user_id              int               User ID
  ---------------------------------------------------------------------------
  Return:
    TRUE if user is online
    FALSE if user is not online
  **************************************************************************/
  public function isOnline($user_id = 0) {
    if ($user_id) {
      $query = "SELECT 1 FROM " . PREFIX . "session WHERE direct_login = 0 AND user_id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      
      return $result->num_rows > 0;
    }
    
    return false;
  }


  /**************************************************************************
  getUsersRoom
  ---------------------------------------------------------------------------
  Task:
    Get user's room ID
  ---------------------------------------------------------------------------
  Parameters:
    $user_id              int               User ID
  ---------------------------------------------------------------------------
  Return:
    Room ID (int)
  **************************************************************************/
  public function getUsersRoom($user_id = 0) {
    if ($user_id) {
      $query = "SELECT room_id FROM " . PREFIX . "session WHERE direct_login = 0 AND user_id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      
      return $data['room_id'] ?? 0;
    }
    
    return 0;
  }

  /**************************************************************************
  getUsersSession
  ---------------------------------------------------------------------------
  Task:
    Get user's session ID
  ---------------------------------------------------------------------------
  Parameters:
    $user_id              int               User ID
  ---------------------------------------------------------------------------
  Return:
    (string) Session ID
  **************************************************************************/
  public function getUsersSession($user_id = 0) {
    if ($user_id) {
      $query = "SELECT id FROM " . PREFIX . "session WHERE user_id = ? LIMIT 1";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      
      return $data['id'] ?? '';
    }
    
    return '';
  }
}
?>