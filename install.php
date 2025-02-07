<?php
/*
 * ----------------------------------------------
 * This script installs PCPIN Chat on your server
 * ----------------------------------------------
 * Author: Kanstantin Reznichak <k.reznichak@pcpin.com>
 * Homepage: https://www.pcpin.com/
 * Support forum: https://community.pcpin.com/
 * ----------------------------------------------
 * Updated to PHP 8 By: M0RPH10U5
 */

 // Offset
 define('offset', './');

 // Load Configuration
 require_once './config/config.inc.php';

 // Preform global actions and load classes
 require_once './config/prepend.inc.php';

 // Installation Base DIR
 define('PCPIN_INSTALL_BASEDIR', '/install_main.php');

 // Load Main Install Script Part
 require_once PCPIN_INSTALL_BASEDIR . '/install_main.php';

 ?>