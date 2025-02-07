<?php

// Initialize progress states
$progress = [
    'progress_1' => 'disabled',
    'progress_2' => 'disabled',
    'progress_3' => 'disabled',
    'progress_4' => 'disabled',
    'progress_5' => 'disabled',
    'progress_6' => 'disabled'
];

// Determine progress based on $include value
for ($i = 1, $step = 1000; $i <= 6; $i++, $step += 100) {
    if ($include >= $step) {
        $progress["progress_$i"] = ($include == $step) ? 'active' : 'finished';
    }
}

// Load template with output buffering
ob_start();
$templatePath = PCPIN_INSTALL_TEMPLATES . '/progress.tpl.php';

if (file_exists($templatePath)) {
    require_once $templatePath;
} else {
    die('ERROR: Template file not found (' . htmlspecialchars($templatePath) . ').');
}

$_progress = db_get_clean();
?>