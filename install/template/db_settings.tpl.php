<form id="installform" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="" />
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="do_download" value="" />
  <input type="hidden" name="timestamp" value="<?php echo md5(microtime()); ?>" />
  <input type="hidden" name="db_db" value="" />
  <table border="0" width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <h3>1. Database connection settings</h3>
        <br />
<?php if (!empty($errortext)) { ?>
        <table class="main_table" cellpadding="5" cellspacing="1">
<?php foreach ($errortext as $error) { ?>
          <tr valign="middle">
            <td class="main_table_cell_error" align="left">
              <b><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></b>
            </td>
          </tr>
<?php } ?>
        </table>
        <br />
<?php } ?>
        <table class="main_table" cellpadding="5" cellspacing="1">
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>MySQL database server URL or IP address:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>" size="36" maxlength="255" title="Database server URL or IP address" />
            </td>
          </tr>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>MySQL user:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>" size="36" maxlength="255" title="MySQL user" />
            </td>
          </tr>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>MySQL password:</b>
            </td>
            <td class="main_table_cell" align="left">
              <input type="password" name="db_pw" value="<?php echo htmlspecialchars($db_pw); ?>" size="36" maxlength="255" title="MySQL password" />
            </td>
          </tr>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>Prefix for all database table names:</b>
              <br />
              It is recommended to keep this value unchanged
            </td>
            <td class="main_table_cell" align="left">
              <input type="text" name="db_prefix" value="<?php echo htmlspecialchars($db_prefix); ?>" size="36" maxlength="255" title="Prefix for all database table names" />
            </td>
          </tr>
<?php
if (!empty($conn) && isset($conn) && !empty($databases)) {
  // Show databases
?>
          <tr valign="middle">
            <td class="main_table_cell" align="left">
              <b>MySQL database name:</b>
            </td>
            <td class="main_table_cell" align="left">
              <select name="db_db">
<?php
  foreach ($databases as $database) {
?>
              <option value="<?php echo htmlspecialchars($database); ?>" <?php echo ($database == $db_db) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($database); ?></option>
<?php } ?>
              </select>
            </td>
          </tr>
<?php } ?>
        </table>
        <br />
<?php
        if (isset($db_inc_created) && $db_inc_created === false) {
          // Failed to create db.inc.php file
?>
        <table class="main_table" cellpadding="5" cellspacing="1">
          <tr valign="middle">
            <td class="main_table_cell_error" align="left">
              Failed to write database configuration file <b>config/db.inc.php</b>
              <br />
              You must fix this error in order to continue
              <br />
              There are two possible solutions:
              <br />
              <ul>
                <li>
                  Change mode of the file <b>config/db.inc.php</b> to <b>666</b>
                </li>
                <br />
                - or -
                <li>
                  Download file <b>db.inc.php</b> <a href="" onclick="doSubmit('include=<?php echo $include; ?>&do_download=1'); return false;"><font color="#000099"><b>HERE</b></font></a> and put it into directory <b>config</b>, replacing the old one.
                </li>
              </ul>
              Directory <b>config</b> located in your chat root directory.
              <br /><br />
              Then press <b>RETRY</b>
            </td>
          </tr>
        </table>
        <br />
<?php } ?>
        <input type="button" value="&nbsp;&nbsp;&nbsp;<?php echo (empty($errortext) && (!isset($db_inc_created) || $db_inc_created === true)) ? 'Continue' : 'RETRY'; ?>&nbsp;&nbsp;&nbsp;" onclick="doSubmit('include=<?php echo $include; ?>&do_download=0')" />
      </td>
    </tr>
  </table>
</form>