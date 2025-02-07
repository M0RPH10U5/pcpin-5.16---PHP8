<?php
// Check rights
if (!($current_user->level & 512)){
  die("HACK?");
}

// Declare class
$badword = new badword();

// Delete bad word
if ($edit && $delete && $badword_id){
  $badword->deleteBadword($session, $badword_id);
  unset($badword_id);
}

if ($add || $badword_id) {
  // Add bad word
  if ($submitted) {
    // Save bad word
    // Validate form
    unset($error);
    // Check word
    common::dTrim($word);

    if (empty($word)) {
      $error[] = $lng["wordempty"];
    } elseif (strpos($word, "'") || strpos($word, "\\") || strpos($word, "\"") || strpos($word, "<") || strpos($word, ">")) {
      $error[] = $lng["invalidcharsinword"];
    } else {
      if (!$edit) {
        $badword->readBadword($session, 0, $word);
        if ($badword->id) {
          // Bad word already exists
          $error[] = $lng["badwordexists"];
        }
      }
    }
    if (!isset($error)) {
      // No errors
      // Delete old bad word
      if ($badword_id) {
        $badword->deleteBadWord($session, $badword_id); // Delete old bad word
      }
      // Save bad word
      $badword->saveBadWord($session, $word, $replacement);
      unset($badword_id);
      $edit = 1;
    } else {
      unset($submitted);
    }
  }
  if (!$submitted){
    // Show form
    if ($edit) {
      $badword->readBadWord($session, $badword_id);
      $word = $badword->word;
      $replacement = $badword->replacement;
    }
    // Load teplate
    require(TEMPLATEPATH . "/admin_badword.tpl.php");
  }
}
if ($edit && !$badword_id) {
  // Show bad word list
  $badwords = $badword->listBadWords($session);
  $badwords_count = count($badwords);
  // Load teplate
  require(TEMPLATEPATH . "/admin_badwordslist.tpl.php");
}
?>