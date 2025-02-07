<?php
/****************************************************************************
CLASS session
-----------------------------------------------------------------------------
Task:
  This class contains common used methods.
****************************************************************************/

class Common{

  /**************************************************************************
  common
  ---------------------------------------------------------------------------
  Task:
    Constructor.
    Creates common object.
  ---------------------------------------------------------------------------
  Parameters: --
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function __construct() {}

  /**************************************************************************
  dTrim
  ---------------------------------------------------------------------------
  Task:
    Collapse all double whitespaces within the string
  ---------------------------------------------------------------------------
  Parameters:
    $string         string          String to process
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function dTrim(string &$string): void {
    $string = preg_replace('/\s+/', ' ', $string);
  }

  /**************************************************************************
  doHtmlEntities
  ---------------------------------------------------------------------------
  Task:
    Replacing characters with their HTML values where possible
  ---------------------------------------------------------------------------
  Parameters:
    $string         string          String to process
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function doHtmlEntities(string &$string): void {
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }

  /**************************************************************************
  addCommand
  ---------------------------------------------------------------------------
  Task:
    Add command to the command string
  ---------------------------------------------------------------------------
  Parameters:
    command               string            Single command
    command_string        string            Command string
    separator             string            Separator between commands
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function addCommand(string $command = "", string &$command_string, string $separator = ""): void {
    if (!empty($command_string)) {
      $command_string .= $separator;
    }
    $command_string .= $command;
  }

  /**
   * E-Mail address validator
   *
   * @param   string  $email  E-Mail address
   * @param   int     $level    Validation level
   *                              Value     Description
   *                                0         No validation
   *                                1         Well-formness check
   *                                2         Hostname (or DNS record, if Hostname failed) resolution
   *                                3         Recipient account availability check
   * @return  boolean TRUE if email address is valid or FALSE if not
   */
  public function checkEmail(string $email = '', int $level = 1): bool {
    $email = trim($email);
    if ($level === 0) return true; 
    
    if ($level >= 1) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    }

    if ($level >= 2) {
        $hostname = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($hostname, "MX") && !checkdnsrr($hostname, "A")) {
            return false;
        }
    }

    if ($level >= 3) {
        $hostname = substr(strrchr($email, "@"), 1);
        $mxhosts = [];
        if (getmxrr($hostname, $mxhosts)) {
            foreach ($mxhosts as $mx) {
                $connection = @fsockopen($mx, 25, $errno, $errstr, 5);
                if ($connection) {
                    fclose($connection);
                    return true;
                }
            }
            return false;
        }
    }
    return true;
}

  /**************************************************************************
  checkDigits
  ---------------------------------------------------------------------------
  Task:
    Check string for containing digits only
  ---------------------------------------------------------------------------
  Parameters:
    digits            string            String to check
  ---------------------------------------------------------------------------
  Return:
    TRUE if string contains digits only
    FALSE if not
  **************************************************************************/
  public function checkDigits(string $digits = ""): bool {
    return ctype_digit($digits);
  }

  /**************************************************************************
  convertTextToJavaScriptVar
  ---------------------------------------------------------------------------
  Task:
    Convert any text into a string that can be used as JavaScript string
    variable.
  ---------------------------------------------------------------------------
  Parameters:
    text            string            Text to convert
  ---------------------------------------------------------------------------
  Return:
    Converted text
  **************************************************************************/
  public function convertTextToJavaScriptVar(string $text = ""): string {
    return str_replace(["\n", "\r", "\""], ["\\n", "\\r", "\\\""], $text);
  }

  /**************************************************************************
  convertDateFromTimestamp
  ---------------------------------------------------------------------------
  Task:
    Convert Unix timestamp into human-readable date string depending on
    chat settings.
  ---------------------------------------------------------------------------
  Parameters:
    session               object              Session handle
    timestamp             int                 Unix timestamp
  ---------------------------------------------------------------------------
  Return:
    Date string
  **************************************************************************/
  public function convertDateFromTimestamp(object $session, int $timestamp): string {
    return date($session->config->date_format, $timestamp);
  }

  /**************************************************************************
  randomString
  ---------------------------------------------------------------------------
  Task:
    Generate random string of characters from range [A..Za..z0..9]
  ---------------------------------------------------------------------------
  Parameters:
    length                int                 Desired string length
  ---------------------------------------------------------------------------
  Return:
    Generated string
  **************************************************************************/
  public function randomString(int $length = 10): string {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $randomString;
  }
}
?>