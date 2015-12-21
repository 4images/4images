<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: resizer.php                                          *
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
ini_set('memory_limit', '1024M');
$nozip = 1;
define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');
require(ROOT_PATH.'includes/image_utils.php');

if ($action == "") {
  $action = "selectoptions";
}

show_admin_header();

$convert_options = init_convert_options();
if ($convert_options['convert_error']) {
  echo $convert_options['convert_error'];
  show_admin_footer();
  exit;
}

if ($action == "resizeimages") {
  $path = ($HTTP_POST_VARS['image_type'] == "media") ? MEDIA_PATH : THUMB_PATH;
  $sql_image_type = ($HTTP_POST_VARS['image_type'] == "media") ? "image_media_file" : "image_thumb_file";
  $dimension = (isset($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : $config['max_image_width']; 
  $height = (isset($HTTP_POST_VARS['height'])) ? intval($HTTP_POST_VARS['height']) : $config['max_image_height']; 
  $resize_type = (isset($HTTP_POST_VARS['resize_type'])) ? intval($HTTP_POST_VARS['resize_type']) : $config['auto_thumbnail_resize_type'];
  $quality = (isset($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) <= 100) ? intval($HTTP_POST_VARS['quality']) : 100;
  $image_list = (isset($HTTP_POST_VARS['image_list'])) ? $HTTP_POST_VARS['image_list'] : "";
  $image_dimensions = (isset($HTTP_POST_VARS['image_dimensions'])) ? $HTTP_POST_VARS['image_dimensions'] : "";

  if (!empty($image_list)) {
    $image_id_sql = "";
    foreach ($image_list as $key => $val) {
      if ($val == 1) {
        $image_id_sql .= (($image_id_sql != "") ? ", " : "" ).$key;
      }
    }

    $sql = "SELECT image_id, cat_id, $sql_image_type
            FROM ".IMAGES_TABLE."
            WHERE image_id IN($image_id_sql)";
    $result = $site_db->query($sql);

    $image_cache = array();
    while ($row = $site_db->fetch_array($result)) {
      $image_cache[$row['image_id']] = $row;
    }

    foreach ($image_list as $key => $val) {
      if ($val == 1) {
        echo "<p>".$lang['resizing_image']."<b>".$image_cache[$key][$sql_image_type]."</b> (".$image_dimensions[$key].") ....&nbsp;&nbsp;\n";
        flush();
        @set_time_limit(90);
        if (resize_image($path."/".$image_cache[$key]['cat_id']."/".$image_cache[$key][$sql_image_type], $quality, $dimension, $resize_type)) {
          echo "<br />&nbsp;&nbsp;&nbsp;<b>".$lang['resizing_image_success']."</b><p>";
        }
        else {
          echo "<br />&nbsp;&nbsp;&nbsp;<b class=\"marktext\">".$lang['resizing_image_error']."</b><p>";
        }
      }
    }
  }
  else {
    echo "<b>Just relaxing because you give me nothing to do!</b>";
  }
}

if ($action == "selectoptions") {
  show_form_header("resizer.php", "selectoptions");
  show_table_header($lang['resize_images'], 2);

  $image_type = (isset($HTTP_POST_VARS['image_type'])) ? trim($HTTP_POST_VARS['image_type']) : "media";

  $select_image_type = "<select name=\"image_type\">";
  $select_image_type .= "<option value=\"media\"";
  if ($image_type == "media") {
    $select_image_type .= " selected";
  }
  $select_image_type .= ">".$lang['resize_image_files']."</option>";
  $select_image_type .= "<option value=\"thumb\"";
  if ($image_type == "thumb") {
    $select_image_type .= " selected";
  }
  $select_image_type .= ">".$lang['resize_thumb_files']."</option>";
  $select_image_type .= "</select>";

  show_custom_row($lang['resize_image_type_desc'], $select_image_type);

  $dimension = (isset($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : $config['max_image_width']; 
  $height = (isset($HTTP_POST_VARS['height'])) ? intval($HTTP_POST_VARS['height']) : $config['max_image_height']; 
  $resize_type = (isset($HTTP_POST_VARS['resize_type'])) ? intval($HTTP_POST_VARS['resize_type']) : $config['auto_thumbnail_resize_type'];
  $quality = (isset($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) <= 100) ? intval($HTTP_POST_VARS['quality']) : 100;

  $num_newimages = (isset($HTTP_POST_VARS['num_newimages']) && intval($HTTP_POST_VARS['num_newimages'])) ? intval($HTTP_POST_VARS['num_newimages']) : 10;

  show_input_row($lang['max_imagewidth'], "dimension", $dimension); 
  show_input_row($lang['max_imageheight'], "height", $height);

  $resize_type_1_checked = ($resize_type == 1) ? " checked=\"checked\"" : "";
  $resize_type_2_checked = ($resize_type == 2) ? " checked=\"checked\"" : "";
  $resize_type_3_checked = ($resize_type == 3) ? " checked=\"checked\"" : "";

  $resize_type_radios = "<input type=\"radio\" name=\"resize_type\" value=\"1\"".$resize_type_1_checked."> ".$lang['resize_proportionally']."<br />";
  $resize_type_radios .= "<input type=\"radio\" name=\"resize_type\" value=\"2\"".$resize_type_2_checked."> ".$lang['resize_fixed_width']."<br />";
  $resize_type_radios .= "<input type=\"radio\" name=\"resize_type\" value=\"3\"".$resize_type_3_checked."> ".$lang['resize_fixed_height']."<br />";
  show_custom_row($lang['resize_proportions_desc'], $resize_type_radios);

  show_input_row($lang['resize_quality_desc'], "quality", $quality);
  show_input_row($lang['num_newimages_desc'], "num_newimages", $num_newimages);
  show_form_footer($lang['resize_check'], "");
  echo "</form>";
}

if (isset($HTTP_POST_VARS['action']) && $action == "selectoptions") {

  $path = ($HTTP_POST_VARS['image_type'] == "media") ? MEDIA_PATH : THUMB_PATH;
  $sql_image_type = ($HTTP_POST_VARS['image_type'] == "media") ? "image_media_file" : "image_thumb_file";
  $dimension = (isset($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : $config['max_image_height']; 
  $height = (isset($HTTP_POST_VARS['height'])) ? intval($HTTP_POST_VARS['height']) : $config['max_image_height'];
  $quality = (isset($HTTP_POST_VARS['quality'])) ? intval($HTTP_POST_VARS['quality']) : 75;

  $sql = "SELECT image_id, image_name, cat_id, $sql_image_type
          FROM ".IMAGES_TABLE;
  $result = $site_db->query($sql);

  $imgs = "";
  if ($result) {
    $bgcounter = 0;
    $image_counter = 0;
    while ($image_row = $site_db->fetch_array($result)) {
      if (!empty($image_row[$sql_image_type]) && file_exists($path."/".$image_row['cat_id']."/".$image_row[$sql_image_type])) {
        if (!$image_info = getimagesize($path."/".$image_row['cat_id']."/".$image_row[$sql_image_type])) {
          continue;
        }
        if ($image_info[2] == 1 || $image_info[2] == 2 || $image_info[2] == 3) {
          $ok = 0;
          if ($resize_type == 1 && ($image_info[0] > $dimension || $image_info[1] > $height)) {
            $ok = 1;
          }
          elseif ($resize_type == 2 && $image_info[0] > $dimension) {
            $ok = 1;
          }
          elseif ($resize_type == 3 && $image_info[1] > $height) {
            $ok = 1;
          }
          if ($ok) {
            $imgs .= "<tr class=\"".get_row_bg()."\">";
            $imgs .= "<td><input type=\"checkbox\" name=\"image_list[".$image_row['image_id']."]\" value=\"1\" checked=\"checked\"></td>\n";
            $imgs .= "<td><b>".$image_row[$sql_image_type]."</b></td>\n";
            $imgs .= "<td>".$image_info[0]."x".$image_info[1]."</td>";

            $width_height = get_width_height($dimension, $image_info[0], $image_info[1], $resize_type, $height);

            $imgs .= "<td>".$width_height['width']."x".$width_height['height']."</td>";
            $imgs .= "<td>".$quality."</td>";
            $imgs .= "</tr>\n";
            $imgs .= "<input type=\"hidden\" name=\"image_dimensions[".$image_row['image_id']."]\" value=\"".$image_info[0]."x".$image_info[1]."\">";
            $image_counter++;
          }
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
    show_description_row($lang['no_search_results']);
    show_table_footer();
  }
  else {
    show_form_header("resizer.php", "resizeimages", "form");
    show_hidden_input("image_type", $image_type);
    show_hidden_input("height", $height);
    show_hidden_input("dimension", $dimension);
    show_hidden_input("resize_type", $resize_type);
    show_hidden_input("quality", $quality);
    echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr class=\"tableseparator\">\n<td class=\"tableseparator\">\n<input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\" checked=\"checked\">\n</td>\n<td class=\"tableseparator\">".$lang['field_image_file']."</td>\n<td class=\"tableseparator\">".$lang['resize_org_size']."</td>\n<td class=\"tableseparator\">".$lang['resize_new_size']."</td>\n<td class=\"tableseparator\">".$lang['resize_new_quality']."</td>\n</tr>\n";
    echo $imgs;
    show_form_footer($lang['resize_start'], "", 5);
  }
}
show_admin_footer();
?>