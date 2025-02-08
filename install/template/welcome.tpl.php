<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="timestamp" value="<?php echo htmlspecialchars(md5(microtime()), ENT_QUOTES, 'UTF-8'); ?>" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <br /><br /><br /><br /><br />
        <h2>Welcome to the PCPIN Chat installation!</h2>
        <h3>Version <?php echo htmlspecialchars(PCPIN_CHAT_VERSION, ENT_QUOTES, 'UTF-8'); ?></h3>
        <br /><br />
        <table border="0" cellspacing="0" cellpadding="3">
          <tr valign="middle">
            <td align="left">
              <b>1. Database connection settings</b>
            </td>
          </tr>
          <tr valign="middle">
            <td align="left">
              <b>2. Files, directories and permissions</b>
            </td>
          </tr>
          <tr valign="middle">
            <td align="left">
              <b>3. Import settings</b>
            </td>
          </tr>
          <tr valign="middle">
            <td align="left">
              <b>4. Create Administrator account</b>
            </td>
          </tr>
          <tr valign="middle">
            <td align="left">
              <b>5. Final configuration</b>
            </td>
          </tr>
          <tr valign="middle">
            <td align="left">
              <b>6. Install chat</b>
            </td>
          </tr>
        </table>
        <br />
        <div id="startBtn" style="visibility:hidden">
          <br />
          <input type="button" value="Start installation/update" onclick="doSubmit('include=1000')" />
        </div>
      </td>
    </tr>
  </table>
</form>
