<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="timestamp" value="<?php echo md5(microtime()); ?>" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <h3>5. Final configuration</h3>
        <br />
<?php
if (!empty($errortext)) {
?>
        <table class="main_table" cellpadding="5" cellspacing="1">
<?php
  foreach ($errortext as $error) {
?>
          <tr valign="middle">
            <td class="main_table_cell_error" align="left">
              <b><?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?></b>
            </td>
          </tr>
<?php
  }
?>
        </table>
        <br />
<?php
}
?>
        <table class="main_table" cellpadding="5" cellspacing="1">
<?php
foreach ($settings as $setting_name => $setting_data) {
?>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b><?php echo htmlentities($setting_data['description1'], ENT_QUOTES, 'UTF-8'); ?></b>
<?php
  if (!empty($setting_data['description2'])) {
?>
              <br />
              <?php echo htmlentities($setting_data['description2'], ENT_QUOTES, 'UTF-8'); ?>
<?php
  }
?>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="settings[<?php echo htmlentities($setting_name, ENT_QUOTES, 'UTF-8'); ?>]" value="<?php echo htmlentities($setting_data['value'], ENT_QUOTES, 'UTF-8'); ?>" size="64" maxlength="255" />
            </td>
          </tr>
<?php
}
?>
        </table>
        <br />
        <input type="button" value="&nbsp;&nbsp;&nbsp;Continue&nbsp;&nbsp;&nbsp;" onclick="doSubmit('include=<?php echo $include; ?>')" />
      </td>
    </tr>
  </table>
</form>