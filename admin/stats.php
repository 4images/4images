<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: stats.php                                            *
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
define('ROOT_PATH', './../');
require('admin_global.php');

if ($action == "") {
  $action = "resetstats";
}

show_admin_header();

if ($action == "updatestats") {
  $cat_id = intval($HTTP_POST_VARS['cat_id']);
  $cat_hits = trim($HTTP_POST_VARS['cat_hits']);
  $image_hits = trim($HTTP_POST_VARS['image_hits']);
  $image_downloads = trim($HTTP_POST_VARS['image_downloads']);
  $image_rating = trim($HTTP_POST_VARS['image_rating']);
  $image_votes = trim($HTTP_POST_VARS['image_votes']);

  $where_sql = ($cat_id) ? " WHERE cat_id = $cat_id" : "";

  echo "<b>".$lang['nav_categories_edit']."</b><br />";
  if ($cat_hits !== "") {
    echo $lang['field_hits']."...";
    flush();

    $sql = "UPDATE ".CATEGORIES_TABLE."
            SET cat_hits = $cat_hits
            $where_sql";
    $result = $site_db->query($sql);

    echo ($result) ? "<b>OK</b><br />" : "<b><span class=\"marktext\">ERROR</span></b><br />";
  }

  echo "<br /><br /><b>".$lang['nav_images_edit']."</b><br />";
  if ($image_hits !== "") {
    echo $lang['field_hits']."...";
    flush();

    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_hits = $image_hits
            $where_sql";
    $result = $site_db->query($sql);

    echo ($result) ? "<b>OK</b><br />" : "<b><span class=\"marktext\">ERROR</span></b><br />";
  }
  if ($image_downloads !== "") {
    echo $lang['field_downloads']."...";
    flush();

    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_downloads = $image_downloads
            $where_sql";
    $result = $site_db->query($sql);

    echo ($result) ? "<b>OK</b><br />" : "<b><span class=\"marktext\">ERROR</span></b><br />";
  }
  if ($image_rating !== "") {
    echo $lang['field_rating']."...";
    flush();
    $image_rating = sprintf("%.2f", intval($image_rating));

    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_rating = $image_rating
            $where_sql";
    $result = $site_db->query($sql);

    echo ($result) ? "<b>OK</b><br />" : "<b><span class=\"marktext\">ERROR</span></b><br />";
  }
  if ($image_votes !== "") {
    echo $lang['field_votes']."...";
    flush();

    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_votes = $image_votes
            $where_sql";
    $result = $site_db->query($sql);

    echo ($result) ? "<b>OK</b><br />" : "<b><span class=\"marktext\">ERROR</span></b><br />";
  }
}

if ($action == "resetstats") {
  if ($msg !== "") {
    printf("<b>%s</b>\n", $msg);
  }
  show_form_header("stats.php", "updatestats", "form", 1);
  show_table_header($lang['nav_general_stats'], 2);
  show_description_row($lang['reset_stats_desc'], 2);
  show_cat_select_row($lang['field_category'], 0, 2);
  show_table_separator($lang['nav_categories_edit'], 2);
  show_input_row($lang['field_hits'], "cat_hits", "", $textinput_size2);
  show_table_separator($lang['nav_images_edit'], 2);
  show_input_row($lang['field_hits'], "image_hits", "", $textinput_size2);
  show_input_row($lang['field_downloads'], "image_downloads", "", $textinput_size2);
  show_input_row($lang['field_rating']." (1-".MAX_RATING.")", "image_rating", "", $textinput_size2);
  show_input_row($lang['field_votes'], "image_votes", "", $textinput_size2);
  show_form_footer($lang['save_changes'], "", 2);
}

show_admin_footer();
?>