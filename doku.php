<?php
/**
 * DokuWiki mainscript
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

//  xdebug_start_profiling();

// PHProjekt watch: only as logged in use accessible
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
      die();

if (isset($_SERVER['HTTP_X_DOKUWIKI_DO'])){
    $ACT = trim(strtolower($_SERVER['HTTP_X_DOKUWIKI_DO']));
} elseif (!empty($IDX)) {
    $ACT = 'index';
} elseif (isset($_REQUEST['do'])) {
    $ACT = $_REQUEST['do'];
} else {
    $ACT = 'show';
}

  /* This code is to limitate the dokuwiki plugin to only one group of the phprojekt. 
   * The groupid is defined in the dokuwiki.inc.php file.
  */
  if (defined('PHPDW_PERMIT_GROUP') && $user_group != PHPDW_PERMIT_GROUP) 
    die('Currently there is no wiki for your group installed - please consult your system administrator');
  
  if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/');
require_once(DOKU_INC.'inc/init.php');

//import variables
$QUERY = trim($_REQUEST['id']);
$ID    = getID();
$NS    = getNS($ID);
$REV   = $_REQUEST['rev'];
$IDX   = $_REQUEST['idx'];
$DATE  = $_REQUEST['date'];
$RANGE = $_REQUEST['range'];
$HIGH  = $_REQUEST['s'];
if(empty($HIGH)) $HIGH = getGoogleQuery();

if (isset($_POST['wikitext'])) {
    $TEXT  = cleanText($_POST['wikitext']);
}
$PRE   = cleanText($_POST['prefix']);
$SUF   = cleanText($_POST['suffix']);
$SUM   = $_REQUEST['summary'];

//sanitize revision
$REV = preg_replace('/[^0-9]/','',$REV);

//make infos about the selected page available
$INFO = pageinfo();

//export minimal infos to JS, plugins can add more
$JSINFO['id']        = $ID;
$JSINFO['namespace'] = (string) $INFO['namespace'];


// handle debugging
if($conf['allowdebug'] && $ACT == 'debug'){
    html_debug();
    exit;
}

//send 404 for missing pages if configured or ID has special meaning to bots
if(!$INFO['exists'] &&
  ($conf['send404'] || preg_match('/^(robots\.txt|sitemap\.xml(\.gz)?|favicon\.ico|crossdomain\.xml)$/',$ID)) &&
  ($ACT == 'show' || substr($ACT,0,7) == 'export_') ){
    header('HTTP/1.0 404 Not Found');
}

//prepare breadcrumbs (initialize a static var)
if ($conf['breadcrumbs']) breadcrumbs();

// check upstream
checkUpdateMessages();

$tmp = array(); // No event data
trigger_event('DOKUWIKI_STARTED',$tmp);

//close session
session_write_close();

//do the work
act_dispatch($ACT);

$tmp = array(); // No event data
trigger_event('DOKUWIKI_DONE', $tmp);

//  xdebug_dump_function_profile(1);
