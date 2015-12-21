<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: thumbnailer.php                                      *
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
require(ROOT_PATH.'includes/image_utils.php');

if ($action == "") {
  $action = "checkthumbnails";
}

show_admin_header();

$convert_options = init_convert_options();
if ($convert_options['convert_error']) {
  echo $convert_options['convert_error'];
  show_admin_footer();
  exit;
}

if ($action == "createthumbnails") {
  $dimension = (isset($HTTP_POST_VARS['dimension']) && intval($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : 100;
  $resize_type = (isset($HTTP_POST_VARS['resize_type']) && intval($HTTP_POST_VARS['resize_type'])) ? intval($HTTP_POST_VARS['resize_type']) : 1;
  $quality = (isset($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) <= 100) ? intval($HTTP_POST_VARS['quality']) : 100;
  $image_list = (isset($HTTP_POST_VARS['image_list'])) ? $HTTP_POST_VARS['image_list'] : "";

  if (!empty($image_list)) {
    $image_id_sql = "";
    foreach ($image_list as $key => $val) {
      if ($val == 1) {
        $image_id_sql .= (($image_id_sql != "") ? ", " : "" ).$key;
      }
    }

    $sql = "SELECT image_id, cat_id, image_name, image_media_file, image_thumb_file
            FROM ".IMAGES_TABLE."
            WHERE image_id IN($image_id_sql)";
    $result = $site_db->query($sql);

    $image_cache = array();
    while ($row = $site_db->fetch_array($result)) {
      $image_cache[$row['image_id']] = $row;
    }

    foreach ($image_list as $key => $val) {
      if ($val == 1) {
        echo "<p>".$lang['creating_thumbnail'].format_text($image_cache[$key]['image_name'], 2)." (".$image_cache[$key]['image_media_file'].") ....&nbsp;&nbsp;\n";
        flush();
        @set_time_limit(90);

        if (is_remote($image_cache[$key]['image_media_file'])) {
          $src = $image_cache[$key]['image_media_file'];
          $dest = create_unique_filename(THUMB_PATH."/".$image_cache[$key]['cat_id'], filterFileName($image_cache[$key]['image_media_file']));
        } else {
          $src = MEDIA_PATH."/".$image_cache[$key]['cat_id']."/".$image_cache[$key]['image_media_file'];
          $dest = $image_cache[$key]['image_media_file'];
        }

        if (create_thumbnail($src, THUMB_PATH."/".$image_cache[$key]['cat_id']."/".$dest, $quality, $dimension, $resize_type)) {
          $sql = "UPDATE ".IMAGES_TABLE."
                  SET image_thumb_file = '".addslashes($dest)."'
                  WHERE image_id = $key";
          $site_db->query($sql);

          echo "<br />&nbsp;&nbsp;&nbsp;<b>".$lang['creating_thumbnail_success']."</b><p>";
        }
        else {
          echo "<br />&nbsp;&nbsp;&nbsp;<b class=\"marktext\">".$lang['creating_thumbnail_error']."</b><p>";
        }
      }
    }
  }
  else {
    echo "<b>Just relaxing because you give me nothing to do!</b>";
  }
}

if ($action == "checkthumbnails") {
  $num_newimages = (isset($HTTP_POST_VARS['num_newimages']) && intval($HTTP_POST_VARS['num_newimages'])) ? intval($HTTP_POST_VARS['num_newimages']) : 10;

  show_form_header("thumbnailer.php", "checkthumbnails");
  show_table_header($lang['check_thumbnails'], 2);
  $desc = $lang['check_thumbnails_desc'];
  $desc .= "&nbsp;&nbsp;&nbsp;&nbsp;".$lang['num_newimages_desc']."<input type=\"text\" name=\"num_newimages\" value=\"".$num_newimages."\" size=\"5\">";
  show_custom_row($desc, "<input type=\"submit\" value=\"".$lang['check_thumbnails']."\" class=\"button\">");
  show_table_footer();
  echo "</form>";
}

if (isset($HTTP_POST_VARS['action']) && $HTTP_POST_VARS['action'] == "checkthumbnails") {
  $sql = "SELECT image_id, image_name, cat_id, image_media_file, image_thumb_file
          FROM ".IMAGES_TABLE;
  $result = $site_db->query($sql);

  $imgs = "";
  if ($result) {
    $bgcounter = 0;
    $image_counter = 0;
    while ($image_row = $site_db->fetch_array($result)) {
      if ($image_row['image_thumb_file'] == "") {
        $exists = false;
      } else {
        if (is_remote($image_row['image_thumb_file'])) {
          $exists = true;
        } else {
          $exists = file_exists(THUMB_PATH."/".$image_row['cat_id']."/".$image_row['image_thumb_file']);
        }
      }
      if (!$exists && (file_exists(MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file']) || is_remote($image_row['image_media_file']))) {
        $src = is_remote($image_row['image_media_file']) ? $image_row['image_media_file'] : MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file'];
        $image_info = getimagesize($src);
        if ($image_info[2] == 1 || $image_info[2] == 2 || $image_info[2] == 3) {
          $imgs .= "<tr class=\"".get_row_bg()."\">";
          $imgs .= "<td width=\"20%\"><input type=\"checkbox\" name=\"image_list[".$image_row['image_id']."]\" value=\"1\" checked=\"checked\"></td>\n";
          $imgs .= "<td width=\"30%\"><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_row['image_id'])."\" target=_blank>".format_text($image_row['image_name'], 2)."</a></b></td>\n";
          $imgs .= "<td width=\"25%\">".$image_row['image_media_file']."</td>\n";
          $imgs .= "<td width=\"25%\">".format_text($cat_cache[$image_row['cat_id']]['cat_name'], 2)."</td></tr>\n";
          $image_counter++;
        }
      }
      if ($image_counter == $num_newimages) {
        break;
      }
    }
  }
  if (empty($imgs)) {
    echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    $bgcounter = 0;
    show_description_row($lang['no_search_results'], 4);
    show_table_footer();
  }
  else {
    show_form_header("thumbnailer.php", "createthumbnails", "form");
    echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr class=\"tableseparator\">\n<td class=\"tableseparator\"><input name=allbox type=checkbox onClick=\"CheckAll();\" checked=\"checked\"></td>\n<td class=\"tableseparator\">".$lang['field_image_name']."</td>\n<td class=\"tableseparator\">".$lang['field_image_file']."</td>\n<td class=\"tableseparator\">".$lang['field_category']."</td>\n</tr>\n";
    echo $imgs;
    show_table_separator($lang['convert_options'], 4);
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"2\"><p class=\"rowtitle\">".$lang['convert_thumbnail_dimension']."</p></td>\n";
    echo "<td colspan=\"2\"><p><input type=\"text\" size=\"" . $textinput_size . "\" name=\"dimension\" value=\"" . $config['auto_thumbnail_dimension'] . "\"></p></td>\n</tr>\n";

    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"2\" valign=\"top\"><p class=\"rowtitle\">".$lang['resize_proportions_desc']."</p></td>\n";
    echo "<td colspan=\"2\"><p>";
    echo "<input type=\"radio\" name=\"resize_type\" value=\"1\" checked=\"checked\"> ".$lang['resize_proportionally']."<br />";
    echo "<input type=\"radio\" name=\"resize_type\" value=\"2\"> ".$lang['resize_fixed_width']."<br />";
    echo "<input type=\"radio\" name=\"resize_type\" value=\"3\"> ".$lang['resize_fixed_height']."<br />";
    echo "</p></td>\n</tr>\n";

    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"2\"><p class=\"rowtitle\">".$lang['convert_thumbnail_quality']."</p></td>\n";
    echo "<td colspan=\"2\"><p><input type=\"text\" size=\"" . $textinput_size . "\" name=\"quality\" value=\"" . $config['auto_thumbnail_quality'] . "\"></p></td>\n</tr>\n";
    show_form_footer($lang['create_thumbnails'], "", 4);
  }
}

show_admin_footer();
?>