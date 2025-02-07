<?php
// Check rights
if (!($current_user->level & 128)) {
  die("HACK?");
}

// Declare class
$advertisement = new advertisement();

if ($add) {
  // Add new advertisement
  if ($submitted) {
    // Save advertisement
    // Validate form
    unset($error);
    // Check text
    if(empty($text)) {
      $error[] = $lng["textempty"];
    }
    // Check date
    if (!checkdate($start_month, $start_day, $start_year)) {
      $error[] = $lng["startdateinvalid"];
    }
    if (!checkdate($stop_month, $stop_day, $stop_year)) {
      $error[] = $lng["stopdateinvalid"];
    }
    // Check time
    if (!common::checkDigits($start_hour) || $start_hour > 23 || !common::checkDigits($start_minute) || $start_minute > 59 || !common::checkDigits($start_second) || $start_second > 59) {
        $error[] = $lng["starttimeinvalid"];
    }
    if (!common::checkDigits($stop_hour) || $stop_hour > 23 || !common::checkDigits($stop_minute) || $stop_minute > 59 || !common::checkDigits($stop_second) || $stop_second > 59){
        $error[]=$lng["stoptimeinvalid"];
    }
    if (!isset($error)) {
      // No errors
      // Generate MySQL DATETIME string
      $start = mktime($start_hour, $start_minute, $start_second, $start_month, $start_day, $start_year);
      $stop = mktime($stop_hour, $stop_minute, $stop_second, $stop_month, $stop_day, $stop_year);
      // Saving advertisement
      $advertisement->text = $text;
      $advertisement->start = $start;
      $advertisement->stop = $stop;
      $advertisement->period = $period;
      $advertisement->min_roomusers = $min_roomusers;
      $advertisement->show_private = $show_private;
      $advertisement->insertAdvertisement($session, $advertisement_id);
      $edit = 1;
      unset($advertisement_id);
    } else {
      unset($submitted);
    }
  }
  if (!$submitted) {
    // Show form
    // Set defaults
    $banner_url = empty($banner_url) ? "http://" : $banner_url;
    $banner_href = empty($banner_href) ? "http://" : $banner_href;

    $start_year = $start_year ?: date("Y");
    $start_month = $start_month ?: date("m");
    $start_day = $start_day ?: date("d");
    $start_hour = $start_hour ?: date("H");
    $start_minute = $start_minute ?: date("i");
    $start_second = $start_second ?: date("s");

    $stop_year = $stop_year ?: $start_year + 1;
    $stop_month = $stop_month ?: $start_month;
    $stop_day = $stop_day ?: $start_day;
    $stop_hour = $stop_hour ?: $start_hour;
    $stop_minute = $stop_minute ?: $start_minute;
    $stop_second = $stop_second ?: $start_second;

    $period = $period ?: 5;
    $min_roomusers = $min_roomusers ?: 2;
    $show_private = isset($show_private) ? $show_private : 0;
    ${'checked_show_private_' . $show_private} = 'Checked';

    // Load the template
    require(TEMPLATEPATH . "/admin_advertisement.tpl.php");
  }
}
if ($edit) {
  if ($delete && $advertisement_id) {
    // Delete advertisement
    $advertisement->deleteAdvertisement($session, $advertisement_id);
    unset($advertisement_id);
  }
  // Edit advertisement
  if (!$advertisement_id){
    // List advertisements
    $advertisements = $advertisement->listAdvertisements($session);
    $advertisements_count = count($advertisements);
    // Load teplate
    require(TEMPLATEPATH . "/admin_advertisementlist.tpl.php");
  } else {
    // Load advertisement
    $advertisement->readAdvertisement($session, $advertisement_id);
    $text = $advertisement->text;

    $start_year = date("Y", $advertisement->start);
    $start_month = date("m", $advertisement->start);
    $start_day = date("d", $advertisement->start);
    $start_hour = date("H", $advertisement->start);
    $start_minute = date("i", $advertisement->start);
    $start_second = date("s" ,$advertisement->start);

    $stop_year = date("Y", $advertisement->stop);
    $stop_month = date("m", $advertisement->stop);
    $stop_day = date("d", $advertisement->stop);
    $stop_hour = date("H", $advertisement->stop);
    $stop_minute = date("i", $advertisement->stop);
    $stop_second = date("s", $advertisement->stop);

    $period = $advertisement->period;
    $min_roomusers = $advertisement->min_roomusers;
    $show_private = $advertisement->show_private;

    ${'checked_show_private_' . $show_private} = 'Checked';
    // Load teplate
    require(TEMPLATEPATH . "/admin_advertisement.tpl.php");
  }
}
?>