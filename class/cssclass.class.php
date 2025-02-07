<?php
/****************************************************************************
CLASS cssClass
-----------------------------------------------------------------------------
Task:
  Manage CSS classes
****************************************************************************/

class cssClass{

  // CSS settings structure
  public $cssList = null;

  /**************************************************************************
  generateCSS
  ---------------------------------------------------------------------------
  Task:
    Generate CSS
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
  ---------------------------------------------------------------------------
  Return:
    CSS
  **************************************************************************/
  public function generateCSS(&$db) {
    $this->loadStructure($db);
    $css_text = "<STYLE>";
    foreach ($this->cssList as $class_id => $class_properties) {
      $css_text .= $class_properties['name'] . "{";
      foreach ($class_properties['properties'] as $property_id => $property_data) {
        if ($property_data['value']) {
          $css_text .= $property_data['name'] . ":" . $property_data['value'] . ";";
        }
      }
      $css_text .= "}";
    }
    $css_text .= "</STYLE>";
    return $css_text;
  }

  /**************************************************************************
  generateCSSBodyBGColor
  ---------------------------------------------------------------------------
  Task:
    Generate short CSS with body background-color property only.
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
  ---------------------------------------------------------------------------
  Return:
    CSS
  **************************************************************************/
  public function generateCSSBodyBGColor(&$db) {
    $this->loadStructure($db);
    $found = false;
    foreach ($this->cssList as $class_id => $class_properties) {
      if (strtolower($class_properties['name']) == "body") {
        foreach ($class_properties['properties'] as $property_id => $property_data) {
          if (strtolower($property_data['name']) == "background-color") {
            $found = true;
            $css_text = "<STYLE>body{background-color:" . $property_data['value'] . "}</STYLE>";
          }
        }
      }
    }
    return $css_text ?? '';
  }

  /**************************************************************************
  loadStructure
  ---------------------------------------------------------------------------
  Task:
    Load CSS structure with values from database
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
  ---------------------------------------------------------------------------
  Return:
    Array with CSS classes
  **************************************************************************/
  public function loadStructure(&$db) {
    unset($this->cssList);
    // List CSS classes
    $query = "SELECT * FROM " . PREFIX . "cssclass";
    $stmt = $db->prepare($query);
    $stmt->execute();
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $classes[$data['id']] = array("name" => $data['name'], "description" => $data['description']);
    }

    // List CSS properties
    $query = "SELECT * FROM " . PREFIX . "cssproperty";
    $stmt = $db->prepare($query);
    $stmt->execute();
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $properties[$data['id']] = array("name" => $data['name'], "choice" => $data['choice'], "description" => $data['description']);
    }

    // List CSS values
    $query = "SELECT * FROM " . PREFIX . "fk_cssvalue ORDER BY class_id, property_id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $this->cssList[$data['class_id']]['name'] = $classes[$data['class_id']]['name'];
      $this->cssList[$data['class_id']]['description'] = $classes[$data['class_id']]['description'];
      $this->cssList[$data['class_id']]['properties'][$data['property_id']] = array(
        "name" => $properties[$data['property_id']]['name'],
        "choice" => $properties[$data['property_id']]['choice'],
        "description" => $properties[$data['property_id']]['description'],
        "value" => $data['value']
      );
    }
  }

  /**************************************************************************
  generateFormattedCSS
  ---------------------------------------------------------------------------
  Task:
    Generate formatted CSS
  ---------------------------------------------------------------------------
  Parameters:
    $db             Object          Database handle
  ---------------------------------------------------------------------------
  Return:
    CSS
  **************************************************************************/
  public function generateFormattedCSS(&$db) {
    $css_formatted = '';
    $this->loadStructure($db);
    $css_classes = $this->cssList;
    foreach ($css_classes as $key => $class) {
      $css_formatted .= "/********************************\r\n* " . strtoupper($class['description']) . "\r\n*********************************/\r\n";
      $css_formatted .= $class['name'] . "{\r\n";
      foreach ($class['properties'] as $key => $property) {
        if (strlen($property['value'])) {
          if ($property['choice'] == '~color~') {
            $property['value'] = '#' . $property['value'];
          }
          $css_formatted .= "  " . $property['name'] . ":" . $property['value'] . ";\r\n";
        }
      }
      $css_formatted .= "}";
    }
    return $css_formatted;
  }
}
?>