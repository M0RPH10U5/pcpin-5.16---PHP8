<?php
/****************************************************************************
CLASS dbAccess
-----------------------------------------------------------------------------
Task:
  Manage database access
****************************************************************************/
class dbAccess{
    private $pdo;

  /**************************************************************************
  dbAccess
  ---------------------------------------------------------------------------
  Task:
    Constructor. Creates dbaccess object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
    try {
      $dsn = 'mysql:host=' . DBSERVER . ';dbname=' . DBSEGMENT;
      $this->pdo = new PDO($dsn, DBLOGIN, DBPASSWORD);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->checkMySQLVersion();
    } catch (PDOException $e) {
      die("Could not connect to the database: " . $e->getMessage());
    }
  }


  /**************************************************************************
  connect
  ---------------------------------------------------------------------------
  Task:
    Conects to database.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  private function checkMySQLVersion() {
    $query = 'SELECT VERSION()';
    $stmt = $this->pdo->query($query);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $mysql_version = $data['VERSION()'];

    $mysql_exists = explode('.', $mysql_version);
    $mysql_needed = explode('.', PCPIN_REQUIRESMYSQL);

    foreach ($mysql_needed as $key => $val) {
      if (!isset($mysql_exists[$key])) {
        // Installed MySQL version is OK
        break;
      } elseif ($val > $mysql_exists[$key]) {
        die("<b>Fatal error</b>: Installed MySQL server version is <b>$mysql_version</b> (minimum required MySQL version is <b>" . PCPIN_REQUIRESMYSQL . "</b>)");
      } elseif ($val < $mysql_exists[$key]) {
        // Installed MySQL version is OK
        break;
      }
    }
  }

  /**************************************************************************
  query
  ---------------------------------------------------------------------------
  Task:
    Queries SQL database and returns pointer to result.
  ---------------------------------------------------------------------------
  Parameters:
    sql       string      SQL string
  ---------------------------------------------------------------------------
  Return:
    RESOURCE              SQL query result
  **************************************************************************/
  public function query($sql = "") {
    $stmt = $this->pdo->query($sql);
    return $stmt;
  }


  /**************************************************************************
  fetchArray
  ---------------------------------------------------------------------------
  Task:
    Reads dataset from query result
  ---------------------------------------------------------------------------
  Parameters:
    resource        Resource          SQL query result resource
    result_type     int               Type of result
  ---------------------------------------------------------------------------
  Return:
    array                   Dataset fetched from SQL query result
  **************************************************************************/
  public function fetchArray($stmt, $result_type = PDO::FETCH_ASSOC) {
    return $stmt->fetch($result_type);
  }


  /**************************************************************************
  fetchAll
  ---------------------------------------------------------------------------
  Task:
    Fetches all datasets from query result
  ---------------------------------------------------------------------------
  Parameters:
    result          RESOURCE      SQL query result
    result_type     int           Result type
  ---------------------------------------------------------------------------
  Return:
    array                   All datasets fetched from SQL query result
  **************************************************************************/
  public function fetchAll($stmt, $result_type = PDO::FETCH_ASSOC) {
    return $stmt->fetchAll($result_type);
  }


  /**************************************************************************
  analyzeDB
  ---------------------------------------------------------------------------
  Task:
    Analyze database tables
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return:
    Boolean TRUE if table(s) need(s) to be optimized
  **************************************************************************/
  public function testDB() {
    $query = "SHOW TABLES LIKE '" . PREFIX . "%'";
    $stmt = $this->query($query);
    $tables = $this->fetchAll($stmt);

    foreach ($tables as $data) {
      $query = "CHECK TABLE {$data[0]}";
      $stmt2 = $this->query($query);
      $data2 = $this->fetchArray($stmt2);

      if ($data2['Msg_text'] != "OK") {
        // Error or warning found. One or more tables need optimization.
        return true;
      }
    }

    // Looking for overhead
    $query = "SHOW TABLE STATUS LIKE '" . PREFIX . "%'";
    $stmt = $this->query($query);
    $tables = $this->fetchAll($stmt);

    foreach ($tables as $data) {
      if ($data['Data_free']) {
        // Overhead found. One or more tables need optimization.
        return true;
      }
    }

    return false;  // No optimization needed
  }

  /**************************************************************************
  optimizeDB
  ---------------------------------------------------------------------------
  Task:
    Optimize database tables
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function optimizeDB() {
    $query = "SHOW TABLES LIKE '" . PREFIX . "%'";
    $stmt = $this->query($query);
    $tables = $this->fetchAll($stmt);

    foreach ($tables as $table) {
      $query = "OPTIMIZE TABLE {$table[0]}";
      $this->query($query);
    }
  }

  /**************************************************************************
  getUsersRoom
  ---------------------------------------------------------------------------
  Task:
    Get user's room ID
  ---------------------------------------------------------------------------
  Parameters:
    $user_id            int             User ID
  ---------------------------------------------------------------------------
  Return:
    Room ID (int)
  **************************************************************************/
  public function getUsersRoom($user_id = 0) {
    if ($user_id) {
      $query = "SELECT room_id FROM " . PREFIX . "session WHERE user_id = :user_id";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([':user_id' => $user_id]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      return $data['room_id'];
    }
    return null;
  }
}
?>