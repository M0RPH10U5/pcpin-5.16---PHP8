<?php
/****************************************************************************
CLASS smilie
-----------------------------------------------------------------------------
Task:
  Manage smilie
****************************************************************************/

class smilie{

  /* Class variables */

  /* ID
  *  Type: int
  */
  public $id = 0;

  /* Image name
  *  Type: string
  */
  public $image = '';

  /* Text equivalent
  *  Type: string
  */
  public $text = '';



  /**************************************************************************
  smilie
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create smilie object
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    // Constructor logic, if needed
  }

  /**************************************************************************
  listSmilies
  ---------------------------------------------------------------------------
  Task:
    List smilies
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return:
    Array with smilies
  **************************************************************************/
  public function listSmilies($session) {
    $query = "SELECT * FROM " . PREFIX . "smilie ORDER BY id";
    $result = $session->db->query($query);
    return $session->db->fetchAll($result);
  }

  /**************************************************************************
  insertSmilie
  ---------------------------------------------------------------------------
  Task:
    Insert smilie into database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $smilie_id        int           Smilie ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function insertSmilie($session, $smilie_id = 0) {
    if ($smilie_id) {
      // Delete old smilie
      $this->deleteSmilie($session, $smilie_id);
    }
    
    $query = "INSERT INTO " . PREFIX . "smilie (image, text) VALUES (?, ?)";
    $stmt = $session->db->prepare($query);
    $stmt->bind_param('ss', $this->image, $this->text);
    $stmt->execute();
  }

  /**************************************************************************
  readSmilie
  ---------------------------------------------------------------------------
  Task:
    Read smilie from database
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $smilie_id        int           Smilie ID
    $text             string        Text equivalent
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readSmilie($session, $smilie_id = 0, $text = "") {
    if ($smilie_id) {
      // Smilie ID given
      $query = "SELECT * FROM " . PREFIX . "smilie WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $smilie_id);
    } elseif ($text) {
      // Text equivalent given
      $query = "SELECT * FROM " . PREFIX . "smilie WHERE text = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('s', $text);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
      foreach ($data as $key => $val) {
        if (!preg_match("/^\d+$/", $key)) {
          // Using alphanumeric keys only
          $this->$key = $val;
        }
      }
    }
  }

  /**************************************************************************
  deleteSmilie
  ---------------------------------------------------------------------------
  Task:
    Delete smilie
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $smilie_id        int           Smilie ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteSmilie($session, $smilie_id = 0) {
    if ($smilie_id) {
      // Read smilie
      $this->readSmilie($session, $smilie_id);
      
      // Delete from database
      $query = "DELETE FROM " . PREFIX . "smilie WHERE id = ? LIMIT 1";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $smilie_id);
      $stmt->execute();
      
      // Delete file
      $image_path = IMAGEPATH . "/smilies/" . $this->image;
      if (file_exists($image_path)) {
        unlink($image_path);
      }
    }
  }

  /**************************************************************************
  updateSmilie
  ---------------------------------------------------------------------------
  Task:
    Updates smilie
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    id                int           Smilie ID
    fields            string        Fields to update
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateSmilie($session, $id = 0, $fields = "") {
    if ($id && $fields) {
      $query = "UPDATE " . PREFIX . "smilie SET $fields WHERE id = ?";
      $stmt = $session->db->prepare($query);
      $stmt->bind_param('i', $id);
      $stmt->execute();
      
      $this->readSmilie($session, $id);
    }
  }
}
?>