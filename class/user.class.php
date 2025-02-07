<?php
/****************************************************************************
CLASS user
-----------------------------------------------------------------------------
Task:
  Manage users
****************************************************************************/

class user{

  /* Class variables */

  /* User ID
  *  Type: int
  */
  public $id = 0;

  /* User login name
  *  Type: string
  */
  public $login = 0;

  /* User password (MD5 encoded)
  *  Type: string
  */
  public $password = 0;

  /* Binary-represented user level
  *  Type: int
  *  Values:
  *     bit#          Description (if bit set)
  *     0 (1)           Chat statistics
  *     1 (2)           Chat design
  *     2 (4)           Chat settings
  *     3 (8)           Edit users
  *     4 (16)          Kick users
  *     5 (32)          Ban users and IP addresses
  *     6 (64)          Post global messages
  *     7 (128)         Manage advertisements
  *     8 (256)         Manage smilies
  *     9 (512)         Manage bad words
  *     10 (1024)       Manage privileges
  *     11 (2048)       Manage rooms
  *     12 (4096)       
  *     13 (8192)       
  *     14 (16384)      
  *     15 (32768)      
  *     16 (65536)      
  */
  public $level = 0;

  /* User join date and time (UNIX TIMESTAMP)
  *  Type: int
  */
  public $joined = 0;

  /* Real name
  *  Type: string
  */
  public $name = 0;

  /* user sex
  *  Type: string
  */
  public $sex = '';

  /* User nik color
  *  Type: string
  */
  public $color = '';

  /* User email
  *  Type: string
  */
  public $email = '';

  /* Whether to hide email from other users or not
  *  Type: int
  *  Possible values:
  *    1: Hide email
  *    2: Don't hide email
  */
  public $hide_email = 0;

  /* User age
  *  Type: int
  */
  public $age = 0;

  /* User location
  *  Type: string
  */
  public $location = '';

  /* User about himself
  *  Type: string
  */
  public $about = '';

  /* User's photo filename
  *  Type: string
  */
  public $photo;

  /* Is user guest?
  *  Type: int
  *  Values:
  *     0: No
  *     1: Yes
  */
  public $guest = 0;

  /* Passcode. Need to activate account or to get new password.
  *  Type: string
  */
  public $passcode = '';

  /* Activated
  *  Type: int
  */
  public $activated = 0;

  /* Last login time (UNIX TIMESTAMP)
  *  Type: int
  */
  public $last_login = 0;

  /* User's cookie
  *  Type: string
  */
  public $cookie = '';

  /* Last used IP address
  *  Type: string
  */
  public $last_ip = '';



