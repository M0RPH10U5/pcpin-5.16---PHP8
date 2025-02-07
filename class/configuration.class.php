<?php
/****************************************************************************
CLASS configuration
-----------------------------------------------------------------------------
Task:
  Manage configuration
****************************************************************************/

class Configuration{

  /* Class variables: Diverse configuration variables */

  /**************************************************************************
  Constructor
  ---------------------------------------------------------------------------
  Task:
    Create configuration object.
    Load configuration.
  ---------------------------------------------------------------------------
  Parameters:
          $db         Object          Database handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct($db) {
    $this->loadConfiguration($db);
}

  /**************************************************************************
  loadConfiguration
  ---------------------------------------------------------------------------
  Task:
    Load configuration from database
  ---------------------------------------------------------------------------
  Parameters:
          $db         Object          Database handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function loadConfiguration($db) {
    $query = "SELECT * FROM " . PREFIX . "configuration";
    $result = $db->query($query);
    while ($data = $db->fetchArray($result)) {
        if (!preg_match("/^\d+$/", $data['name'])) {
            /* Using alphanumeric keys only */
            $this->{$data['name']} = $data['value'];
        }
    }
}

  /**************************************************************************
  loadFullConfiguration
  ---------------------------------------------------------------------------
  Task:
    Load full configuration with comments from database
  ---------------------------------------------------------------------------
  Parameters: --
    $session          Object          Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function loadFullConfiguration($session) {
    $query = "SELECT * FROM " . PREFIX . "configuration";
    $result = $session->db->query($query);
    return $session->db->fetchAll($result);
}

  /**************************************************************************
  changeParameter
  ---------------------------------------------------------------------------
  Task:
    Change value of one parameter
  ---------------------------------------------------------------------------
  Parameters: --
    $session            Object          Session handle
    $parameter_name     string          Name of the parameter
    $value              string          New value
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function changeParameter($session, $parameter_name = "", $value = "") {
    if ($parameter_name) {
        $query = "UPDATE `" . PREFIX . "configuration` SET `value` = ? WHERE `name` = ?";
        $stmt = $session->db->prepare($query);
        $stmt->execute([$value, $parameter_name]);
    }
  }
}
?>