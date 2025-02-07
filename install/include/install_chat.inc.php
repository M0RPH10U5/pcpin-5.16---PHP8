<?php
$next_include = 1600;

$_body_onload = isset($_body_onload) ? $_body_onload . ' parent.ctl.showButton(); ' : 'parent.ctl.showButton();';

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/install_chat.tpl.php');
?>