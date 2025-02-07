<?php
/****************************************************************************
CLASS ban
-----------------------------------------------------------------------------
Task:
  Manage banned users and IP addresses
****************************************************************************/

class Ban{

  /* Class variables */
  public int $id = 0;
  public int $user_id = 0;
  public string $ip = '';
  public int $bandate = 0;

  /**************************************************************************
  ban
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Ban user and/or IP address.
  ---------------------------------------------------------------------------
  Parameters:
    $session            object          Session handle
    $user_id            int             User ID
    $ip                 string          IP address
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function banUser($session, int $user_id = 0, string $ip = ""): void {
    if ($user_id) {
        // Delete previous bans for the same user
        $query = "DELETE FROM " . PREFIX . "ban WHERE user_id = :user_id";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        // Insert new ban record
        $query = "INSERT INTO " . PREFIX . "ban (user_id, bandate) VALUES (:user_id, UNIX_TIMESTAMP())";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
    }

    if ($ip) {
        // Delete previous bans for the same IP
        $query = "DELETE FROM " . PREFIX . "ban WHERE ip = :ip";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':ip' => $ip]);

        // Insert new ban record
        $query = "INSERT INTO " . PREFIX . "ban (ip, bandate) VALUES (:ip, UNIX_TIMESTAMP())";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':ip' => $ip]);
    }
}

  /**************************************************************************
  banList
  ---------------------------------------------------------------------------
  Task:
    List banned users and IP addresses
  ---------------------------------------------------------------------------
  Parameters:
    $session            object          Session handle
    $usr_sortby         int             Sort banned users by: (0: Username, 1: Ban date)
    $usr_sortdir        int             Banned users sort direction: (0: Ascending, 1: Descending)
    $ip_sortby          int             Sort banned IP addresses by: (0: IP, 1: Ban date)
    $ip_sortdir         int             Banned IP addresses sort direction: (0: Ascending, 1: Descending)
  ---------------------------------------------------------------------------
  Return:
    Array with banned users and IP addresses
  **************************************************************************/
  public function banList($session, int $usr_sortby = 0, int $usr_sortdir = 0, int $ip_sortby = 0, int $ip_sortdir = 0): array {
    $list = [];

    $usr_orderdir = $usr_sortdir ? 'DESC' : 'ASC';
    $usr_orderby = $usr_sortby ? 'bandate ' . $usr_orderdir : 'login ' . $usr_orderdir;
    $ip_orderdir = $ip_sortdir ? 'DESC' : 'ASC';
    $ip_orderby = $ip_sortby ? 'ip ' . $ip_orderdir : 'bandate ' . $ip_orderdir;

    // Get banned users
    $query = "SELECT bb.*, us.login 
              FROM " . PREFIX . "ban bb
              LEFT JOIN " . PREFIX . "user us ON us.id = bb.user_id
              WHERE bb.user_id > 0
              ORDER BY $usr_orderby";
    $list = $session->db->fetchAll($session->db->query($query));

    // Get banned IPs
    $query = "SELECT * FROM " . PREFIX . "ban WHERE user_id = 0 ORDER BY $ip_orderby";
    $result = $session->db->query($query);
    while ($data = $session->db->fetchArray($result)) {
        $list[] = $data;
    }

    return $list;
}

  /**************************************************************************
  unBan
  ---------------------------------------------------------------------------
  Task:
    Remove user/IP address from banlist
  ---------------------------------------------------------------------------
  Parameters:
    $session            object          Session handle
    $id                 int             Ban-ID
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function unBan($session, int $id): void {
    if ($id) {
        $query = "DELETE FROM " . PREFIX . "ban WHERE id = :id";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}

  /**************************************************************************
  checkUser
  ---------------------------------------------------------------------------
  Task:
    Check whether user banned or not
  ---------------------------------------------------------------------------
  Parameters:
    $session            object          Session handle
    $user_id            int             User-ID
  ---------------------------------------------------------------------------
  Return:
    TRUE if user is not banned
    FALSE if user is banned
  **************************************************************************/
  public function checkUser($session, int $user_id): bool {
    if (!$user_id) {
        return false;
    }

    $query = "SELECT 1 FROM " . PREFIX . "ban WHERE user_id = :user_id LIMIT 1";
    $stmt = $session->db->prepare($query);
    $stmt->execute([':user_id' => $user_id]);

    return !$stmt->fetchColumn();
}

  /**************************************************************************
  checkIP
  ---------------------------------------------------------------------------
  Task:
    Check whether IP address banned or not
  ---------------------------------------------------------------------------
  Parameters:
    $session            object          Session handle
    $ip                 string          IP address
  ---------------------------------------------------------------------------
  Return:
    TRUE if IP address is not banned
    FALSE if IP address is banned
  **************************************************************************/
  public function checkIP($session, string $ip): bool {
    if (empty($ip)) {
        return false;
    }

    $query = "SELECT 1 FROM " . PREFIX . "ban WHERE ip = :ip LIMIT 1";
    $stmt = $session->db->prepare($query);
    $stmt->execute([':ip' => $ip]);

    return !$stmt->fetchColumn();
}
}
?>