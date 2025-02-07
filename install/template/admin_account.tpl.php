<script>
  function checkChkBox(){
    var myForm = document.getElementById('installform');
    if (myForm && myForm.do_skip) {
      if (myForm.do_skip.checked) {
        myForm.admin_login.disabled = true;
        myForm.admin_pw.disabled = true;
        myForm.admin_email.disabled = true;
      } else {
        myForm.admin_login.disabled = false;
        myForm.admin_pw.disabled = false;
        myForm.admin_email.disabled = false;
      }
    }
  }
</script>
<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="timestamp" value="<?php echo md5(microtime())?>" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <h3>4. Create Administrator account</h3>
        <br />
        <?php if (!empty($errortext)): ?>
          <table class="main_table" cellpadding="5" cellspacing="1">
            <?php foreach ($errortext as $error): ?>
              <tr valign="middle">
                <td class="main_table_cell_error" align="left">
                  <b><?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?></b>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          <br />
        <?php endif; ?>
        <table class="main_table" cellpadding="5" cellspacing="1">
          <?php if ($keep_users): ?>
            <tr valign="middle">
              <td class="main_table_cell" align="left" colspan="2">
                <label for="do_skip">
                  <input type="checkbox" onclick="checkChkBox()" onchange="checkChkBox()" name="do_skip" id="do_skip" value="1" />
                  Do not create new Administrator account
                </label>
              </td>
            </tr>
          <?php endif; ?>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>Administrator username:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="admin_login" value="<?php echo htmlentities($admin_login, ENT_QUOTES, 'UTF-8'); ?>" size="18" maxlength="255" />
            </td>
          </tr>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>Administrator password:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="admin_pw" value="<?php echo htmlentities($admin_pw, ENT_QUOTES, 'UTF-8'); ?>" size="18" maxlength="255" />
            </td>
          </tr>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>Administrator Email address:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="admin_email" value="<?php echo htmlentities($admin_email, ENT_QUOTES, 'UTF-8'); ?>" size="18" maxlength="255" />
            </td>
          </tr>
        </table>
        <br />
        <input type="button" value="&nbsp;&nbsp;&nbsp;Continue&nbsp;&nbsp;&nbsp;" onclick="doSubmit('include=<?php echo $include; ?>')" />
      </td>
    </tr>
  </table>
</form>