<?php
$badword = new badword();
$badwords = $badword->listBadWords($session);
?>

/* Bad words */
var badWords_word = [];
var badWords_replacement = [];
<?php
for ($i = 0; $i < count($badwords); $i++) {
?>
  badWords_word[<?php echo $i; ?>] = new RegExp("<?php echo htmlspecialchars($badwords[$i]['word'], ENT_QUOTES, 'UTF-8'); ?>", "gi");
  badWords_replacement[<?php echo $i; ?>] = "<?php echo htmlspecialchars($badwords[$i]['replacement'], ENT_QUOTES, 'UTF-8'); ?>";
<?php
}
?>

/**************************************************************************
FUNCTION replaceBadWords
---------------------------------------------------------------------------
Task:
  Replace bad words
---------------------------------------------------------------------------
Parameters:
  message       string        Message body
---------------------------------------------------------------------------
Return:
  Message string
**************************************************************************/
function replaceBadWords(message) {
  try {
    var badWordsCount = badWords_word.length;
    for (var i = 0; i < badWordsCount; i++) {
      message = message.replace(badWords_word[i], badWords_replacement[i]);
    }
  } catch (e) {}
  return message;
}

