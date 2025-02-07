<?php
// Load template
$templatePath = PCPIN_INSTALL_TEMPLATES . '/main.tpl.php';

if (file_exists($templatePath)) {
    require_once $templatePath;
} else {
    die('ERROR: Template file not found (' . htmlspecialchars($templatePath) . ').');
}
?>