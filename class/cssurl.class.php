<?php
/****************************************************************************
CLASS cssURL
-----------------------------------------------------------------------------
Task:
  Manage URL of external CSS
****************************************************************************/

class cssURL{

  // URL to external CSS
  public $cssurl;

  /**************************************************************************
  cssURL
  ---------------------------------------------------------------------------
  Task:
    Load (if any) CSS URL from database
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct(&$db) {
    $query = 'SELECT * FROM ' . PREFIX . 'cssurl LIMIT 1';
    $stmt = $db->prepare($query);
    $stmt->execute();
    if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      foreach ($data as $key => $val) {
        if (!preg_match("/^\d+$/", $key)) {
          // Using alphanumerical keys only
          $this->$key = $val;
        }
      }
    }
  }

  /**************************************************************************
  updateCSSURL
  ---------------------------------------------------------------------------
  Task:
    Updates CSS URL
  ---------------------------------------------------------------------------
  Parameters:
    $db               Object        Database handle
    url               string        New URL
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateCSSURL(&$db, $url = '') {
    $query = 'UPDATE ' . PREFIX . 'cssurl SET cssurl = :url';
    $stmt = $db->prepare($query);
    $stmt->execute([':url' => $url]);
    
    // Refresh object with updated value
    $this->__construct($db);
  }
}
?>