  /**************************************************************************
  user
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Creates user object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {
  }

  /**************************************************************************
  readUser
  ---------------------------------------------------------------------------
  Task:
    Read userdata from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $user_id          int           User ID: OPTIONAL (if not set then will
                                                       be taken from the
                                                       current session)
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readUser($session, int $user_id = 0) {
    if (!$user_id) {
      $user_id = $session->user_id;
    }

    $query = "SELECT * FROM " . PREFIX . "user WHERE id = :user_id LIMIT 1";
    $stmt = $session->db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
      foreach ($data as $key => $val) {
        if (!is_numeric($key)) {
          $this->$key = $val;
        }
      }
    }

    // Get user's IP address
    $query = "SELECT ip FROM " . PREFIX . "session WHERE user_id = :user_id LIMIT 1";
    $stmt = $session->db->prepare($query);
    $stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->ip = $data['ip'] ?? '';
  }

  /**************************************************************************
  checkLogin
  ---------------------------------------------------------------------------
  Task:
    Check login and password.
    If login and password are OK then load user data.
    Update user's last login time.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function checkLogin($session) {
    $query = 'SELECT * FROM ' . PREFIX . 'user WHERE (cookie = :cookie AND cookie <> "") OR (login = :login AND password = :password) AND activated = 1 LIMIT 1';
    $stmt = $session->db->prepare($query);
    $stmt->bindParam(':cookie', $this->cookie, PDO::PARAM_STR);
    $stmt->bindParam(':login', $this->login, PDO::PARAM_STR);
    $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
      // Login is OK
      $this->readUser($session, $data['id']);
      // Generate new cookie
      $this->cookie = common::randomString(32);
      $this->updateUser($session, $this->id, 'passcode = "", last_login = UNIX_TIMESTAMP(), cookie = :cookie, last_ip = :ip', [
        ':cookie' => $this->cookie,
        ':ip' => IP
      ]);
    }
  }

  /**************************************************************************
  updateUser
  ---------------------------------------------------------------------------
  Task:
    Updates userdata
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    id                int           User ID
    fields            string        Fields to update
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function updateUser($session, int $id = 0, string $fields = "", array $params = []) {
    if ($id && $fields) {
      $query = "UPDATE " . PREFIX . "user SET $fields WHERE id = :id";
      $stmt = $session->db->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      foreach ($params as $param => $value) {
        $stmt->bindParam($param, $value);
      }
      $stmt->execute();
      $this->readUser($session, $id);
    }
  }

  /**************************************************************************
  changePassword
  ---------------------------------------------------------------------------
  Task:
    Change user's password
  ---------------------------------------------------------------------------
  Parameters:
    $session            Object        Session handle
    $old_password       string        Old password
    $new_password_1     string        New password
    $new_password_2     string        New password again
  ---------------------------------------------------------------------------
  Return:
    int     error code
  **************************************************************************/
  public function changePassword($session, string $old_password = "", string $new_password_1 = "", string $new_password_2 = "") {
    // Check current password
    if (password_verify($old_password, $this->password)) {
      // Compare new passwords
      if ($new_password_1 === $new_password_2) {
        // Check new password length
        if (strlen($new_password_1) >= $session->config->password_length_min && strlen($new_password_1) <= $session->config->password_length_max) {
          // Check characters in new password
          if (preg_match("/^[a-zA-Z0-9]*$/", $new_password_1)) {
            $new_hashed_password = password_hash($new_password_1, PASSWORD_BCRYPT);
            $this->updateUser($session, $session->user_id, "password = :password", [':password' => $new_hashed_password]);
            return 0;
          } else {
            // Illegal characters in new password
            return 4;
          }
        } else {
          // Illegal password length
          return 3;
        }
      } else {
        // New passwords are not identical
        return 2;
      }
    } else {
      // Wrong current password
      return 1;
    }
  }

