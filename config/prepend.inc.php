<?PHP
/* WARNING: DON'T EDIT THIS FILE BEFORE YOU ARE KNOW WHAT YOU ARE DOING! */

define('PCPIN_REQUIRESMYSQL', '4');
define('PCPIN_REQUIRESPHP', '4.3');

/**
 * Check PHP version
 */


/**
 * Fix disabled "magic_quotes_gpc" setting in php.ini
 */
$magic_quotes_gpc=get_magic_quotes_gpc();

// Get request variables processing order
$variables_order = @ini_get('variables_order') ?: 'EGPCS';
$variables_order = strtolower($variables_order);

foreach (str_split($variables_order) as $var) {
  switch($var) {
    case  'g' : // _GET
                if (!empty($_GET)) {
                    $_GET = !$magic_quotes_gpc ? addSlashesRecursive($_GET) : $_GET;
                    extract($_GET);
                }
                break;
    case  'p' : // _POST
                if (!empty($_POST)) {
                  $_POST = !$magic_quotes_gpc ? addSlashesRecursive($_POST) : $_POST;
                  extract($_POST);
                }
                if (!empty($_FILES)) {
                    extract($_FILES);
                }
                break;
    case  'c' : // _COOKIE
                if (!empty($_COOKIE)) {
                  $_COOKIE = !$magic_quotes_gpc ? addSlashesRecursive($_COOKIE) : $_COOKIE;
                  extract($_COOKIE);
                }
                break;
    case  'e' : // _ENV (not used here)            
    case  's' : // _SESSION (not used here)
    default   : 
                break;
  }
}

// Free memory
unset($variables_order, $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION);

/* Loading all classes */
$classes_array = [
    'common', 'tcp', 'dbaccess', 'configuration', 'session', 'user',
    'room', 'usermessage', 'systemmessage', 'globalmessage',
    'log','ban','advertisement','fk_advertisement','smilie',
    'badword','roompass','fk_cssvalue','cssclass','email',
    'cssurl','maxusers'
];

foreach ($classes_array as $class) {
  require (CLASSPATH . '/' . $class . '.class.php');
}

/* Get user's IP address */
define('IP', $_SERVER['REMOTE_ADDR'] ?? getenv('REMOTE_ADDR'));

/* Seed the random number generator */
mt_srand((double) microtime() * 1000000);


/**
 * Adds slashes to all scalar array values recursively
 * @param   array     $target               Target array
 * @return  array     Array with stripped slashes
 */
function addSlashesRecursive($target) {
  $target_new = [];
  if (!empty($target) && is_array($target)) {
    foreach ($target as $key => $val) {
      $key = addslashes($key);
      if (is_array($val)) {
        // Value is an array. Start recursion.
        $target_new[$key] = addSlashesRecursive($val);
      } elseif (is_scalar($val)) {
        // Add slashes to the scalar value
        $target_new[$key] = addslashes($val);
      }
    }
  }
  return $target_new;
}
?>