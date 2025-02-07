<?php
/****************************************************************************
CLASS fk_cssvalue
-----------------------------------------------------------------------------
Task:
  Manage CSS values
****************************************************************************/

class fk_cssvalue{


  /**************************************************************************
  changeCSSValue
  ---------------------------------------------------------------------------
  Task:
    Change CSS property value
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
    $class_id       int             CSS class ID
    $property_id    int             CSS property ID
    $value          string          Value
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function changeCSSValue(&$db, $class_id = 0, $property_id = 0, $value = "") {
    if ($class_id && $property_id) {
      // Use prepared statement to prevent SQL injection
      $query = "UPDATE " . PREFIX . "fk_cssvalue SET value = ? WHERE class_id = ? AND property_id = ? LIMIT 1";
      $stmt = $db->prepare($query);
      $stmt->bind_param('sii', $value, $class_id, $property_id);
      $stmt->execute();
      $stmt->close();
    }
  }
}
?>