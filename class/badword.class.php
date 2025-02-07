<?php
/****************************************************************************
CLASS badword
-----------------------------------------------------------------------------
Task:
  Manage bad words
****************************************************************************/

class BadWord {

  /* Class variables */
  public int $id = 0;
  public string $word = '';
  public string $replacement = '';

  /**************************************************************************
  badword
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Creates badword object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {}

  /**************************************************************************
  listBadWords
  ---------------------------------------------------------------------------
  Task:
    Read all bad words from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
  ---------------------------------------------------------------------------
  Return:
    Array with bad words
  **************************************************************************/
  public function listBadWords($session): array {
    $query = "SELECT * FROM " . PREFIX . "badword ORDER BY word ASC";
    $result = $session->db->query($query);
    return $session->db->fetchAll($result);
  }

  /**************************************************************************
  readBadWord
  ---------------------------------------------------------------------------
  Task:
    Read bad word from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $id               int           Bad word ID
    $word             string        Bad word
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function readBadWord($session, int $id = 0, string $word = ""): void {
    if (!$id && !$word) {
        return;
    }

    $where = $id ? "id = :id" : "word = :word";
    $query = "SELECT * FROM " . PREFIX . "badword WHERE $where LIMIT 1";

    $stmt = $session->db->prepare($query);
    $params = $id ? [':id' => $id] : [':word' => $word];
    $stmt->execute($params);

    $data = $stmt->db->fetch();
    if ($data) {
        foreach ($data as $key => $val) {
            if (!isnumeric($key)) {
                $this->$key = $val;
            }
        }
    }
  }

  /**************************************************************************
  deleteBadWord
  ---------------------------------------------------------------------------
  Task:
    Delete bad word from database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $word             string        Bad word
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function deleteBadWord($session, int $id): void {
    if ($id) {
        $query = "DELETE FROM " . PREFIX . "badword WHERE id = :id LIMIT 1";
        $stmt = $session->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
  }

  /**************************************************************************
  saveBadWord
  ---------------------------------------------------------------------------
  Task:
    Add bad word into database.
  ---------------------------------------------------------------------------
  Parameters:
    $session          Object        Session handle
    $word             string        Bad word
    $replacement      string        Replacement
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function saveBadWord($session, string $word, string $replacement = ""): void {
    if (!empty($word)) {
        $query = "INSERT INTO " . PREFIX . "badword (word, replacement) VALUES (:word, :replacement)";
        $stmt = $session->db->prepare($query);
        $stmt->execute([
            ':word' => $word,
            ':replacement' => $replacement
        ]);
    }
  }
}
?>