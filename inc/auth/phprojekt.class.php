<?php
/**
 * PunBB auth backend
 *
 * Uses external Trust mechanism to check against PunBB's
 * user cookie. PunBB's PUN_ROOT must be defined correctly.
 *
 * @author    Andreas Gohr <andi@splitbrain.org>
 */

require_once DOKU_INC.'inc/auth/mysql.class.php';

class auth_phprojekt extends auth_mysql {

  /**
   * Constructor.
   *
   * Sets additional capabilities and config strings
   */
  function auth_phprojekt(){
    global $conf;
    $this->cando['external'] = true;
    $this->cando['logoff']   = true;
    
    $db_host = PHPR_DB_HOST;
    $db_name = PHPR_DB_NAME;
    $db_prefix = PHPR_DB_PREFIX;
    $db_username = PHPR_DB_USER;
    $db_password = PHPR_DB_PASS;
    
    // now set up the mysql config strings
    $conf['auth']['mysql']['server']   = $db_host;
    $conf['auth']['mysql']['user']     = $db_username;
    $conf['auth']['mysql']['password'] = $db_password;
    $conf['auth']['mysql']['database'] = $db_name;

    $conf['auth']['mysql']['checkPass']   = "SELECT u.pw AS pass
                                               FROM ${db_prefix}users AS u, ${db_prefix}gruppen AS g
                                              WHERE u.gruppe = g.ID
                                                AND u.loginname = '%{user}'
                                                AND g.name   != 'Guest'";
    $conf['auth']['mysql']['getUserInfo'] = "SELECT password AS pass, concat(vorname,' ',nachname) AS name, email AS mail,
                                                    u.ID as id, g.name as `group`
                                               FROM ${db_prefix}users AS u, ${db_prefix}gruppen AS g
                                              WHERE u.gruppe = g.ID
                                                AND u.loginname = '%{user}'";
    $conf['auth']['mysql']['getGroups']   = "SELECT g.name as `group`
                                               FROM ${db_prefix}users AS u, ${db_prefix}gruppen AS g
                                              WHERE u.gruppe = g.ID
                                                AND u.loginname = '%{user}'";
    $conf['auth']['mysql']['getUsers']    = "SELECT DISTINCT u.loginname AS user
                                               FROM ${db_prefix}users AS u, ${db_prefix}gruppen AS g
                                              WHERE u.gruppe = g.ID";
    $conf['auth']['mysql']['FilterLogin'] = "u.loginname LIKE '%{user}'";
    $conf['auth']['mysql']['FilterName']  = "u.vorname LIKE '%{name}'";
    $conf['auth']['mysql']['FilterEmail'] = "u.email    LIKE '%{email}'";
    $conf['auth']['mysql']['FilterGroup'] = "g.name    LIKE '%{group}'";
    $conf['auth']['mysql']['SortOrder']   = "ORDER BY u.loginname";
    $conf['auth']['mysql']['addUser']     = "INSERT INTO ${db_prefix}users
                                                    (username, password, email, realname)
                                             VALUES ('%{user}', '%{pass}', '%{email}', '%{name}')";
    $conf['auth']['mysql']['addGroup']    = "INSERT INTO ${db_prefix}groups (g_title) VALUES ('%{group}')";
    $conf['auth']['mysql']['addUserGroup']= "UPDATE ${db_prefix}users
                                                SET group_id=%{gid}
                                              WHERE id='%{uid}'";
    $conf['auth']['mysql']['delGroup']    = "DELETE FROM ${db_prefix}groups WHERE g_id='%{gid}'";
    $conf['auth']['mysql']['getUserID']   = "SELECT id FROM ${db_prefix}users WHERE loginname='%{user}'";
    $conf['auth']['mysql']['updateUser']  = "UPDATE ${db_prefix}users SET";
    $conf['auth']['mysql']['UpdateLogin'] = "username='%{user}'";
    $conf['auth']['mysql']['UpdatePass']  = "password='%{pass}'";
    $conf['auth']['mysql']['UpdateEmail'] = "email='%{email}'";
    $conf['auth']['mysql']['UpdateName']  = "realname='%{name}'";
    $conf['auth']['mysql']['UpdateTarget']= "WHERE id=%{uid}";
    $conf['auth']['mysql']['delUserGroup']= "UPDATE ${db_prefix}users SET g_id=4 WHERE id=%{uid}";
    $conf['auth']['mysql']['getGroupID']  = "SELECT g_id AS id FROM ${db_prefix}groups WHERE g_title='%{group}'";

    $conf['auth']['mysql']['TablesToLock']= array("${db_prefix}users", "${db_prefix}users AS u",
                                                  "${db_prefix}groups", "${db_prefix}groups AS g");

    $conf['auth']['mysql']['debug'] = 1;
    // call mysql constructor
    $this->auth_mysql();
  }

  /**
   * Just checks against the $pun_user variable
   */
  function trustExternal($user,$pass,$sticky=false){
    global $USERINFO;
    global $conf;
    global $lang;
    $sticky ? $sticky = true : $sticky = false; //sanity check
    
//    print "trustExternal called!";
// var_dump($_SESSION);    

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
      // okay we're logged in - set the globals
      $USERINFO['pass'] = $_SESSION['user_pw'];
      $USERINFO['name'] = utf8_encode( sprintf("%s %s", $_SESSION['user_firstname'], $_SESSION['user_name']) );
      $USERINFO['mail'] = $_SESSION['user_email'];
      $USERINFO['grps'] = array($pun_user['g_title']);

      $_SERVER['REMOTE_USER'] = $_SESSION['loginstring'];
      $_SESSION[DOKU_COOKIE]['auth']['user'] = $_SESSION['loginstring'];
      $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
      return true;
    }

    // to be sure
    auth_logoff();
    return false;    
  }

  /**
   * remove punbb cookie on logout
   */
  function logOff(){
  }
}
//Setup VIM: ex: et ts=2 enc=utf-8 :
