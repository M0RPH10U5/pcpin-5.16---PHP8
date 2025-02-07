<?php
if(!isset($db_host)) $db_host=DBSERVER;
if(!isset($db_user)) $db_user=DBLOGIN;
if(!isset($db_pw)) $db_pw=DBPASSWORD;
if(!isset($db_db)) $db_db=DBSEGMENT;
if(!isset($db_prefix)) $db_prefix=PREFIX;

$databases = [];

// Try to connect to database using PDO
try {
	$conn = new PDO("mysql:host=$db_host", $db_user, $db_pw);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	// Server connected, get databases list
	$stmt = $conn->query('SHOW DATABASES');
	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if ($data['Database'] != 'mysql' && $data['Database'] != 'information_schema') {
			$database[] = $data['Database'];
		}
	}
	
  if(!empty($databases)){
    sort($databases);
	
    if($db_db != ''){
      // Try to select database
      try {
		  $conn->exec("USE `$db_db`");
		  
        if(empty($read_only)){
          // Database selected. Check user privileges.
          $error='User "'.$db_user.'" has insufficient privileges for database "'.$db_db.'"';
          $table_name='pcpin_test_tbl_'.md5(microtime());
		  
		  // Test CREATE TABLE
		  $conn->exec("CREATE TABLE $table_name (pcpin_test VARCHAR(1) NOT NULL) ENGINE=MyISAM");
          
		  // Test DROP TABLE
          $conn->exec("DROP TABLE $table_name");
          
		  // Create table again for more tests
          $conn->exec("CREATE TABLE $table_name (pcpin_test VARCHAR(1) NOT NULL) ENGINE=MyISAM");
		  
		  // Test INSERT
          $conn->exec("INSERT INTO $table_name (pcpin_test) VALUES ('1')");
          
		  // Test SELECT
		  $stmt = $conn->query("SELECT * FROM $table_name");
		  $stmt->$fetchAll();
		  
		  // Test ALTER TABLE
          $conn->exec("ALTER TABLE $table_name CHANGE pcpin_test pcpin_test TEXT NOT NULL");
		  
		  // Test UPDATE
		  $conn->exec("UPDATE $table_name SET pcpin_test ='2'");
		  
		  // Test DELETE
		  $conn->exec("DELETE FROM $table_name");
		  
		  // All privileges OK, drop the table
			  $conn->exec("DROP TABLE $table_name");
			}
		  } catch (PDOException $e) {
			if (empty($quiet)) {
				$errortext[] = 'Error selecting database: ' . $e->getMessage();
			}
           }
          }
    } else {
		// No available databases found
		if (empty($quiet)) {
			$errortext [] = 'No avalable databases found';
        }
    }
  } catch (PDOException $e) {
    // Failed to connect to MySQL server
    if(empty($quiet)) {
      $errortext[]='Database connection failed: ' . $e->getMessage();
    }
  }
?>