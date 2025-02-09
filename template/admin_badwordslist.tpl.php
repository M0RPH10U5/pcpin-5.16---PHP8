<?php
// Load headers
require TEMPLATEPATH . "/all_header.tpl.php";
?>
<HTML><HEAD>
<?php echo htmlspecialchars($css ?? '') ?>
</HEAD>
<DIV align="center">
<TABLE class="dforeground" border="0" cellspacing="1" cellpadding="6">
  <TR valign="center">
    <TD class="hforeground" align="center" colspan="3">
      <B><?php echo htmlspecialchars($lng["badwords"] ?? '') ?></B>
    </TD>
  </TR>
<?php
if ($badwords_count) {
?>
    <TR valign="center">
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng["badword"] ?? '') ?></B>
      </TD>
      <TD class="hforeground" align="left">
        <B><?php echo htmlspecialchars($lng["replacement"] ?? '') ?></B>
      </TD>
      <TD class="hforeground">
        &nbsp;
      </TD>
    </TR>
<?php
  for ($i=0; $i < $badwords_count; $i++) {
?>
    <TR valign="center">
      <TD class="hforeground" align="left">
        <?php echo htmlspecialchars($badwords[$i]['word'] ?? '') ?>
      </TD>
      <TD class="hforeground" align="left">
        <?php echo htmlspecialchars($badwords[$i]['replacement'] ?? '') ?>
      </TD>
      <TD class="hforeground" align="center">
        <A href="main.php?session_id=<?php echo htmlspecialchars($session_id ?? '') ?>&include=<?php echo htmlspecialchars($include ?? '') ?>&edit=1&badword_id=<?php echo htmlspecialchars($badwords[$i]['id'] ?? '') ?>"><?php echo htmlspecialchars($lng["edit"] ?? '') ?></A>
        &nbsp;
        <A href="main.php?session_id=<?php echo htmlspecialchars($session_id ?? '') ?>&include=<?php echo htmlspecialchars($include ?? '') ?>&edit=1&delete=1&badword_id=<?php echo htmlspecialchars($badwords[$i]['id'] ?? '') ?>"><?php echo htmlspecialchars($lng["delete"] ?? '') ?></A>
      </TD>
    </TR>
<?php
  }
?>
  </TABLE>
  <BR><BR>
<?php
} else {
?>
  <TABLE class="dforeground" width="90%" border="0" cellspacing="1" cellpadding="6">
    <TR>
      <TD class="error" align="left">
        <B><I><?php echo htmlspecialchars($lng["nobadwordsfound"] ?? '') ?></I></B>
      </TD>
    </TR>
  </TABLE>
<?php
}
?>
</DIV>
</BODY>
</HTML>