  /**************************************************************************
  listUsers
  ---------------------------------------------------------------------------
  Task:
    List chat users
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $username         string        Username
    $orderby          string        Field to order by
    $startfrom        int           Start from dataset number X
    $limit            int           Return max Y datasets
    $online_status    int           Online status (0: any, 1: online, 2: offline)
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function listUsers(&$session, $username = "", $orderby = "", $startfrom = 0, $limit = 0, $online_status = 0) {
    $where = "1"; // Default condition
    $params = []; // Array to store parameters for prepared statements

    if ($username) {
        $where .= " AND us.login = :username";
        $params[':username'] = $username;
    }

    if (!$orderby) {
        $orderby = "us.login ASC";
    }

    $limitby = "";
    if (!empty($limit)) {
        $limitby = "LIMIT :startfrom, :limit";
        $params[':startfrom'] = $startfrom;
        $params[':limit'] = $limit;
    }

    if ($online_status == 1) {
        $where .= ' AND se.id IS NOT NULL';
    } elseif ($online_status == 2) {
        $where .= ' AND se.id IS NULL';
    }

    $query = "SELECT us.* FROM " . PREFIX . "user us LEFT JOIN " . PREFIX . "session se ON (se.user_id = us.id) WHERE $where ORDER BY $orderby $limitby";

    // Prepare the query
    $stmt = $session->db->prepare($query);
    $stmt->execute($params); // Execute with bound parameters

    $list = [];
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($session->isOnline($data['id'])) {
            $data['online'] = 1;
        }
        $list[] = $data;
    }

    return $list;
}

  /**************************************************************************
  addUser
  ---------------------------------------------------------------------------
  Task:
    Add new user
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function addUser(&$session) {
    $query = "INSERT INTO " . PREFIX . "user (login, password, level, joined, name, sex, color, email, hide_email, age, location, about, photo, guest, activated) 
              VALUES (:login, :password, :level, UNIX_TIMESTAMP(), :name, :sex, :color, :email, :hide_email, :age, :location, :about, :photo, :guest, :activated)";

    $stmt = $session->db->prepare($query);
    $stmt->execute([
        ':login' => $this->login,
        ':password' => $this->password,
        ':level' => $this->level,
        ':name' => $this->name,
        ':sex' => $this->sex,
        ':color' => $this->color,
        ':email' => $this->email,
        ':hide_email' => $this->hide_email,
        ':age' => $this->age,
        ':location' => $this->location,
        ':about' => $this->about,
        ':photo' => $this->photo,
        ':guest' => $this->guest,
        ':activated' => $this->activated
    ]);

    // Reload user
    $this->findUser($session, $this->login);
}

  /**************************************************************************
  deleteUser
  ---------------------------------------------------------------------------
  Task:
    Delete user
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $user_id          int           User ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteUser(&$session, $user_id) {
    if ($user_id) {
        // Read user data
        $tmp = new User();
        $tmp->readUser($session, $user_id);

        // Delete photo
        if ($tmp->photo != '' && $tmp->photo != 'nophoto.jpg') {
            unlink(IMAGEPATH . "/userphotos/{$tmp->photo}");
        }

        // Delete user from ban list
        $query = "DELETE FROM " . PREFIX . "ban WHERE user_id = :user_id LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        // Delete user from database
        $query = "DELETE FROM " . PREFIX . "user WHERE id = :user_id LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
    }
  }

  /**************************************************************************
  findUser
  ---------------------------------------------------------------------------
  Task:
    Find user and read his data
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $login            string        Login
    $email            string        Email address
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function findUser(&$session, $login = "", $email = "") {
    $where = "";
    $params = [];

    if ($login) {
        $where .= " AND login = :login";
        $params[':login'] = $login;
    }

    if ($email) {
        $where .= " AND email = :email";
        $params[':email'] = $email;
    }

    if ($where) {
        $query = "SELECT id FROM " . PREFIX . "user WHERE 1 $where LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute($params);

        if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->readUser($session, $data['id']);
        }
    }
  }

  /**************************************************************************
  generatePassCode
  ---------------------------------------------------------------------------
  Task:
    Generate new passcode
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $user_id          int           User ID
    $length           int           Passcode length
  ---------------------------------------------------------------------------
  Return:
    (string)  Passcode
  **************************************************************************/
  public function generatePassCode(&$session, $user_id = 0, $length = 0) {
    $passcode = "";
    $this->readUser($session, $user_id);

    if ($this->id) {
        // Generate new code
        if ($length < 3) {
            $length = 3; // Default value
        }

        $passcode = '';
        $loop = ceil($length / 32);
        while ($loop) {
            $passcode .= md5(mt_rand(-time(), time()) . microtime());
            $loop--;
        }

        $passcode = substr($passcode, 0, $length);

        // Save code into database
        $this->updateUser($session, $this->id, 'passcode = :passcode', [':passcode' => md5($passcode)]);
    }

    return $passcode;
  }

