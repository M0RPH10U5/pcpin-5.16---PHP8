<?php
/****************************************************************************
CLASS maxusers
-----------------------------------------------------------------------------
Task:
  Save top online users count for day, month, week, year and total.
****************************************************************************/

class maxusers{

  /* Class variables */

  public $max_users = 0;
  public $time = 0;


  /**************************************************************************
  maxusers
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Creates maxusers object.
    Read max users value from database. Update max users value.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct(&$session) {
    $this->readMaxUsers($session);
    $this->updateMaxUsers($session);
  }

  /**************************************************************************
  readMaxUsers
  ---------------------------------------------------------------------------
  Task:
    Read top online users count from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readMaxUsers(&$session) {
    $this->max_users = 0;
    $this->time = 0;
    
    // Using prepared statements to prevent SQL injection
    $query = 'SELECT * FROM ' . PREFIX . 'maxusers LIMIT 1';
    $stmt = $session->db->prepare($query);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    if ($data) {
      foreach ($data as $key => $val) {
        $this->$key = $val;
      }
    }
  }

  /**************************************************************************
  updateMaxUsers
  ---------------------------------------------------------------------------
  Task:
    Update top online users count, if needed.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateMaxUsers(&$session) {
    // Get current users count
    $users_count = $session->countRoomUsers();

    if ($users_count > $this->max_users) {
      // Using prepared statements to prevent SQL injection
      $query = 'UPDATE ' . PREFIX . 'maxusers SET max_users = ?, time = UNIX_TIMESTAMP() LIMIT 1';
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $users_count);
      $stmt->execute();
      $this->readMaxUsers($session);
    }
  }
}
?>