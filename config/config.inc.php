<?php
/* WARNING: DON'T EDIT THIS FILE BEFORE YOU ARE KNOW WHAT YOU ARE DOING! */
/* This configuration file bs pathes */

/* Cookie lifetime, seconds */
define('COOKIE_LIFETIME', 2592000); // 2592000 seconds = 30 days

/* Turn debugging on. CHANGE THIS SETTING ONLY IF YOU KNOW WHAT ARE YOU DOING! */
define('DEBUG', false);

/* PHP warnings and notices */
error_reporting(DEBUG ? E_ALL : 0);

/* Path to directory that contains classes */
define('CLASSPATH', OFFSET . 'class');

/* Path to directory that contains includes */
define('INCLUDEPATH', OFFSET . 'include');

/* Path to directory that contains templates */
define('TEMPLATEPATH', OFFSET . 'template');

/* Path to directory that contains JavaScript scripts */
define('SCRIPTPATH', OFFSET . 'script');

/* Path to directory that contains language files */
define('LANGUAGEPATH', OFFSET . 'language');

/* Path to directory that contains images */
define('IMAGEPATH', OFFSET . 'images');

/* Path to directory that contains sounds */
define('SOUNDPATH', OFFSET . 'sounds');

/* Path to directory that contains log files */
define('LOGSPATH', OFFSET . 'logs');

?>