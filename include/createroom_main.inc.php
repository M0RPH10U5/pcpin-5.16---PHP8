<?php
/* Create new room page */

// Set defaults
if (!isset($protect)) {
  $protect = 0;
}

// Check rights
if ($admin_manage_rooms) {
  if (!($current_user->level & 2048)) {
    unset($admin_manage_rooms);
  }
}
if (!($session->config->allow_userrooms == 2 || ($session->config->allow_userrooms == 1 && !$current_user->guest)) && !$admin_manage_rooms) {
  die();
}

if ($createroom) {
  // Validate form
  $roomname = trim($roomname);
  if (strlen($roomname) > 0) {
    // Room name is not empty
    // Looking for rooms with the same name
    $room = new room();
    $room->listRooms($session);
    $roomlist = $room->roomlist;
    $roomlist_count = count($roomlist);
    $room_found = false;
    $roomname_lo = strtolower($roomname);
    for ($i = 0; $i < $roomlist_count && !$room_found; $i++) {
      if ($roomname_lo == strtolower($roomlist[$i]['name'])) {
        // Room with the same name already exists
        $room_found = true;
      }
    }
    if (!$room_found) {
      // Create a room
      $room_type = $protect ? 3 : 1;
      // Image?
      $image_name = '';
      if (empty($bgimg['error']) && !empty($bgimg['size'])) {
        // Check file size.
        if ($bgimg['size'] <= $session->config->max_roomimage_size) {
          // File size is OK
          // Store file
          $tmp_name = md5($session_id . microtime() . rand(-time(), time()));
          $tmp_fullname = IMAGEPATH . '/rooms/' . $tmp_name;
          move_uploaded_file($bgimg['tmp_name'], $tmp_fullname);
          // Check file mime type
          $type_ok = false;
          $allowed_types = [
            'jpg' => '.jpg',
            'jpeg' => '.jpeg',
            'gif' => '.gif',
            'ief' => '.ief',
            'png' => '.png',
            'tiff' => '.tiff',
            'bmp' => '.bmp',
            'wbmp' => '.wbmp'
        ];
          if (function_exists('getimagesize')) {
            $imgdata = getimagesize($tmp_fullname);
            if (empty($imgdata) || empty($imgdata['mime'])) {
              $imgdata = null;
            }
          } else {
            $imgdata = null;
          }
          foreach ($allowed_types as $chk_type => $extension) {
            if (!empty($imgdata)) {
              $type_ok = !empty($imgdata[0]) && !empty($imgdata[1]) && false !== strpos(strtolower($imgdata['mime']), $chk_type);
            } else {
              $type_ok = false !== strpos(strtolower($photo['type']), $chk_type);
            }
            if ($type_ok) {
              rename($tmp_fullname, $tmp_fullname . $extension);
              $image_name = $tmp_name . $extension;
              break;
            }
          }
          if (!$type_ok) {
            // File is not an image or has non-supported format
            unlink($tmp_fullname);
          }
        }
      }
      $room->createRoom($session, $roomname, $room_type - $admin_manage_rooms, md5($roompassword), $image_name);
      $room->listRooms($session, 0, $roomname, $room_type - $admin_manage_rooms);
      $new_room_id = $room->roomlist[0]['id'];
      // Updating all roomlists
      systemmessage::insertMessage($session, $new_room_id, 5);
      // Redirect user into the new room
?>
<html><body onload="document.entermyroom.submit();">
<?php
      if (!$admin_manage_rooms) {
?>
<form name="entermyroom" action="main.php" method="post" target="_parent">
  <input type="hidden" name="include" value="4">
  <input type="hidden" name="room_password" value="<?php echo htmlspecialchars($roompassword, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($new_room_id, ENT_QUOTES, 'UTF-8'); ?>">
<?php
      } else {
?>
<form name="entermyroom" action="main.php" method="post">
  <input type="hidden" name="include" value="29">
  <input type="hidden" name="frame" value="main">
<?php
      }
?>
  <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>">
</form>
</body></html>
<?php
      die();
    } else {
      // Room with the same name already exists
      $errortext = str_replace("{ROOM}", $roomname, $lng["roomalreadyexists"]);
    }
  } else {
    // Room name is empty
    $errortext = $lng["roomnameempty"];
  }
}

// Display form
$protect_0_checked = ($protect == 0) ? 'checked="checked"' : '';
$protect_1_checked = ($protect == 1) ? 'checked="checked"' : '';

/* Load page template */
require TEMPLATEPATH . "/createroom_main.tpl.php";
?>
