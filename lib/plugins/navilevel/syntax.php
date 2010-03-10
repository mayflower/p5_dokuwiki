<?php
/**
 *  Add smart navigation capability to dokuwiki
 *  This is the version #2 of this plugin. Don't use the 1st version as it creates invalid HTML.
 *
 *  by Thanos Massias <tm@navarino.gr>
 *  under the terms of the GNU GPL v2.
 *
 *  Roland Hellebart's tree plugin ( his http://wiki.splitbrain.org/plugin:tree )
 *  was used as a base for this.
 *
 *  @license    GNU_GPL_v2
 *  @author     Thanos Massias <tm@navarino.gr>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 *  All DokuWiki plugins to extend the parser/rendering mechanism
 *  need to inherit from this class
 */
 class syntax_plugin_navilevel extends DokuWiki_Syntax_Plugin {
 
  
    function getInfo(){
        return array(
            'author' => 'Thanos Massias',
            'email'  => 'tm@navarino.gr',
            'date'   => '2006-05-30',
            'name'   => 'navilevel Plugin',
            'desc'   => 'Add smart Navigation Capability',
            'url'    => 'http://dolphin.navarino.gr/navilevel',
        );
    }
  
    /**
     *  What kind of syntax are we?
     */
    function getType(){
        return 'protected';
    }
 
    /**
     *  What kind of syntax do we allow (optional)
     */
    function getAllowedTypes() {
        return array('protected');
    }
 
    /**
     *  What about paragraphs? (optional)
     */
    function getPType(){
        return 'block';
    }
 
    /**
     *  Where to sort in?
     */
    function getSort(){
        return 203;
    }
 
    /**
     *  Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('<navilevel.*?>(?=.*?</navilevel>)',$mode,'plugin_navilevel');
    }
    
    function postConnect() {
        $this->Lexer->addExitPattern('</navilevel>','plugin_navilevel');
    }
 
    /**
     *  Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER :
            break;
          case DOKU_LEXER_MATCHED :
            break;
          case DOKU_LEXER_UNMATCHED :
            break;
          case DOKU_LEXER_EXIT :
            break;
          case DOKU_LEXER_SPECIAL :
            break;
        }
        return array($match, $state);
    }
 
    /**
     *  Create output
     */
    function render($mode, &$renderer, $data) {
        $lowlevel = 2;
        global $ID;
        global $ullevellast; 
        $linklineID = ':'.$ID;
        //  In case you want the plugin to work correctly within a sidebar add the 
        //  following two lines in '/dokuwiki_home/lib/tpl/template_name/main.php':
        //  ---------------------
        //  global $PPID;
        //  $PPID = $ID;
        //  ---------------------
        //  just after "require_once(dirname(__FILE__).'/tpl_functions.php');"
        global $PPID;
        if ('X'.$PPID != 'X'){
            if ($ID != $PPID){
                $linklineID = ':'.$PPID;
            }
        }
        $partsID = explode(':', $linklineID);
        $countpartsID = count($partsID);
 
        if($mode == 'xhtml'){
            switch ($data[1]) {
                case DOKU_LEXER_ENTER :
                  $renderer->doc .= "\n";
                  break;
    
                case DOKU_LEXER_MATCHED :
                  break;
      
                case DOKU_LEXER_UNMATCHED :
                    $content = $data[0];
 
                    //  clean up the input data
                    //  clear any trailing or leading empty lines from the data set
                    $content = preg_replace("/[\r\n]*$/","",$content);
                    $content = preg_replace("/^\s*[\r\n]*/","",$content);
     
                    //  Not sure if PHP handles the DOS \r\n or Mac \r, so being paranoid
                    //  and converting them if they exist to \n
                    $content = preg_replace("/\r\n/","\n",$content);
                    $content = preg_replace("/\r/","\n",$content);
                    $result = $this->tree_explode_node($content);
 
                    $ullevellast = 0;
    
                    foreach ($result as $input){
                        $arrinput = explode('|', $input);
                        $linkline = $arrinput[0];
                        $linkline = trim($linkline);
                        $linktext = $arrinput[1];
                        $linktext = trim($linktext);
                        if (strlen($linkline) > 0) {
                            $linkline = cleanID($linkline);
                            if (substr($linkline,0,1) != ':'){
                                $linkline = ':'.$linkline;
                            }
 
                            $parts = explode(':', $linkline);
                            $countparts = count($parts);
            
                            $listlink = 0;
                            //  low level links
                            if ($countparts < (2 + $lowlevel)){
                                $listlink = 1;
                            }else{
                                //  children, yes - grandchildren and beyond, no
                                if ($countparts <= $countpartsID + 1){
                                    $listlink = 1;
                                    $tmppathID = '';
                                    $tmppath   = '';
                                    $i = 0;
                                    foreach ($parts as $part){
                                        if (($i > 0) && ($i < $countparts - 2)){
                                            if ('X'.$partsID[$i] != 'X') {
                                                $tmppathID .= ':'.$partsID[$i];
                                                $tmppath   .= ':'.$parts[$i];
                                                if ($tmppathID !== $tmppath){
                                                    $listlink = 0;
                                                }
                                            }
                                        }
                                        $i++;
                                    }
                                }
                            }
        
                            if ($listlink){
                                $ullevel = $countparts - 1;
                                if ($ullevel == $ullevellast){
                                    $renderer->doc .= "\n</li><!-- 188 -->";
                                }
                                if ($ullevel > $ullevellast){
                                    $renderer->doc .= "\n<ul>";
                                }
                                if ($ullevel < $ullevellast){
                                    for ($j = 0; $j < ($ullevellast - $ullevel); $j++){
                                        $renderer->doc .= "\n</li><!-- 196 -->\n</ul><!-- 196 -->";
                                    }
                                    $renderer->doc .= "\n</li><!-- 197 -->";
                                }
                                $ullevellast = $ullevel;
                                $renderer->doc .= "\n".'<li class="level'.$ullevel.'"><div class="li"> '; 
                                $renderer->doc .= $renderer->internallink($linkline);
                                if (strlen($linktext) > 0) {
                                    $renderer->doc .= ' '.$linktext;
                                }
                                $renderer->doc .= "</div><!-- 206 -->";
                            }else{
                                $renderer->doc .= "";
                            }
                        }
                    } 
                    break;
  
                case DOKU_LEXER_EXIT :
                    $ullevel = 1;
                    if ($ullevel == $ullevellast){
                        $renderer->doc .= "\n</li><!-- 216 -->";
                    }
                    if ($ullevel > $ullevellast){
                        $renderer->doc .= "\n<ul>";
                    }
                    if ($ullevel < $ullevellast){
                        for ($j = 0; $j < ($ullevellast - $ullevel); $j++) {
                            $renderer->doc .= "\n</li><!-- 223 -->\n</ul><!-- 223 -->";
                        }
                        // #$renderer->doc .= "\n</li><!-- 225 -->"; // invalid XHTML, 2006-06-26, hella.breitkopf@guug.de
                    }
                    // #$renderer->doc .= "\n</ul><!-- 228 -->"; // invalid XHTML, 2006-06-26, reported: http://wiki.splitbrain.org/plugin:navilevel#to_much_li_and_ul
                    break;
 
                case DOKU_LEXER_SPECIAL :
                    break;
            }
            return false;
        }
        
        // unsupported $mode
        return false;
    } 
 
    function tree_explode_node(&$str) {
        $len = strlen($str) + 1;
        $inside = false;
        $word = '';
        for ($i = 0; $i < $len; ++$i) {
            $next = $i+1;
            if ($str[$i] == "\n") {
                $out[] = $word;
                $word = '';
            } elseif ($next == $len) {
                $out[] = $word;
                $word = '';
            } else {
                $word .= $str[$i];
            }
        }
        $str = substr($str, $next);
        $out[] = $word;
        return $out;
    }
}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
