<?php
// Load headers
require TEMPLATEPATH."/all_header.tpl.php";
?>
<HTML><HEAD>
<?php echo htmlspecialchars($css ?? '') ?>

<SCRIPT>
  function checkAdvertisement(advertisementHTML){
    advertisementHTML=advertisementHTML.split("|_/CR_|").join("\r");
    advertisementHTML=advertisementHTML.split("|_/LF_|").join("\n");
    advertisementWindow=window.open("about:blank", "advertisement_test", "fullscreen=no, toolbar=no, status=no, menubar=no, scrollbars=auto, resizable=yes, directories=no, width=600, height=400");
    advertisementWindow.window.document.open();
    advertisementWindow.window.document.write(advertisementHTML);
    advertisementWindow.window.document.close();
    advertisementWindow.window.focus();
  }
</SCRIPT>
</HEAD>
<BODY>
<DIV align="center">
  <TABLE class="dforeground" width="90%" border="0" cellspacing="1" cellpadding="6">
    <TR>
      <TD class="hforeground" align="center" colspan="2">
        <B><?php echo htmlspecialchars($lng['advertisements'] ?? '') ?></B>
      </TD>
    </TR>
  </TABLE>
  <BR><BR>
<?php
if ($advertisements_count) {
  for ($i=0; $i < $advertisements_count; $i++) {
?>
  <TABLE class="dforeground" width="90%" border="0" cellspacing="1" cellpadding="6">
    <TR>
      <TD class="hforeground" align="left" colspan="2">
        <?php echo htmlentities($advertisements[$i]['text'] ?? '') ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="left" width="50%">
        <B><?php echo htmlspecialchars($lng['start'] ?? '') ?>:</B>
      </TD>
      <TD class="hforeground" align="left" width="50%">
        <?php echo htmlspecialchars(common::convertDateFromTimestamp($session, $advertisements[$i]['start'] ?? '')) ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng['stop'] ?? '') ?>:</B>
      </TD>
      <TD class="hforeground" align="left">
        <?php echo htmlspecialchars(common::convertDateFromTimestamp($session, $advertisements[$i]['stop'] ?? '')) ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng['period'] ?? '') ?>:</B>
      </TD>
      <TD class="hforeground" align="left">
        <?php echo htmlspecialchars($advertisements[$i]['period'] ?? '') ?>&nbsp;<?php echo htmlspecialchars($lng['minutes'] ?? '') ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng['minimumusersinroom'] ?? '') ?>:</B>
      </TD>
      <TD class="hforeground" align="left">
        <?php echo htmlspecialchars($advertisements[$i]['min_roomusers'] ?? '') ?>&nbsp;<?php echo htmlspecialchars($lng['userssmall'] ?? '') ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng['alsoshowinprivaterooms'] ?? '') ?>:</B>
      </TD>
      <TD class="hforeground" align="left">
        <?php echo str_replace("0", htmlspecialchars($lng['no'] ?? ''), str_replace("1", htmlspecialchars($lng['yes'] ?? ''), htmlspecialchars($advertisements[$i]['show_private'] ?? ''))) ?>
      </TD>
    </TR>
    <TR>
      <TD class="hforeground" align="center" colspan="2">
        <A href="#" onclick="checkAdvertisement('<?php echo str_replace("\n","|_/LF_|",str_replace("\r","|_/CR_|",htmlentities($advertisements[$i]['text'] ?? ''))) ?>');"><?php echo htmlspecialchars($lng['check'] ?? '') ?></A>
        &nbsp;
        <A href="main.php?session_id=<?php echo htmlspecialchars($session_id ?? '') ?>&include=<?php echo htmlspecialchars($include ?? '') ?>&edit=1&advertisement_id=<?php echo htmlspecialchars($advertisements[$i]['id'] ?? '') ?>"><?php echo htmlspecialchars($lng['edit'] ?? '') ?></A>
        &nbsp;
        <A href="main.php?session_id=<?php echo htmlspecialchars($session_id ?? '') ?>&include=<?php echo htmlspecialchars($include ?? '') ?>&edit=1&delete=1&advertisement_id=<?php echo htmlspecialchars($advertisements[$i]['id'] ?? '') ?>"><?php echo htmlspecialchars($lng['delete'] ?? '') ?></A>
      </TD>
    </TR>
  </TABLE>
  <BR><BR>
<?php
  }
} else {
?>
  <TABLE class="dforeground" width="90%" border="0" cellspacing="0" cellpadding="6">
    <TR>
      <TD class="error">
        <B><I><?php echo htmlspecialchars($lng['noadvertisementsfound'] ?? '') ?></I></B>
      </TD>
    </TR>
  </TABLE>
<?php
}
?>
</DIV>
</BODY>
</HTML>
