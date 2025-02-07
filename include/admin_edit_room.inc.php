<?PHP
// Check rights
if (!($current_user->level & 2048)) {
  die("HACK?");
}
if (empty($edit_room_id)) {
  die("HACK?");
}

$room = new room();
$room->readRoom($session, $edit_room_id);

$errortext = [];
$roomname = $roomname ?? '';
$bgimg = $bgimg ?? null;
$new_password = $new_password ?? '';
$room_type = $room_type ?? 1;

if (empty($save_room)) {
  $roomname = $room->name;
}

if (!empty($delete_image) && !empty($room->bgimg)) {
  if (unlink(IMAGEPATH . '/rooms/' . $room->bgimg)) {
    $room->updateRoom($session, $edit_room_id, 'bgimg=""');
    // Restart room
    systemMessage::insertMessage($session, $edit_room_id, 10);
    header('Location: main.php?session_id=' . $session_id . '&include=29&frame=main');
    exit();
  }
} elseif (!empty($save_room)) {
  $roomname = trim($roomname);
  if ($roomname == '') {
    $errortext[] = $lng['roomnameempty'];
  } else {
    $room->listRooms($session, 0, $roomname);
    if (!empty($room->roomlist) && $room->roomlist[0]['id'] != $edit_room_id) {
      $errortext[] = str_replace('{ROOM}', htmlentities($roomname), $lng['roomalreadyexists']);
    }
  }
  if (empty($errortext)) {
    // New image?
    $image_name = $room->bgimg;
    if (!empty($bgimg) && empty($bgimg['error']) && !empty($bgimg['size'])) {
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
            'jpg'  =>  '.jpg',
            'jpeg' =>  '.jpeg',
            'gif'  =>  '.gif',
            'ief'  =>  '.ief',
            'png'  =>  '.png',
            'tiff' =>  '.tiff',
            'bmp'  =>  '.bmp',
            'wbmp' =>  '.wbmp'
        ];
        $imgdata = function_exists('getimagesize') ? getimagesize($tmp_fullname) : null;
        
        foreach ($allowed_types as $chk_type => $extension) {
          if (!empty($imgdata)) {
            $type_ok = !empty($imgdata[0]) && !empty($imgdata[1]) && strpos(strtolower($imgdata['mime']), $chk_type) !== false;
          } else {
            $type_ok = strpos(strtolower($bgimg['type']), $chk_type) !== false;
          }
          if ($type_ok) {
            rename($tmp_fullname, $tmp_fullname . $extension);
            $image_name = $tmp_name . $extension;
            BREAK;
          }
        }
        if (!$type_ok) {
          // File is not an image or has non-supported format
          unlink($tmp_fullname);
        }
      }
    }
    if (!empty($protectwithpass)) {
        $new_password = !empty($new_password) ? password_hash($new_password, PASSWORD_BCRYPT) : $room->password;
      } else {
        $new_password = '';
      }
    }
    if (!empty($new_password)) {
      $room_type |= 2;
    }
    // Last main room cannot be declared as "userroom"
    if ($room_type != 0 && $room_type != 2){
      $room_type_new = $room_type - 1;
      $room->listRooms($session);
      if (!empty($room->roomlist)) {
        foreach ($room->roomlist as $room_record) {
          if (($room_record['type'] == 0 || $room_record['type'] == 2) && $room_record['id'] != $edit_room_id) {
            $room_type_new++;
            break;
          }
        }
      }
      $room_type = $room_type_new;
    }
    $room->updateRoom($session, $edit_room_id,
        'bgimg = "' . addslashes($image_name) . '",
        name = "' . addslashes($roomname) . '",
        password = "' . addslashes($new_password) . '",
        type = "' . addslashes($room_type) . '"');

    header('Location: main.php?session_id=' . $session_id . '&include=29&frame=main');
    // Restart room
    systemMessage::insertMessage($session, $edit_room_id, 10);
    exit();
  }
require TEMPLATEPATH . '/admin_edit_room.tpl.php';
?>