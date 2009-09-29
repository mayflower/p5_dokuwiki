<?php

define('PHPDW_PERMIT_GROUP', 2);

$__olddir = getcwd();
chdir(dirname(__FILE__));
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__)).'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php');
require_once(DOKU_INC.'inc/events.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/html.php');
require_once(DOKU_INC.'inc/auth.php');
require_once(DOKU_INC.'inc/actions.php');

ob_start();
tpl_metaheaders(true,false);
$__headers = ob_get_contents();
ob_end_clean();

$GLOBALS['he_add'][] = $__headers;

chdir($__olddir);