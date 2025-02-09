<?php
// Load headers
require TEMPLATEPATH."/all_header.tpl.php";
?>
<HTML><HEAD>
<SCRIPT>
  function checkAdvertisement(){
    advertisementWindow=window.open("about:blank");
    advertisementWindow.window.document.open();
    advertisementWindow.window.document.write(document.advertisementform.text.value);
    advertisementWindow.window.document.close();
  }
</SCRIPT>
<?php echo htmlspecialchars($css ?? '') ?>
</HEAD><BODY>
<DIV align="center">
  <TABLE class="dforeground" border="0" cellspacing="1" cellpadding="6">
    <FORM name="advertisementform" action="main.php" method="post">
      <INPUT type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id ?? '') ?>">
      <INPUT type="hidden" name="include" value="<?php echo htmlspecialchars($include ?? '') ?>">
      <INPUT type="hidden" name="advertisement_id" value="<?php echo htmlspecialchars($advertisement_id ?? '') ?>">
      <INPUT type="hidden" name="add" value="1">
      <INPUT type="hidden" name="submitted" value="1">
      <TR>
        <TD class="hforeground" colspan="2" align="center">
          <B><?php echo htmlspecialchars($lng["addadvertisement"] ?? '') ?></B>
        </TD>
      </TR>
<?php
if (is_array($error)) {
  for ($i=0; $i < count($error); $i++) {
?>
      <TR>
        <TD class="error" colspan="2" align="left">
          <B><I><?php echo htmlspecialchars($error[$i] ?? '') ?></I></B>
        </TD>
      </TR>
<?php
  }
}
?>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["advertisementtext"] ?? '') ?>:</B>
          <BR>(<?php echo htmlspecialchars($lng["htmlallowed"] ?? '') ?>)
        </TD>
        <TD class="hforeground" align="left">
          <TEXTAREA name="text" class="textinputs" rows="8" cols="40"><?php echo htmlspecialchars($text ?? '') ?></TEXTAREA>
          <BR>
          <INPUT type="button" class="buttons" onclick="checkAdvertisement();" value="<?php echo htmlspecialchars($lng["check"] ?? '') ?>">
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["start"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <?php echo htmlspecialchars($lng["date"] ?? '') ?>:
          <INPUT type="text" class="textinputs" name="start_year" value="<?php echo htmlspecialchars($start_year ?? '') ?>" size="3" maxlength="4">
          .&nbsp;<INPUT type="text" class="textinputs" name="start_month" value="<?php echo htmlspecialchars($start_month ?? '') ?>" size="1" maxlength="2">
          .&nbsp;<INPUT type="text" class="textinputs" name="start_day" value="<?php echo htmlspecialchars($start_day ?? '') ?>" size="1" maxlength="2">
          &nbsp;(<?php echo htmlspecialchars($lng["yyyymmdd"] ?? '') ?>)
          <BR><?php echo htmlspecialchars($lng["time"] ?? '') ?>:
          <INPUT type="text" class="textinputs" name="start_hour" value="<?php echo htmlspecialchars($start_hour ?? '') ?>" size="1" maxlength="2">
          :&nbsp;<INPUT type="text" class="textinputs" name="start_minute" value="<?php echo htmlspecialchars($start_minute ?? '') ?>" size="1" maxlength="2">
          :&nbsp;<INPUT type="text" class="textinputs" name="start_second" value="<?php echo htmlspecialchars($start_second ?? '') ?>" size="1" maxlength="2">
          &nbsp;(<?php echo htmlspecialchars($lng["hhmmss"] ?? '') ?>)
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["stop"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <?php echo htmlspecialchars($lng["date"] ?? '') ?>:
          <INPUT type="text" class="textinputs" name="stop_year" value="<?php echo htmlspecialchars($stop_year ?? '') ?>" size="3" maxlength="4">
          .&nbsp;<INPUT type="text" class="textinputs" name="stop_month" value="<?php echo htmlspecialchars($stop_month ?? '') ?>" size="1" maxlength="2">
          .&nbsp;<INPUT type="text" class="textinputs" name="stop_day" value="<?php echo htmlspecialchars($stop_day ?? '') ?>" size="1" maxlength="2">
          &nbsp;(<?php echo htmlspecialchars($lng["yyyymmdd"] ?? '') ?>)
          <BR><?php echo htmlspecialchars($lng["time"] ?? '') ?>:
          <INPUT type="text" class="textinputs" name="stop_hour" value="<?php echo htmlspecialchars($stop_hour ?? '') ?>" size="1" maxlength="2">
          :&nbsp;<INPUT type="text" class="textinputs" name="stop_minute" value="<?php echo htmlspecialchars($stop_minute ?? '') ?>" size="1" maxlength="2">
          :&nbsp;<INPUT type="text" class="textinputs" name="stop_second" value="<?php echo htmlspecialchars($stop_second ?? '') ?>" size="1" maxlength="2">
          &nbsp;(<?php echo htmlspecialchars($lng["hhmmss"] ?? '') ?>)
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["period"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <INPUT type="text" class="textinputs" name="period" value="<?php echo htmlspecialchars($period ?? '') ?>" size="4" maxlength="10">&nbsp;<?php echo htmlspecialchars($lng["minutes"] ?? '') ?>
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["minimumusersinroom"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <INPUT type="text" class="textinputs" name="min_roomusers" value="<?php echo htmlspecialchars($min_roomusers ?? '') ?>" size="4" maxlength="5">&nbsp;<?php echo htmlspecialchars($lng["userssmall"] ?? '') ?>
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["alsoshowinprivaterooms"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <INPUT type="radio" name="show_private" value="0" <?php echo htmlspecialchars($checked_show_private_0 ?? '') ?>>&nbsp;<?php echo htmlspecialchars($lng["no"] ?? '') ?>
          <BR>
          <INPUT type="radio" name="show_private" value="1" <?php echo htmlspecialchars($checked_show_private_1 ?? '') ?>>&nbsp;<?php echo htmlspecialchars($lng["yes"] ?? '') ?>
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" colspan="2" align="center">
          <INPUT type="submit" class="buttons" value="<?php echo htmlspecialchars($lng["addadvertisement"] ?? '') ?>">
        </TD>
      </TR>
    </FORM>
  </TABLE>
</DIV>
</BODY>
</HTML>
