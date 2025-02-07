<?php
// Load headers
require TEMPLATEPATH . "/all_header.tpl.php";
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($ISO_639_LNG, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="<?php echo htmlspecialchars($lng["charset"], ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars($session->config->title, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php echo $css; ?>
</head>
<body>
<div align="center">
    <table class="dforeground" border="0" cellspacing="1" cellpadding="6">
        <?php if (!empty($password_changed)) : ?>
            <form name="profileform" action="main.php" method="post">
                <input type="hidden" name="language" value="<?php echo htmlspecialchars($language, ENT_QUOTES, 'UTF-8'); ?>">
                <tr>
                    <td class="hforeground" colspan="2" align="center">
                        <b><?php echo htmlspecialchars($lng["passchanged"], ENT_QUOTES, 'UTF-8'); ?></b>
                    </td>
                </tr>
                <tr>
                    <td class="hforeground" colspan="2" align="center">
                        <input type="submit" class="buttons" value="<?php echo htmlspecialchars($lng["ok"], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
            </form>
        <?php else : ?>
            <tr>
                <td colspan="2" class="hforeground" align="center">
                    <b><?php echo htmlspecialchars($lng["accountactivation"], ENT_QUOTES, 'UTF-8'); ?></b>
                </td>
            </tr>
            <?php if (!empty($errortext) && is_array($errortext)) : ?>
                <?php foreach ($errortext as $error) : ?>
                    <tr>
                        <td colspan="2" class="error" align="left">
                            <b><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></b>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <form name="profileform" action="main.php" method="post">
                <input type="hidden" name="language" value="<?php echo htmlspecialchars($language, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="include" value="2">
                <input type="hidden" name="confirm" value="1">
                <input type="hidden" name="a" value="<?php echo htmlspecialchars($a, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="submitted" value="1">
                <tr>
                    <td class="hforeground" align="left">
                        <b><?php echo htmlspecialchars($lng["newpass"], ENT_QUOTES, 'UTF-8'); ?>:</b>
                    </td>
                    <td class="hforeground" align="left">
                        <input type="password" class="textinputs" name="new_password_1" maxlength="<?php echo (int) $session->config->password_length_max; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="hforeground" align="left">
                        <b><?php echo htmlspecialchars($lng["newpassagain"], ENT_QUOTES, 'UTF-8'); ?>:</b>
                    </td>
                    <td class="hforeground" align="left">
                        <input type="password" class="textinputs" name="new_password_2" maxlength="<?php echo (int) $session->config->password_length_max; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="hforeground" colspan="2" align="center">
                        <input type="submit" class="buttons" value="<?php echo htmlspecialchars($lng["savechanges"], ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
            </form>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
