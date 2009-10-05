<?php
// Use the special auth class for phprojekt.
$conf['authtype']    = 'phprojekt';   
// Change the DOKU_BASE here if you don't install the phprojekt in the 
// webserver root!!
define('DOKU_BASE', '/addons/');
// This is the prefix for every url in the phprojekt context.
define('DOKU_SCRIPT', 'addon.php?addon=dokuwiki');
// Here is the name of the directory in the addons directory
$conf['modulename'] = 'dokuwiki';
// comma-seperated list of host address to restict access on the rss feed i
// without auth.
$conf['feed_allow_host'] = '127.0.0.1';


/*****************************************************************************
 * Database configuration for phprojekt database works at the moment only with
 * MYSQL. Please don't change the follow line.
 *****************************************************************************/
/* Options to configure database access. You need to set up this
 * options carefully, otherwise you won't be able to access you
 * database.
 */
$conf['auth']['phprojekt']['server']   = PHPR_DB_HOST;
$conf['auth']['phprojekt']['user']     = PHPR_DB_USER;
$conf['auth']['phprojekt']['password'] = PHPR_DB_PASS;
$conf['auth']['phprojekt']['database'] = PHPR_DB_NAME;
$conf['auth']['phprojekt']['prefix'] = PHPR_DB_PREFIX;
/* This option enables debug messages in the mysql module. It is
 * mostly usefull for system admins.
 */
$conf['auth']['phprojekt']['debug'] = 0;

/* Normally password encryption is done by DokuWiki (recommended) but for
 * some reasons it might be usefull to let the database do the encryption.
 * Set 'forwardClearPass' to '1' and the cleartext password is forwarded to
 * the database, otherwise the encrypted one.
 */
$conf['auth']['phprojekt']['forwardClearPass'] = 0;

/* Multiple table operations will be protected by locks. This array tolds
 * the module which tables to lock. If you use any aliases for table names
 * these array must also contain these aliases. Any unamed alias will cause
 * a warning during operation. See the example below.
 */
$conf['auth']['phprojekt']['TablesToLock']= array("users", "users AS u",
                "groups", "groups AS g", "usergroup", "usergroup AS ug");

/***********************************************************************/
/*       Basic SQL statements for user authentication (required)       */
/***********************************************************************/

/* This statement is used to grant or deny access to the wiki. The result
 * should be a table with exact one line containing at least the password
 * of the user. If the result table is empty or contains more than one
 * row, access will be denied.
 *
 * The module access the password as 'pass' so a alias might be necessary.
 *
 * Following patters will be replaced:
 *   %{user}	user name
 *   %{pass}	encrypted or clear text password (depends on 'encryptPass')
 *   %{dgroup}	default group name 
 */
$conf['auth']['phprojekt']['checkPass']   = "SELECT u.pw AS pass
                                         FROM %{db_prefix}users AS u,
                                              %[db_prefix}gruppe AS g
                                         WHERE u.gruppe = g.ID
                                           AND u.loginname = '%{user}'
                                           AND g.name != 'Guest'";

/* This statement should return a table with exact one row containing
 * information about one user. The field needed are:
 * 'pass'  containing the encrypted or clear text password
 * 'name'  the user's full name
 * 'mail'  the user's email address
 *
 * Keep in mind that Dokuwiki will access thise information through the
 * names listed above so aliasses might be neseccary.
 *
 * Following patters will be replaced:
 *   %{user}	user name
 */
$conf['auth']['phprojekt']['getUserInfo'] = "SELECT u.pw AS pass, 
                                                    CONCAT(u.vorname,' ',u.nachname) AS name, 
                                                    u.email AS mail
                                               FROM %{db_prefix}users AS u, %{db_prefix}gruppen AS g
                                              WHERE u.gruppe = g.ID 
                                                AND u.loginname='%{user}'
                                                AND g.name != 'Guest'";

/* This statement is used to get all groups a user is member of. The
 * result should be a table containing all groups the given user is
 * member of. The module access the group name as 'group' so a alias
 * might be nessecary.
 *
 * Following patters will be replaced:
 *   %{user}	user name
 */
$conf['auth']['phprojekt']['getGroups']   = "SELECT g.name as `group`
                                         FROM %{db_prefix}grup_user AS gu
                                         JOIN (%{db_prefix}users AS u, %{db_prefix}gruppen AS g)
                                           ON (u.ID = gu.user_ID AND g.ID = gu.grup_ID)
                                        WHERE u.loginname = '%{user}'";

/***********************************************************************/
/*      Additional minimum SQL statements to use the user manager      */
/***********************************************************************/

/* This statement should return a table containing all user login names
 * that meet certain filter criteria. The filter expressions will be added
 * case dependend by the module. At the end a sort expression will be added.
 * Important is that this list contains no double entries fo a user. Each
 * user name is only allowed once in the table.
 *
 * The login name will be accessed as 'user' to a alias might be neseccary.
 * No patterns will be replaced in this statement but following patters
 * will be replaced in the filter expressions:
 *   %{user}	in FilterLogin  user's login name
 *   %{name}	in FilterName   user's full name
 *   %{email}	in FilterEmail  user's email address
 *   %{group}	in FilterGroup  group name
 */
$conf['auth']['phprojekt']['getUsers']    = "SELECT DISTINCT u.loginname AS user
                                         FROM %{db_prefix}grup_user as gu 
                                         JOIN (%{db_prefix}users AS u, %{db_prefix}gruppen AS g)
                                           ON ( u.ID = gu.user_ID AND g.ID = gu.grup_ID)";
$conf['auth']['phprojekt']['FilterLogin'] = "u.loginname LIKE '%{user}'";
$conf['auth']['phprojekt']['FilterName']  = "CONCAT(u.vorname,' ',u.nachname) LIKE '%{name}'";
$conf['auth']['phprojekt']['FilterEmail'] = "u.email LIKE '%{email}'";
$conf['auth']['phprojekt']['FilterGroup'] = "g.name LIKE '%{group}'";
$conf['auth']['phprojekt']['SortOrder']   = "ORDER BY u.loginname";

/* This statement should return the database index of a given user name.
 * The module will access the index with the name 'id' so a alias might be
 * necessary.
 * following patters will be replaced:
 *   %{user}	user name 
 */
$conf['auth']['phprojekt']['getUserID']   = "SELECT ID AS id
                                         FROM %{db_prefix}users
                                         WHERE loginname='%{user}'";

/* This statement should return the database index of a given group name.
 * The module will access the index with the name 'id' so a alias might
 * be necessary.
 *
 * Following patters will be replaced:
 *   %{group}	group name 
 */
$conf['auth']['phprojekt']['getGroupID']  = "SELECT ID AS id
                                         FROM %{db_prefix}groups
                                         WHERE name='%{group}'";


