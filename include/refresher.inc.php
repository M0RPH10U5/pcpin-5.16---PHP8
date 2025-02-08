<?php
/* This is a refresher page */

/* Refresh time */
$refresh_time = 1000 * $session->config->main_refresh;

/* Loading 'refresher' frame */
require TEMPLATEPATH . "/refresher.tpl.php";
?>
