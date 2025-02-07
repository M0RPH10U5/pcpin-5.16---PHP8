<?php
/****************************************************************************
CLASS roompass
-----------------------------------------------------------------------------
Task:
  Manage temporary passes for password-protected rooms
****************************************************************************/

class roompass{


  /* Class variables */

  /* Room ID
  *  Type: int
  */
  public $room_id = 0;

  /* User ID
  *  Type: int
  */
  public $user_id = 0;


  /**************************************************************************
  roompass
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create roompass object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
  }

  /**************************************************************************
  createPass
  ---------------------------------------------------------------------------
  Task:
    Create 'one-time' pass for user to enter password-protected room
  ---------------------------------------------------------------------------
  Parameters:
    $session          object        Session handle
    $room_id          int           Room ID
    $user_id          int           Pass owner
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function createPass(&$session, $room_id = 0, $user_id = 0) {
    if ($room_id && $user_id) {
      // Delete old pass if exists
      $this->deletePass($session, $room_id, $user_id);
      // Insert new pass
      $query = "INSERT INTO " . PREFIX . "roompass (room_id, user_id) VALUES (?, ?)";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('ii', $room_id, $user_id);
      $stmt->execute();
    }
  }

  /**************************************************************************
  deletePass
  ---------------------------------------------------------------------------
  Task:
    Delete 'one-time' pass for user to enter password-protected room
  ---------------------------------------------------------------------------
  Parameters:
    $session          object        Session handle
    $room_id          int           Room ID
    $user_id          int           Pass owner
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deletePass(&$session, $room_id = 0, $user_id = 0) {
    if ($room_id || $user_id) {
      $where = "1";
      if ($room_id) {
        $where .= " AND room_id = ?";
      }
      if ($user_id) {
        $where .= " AND user_id = ?";
      }
      
      $query = "DELETE FROM " . PREFIX . "roompass WHERE $where";
      $stmt = $session->db->prepare($query);
      
      if ($room_id && $user_id) {
        $stmt->bind_param('ii', $room_id, $user_id);
      } elseif ($room_id) {
        $stmt->bind_param('i', $room_id);
      } elseif ($user_id) {
        $stmt->bind_param('i', $user_id);
      }
      
      $stmt->execute();
    }
  }

  /**************************************************************************
  checkPass
  ---------------------------------------------------------------------------
  Task:
    Check 'one-time' pass for user to enter password-protected room
  ---------------------------------------------------------------------------
  Parameters:
    $session          object        Session handle
    $room_id          int           Room ID
    $user_id          int           Pass owner
    $delete           bool          Whether to delete pass or not
  ---------------------------------------------------------------------------
  Return:
    Booleat TRUE if pass exists
  **************************************************************************/
  public function checkPass(&$session, $room_id = 0, $user_id = 0, $delete = 0) {
    if ($room_id && $user_id) {
      // Read pass
      $query = "SELECT 1 FROM " . PREFIX . "roompass WHERE room_id = ? AND user_id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('ii', $room_id, $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        // Password found
        if ($delete) {
          $this->deletePass($session, $room_id, $user_id);
        }
        return true;
      }
    }
    return false;
  }
}
?>