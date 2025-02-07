<?php
/****************************************************************************
CLASS fk_advertisement
-----------------------------------------------------------------------------
Task:
  Foreign keys between 'advertisement' and 'room' tables
****************************************************************************/

class fk_advertisement{

  /* Class variables */
  public $advertisement_id = 0;
  public $room_id = 0;
  public $last_time = 0;

  /**************************************************************************
  advertisement
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create fk_advertisement object
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
  }

  /**************************************************************************
  update
  ---------------------------------------------------------------------------
  Task:
    Update record in database
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $room_id              int           Room ID
    $advertisement_id     int           Advertisement ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function update(&$session, $room_id = 0, $advertisement_id = 0) {
    if ($room_id && $advertisement_id) {
      // Look for the record using prepared statement to prevent SQL injection
      $query = "SELECT 1 FROM " . PREFIX . "fk_advertisement WHERE room_id = ? AND advertisement_id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('ii', $room_id, $advertisement_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
        // Record does not exist. Create a new one.
        $query = "INSERT INTO " . PREFIX . "fk_advertisement (advertisement_id, room_id, last_time) VALUES (?, ?, UNIX_TIMESTAMP())";
        $stmt = $session->db->prepare($query);
        $stmt->bind_param('ii', $advertisement_id, $room_id);
        $stmt->execute();
      } else {
        // Record exists. Update.
        $query = "UPDATE " . PREFIX . "fk_advertisement SET last_time = UNIX_TIMESTAMP() WHERE room_id = ? AND advertisement_id = ? LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->bind_param('ii', $room_id, $advertisement_id);
        $stmt->execute();
      }
      $stmt->close();
    }
  }

  /**************************************************************************
  getTime
  ---------------------------------------------------------------------------
  Task:
    Read last advertisement show time from database
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $room_id              int           Room ID
    $advertisement_id     int           Advertisement ID
  ---------------------------------------------------------------------------
  Return:
    Last advertisement show time (UNIX timestamp)
  **************************************************************************/
  public function check(&$session, $room_id = 0, $advertisement_id = 0) {
    if ($room_id && $advertisement_id) {
      // Look for the record using prepared statement
      $query = "SELECT last_time FROM " . PREFIX . "fk_advertisement WHERE room_id = ? AND advertisement_id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('ii', $room_id, $advertisement_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_array(MYSQLI_ASSOC);
      $stmt->close();
      
      return $data['last_time'];
    }
  }
}
?>