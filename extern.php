<?php
/* This is a PCPIN Chat redirection to external server script */

/* Offset */
define("offset","./");

/* Load configuration */
require "./config/config.inc.php";

/* Execute global actions and load classes */
require "./config/prepend.inc.php";

/* Load database connection settings */
include "./config/db.inc.php";

/* Creating session handle */
if (empty($session_id)){
  $session_id='';
}
$session=new session($session_id);

if (!isset($ext)){
  $ext='';
}
?>
<html>
    <head>
        <script>
            window.location.href="<?PHP ECHO HTMLENTITIES($ext)?>";
        </script>
    </head>
</html>
<?php exit; ?>