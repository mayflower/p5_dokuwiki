<?php

$conf['lang']        = 'de';              //your language
$conf['youarehere']  = 1;                 //show "You are here" navigation? 0|1
$conf['allowdebug']  = 0;                 //allow debug output, enable if needed 0|1
$conf['htmlok']      = 1;                 //may raw HTML be embedded? This may break layout and XHTML validity 0|1
$conf['useheading']  = 1;                 //use the first heading in a page as its name
$conf['camelcase']   = 0;                 //Use CamelCase for linking? (I don't like it) 0|1
$conf['template']    = 'sidebar';         //see tpl directory
$conf['useacl']      = 1;                //Use Access Control Lists to restrict access?
$conf['autopasswd']  = 0;                //autogenerate passwords and email them to user
$conf['authtype']    = 'phprojekt';          //which authentication backend should be used
$conf['passcrypt']   = 'crypt';           //Used crypt method (smd5,md5,sha1,ssha,crypt,mysql,my411)
$conf['defaultgroup']= 'default';           //Default groups new Users are added to
$conf['openregister'] = '';

$conf['superuser']   = 'root';    //The admin can be user or @group
$conf['manager']     = '@Admin,@Geschäftsführung';    //The manager can be user or @group
require_once("phprojekt.conf.php");

// Change the DOKU_BASE here if you don't install the phprojekt in the webserver root!!
define('DOKU_BASE', '/addons/');
define('DOKU_SCRIPT', 'addon.php?addon=dokuwiki');
