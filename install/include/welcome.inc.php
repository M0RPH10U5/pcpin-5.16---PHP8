<?php
$next_include = 1000;

$_body_onload = "document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('startBtn');
    if (startBtn) {
        startBtn.style.visibility = 'visible';
    }
});";

// Load template safely
if (file_exists($templatePath)) {
    require_once $templatePath;
} else {
    die('Error: Template file not found (' . htmlspecialchars($templatePath) . ').');
}
?>