<?php
if (!isset($db_host)) $db_host = DBSERVER;
if (!isset($db_user)) $db_user = DBLOGIN;
if (!isset($db_pw)) $db_pw = DBPASSWORD;
if (!isset($db_db)) $db_db = DBSEGMENT;
if (!isset($db_prefix)) $db_prefix = PREFIX;

$databases = [];
$errortext = [];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Try to connect to database
    $conn = @mysqli_connect($db_host, $db_user, $db_pw);

    if (!$conn) {
        if (empty($quiet)) {
            $errortext[] = 'MySQL error ' . mysqli_connect_errno() . ': ' . mysqli_connect_error();
        }
    } else {
        // Server connected
        $result = mysqli_query($conn, 'SHOW DATABASES');

        while ($data = mysqli_fetch_assoc($result)) {
            if ($data['Database'] !== 'mysql' && $data['Database'] !== 'information_schema') {
                $databases[] = $data['Database'];
            }
        }

        if (!empty($databases)) {
            sort($databases);

            if ($db_db !== '') {
                // Try to select database
                if (!mysqli_select_db($conn, $db_db)) {
                    if (empty($quiet)) {
                        $errortext[] = 'MYSQL error ' . mysqli_errno($conn) . ': ' . mysqli_error($conn);
                    }
                } else {
                    if (empty($read_only)) {
                        // Database selected. Check user privileges.
                        $error = 'User "' . $db_user . '" has inusufficient privileges for database "' . $db_db . '"';
                        $table_name = 'pcpin_test_tbl_' . md5(microtime());

                        $queries = [
                            "CREATE TABLE $table_name (pcpin_test VARCHAR(1) NOT NULL) ENGINE=InnoDB",
                            "DROP TABLE $table_name",
                            "CREATE TABLE $table_name (pcpin_test VARCHAR(1) NOT NULL) ENGINE=InnoDB",
                            "INSERT INTO $table_name (pcpin_test) VALUES ('1')",
                            "SELECT * FROM $table_name",
                            "ALTER TABLE $table_name CHANGE pcpin_test pcpin_test TEXT NOT NULL",
                            "UPDATE $table_name SET pcpin_test = '2'",
                            "DELETE FROM $table_name",
                            "DROP TABLE $table_name"
                        ];

                        foreach ($queries as $index => $query) {
                            mysqli_query($conn, $query);
                            if (mysqli_errno($conn)) {
                                $failures = [
                                    0 => 'CREATE TABLE',
                                    1 => 'DROP TABLE',
                                    2 => 'CREATE TABLE',
                                    3 => 'INSERT',
                                    4 => 'SELECT',
                                    5 => 'ALTER TABLE',
                                    6 => 'UPDATE',
                                    7 => 'DELETE',
                                    8 => 'DROP TABLE'
                                ];
                                $errortext[] = $error . "('" . $failures[$index] . "' test failed)";
                                if ($index < 8) {
                                    mysqli_query($conn, "DROP TABLE $table_name"); // Cleanup
                                }
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            if (empty($quiet)) {
                $errortext[] = 'No available databases found';
            }
        }
    }
} catch (Exception $e) {
    $errortext[] = 'Exception: ' . $e->getMessage();
}
?>