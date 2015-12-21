<?php // PLUGIN_TITLE: Migrate Keywords
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: migrate_keywords.php                                 *
 *        Copyright: (C) 2002-2015 4homepages.de                          *
 *            Email: jan@4homepages.de                                    * 
 *              Web: http://www.4homepages.de                             * 
 *    Scriptversion: 1.7.13                                               *
 *                                                                        *
 *    Never released without support from: Nicky (http://www.nicky.net)   *
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
define('ROOT_PATH', "./../../");
require(ROOT_PATH.'admin/admin_global.php');
include(ROOT_PATH.'includes/search_utils.php');


if ($config['language_dir'] == 'deutsch') {
  $lang_migration_success = 'Keywords migrated successfully';
  $lang_migration_perform = 'Migrate keywords from 4images version 1.7.7 and below to new format';
  $lang_migration_title   = 'Migrate keywords';
} else {
  $lang_migration_success = 'Keywords migrated successfully';
  $lang_migration_perform = 'Migrate keywords from 4images version 1.7.7 and below to new format';
  $lang_migration_title   = 'Migrate keywords';
}


show_admin_header();
$m = "";

if ($action == "migratekeywords") {
    @set_time_limit(0);

    $sql = "SELECT image_id, image_keywords, image_name, image_description FROM ".IMAGES_TABLE." WHERE image_keywords LIKE '% %';";
    $image_result = $site_db->query($sql);
    while ($image_row = $site_db->fetch_array($image_result)) {

        $image_keywords = $image_row['image_keywords'];
        $image_id       = intval($image_row['image_id']);
        
        $image_keywords = preg_replace("/[\n\r\s]/is", ",", $image_keywords);
        $image_keywords_arr = explode(',', $image_keywords);
        array_walk($image_keywords_arr, 'trim_value');
        $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

        $sql = "UPDATE ".IMAGES_TABLE." SET image_keywords = '" . $image_keywords . "' WHERE image_id = " . $image_id . " LIMIT 1;";
        $result = $site_db->query($sql);
        if ( $result ) {
            remove_searchwords($image_id);
            $search_words = array(
                "image_name" => $image_row['image_name'],
                "image_description" => $image_row['image_description'],
                "image_keywords" => $image_row['image_keywords'],
            );
            add_searchwords($image_id, $search_words);            
            $msg .= "Image ID: " . $image_row['image_id'] . "; Keywords: " . $image_keywords . "<br />";
        }
    }

    $msg .= "<br />" . $lang_migration_success;
}

if ($msg != "") {
    printf("<b>%s</b><br /><br />\n", $msg);
}

show_form_header($HTTP_SERVER_VARS['PHP_SELF'], "migratekeywords");
show_table_header($lang_migration_title);
show_description_row($lang_migration_perform);
show_form_footer($lang['submit'], "");

show_admin_footer();
?>