<?php
// Use the special auth class for phprojekt.
$conf['authtype']    = 'phprojekt';   //which authentication backend should be used

// Change the DOKU_BASE here if you don't install the phprojekt in the webserver root!!
define('DOKU_BASE', '/addons/');
// This is the prefix for every url in the phprojekt context.
define('DOKU_SCRIPT', 'addon.php?addon=dokuwiki');
// Here is the name of the directory in the addons directory
$conf['modulename'] = 'dokuwiki';
// comma-seperated list of host address to restict access on the rss feed without auth.
$conf['feed_allow_host'] = '127.0.0.1';

// Include the configuration for phprojekt database connection.
require_once("phprojekt.conf.php");

// Set the superuser and manager config on the end of the local.php
$conf['superuser']   = 'root';    //The admin can be user or @group
$conf['manager']     = 'root';    //The manager can be user or @group