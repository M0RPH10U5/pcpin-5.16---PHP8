<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="timestamp" value="<?php echo htmlspecialchars(md5(microtime()), ENT_QUOTES, 'UTF-8'); ?>" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <h3>2. Files, directories and permissions</h3>
        <br />
        <table class="main_table" cellpadding="5" cellspacing="1">
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>Directory</b>
            </td>
            <td class="main_table_cell" align="left">
              <b>Status</b>
            </td>
            <td class="main_table_cell" align="left">
              <b>Solution</b>
            </td>
          </tr>
<?php
foreach ($modes as $mode => $modedata) {
?>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <?php echo htmlspecialchars($modedata['name'], ENT_QUOTES, 'UTF-8'); ?>
              <br />
              <b><?php echo htmlspecialchars($modedata['path'], ENT_QUOTES, 'UTF-8'); ?></b>
            </td>
<?php
  if (true !== $modedata['status']) {
?>
            <td class="main_table_cell" align="left">
              <span style="color:#ff0000">
                NOT WRITABLE
                <br />
                <?php echo htmlspecialchars($modedata['error'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </td>
            <td class="main_table_cell" align="left">
              <?php echo htmlspecialchars($modedata['solution'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
<?php
  } else {
?>
            <td class="main_table_cell" align="left">
              <span style="color:#008800">
                WRITABLE
              </span>
            </td>
            <td class="main_table_cell" align="left">
              &nbsp;
            </td>
<?php
  }
?>
          </tr>
<?php
}
?>
        </table>
        <br />
        <input type="button" value="&nbsp;&nbsp;&nbsp;<?php echo (true === $status) ? 'Continue' : 'RETRY'; ?>&nbsp;&nbsp;&nbsp;" onclick="doSubmit('include=<?php echo (true === $status) ? htmlspecialchars($next_include, ENT_QUOTES, 'UTF-8') : htmlspecialchars($include, ENT_QUOTES, 'UTF-8'); ?>')" />
<?php
if (!$status) {
  // Some errors occurred
?>
        <input type="button" value="&nbsp;&nbsp;&nbsp;Ignore and continue&nbsp;&nbsp;&nbsp;" onclick="doSubmit('include=<?php echo htmlspecialchars($next_include, ENT_QUOTES, 'UTF-8'); ?>')" />
<?php
}
?>
      </td>
    </tr>
  </table>
</form>