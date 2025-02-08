<?php
/* This is a smilies page. */

// List smilies
$smilies = smilie::listSmilies($session);
$smilies_count = count($smilies);

if ($smilies_count) {
  // Calculating rows count
  $rows_count = $smilies_count / $session->config->smiliesInRow;
  if ($rows_count != round($rows_count)) {
    $rows_count = round($rows_count) + 1;
  }
}

// Loading smilies into array
unset($smilies_array);
for ($i = 0; $i < $rows_count; $i++) {
  for ($ii = 0; $ii < $session->config->smiliesInRow; $ii++) {
    $smilie_nr = $i * $session->config->smiliesInRow + $ii;
    if ($smilie_nr < $smilies_count) {
      $smilies_array[$i][$ii] = [
        "image" => IMAGEPATH . "/smilies/" . $smilies[$smilie_nr]['image'],
        "id" => $smilies[$smilie_nr]['id'],
        "nr" => $smilie_nr
    ];
    } else {
      $smilies_array[$i][$ii] = [
        "image" => IMAGEPATH . "/clearpixel.gif",
        "id" => 0,
        "nr" => $smilie_nr
      ];
    }
  }
}

// Table cellspacing
$cellspacing = 3;

// Table cellpadding
$cellpadding = 3;

// Loading template
require TEMPLATEPATH . "/smilies.tpl.php";
?>
