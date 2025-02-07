<?php
/**
 * Class _TCP
 * Contains methods for TCP communication
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2006, Konstantin Reznichak
 */
class _TCP {

  /**
   * Check if are any DNS records corresponding to a given Internet host name or IP address
   * @param   string  $hostname   Host name or IP address
   * @return  boolean  TRUE if any records are found or FALSE if no records were found or if an error occurred
   */
  public function checkDNSRecord(string $hostname = ''): bool {
    $result = false;
    $hostname = strtolower(trim($hostname));

    if ($hostname !== '') {
      if (function_exists('checkdnsrr')) {
        // Non-Windows platform
        $result = checkdnsrr($hostname, 'ANY');
      } else {
        // Windows platform
        @exec('nslookup.exe -type=ANY ' . $hostname, $output = null);
        if (!empty($output)) {
          foreach ($output as $line) {
            if (0 === strpos(strtolower($line), $hostname)) {
              // DNS record found
              $result = true;
              break;
            }
          }
        }
      }
    }
    return $result;
  }

  /**
   * Get MX records as IP addresses corresponding to a given
   * Internet host name sorted by weight
   * @param   string  $hostname   Host name
   * @return  array   Array with IP addresses
   */
  public function getMXRecords(string $hostname = ''): array {
    $ips = [];
    if ($hostname !== '') {
      $records = [];
      if (function_exists('getmxrr')) {
        // Non-Windows platform
        $mxhosts = null;
        $weights = null;
        if (false !== getmxrr($hostname, $mxhosts, $weights)) {
          // Sort MX records by weight
          $key_host = [];
          foreach ($mxhosts as $key => $host) {
            if (!isset($key_host[$weights[$key]])) {
              $key_host[$weights[$key]] = [];
            }
            $key_host[$weights[$key]][] = $host;
          }
          unset($weights);
          $records = [];
          ksort($key_host);
          foreach ($key_host as $hosts) {
            foreach ($hosts as $host) {
              $records[] = $host;
            }
          }
        }
      } else {
        // Windows platform
        $result = shell_exec('nslookup.exe -type=MX ' . $hostname);
        if ($result !== '') {
          if (preg_match_all("'^.*MX preference = (\d{1,10}), mail exchanger = (.*)$'simU", $result, $matches)) {
            if (!empty($matches[2])) {
              array_shift($matches);
              array_multisort($matches[0], $matches[1]);
              $records = $matches[1];
            }
          }
        }
      }
    }

    // Resolve host names
    if (!empty($records)) {
      foreach ($records as $rec) {
        if ($resolved = gethostbynamel($rec)) {
          foreach ($resolved as $ip) {
            $ips[] = $ip;
          }
        }
      }
    }
    return $ips;
  }

  /**
   * Open socket connection to specified host
   * @param   resource  $conn       A reference to connection handler
   * @param   int       $errno      If an error occured: error number
   * @param   string    $errstr     If an error occured: error description
   * @param   string    $hostname   Host name or IP address
   * @param   int       $timeout    Connection timeout
   * @return  boolean
   */
  public function connectHost(&$conn, &$errno, &$errstr, string $host = '', int $timeout = 30): bool {
    if ($host !== '') {
      $errno = null;
      $errstr = null;
      $conn = fsockopen(gethostbyname($host), 25, $errno, $errstr, $timeout);
      if ($conn === false || !is_resource($conn)) {
        $conn = null;
        return false;
      }
      return true;
    }
    return false;
  }

  /**
   * Reads line from a socket connection. Lines must end with CRLF sequence
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $line       A reference to read line
   * @param   int       $limit      Line length limit
   * @return  boolean   TRUE on success or FALSE on error
   */
  public function readLineConn(&$conn, &$line, int $limit = 65535): bool {
    $result = false;
    $line = '';
    if (!empty($conn) && is_resource($conn)) {
      $char = '';
      $last_char = '';
      do {
        $last_char = $char;
        if (($char = fgetc($conn)) === false) {
          break;
        } else {
          $line .= $char;
        }
      } while ($last_char . $char !== "\r\n" && $char !== false);
      if ($line !== '') {
        $result = true;
      }
    }
    return $result;
  }

  /**
   * Reads the last line from a socket connection
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $line       A reference to read line
   * @param   int       $limit      Line length limit
   * @return  boolean   TRUE on success or FALSE on error
   */
  public function readLastLineConn(&$conn, &$line, int $limit = 65535): bool {
    $result = false;
    $line = '';
    if (!empty($conn) && is_resource($conn) && !feof($conn)) {
      while ($this->readLineConn($conn, $line)) {
        if ($line === '') {
          break;
        } elseif (substr($line, 3, 1) === ' ') {
          $result = true;
          break;
        }
      }
    }
    return $result;
  }

  /**
   * Parses status code from response line
   * @param   string    $line       Response line
   * @return  int   Status code
   */
  public function getStatus(string $line = ''): int {
    $status = 0;
    if ($line !== '') {
      $status = (int)substr($line, 0, strpos($line, ' '));
    }
    return $status;
  }

  /**
   * Send data to a socket connection
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $data       Data to send
   * @return  boolean   TRUE on success or FALSE on error
   */
  public function writeDataConn(&$conn, string $data = ''): bool {
    if (is_resource($conn)) {
      return fwrite($conn, $data) !== false;
    }
    return false;
  }
}
?>