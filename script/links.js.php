/**************************************************************************
  FUNCTION addLinks
  ---------------------------------------------------------------------------
  Task:
    Add links to message
  ---------------------------------------------------------------------------
  Parameters:
    message       string        Message body
  ---------------------------------------------------------------------------
  Return:
    Message string with clickable links
  **************************************************************************/
  function addLinks(message){
    try{
      // Splitting message into chunks
      var messageParts = message.split(" ");
      var messagePartsLength = messageParts.length;
      var tmp;
      var tmp2;
      // Check each chunk
      for (var m = 0; m < messagePartsLength; m++) {
        if (messageParts[m].substring(0, 7).toLowerCase() == "http://") {
          // Chunk begins with 'http://'
          var messageParts_tmp = messageParts[m].split('&').join('%26');
          messageParts[m] = "<A href=\"<?php echo htmlspecialchars($session->config->homepage ?? '') ?>/extern.php?ext=" + encodeURIComponent(messageParts_tmp) + "\" target=\"_blank\">" + messageParts[m] + "</A>";
        } else if (messageParts[m].substring(0, 4).toLowerCase() == "www.") {
          // Chunk begins with 'www.'
          var messageParts_tmp = messageParts[m].split('&').join('%26');
          messageParts[m] = "<A href=\"<?php echo htmlspecialchars($session->config->homepage ?? '') ?>/extern.php?ext=http://" + encodeURIComponent(messageParts_tmp) + "\" target=\"_blank\" class=\"msglink\">" + messageParts[m] + "</A>";
        } else {
          tmp = messageParts[m].split("@");
          if (tmp.length == 2) {
            tmp2 = tmp[1].split(".");
            if (tmp2.length == 2 || tmp2.length == 3) {
              // Chunk is eMail address
              messageParts[m] = "<A href=\"mailto:" + messageParts[m] + "\" target=\"_blank\" class=\"msglink\">" + messageParts[m] + "</A>";
            }
          }
        }
      }
      return messageParts.join(" ");
    } catch (e) {}
    return message;
  }
