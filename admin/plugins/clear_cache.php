<?php // PLUGIN_TITLE: Clear Cache
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: clear_cache.php                                      *
 *        Copyright: (C) 2002-2023 4homepages.de                          *
 *            Email: 4images@4homepages.de                                * 
 *              Web: http://www.4homepages.de                             * 
 *    Scriptversion: 1.10                                                 *
 *                                                                        *
 **************************************************************************
 *                                                                        *
 *    Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-       *
 *    bedingungen (Lizenz.txt) für weitere Informationen.                 *
 *    ---------------------------------------------------------------     *
 *    This script is NOT freeware! Please read the Copyright Notice       *
 *    (Licence.txt) for further information.                              *
 *                                                                        *
 *************************************************************************/

$nozip = 1;
define('IN_CP', 1);
$root_path = (false === stristr($_SERVER['PHP_SELF'], "/plugins/")) ? "./../" : "./../../";
define('ROOT_PATH', $root_path);
require(ROOT_PATH.'admin/admin_global.php');

if ($config['language_dir'] == 'deutsch') {
  $lang_clear_cache   = 'Cache leeren';
  $lang_clear_confirm = 'Wollen Sie das Cache-Verzeichnis leeren (%s)?';
  $lang_clear_success = 'Cache-Verzeichnis geleert';
} else {
  $lang_clear_cache   = 'Clear Cache';
  $lang_clear_confirm = 'Do you want to clear the cache directory (%s)?';
  $lang_clear_success = 'Cache directory cleared';
}

show_admin_header();

if ($action == "clearcache") {
    @set_time_limit(0);
    clear_cache();
    $msg = $lang_clear_success;
}

if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
}

show_form_header($HTTP_SERVER_VARS['PHP_SELF'], "clearcache");
show_table_header($lang_clear_cache);
show_description_row(sprintf($lang_clear_confirm, realpath($cache_path)));
show_form_footer($lang['submit'], "");


show_admin_footer();
?>