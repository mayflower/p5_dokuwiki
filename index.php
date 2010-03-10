<?php
/**
 * Forwarder to doku.php
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */
// Check if Phprojekt enviroment is loaded else process with normal dokuwiki call.
if (defined('PHPR_INSTALL_DIR')) {
    define('PHPDW_MODULE_NAME', basename(dirname(__FILE__)));
    if (PHPDW_MODULE_NAME != $conf['modulename']) {
        msg('<H4>The "modulename" configuration parameter in local.php is diffrent to the real directory name!</H4>',2);
    }
    
    require_once(PATH_PRE.'lib/dbman_lib.inc.php');
    $module='DokuWiki';
    // For exporting we don't need the tabs of phprojekt
    if ($exporting=="yes") {
        include_once "doku.php";
    } else {
        // Add the tabs header of phprojekt
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
        // Check if there are call a other view.
        // Only the detail view are using this at the moment.
        if (isset($_REQUEST['modul'])) {
            switch ($_REQUEST['modul']) {
                case 'detail': 
                    include_once "lib/exe/detail.php";
                    break;
                default:
                    include_once 'doku.php';
                    break;
            }
        } else {
            // begin with dokuwiki
            include_once 'doku.php';
        }
        echo "</div>\n";
        chdir($__olddir);
    }
} else {
    header("Location: doku.php");
}
