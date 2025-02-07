<?php
/****************************************************************************
CLASS advertisement
-----------------------------------------------------------------------------
Task:
  Manage advertisement
****************************************************************************/

class Advertisement{

  /* Class variables */

 public int $id = 0;
 public string $text = '';
 public int $start = 0; 
 public int $stop = 0;
 public int $period = 0;
 public int $min_roomusers = 0;
 public int $show_private = 0;
 public int $shows_count = 0;




  /**************************************************************************
  advertisement
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Create advertisement object
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {}

  /**************************************************************************
  listAdvertisements
  ---------------------------------------------------------------------------
  Task:
    List advertisements
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $active           int           Active only
  ---------------------------------------------------------------------------
  Return:
    Array with advertisements
  **************************************************************************/
  public function listAdvertisements($session, int $active = 0): array {
    $where = $active ? "WHERE start <= UNIX_TIMESTAMP() AND stop >= UNIX_TIMESTAMP()" : "";
    $query = "SELECT * FROM " . PREFIX . "advertisement $where";

    $result = $session->db->query($query);
    return $session->db->fetchAll($result);
  }

  /**************************************************************************
  insertAdvertisement
  ---------------------------------------------------------------------------
  Task:
    Insert advertisement into database
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $advertisement_id     int           Advertisement ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function insertAdvertisement($session, int $advertisement_id = 0): void {
    if ($advertisement_id) {
        // Delete old advertisement
        $this->deleteAdvertisement($session, $advertisement_id);
    }

    $query = "INSERT INTO " . PREFIX . "advertisement
              (id, text, start, stop, period, min_roomusers, show_private)
              VALUES (:id, :text, :start, :stop, :period, :min_roomusers, :show_private)";
    
    $stmt = $session->db->prepare($query);
    $stmt->execute([
        ':id' => $advertisement_id ?: null,
        ':text' => $this->text,
        ':start' => $this->start,
        ':stop' => $this->stop,
        ':period' => $this->period,
        ':min_roomusers' => $this->min_roomusers,
        ':show_private' => $this->show_private
    ]);          
  }

  /**************************************************************************
  readAdvertisement
  ---------------------------------------------------------------------------
  Task:
    Read advertisement from database
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $advertisement_id     int           Advertisement ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readAdvertisement($session, int $advertisement_id): void {
    if ($advertisement_id) {
        $query = "SELECT * FROM " . PREFIX . "advertisement WHERE id = :id LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $advertisement_id]);
        $data = $stmt->fetch();

        if ($data) {
            foreach ($data as $key => $val) {
                if (preg_match("/^\d+$/", $key) === 0) { // Avoid numeric indexes
                    $this->$key = $val;
                }
            }
        }
    }
  }

  /**************************************************************************
  deleteAdvertisement
  ---------------------------------------------------------------------------
  Task:
    Delete advertisement from database
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object        Session handle
    $advertisement_id     int           Advertisement ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteAdvertisement($session, int $advertisement_id): void {
    if ($advertisement_id) {
        // Clean up 'fk_advertisement' table
        $query = "DELETE FROM " . PREFIX . "fk_advertisement WHERE advertisement_id = :id";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $advertisement_id]);

        // Delete advertisement
        $query = "DELETE FROM " . PREFIX . "advertisement WHERE id = :id LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $advertisement_id]);
    }
  }

  /**************************************************************************
  updateAdvertisement
  ---------------------------------------------------------------------------
  Task:
    Updates advertisement
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    id                int           Advertisement ID
    fields            string        Fields to update
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public  function updateAdvertisement($session, int $id, string $fields): void {
    if ($id && $fields) {
        $query = "UPDATE " . PREFIX . "advertisement SET $feilds WHERE id = :id";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
  }
}
?>