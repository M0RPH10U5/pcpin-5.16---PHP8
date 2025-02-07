<?php
// Check rights
if (!($current_user->level & 256)) {
  die("HACK?");
}

// Declare class
$smilie = new smilie();

if (($add || $edit) && $smilie_id && $submitted) {
  // Add new smilie
  if ($submitted) {
    // Save smilie
    // Validate form
    $error = array();
    // Check text equivalent
    $text = trim($text);
    if (empty($text)) {
      $error[] = $lng["textequivalentempty"];
    } elseif (strpos($text, "'") || strpos($text, "\\") || strpos($text, "\"") || strpos($text, "<") || strpos($text, ">")) {
      $error[] = $lng["invalidcharsintextequiv"];
    } else {
      if (!$edit) {
        $smilie = new smilie();
        $smilie->readSmilie($session, 0, $text);
        if ($smilie->id) {
          // Smilie with this text equivalent already exists
          $error[] = $lng["equivalentexists"];
        }
      }
    }
    // Check file
    if (!$edit) {
      if ($smiliefile['error']) {
        $error[] = $lng["uploaderror"];
      } elseif (!is_uploaded_file($smiliefile['tmp_name'])) {
        $error[] = $lng["uploaderror"];
      } else {
        // Store file
        $tmp_name = md5($session_id . microtime() . rand(-time(), time())) . '.gif';
        $tmp_fullname = IMAGEPATH . '/smilies/' . $tmp_name;
        move_uploaded_file($smiliefile['tmp_name'], $tmp_fullname);
        // Check file mime type
        $type_ok = false;
        if (function_exists('getimagesize')) {
          $imgdata = getimagesize($tmp_fullname);
          if (empty($imgdata) || empty($imgdata['mime'])) {
            $imgdata = null;
          }
        } else {
          $imgdata = null;
        }
        if (!empty($imgdata)) {
          $type_ok = !empty($imgdata[0]) && !empty($imgdata[1]) && false !== strpos(strtolower($imgdata['mime']), 'gif');
        } else {
          $type_ok = false !== strpos(strtolower($smiliefile['type']), 'gif');
        }
        if (!$type_ok) {
          $error[] = $lng["onlygifsallowed"];
          // Delete invalid file
          unlink($tmp_fullname);
        }
      }
    }
    if (empty($error)) {
      // No errors
      if ($smilie_id) {
        // Update smilie
        $stmt = $db->prepare("UPDATE smilies SET text = ? WHERE id = ?");
        $stmt->bind_param("si", $text, $smilie_id);
        $stmt->execute();
        unset($smilie_id);
      } else {
        // Saving new smilie
        $smilie->text = $text;
        $smilie->insertSmilie($session);
        $edit = 1;
        // Saving image file
        $smilie->readSmilie($session, 0, $text);
        $stmt = $db->prepare("UPDATE smilies SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $tmp_name, $smilie->id);
        $stmt->execute();
      }
    } else {
      if (!$edit) {
        unset($submitted);
      }
    }
  }
  if (!$submitted) {
    // Show form
    // Load template
    require TEMPLATEPATH . "/admin_smilie.tpl.php";
  }
}

if ($edit) {
  if ($delete && $smilie_id) {
    // Delete smilie
    $stmt = $db->prepare("DELETE FROM smilies WHERE id = ?");
    $stmt->bind_param("i", $smilie_id);
    $stmt->execute();
    unset($smilie_id);
  }
  // Edit smilie
  if (!$smilie_id) {
    // List smilies
    $smilies = $smilie->listSmilies($session);
    $smilies_count = count($smilies);
    // Load template
    require TEMPLATEPATH . "/admin_smilieslist.tpl.php";
  } else {
    // Load smilie
    $smilie->readSmilie($session, $smilie_id);
    $text = $smilie->text;
    $image = $smilie->image;
    // Load template
    require TEMPLATEPATH . "/admin_smilie.tpl.php";
  }
}
?>