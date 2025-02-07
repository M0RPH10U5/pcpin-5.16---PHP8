<?php
$next_include = 1200;

// Check modes
clearstatcache();
$modes = [
    'logs' => [
        'path'    =>  LOGSPATH,
        'name'    =>  'Logs directory',
        'error'   =>  'Chat logs will not be saved.',
        'solution'=>  'Change mode of the directory <b>' . LOGSPATH . '</b> to 777',
        'status'  =>  is_writable(LOGSPATH)
    ],
    'rooms' => [
        'path'    =>  IMAGEPATH . '/rooms',
        'name'    =>  'Room images directory',
        'error'   =>  'Room images upload will be impossible.',
        'solution'=>  'Change mode of the directory <b>' . IMAGEPATH . '/rooms</b> to 777',
        'status'  =>  is_writable(IMAGEPATH . '/rooms')
    ],
    'smilies' => [
        'path'    =>  IMAGEPATH . '/smilies',
        'name'    =>  'Smilies directory',
        'error'   =>  'Smilies upload will be impossible.',
        'solution'=>  'Change mode of the directory <b>' . IMAGEPATH . '/smilies</b> to 777',
        'status'  =>  is_writable(IMAGEPATH . '/smilies')
    ],
    'userphotos' => [
        'path'    =>  IMAGEPATH . '/userphotos',
        'name'    =>  'Userphotos directory',
        'error'   =>  'Userphotos upload will be impossible.',
        'solution'=>  'Change mode of the directory <b>' . IMAGEPATH . '/userphotos</b> to 777',
        'status'  =>  is_writable(IMAGEPATH . '/userphotos')
    ],
];

// Any problems
$status = true;
foreach ($modes as $mode) {
  if (!$mode['status']) {
    // Some errors occured
    $status = false;
    break;
  }
}

// Load template
require_once(PCPIN_INSTALL_TEMPLATES . '/filesystem.tpl.php');
?>