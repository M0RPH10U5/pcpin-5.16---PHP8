<?php
/* This is a language selection page */

/* Open languages directory */
$handle = opendir(LANGUAGEPATH);
$lng_array = [];
/* Read each entry */
while ($file = readdir($handle)) {
  if (is_file(LANGUAGEPATH . "/" . $file) && substr($file, -8, 8) == ".lng.php" && is_readable(LANGUAGEPATH . "/" . $file)) {
    /* Adding each passed language file to array */
    $lng_array[] = $file;
  }
}
closedir($handle);
if (empty($lng_array)) {
  /* If no languages were found */
  die("There are no available language files!");
}

// Sort language list alphabetically
sort($lng_array);

/* Trying to get language from user's browser HTTP request */
/* Some browsers can accept multiple languages */
$lng_files_realpath = str_replace("\\", '/', strtolower(realpath(LANGUAGEPATH)));
$accept_array = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
/* Checking each language */
$found = false;
for ($i = 0; $i < count($accept_array) && !$found; $i++) {
  $one_lng_array = explode(";", $accept_array[$i]);
  $accept_language = strtolower(trim($one_lng_array[0]));
  if (!empty($accept_language)) {
    /* Accept-Language acquired. Trying to find passed language file */
    foreach ($lng_array as $lng_name) {
      $lng_path = LANGUAGEPATH . '/' . $lng_name;
      $lng_realpath = str_replace("\\", '/', strtolower(realpath(dirname($lng_path))));
      if ($lng_realpath == $lng_files_realpath && is_file($lng_path) && is_readable($lng_path)) {
        $ISO_639_LNG = null;
        require($lng_path);
        if (!empty($ISO_639_LNG) && is_scalar($ISO_639_LNG) && $accept_language == strtolower(trim($ISO_639_LNG))) {
          /* Language passed to request was found */
          $language = $lng_name;
          $found = true;
          break;
        }
      }
    }
  }
}
/* Load language selection page template */
require(TEMPLATEPATH . "/language.tpl.php");
?>
