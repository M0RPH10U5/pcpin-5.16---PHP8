<?php
    // Ensure TEMPLATEPATH is defined before requiring the template file
    if (defined('TEMPLATEPATH')) {
        require_once TEMPLATEPATH . "/all_header.tpl.php";
    } else {
        die("ERROR: TEMPLATEPATH is not defined.");
    }
?>
<!DOCTYPE html)
<html lang="<?php echo htmlspecialchars($ISO_639_LNG, ENT_QUOTES, 'UTF-8'); ?>">
<HEAD>
  <META http-equiv="Content-Language" content="<?php echo $ISO_639_LNG?>">
  <META http-equiv="Content-Type" content="text/html; charset=<?php echo htmlspecialchars($lng["charset"], ENT_QUOTES, 'UTF-8'); ?>">
  <?php echo isset($css) ? htmlspecialchars($css, ENT_QUOTES, 'UTF-8') : ''; ?>
</HEAD>
<BODY class="message" <?php echo isset($background) ? htmlspecialchars($background, ENT_QUOTES, 'UTF-8') : ''; ?>>
