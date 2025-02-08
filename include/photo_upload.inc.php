<?php
/* This is a photo upload page. */

/* Read userdata from database */
$user = new user();
$user->readUser($session, $profile_user_id);
/* Prepare nickname */
common::doHtmlEntities($user->login);
/* Current user */
$current_user = new user();
$current_user->readUser($session, $session->user_id);

if ($session->user_id == $profile_user_id || $current_user->level & 8) {
  if ($submitted) {
    $errortext = '';
    if ($photo['error']) {
      // No files uploaded
      $errortext = $lng["noimageselected"];
    } else {
      // Check file size.
      if ($photo['size'] > $session->config->max_photo_size) {
        // Uploaded file is too large
        $errortext = str_replace('{SIZE}', $session->config->max_photo_size, $lng['filesizetoobig']);
      } else {
        // Store file
        $tmp_name = md5($session_id . microtime() . rand(-time(), time()));
        $tmp_fullname = IMAGEPATH . '/userphotos/' . $tmp_name;
        move_uploaded_file($photo['tmp_name'], $tmp_fullname);
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
            $tmp_name .= $extension;
            break;
          }
        }
        if (!$type_ok) {
          // File is not an image or has non-supported format
          $errortext = $lng['notanimage'];
          unlink($tmp_fullname);
        } else {
          // Image is OK
          // Delete old image
          if ($user->photo != '' && $user->photo != 'nophoto.jpg') {
            unlink(IMAGEPATH . '/userphotos/' . $user->photo);
          }
          // Update user's profile
          $user->updateUser($session, $profile_user_id, 'photo = "' . $tmp_name . '"');
          // Show user's profile
          header("Location: main.php?include=$back&profile_user_id=$profile_user_id&session_id=$session_id");
          die();
        }
      }
    }
  }
  require TEMPLATEPATH . "/photo_upload.tpl.php";
}
?>
