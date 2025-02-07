<?php
/* Chat design */

// Check rights
if (!($current_user->level & 2)) {
  die("Hack?");
}

$cssurl = new cssURL($session->db);

if (!empty($submitted)) {
  // Save changes
  if (empty($css_source) && !empty($css_url)) {
    // Use external CSS
    // Add 'http://' if needed
    $css_url = trim($css_url);

    if (!filter_var($css_url, FILTER_VALIDATE_URL) && substr($css_url, 0, 2) !== './' && substr($css_url, 0, 1) !== '/') {
      $css_url = 'http://' . $css_url;
    }

    $cssurl->updateCSSURL($session->db, $css_url);
  } else {
    // Use local settings
    $cssurl->updateCSSURL($session->db, '');
    // Extract variables
    $vars = get_defined_vars();

    foreach ($vars as $key => $val) {
      if (strpos($key, "properties_") === 0) {
        $tmp = explode("_", str_replace("properties_", "", $key));

        if (strpos($val, "#") === 0){
          $val=SUBSTR($val,1);
        }

        fk_cssvalue::changeCSSValue($session->db, $tmp[0], $tmp[1], $val);
      }
    }
  }

  // Restarting all users
  systemMessage::insertMessage($session, "", 10);
}

$css_url = $cssurl->cssurl;

$css_source_0_checked = !empty($css_url) ? 'checked="checked"' : '';
$css_source_1_checked = empty($css_url) ? 'checked="checked"' : '';

// Load design
$cssclass = new cssclass();
$cssclass->loadStructure($session->db);
$css_structure = $cssclass->cssList;

REQUIRE TEMPLATEPATH . "/admin_design.tpl.php";
?>
