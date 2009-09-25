<?php
/**
 * Forwarder to doku.php
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

define('PHPDW_MODULE_NAME', basename(dirname(__FILE__)));

require_once(PATH_PRE.'lib/dbman_lib.inc.php');
$module='DokuWiki';
if ($exporting=="yes") {
    include_once "doku.php";
} else {
    $tabs=array();
    $tabs['help']=array('href'=>'/addons/addon.php?addon=' . PHPDW_MODULE_NAME . '&id=wiki:syntax',
                        'text'=> __('Help'),
                        'position'=>'right','id'=>'help1','target'=>'_self','title'=>'Help');  
    echo "<div id='global-header'>";
    echo get_tabs_area($tabs);
    echo "</div>";

    $__olddir = getcwd();
    chdir( dirname(__FILE__) );
    echo "<div id='global-content'>\n";
    include_once 'doku.php';
    echo "</div>\n";
    chdir($__olddir);
    //header("Location: doku.php");
}
?>