  /**************************************************************************
  generatePassword
  ---------------------------------------------------------------------------
  Task:
    Generate new password
  ---------------------------------------------------------------------------
  Parameters:
    $session              Object          Session handle
    $user_id              int             User ID
    $activation_code      string          Activation code
    $password_1           string          New password
    $password_2           string          New password again
  ---------------------------------------------------------------------------
  Return:
    (int)  error code
  **************************************************************************/
  public function generatePassword(&$session, $user_id = 0, $activation_code = "", $new_password_1 = "", $new_password_2 = "") {
    // Read user data
    $this->readUser($session, $user_id);

    if ($this->id) {
        // Check activation code
        if ($this->passcode != '' && $this->passcode == md5($activation_code)) {
            // Activation code OK
            // Compare new passwords
            if ($new_password_1 == $new_password_2) {
                // Check new password length
                if (strlen($new_password_1) >= $session->config->password_length_min && strlen($new_password_1) <= $session->config->password_length_max) {
                    // Check characters in new password
                    if (preg_replace("/[^0-9a-zA-Z]/", "", $new_password_1) == $new_password_1) {
                        // Save new password
                        $this->updateUser($session, $this->id, "passcode = '', password = :password", [':password' => md5($new_password_1)]);
                        return 0;
                    } else {
                        // Illegal characters in new password
                        return 4;
                    }
                } else {
                    // Illegal password length
                    return 3;
                }
            } else {
                // New passwords are not identical
                return 2;
            }
        } else {
            // Wrong activation code
            return 1;
        }
    } else {
        return 1;
    }
  }

  /**************************************************************************
  cleanUp
  ---------------------------------------------------------------------------
  Task:
    Clean up users
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function cleanUp(&$session) {
    // Delete non-activated user accounts
    if ($session->config->delete_notactivated) {
        $delete_before = time() - $session->config->delete_notactivated * 3600;
        $query = "DELETE FROM " . PREFIX . "user WHERE activated = '0' AND joined < $delete_before";
        $session->db->query($query);
    }

    // Kick idle users out
    if ($session->config->kick_idle) {
        $kick_before = time() - $session->config->kick_idle;
        $query = "SELECT se.user_id FROM " . PREFIX . "session se LEFT JOIN " . PREFIX . "user us ON us.id = se.user_id WHERE us.level = 0 AND se.direct_login = 0 AND se.room_id > 0 AND se.last_post_time < $kick_before";
        $result = $session->db->query($query);
        while ($data = $session->db->fetchArray($result)) {
            // Update user's session
            $session2 = new session($session->getUsersSession($data[0]));
            $session2->updateSession("kicked = 1");
            // Post a system message
            systemMessage::insertMessage($session, $data[0], 6);
        }
    }

    // Delete inactive user accounts
    if ($session->config->delete_inactive) {
        $delete_before = time() - $session->config->delete_inactive * 86400;
        // Get all inactive user IDs (non-privileged users only)
        $query = "SELECT id FROM " . PREFIX . "user WHERE level = 0 AND (last_login = 0 AND joined < $delete_before OR last_login > 0 AND last_login < $delete_before)";
        $result = $session->db->query($query);
        while ($data = $session->db->fetchArray($result)) {
            // Delete offline users only
            if (!$session->isOnline($data[0])) {
                $this->deleteUser($session, $data[0]);
            }
        }
    }
  }

  /**************************************************************************
  countUsers
  ---------------------------------------------------------------------------
  Task:
    Count chat users
  ---------------------------------------------------------------------------
  Parameters:
    $session                Object        Session handle
    &$registered            int           Registered users count
    &$registered_online     int           Online registered users count
    &$guests_online         int           Online guest users count
    &$total_online          int           Total online count
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function countUsers(&$session, &$registered, &$registered_online, &$guests_online, &$total_online) {
    $registered = 0;
    $registered_online = 0;
    $guests_online = 0;
    $total_online = 0;

    $query = 'SELECT
               COUNT(DISTINCT se.id) AS total_online,
               COUNT(DISTINCT IF(us.guest = 0, us.id, NULL)) AS registered,
               COUNT(DISTINCT IF(us.guest = 0, se.id, NULL)) AS registered_online,
               COUNT(DISTINCT IF(us.guest = 1, se.id, NULL)) AS guests_online
          FROM ' . PREFIX . 'user us
               LEFT JOIN ' . PREFIX . 'session se ON se.user_id = us.id';

    $result = $session->db->query($query);
    if ($data = $session->db->fetchArray($result, MYSQL_ASSOC)) {
        $registered = $data['registered'];
        $registered_online = $data['registered_online'];
        $guests_online = $data['guests_online'];
        $total_online = $data['total_online'];
    }
  }
}
?>