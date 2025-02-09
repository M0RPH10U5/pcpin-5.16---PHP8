<?php
// Load headers
require TEMPLATEPATH."/all_header.tpl.php";
?>
<HTML><HEAD>
<SCRIPT>
  var allowed=true;
  function autoComplete(){
    if(allowed){
      var charsCount=document.badwordform.word.value.length;
      if(charsCount){
        document.badwordform.replacement.value=document.badwordform.word.value.substr(0,1);
      }
      for(var i=1;i<charsCount;i++){
        document.badwordform.replacement.value+="*";
      }
      document.badwordform.replacement.select();
    }
    return true;
  }
</SCRIPT>
<?php echo htmlspecialchars($css ?? '') ?>
</HEAD><BODY>
<DIV align="center">
  <TABLE class="dforeground" border="0" cellspacing="1" cellpadding="6">
    <FORM name="badwordform" action="main.php" method="post">
      <INPUT type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id ?? '') ?>">
      <INPUT type="hidden" name="include" value="<?php echo htmlspecialchars($include ?? '') ?>">
      <INPUT type="hidden" name="badword_id" value="<?php echo htmlspecialchars($badword_id ?? '') ?>">
      <INPUT type="hidden" name="add" value="<?php echo htmlspecialchars($add ?? '') ?>">
      <INPUT type="hidden" name="edit" value="<?php echo htmlspecialchars($edit ?? '') ?>">
      <INPUT type="hidden" name="submitted" value="1">
      <TR>
        <TD class="hforeground" colspan="2" align="center">
          <B><?php echo htmlspecialchars($lng["addbadword"] ?? '') ?></B>
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
          <B><?php echo htmlspecialchars($lng["badword"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <INPUT type="text" class="textinputs" name="word" value="<?php echo htmlspecialchars($word ?? '') ?>" size="10" maxlength="255" onChange="autoComplete();">
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" align="left">
          <B><?php echo htmlspecialchars($lng["replacement"] ?? '') ?>:</B>
        </TD>
        <TD class="hforeground" align="left">
          <INPUT type="text" class="textinputs" name="replacement" value="<?php echo htmlspecialchars($replacement ?? '') ?>" size="10" maxlength="255" onKeyDown="allowed=false; return true;">
        </TD>
      </TR>
      <TR>
        <TD class="hforeground" colspan="2" align="center">
          <INPUT type="submit" class="buttons" value="<?php echo htmlspecialchars($lng["save"] ?? '') ?>">
        </TD>
      </TR>
    </FORM>
  </TABLE>
</DIV>
</BODY>
</HTML>
