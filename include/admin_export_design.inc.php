<?php
// Export design

// Check rights
if (!($current_user->level & 2)) {
  die("HACK?");
}

// Export file
$css_file = $cssclass->generateFormattedCSS($session->db);
header('Expires: Mon, 01 Jan 2000 15:57:24 GMT');
header('Content-Disposition: attachment; filename="pcpin_design.css"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Type: application/octet-stream');
header('Content-Length: ' . STRLEN($css_file));
echo $css_file;
exit();
?>