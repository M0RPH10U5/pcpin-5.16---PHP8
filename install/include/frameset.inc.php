<?php
// Ensure the constant is defined before using it
if (defined('PCPIN_INSTALL_TEMPLATES')) {
    // Load template
    require_once(PCPIN_INSTALL_TEMPLATES . '/frameset.tpl.php');
} else {
    die('Error: Template path is not defined.');
}
?>