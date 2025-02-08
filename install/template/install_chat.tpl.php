<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="timestamp" value="<?php echo htmlspecialchars(md5(microtime()), ENT_QUOTES, 'UTF-8'); ?>" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <h3>6. Install Chat</h3>
        <br />
        <table class="main_table" cellpadding="5" cellspacing="1">
          <tr valign="middle">
            <td class="main_table_cell" align="center" colspan="3">
              <div id="installButton" style="visibility:hidden;"></div>
              <div id="installSteps" style="visibility:hidden;"></div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>