<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: validateimages.php                                   *
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

include(ROOT_PATH.'includes/search_utils.php');

// Start Upload
include(ROOT_PATH.'includes/upload.php');
$site_upload = new Upload();

if ($action == "") {
  $action = "validateimages";
}

show_admin_header();

if ($action == "updateimage") {
  $error = array();
  $error_msg = "";

  $image_id = (isset($HTTP_POST_VARS['image_id'])) ? intval($HTTP_POST_VARS['image_id']) : intval($HTTP_GET_VARS['image_id']);
  $image_name = trim($HTTP_POST_VARS['image_name']);
  $image_description = trim($HTTP_POST_VARS['image_description']);

  $image_keywords = trim($HTTP_POST_VARS['image_keywords']);
  $image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
  $image_keywords_arr = explode(',', $image_keywords);
  array_walk($image_keywords_arr, 'trim_value');
  $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

  $cat_id = intval($HTTP_POST_VARS['cat_id']);
  $user_id = (intval($HTTP_POST_VARS['user_id']) != 0) ? intval($HTTP_POST_VARS['user_id']) : $user_info['user_id'];

  $image_date = (trim($HTTP_POST_VARS['image_date']) != "") ? "UNIX_TIMESTAMP('".trim($HTTP_POST_VARS['image_date'])."')" : time();

  $remote_file = trim($HTTP_POST_VARS['remote_file']);
  $remote_thumb_file = trim($HTTP_POST_VARS['remote_thumb_file']);

  $old_file_name = trim($HTTP_POST_VARS['old_file_name']);
  $old_thumb_file_name = trim($HTTP_POST_VARS['old_thumb_file_name']);

  $image_download_url = trim($HTTP_POST_VARS['image_download_url']);
  $delete_thumb_file = (isset($HTTP_POST_VARS['delete_thumb_file']) && $HTTP_POST_VARS['delete_thumb_file'] == 1) ? 1 : 0;

  if ($image_name == "") {
    $error['image_name'] = 1;
  }
  if ($cat_id == 0) {
    $error['cat_id'] = 1;
  }
  if ($image_download_url != "" && !is_remote($image_download_url) && !is_local_file($image_download_url)) {
    $error['image_download_url'] = 1;
  }

  if ((empty($HTTP_POST_FILES['file']['tmp_name']) || $HTTP_POST_FILES['file']['tmp_name'] == "none") && $remote_file != "" && !check_remote_media($remote_file) && !check_local_media($remote_file)) {
    $error['remote_file'] = 1;
  }
  if ((empty($HTTP_POST_FILES['thumb_file']['tmp_name']) || $HTTP_POST_FILES['thumb_file']['tmp_name'] == "none") && $remote_thumb_file != "" && !check_remote_thumb($remote_thumb_file) && !check_local_thumb($remote_thumb_file)) {
    $error['remote_thumb_file'] = 1;
  }

  if (!empty($additional_image_fields)) {
    foreach ($additional_image_fields as $key => $val) {
      if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
        $error[$key] = 1;
      }
    }
  }

  if (!empty($HTTP_POST_FILES['file']['tmp_name']) && $HTTP_POST_FILES['file']['tmp_name'] != "none" && !$error) {
    unset($HTTP_POST_VARS['remote_file']);
    @rename(MEDIA_TEMP_PATH."/".$old_file_name, MEDIA_TEMP_PATH."/".$old_file_name.".bak");
    $new_name = $site_upload->upload_file("file", "media", 0);
    if (!$new_name) {
      $error_msg .= $lang['file_upload_error'].": <b>".$HTTP_POST_FILES['file']['name']."</b><br />".$site_upload->get_upload_errors();
      @rename(MEDIA_TEMP_PATH."/".$old_file_name.".bak", MEDIA_TEMP_PATH."/".$old_file_name);
      $error = 1;
    }
    else {
      $log[] = $lang['file_upload_success'].": <b>".$new_name."</b>";
    }
  }
  elseif ((empty($HTTP_POST_FILES['file']['tmp_name']) || $HTTP_POST_FILES['file']['tmp_name'] == "none") && $remote_file != "" && check_remote_media($remote_file)) {
    $new_name = $remote_file;
    if (file_exists(MEDIA_TEMP_PATH."/".$old_file_name)) {
      unlink(MEDIA_TEMP_PATH."/".$old_file_name);
    }
  }
  else {
    $new_name = $old_file_name;
  }

  if ($delete_thumb_file == 1) {
    if (file_exists(THUMB_TEMP_PATH."/".$old_thumb_file_name)) {
      unlink(THUMB_TEMP_PATH."/".$old_thumb_file_name);
    }
    unset($HTTP_POST_VARS['remote_thumb_file']);
    $new_thumb_name = "";
  }
  elseif (!empty($HTTP_POST_FILES['thumb_file']['tmp_name']) && $HTTP_POST_FILES['thumb_file']['tmp_name'] != "none" && !$error) {
    unset($HTTP_POST_VARS['remote_thumb_file']);
    @rename(THUMB_TEMP_PATH."/".$old_thumb_file_name, THUMB_TEMP_PATH."/".$old_thumb_file_name.".bak");
    $new_thumb_name = $site_upload->upload_file("thumb_file", "thumb", 0, get_basefile($new_name));
    if (!$new_thumb_name) {
      $error_msg .= $lang['thumb_upload_error'].": <b>".$HTTP_POST_FILES['thumb_file']['name']."</b><br />".$site_upload->get_upload_errors();
      @rename(THUMB_TEMP_PATH."/".$old_thumb_file_name.".bak", THUMB_TEMP_PATH."/".$old_thumb_file_name);
      @unlink(MEDIA_TEMP_PATH."/".$new_name);
      $error = 1;
    }
    else {
      $log[] = $lang['thumb_upload_success'].": <b>$new_thumb_name</b>";
    }
  }
  elseif ((empty($HTTP_POST_FILES['thumb_file']['tmp_name']) || $HTTP_POST_FILES['thumb_file']['tmp_name'] == "none") && $remote_thumb_file != "" && check_remote_thumb($remote_thumb_file)) {
    $new_thumb_name = $remote_thumb_file;
    if (!empty($old_thumb_file_name) && file_exists(THUMB_TEMP_PATH."/".$old_thumb_file_name)) {
      unlink(THUMB_TEMP_PATH."/".$old_thumb_file_name);
    }
  }
  else {
    $new_thumb_name = $old_thumb_file_name;
  }

  if (empty($error)) {
    $additional_sql = "";
    if (!empty($additional_image_fields)) {
      $table_fields = $site_db->get_table_fields(IMAGES_TABLE);
      foreach ($additional_image_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
          $additional_sql .= ", $key = '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
        }
      }
    }

    $sql = "UPDATE ".IMAGES_TEMP_TABLE."
            SET cat_id = $cat_id, user_id = $user_id, image_name = '$image_name', image_description = '$image_description', image_keywords = '$image_keywords', image_date = $image_date, image_media_file = '$new_name', image_thumb_file = '$new_thumb_name', image_download_url = '$image_download_url'".$additional_sql."
            WHERE image_id = $image_id";
    $result = $site_db->query($sql);

    @unlink(MEDIA_TEMP_PATH."/".$old_file_name.".bak");
    @unlink(THUMB_TEMP_PATH."/".$old_thumb_file_name.".bak");
    $msg = ($result) ? $lang['image_edit_success'] : $lang['image_edit_error'];
  }
  else {
    $msg .= sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $msg .= $error_msg;
  }
  echo "<script language=javascript>\n showProgress();\n hideProgress();\n</script>";
  $action = "editimage";
}

if ($action == "editimage") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  printf("<p>%s</p>\n", $lang['upload_note']);

  $image_id = (isset($HTTP_POST_VARS['image_id'])) ? intval($HTTP_POST_VARS['image_id']) : intval($HTTP_GET_VARS['image_id']);

  $sql = "SELECT *, FROM_UNIXTIME(image_date) AS image_date
          FROM ".IMAGES_TEMP_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);

  show_form_header("validateimages.php", "updateimage", "form", 1);
  show_hidden_input("image_id", $image_id);
  show_hidden_input("old_file_name", $image_row['image_media_file']);
  show_hidden_input("old_thumb_file_name", $image_row['image_thumb_file']);
  show_table_header($lang['nav_images_edit'].": ".format_text($image_row['image_name'], 2), 2);

  $file_src = get_file_path($image_row['image_media_file'], "media", 0, 1);
  show_image_row($lang['image']."<br /><span class=\"smalltext\">(".$image_row['image_media_file'].")</span>", $file_src, 1);

  $value = (is_remote($image_row['image_media_file']) || is_local_file($image_row['image_media_file'])) ? $image_row['image_media_file'] : "";
  show_upload_row($lang['image_file'], "file", "<br /><span class=\"smalltext\">".$lang['allowed_mediatypes_desc'].str_replace(",",", ",$config['allowed_mediatypes'])."</span>", $value);

  if (!empty($image_row['image_thumb_file'])) {
    $thumb_src = get_file_path($image_row['image_thumb_file'], "thumb", 0, 1);
    show_image_row($lang['thumb']."<br /><span class=\"smalltext\">(".$image_row['image_thumb_file'].")</span>", $thumb_src, 1, "delete_thumb_file");
  }
  else {
    $file_type = get_file_extension($image_row['image_media_file']);
    show_image_row($lang['thumb']."<br /><span class=smalltext>(".$lang['no_thumb_found'].")</span>", ICON_PATH."/".$file_type.".gif", 1);
  }

  $value = (is_remote($image_row['image_thumb_file']) || is_local_file($image_row['image_thumb_file'])) ? $image_row['image_thumb_file'] : "";
  show_upload_row($lang['thumb_file'], "thumb_file", "<br /><span class=\"smalltext\">".$lang['allowed_mediatypes_desc']." jpg, gif, png</span>", $value);

  show_input_row($lang['field_download_url'].$lang['download_url_desc'], "image_download_url", "", $textinput_size);
  show_input_row($lang['field_image_name'], "image_name", $image_row['image_name'], $textinput_size);
  show_textarea_row($lang['field_description_ext'], "image_description", $image_row['image_description'], $textarea_size);
  show_textarea_row($lang['field_keywords_ext'], "image_keywords", $image_row['image_keywords'], $textarea_size);

  show_cat_select_row($lang['field_category'], $image_row['cat_id'], 3);
  show_user_select_row($lang['user'], $image_row['user_id']);
  show_input_row($lang['field_date'].$lang['date_desc'], "image_date", $image_row['image_date'], $textinput_size);
  show_additional_fields("image", $image_row, IMAGES_TEMP_TABLE);
  show_form_footer($lang['save_changes'], $lang['reset'], 2, "", " onClick='showProgress()'");
}

if ($action == "saveimages") {
  $image_list = (isset($HTTP_POST_VARS['image_list'])) ? $HTTP_POST_VARS['image_list'] : "";
  if (!empty($image_list)) {
    $image_id_sql = "";
    foreach ($image_list as $key => $val) {
      $image_id_sql .= (($image_id_sql != "") ? ", " : "" ).$key;
    }

    $sql = "SELECT *
            FROM ".IMAGES_TEMP_TABLE."
            WHERE image_id IN($image_id_sql)";
    $result = $site_db->query($sql);

    $image_cache = array();
    while ($row = $site_db->fetch_array($result)) {
      $image_cache[$row['image_id']] = $row;
    }

    foreach ($image_list as $key => $val) {
      flush();
      $image_name = addslashes($image_cache[$key]['image_name']);
      $cat_id = $image_cache[$key]['cat_id'];
      $user_id = $image_cache[$key]['user_id'];
      $image_description = addslashes($image_cache[$key]['image_description']);
      $image_keywords = addslashes($image_cache[$key]['image_keywords']);
      $image_date = $image_cache[$key]['image_date'];
      $image_media_file = addslashes($image_cache[$key]['image_media_file']);
      $image_thumb_file = addslashes($image_cache[$key]['image_thumb_file']);
      $image_download_url = addslashes($image_cache[$key]['image_download_url']);

      $old_media_path = MEDIA_TEMP_PATH."/".$image_media_file;
      $old_thumb_path = THUMB_TEMP_PATH."/".$image_thumb_file;

      if ($val == 1) {
        $new_name = copy_media($image_media_file, "-1", $cat_id);
        $new_thumb_name = copy_thumbnail($new_name, $image_thumb_file, "-1", $cat_id);

        if ($new_name) {
          $additional_field_sql = "";
          $additional_value_sql = "";
          if (!empty($additional_image_fields)) {
            $table_fields = $site_db->get_table_fields(IMAGES_TABLE);
            foreach ($additional_image_fields as $key2 => $val2) {
              if (isset($image_cache[$key][$key2]) && isset($image_cache[$key][$key2])) {
                $additional_field_sql .= ", $key2";
                $additional_value_sql .= ", '".addslashes($image_cache[$key][$key2])."'";
              }
            }
          }

          $current_time = time();
          $sql = "INSERT INTO ".IMAGES_TABLE."
                  (cat_id, user_id, image_name, image_description, image_keywords, image_date, image_media_file, image_thumb_file, image_download_url".$additional_field_sql.")
                  VALUES
                  ($cat_id, $user_id, '$image_name', '$image_description', '$image_keywords', $current_time, '$new_name', '$new_thumb_name', '$image_download_url'".$additional_value_sql.")";
          $result = $site_db->query($sql);
          $image_id = $site_db->get_insert_id();

          if ($result) {
            $sql = "DELETE FROM ".IMAGES_TEMP_TABLE."
                    WHERE image_id = $key";
            $site_db->query($sql);

            $search_words = array();
            foreach ($search_match_fields as $image_column => $match_column) {
              if (isset($image_cache[$key][$image_column])) {
                $search_words[$image_column] = $image_cache[$key][$image_column];
              }
            }
            add_searchwords($image_id, $search_words);
            echo $lang['image_add_success'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$image_media_file.")<br />";
          }
          else {
            echo $lang['image_add_error'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$image_media_file.")<br />";
          }
        }
        else {
          echo $lang['image_add_error'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$image_media_file.")<br />";
        }
      }
      else {
        $sql = "DELETE FROM ".IMAGES_TEMP_TABLE."
                WHERE image_id = $key";
        $site_db->query($sql);
        @unlink($old_media_path);
        @unlink($old_thumb_path);
        echo $lang['image_delete_success'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$image_media_file.")<br />";
      }
    }

  }
  else {
    echo "<b>Just relaxing because you give me nothing to do!</b>";
  }
}

if ($action == "validateimages") {
  $condition = "1=1";

  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : 0;
  if ($cat_id != 0 && $cat_id != "") {
    $condition .= " AND i.cat_id = $cat_id";
  }

  if (isset($HTTP_GET_VARS['orderby']) || isset($HTTP_POST_VARS['orderby'])) {
    $orderby = (isset($HTTP_GET_VARS['orderby'])) ? stripslashes(trim($HTTP_GET_VARS['orderby'])) : stripslashes(trim($HTTP_POST_VARS['orderby']));
  }
  else {
    $orderby = "i.image_date";
  }

  $limitstart = (isset($HTTP_POST_VARS['limitstart'])) ? trim($HTTP_POST_VARS['limitstart']) : "";
  if ($limitstart == "") {
    $limitstart = 0;
  }
  else {
    $limitstart--;
  }

  if (isset($HTTP_GET_VARS['limitnumber']) || isset($HTTP_POST_VARS['limitnumber'])) {
    $limitnumber = (isset($HTTP_GET_VARS['limitnumber'])) ? intval(trim($HTTP_GET_VARS['limitnumber'])) : intval(trim($HTTP_POST_VARS['limitnumber']));
  }
  else {
    $limitnumber = 10;
  }

  if (isset($HTTP_GET_VARS['direction']) || isset($HTTP_POST_VARS['direction'])) {
    $direction = (isset($HTTP_GET_VARS['direction'])) ? trim($HTTP_GET_VARS['direction']) : trim($HTTP_POST_VARS['direction']);
  }
  else {
    $direction = "ASC";
  }

  show_form_header("validateimages.php", "validateimages");
  show_table_header($lang['nav_images_validate'], 2);
  show_cat_select_row($lang['field_category'], "", 2);
  ?>
  <tr class="<?php echo get_row_bg(); ?>"><td><p><b><?php echo $lang['order_by'] ?></b></p></td><td><p>
  <select name="orderby">
  <option value="i.image_name" selected><?php echo $lang['field_image_name'] ?></option>
  <option value="i.cat_id"><?php echo $lang['field_category'] ?></option>
  <option value="i.image_date"><?php echo $lang['field_date'] ?></option>
  <option value="<?php echo get_user_table_field("u.", "user_name"); ?>"><?php echo $lang['field_username'] ?></option>
  </select>
  <select name="direction">
  <option selected value="ASC"><?php echo $lang['asc'] ?></option>
  <option value="DESC"><?php echo $lang['desc'] ?></option>
  </select>
  </p></td></tr>
  <?php
  show_input_row($lang['results_per_page'], "limitnumber", $limitnumber);
  show_form_footer($lang['search'], $lang['reset'], 2);

  $sql = "SELECT COUNT(*) AS images
          FROM ".IMAGES_TEMP_TABLE." i
          WHERE $condition";
  $countimages = $site_db->query_firstrow($sql);

  $limitfinish = $limitstart + $limitnumber;

  $start = 0;
  if ($countimages['images'] > 0) {
    $start = $limitstart + 1;
  }

  echo $lang['found']." <b>".$countimages['images']."</b> ".$lang['showing']." <b>$start</b>-";
  if ($limitfinish > $countimages['images'] == 0) {
    echo "<b>".$limitfinish."</b>.";
  }
  else {
    echo "<b>".$countimages['images']."</b>.";
  }
  echo "<br />".$lang['no_image_found'];

  show_form_header("validateimages.php", "saveimages", "form");
  $bgcounter = 0;
  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  if ($countimages['images'] > 0) {
    echo "<tr class=\"tableseparator\">\n";
    echo "<td class=\"tableseparator\">".$lang['validate']."</td>\n<td class=\"tableseparator\">".$lang['delete']."</td>\n<td class=\"tableseparator\"> </td>\n<td class=\"tableseparator\">".$lang['field_image_name']."</td>\n<td class=\"tableseparator\">".$lang['field_category']."</td>\n<td class=\"tableseparator\">".$lang['field_username']."</td>\n<td class=\"tableseparator\">".$lang['field_date']."</td>\n<td class=\"tableseparator\">".$lang['options']."</td>\n</tr>\n";

    $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_date, i.image_media_file".get_user_table_field(", u.", "user_name")."
            FROM ".IMAGES_TEMP_TABLE." i
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE $condition
            ORDER BY $orderby $direction
            LIMIT $limitstart, $limitnumber";
    $result = $site_db->query($sql);

    while ($image_row = $site_db->fetch_array($result)) {
      echo "<tr class=\"".get_row_bg()."\">";

      $image_path = (is_remote($image_row['image_media_file'])) ? $image_row['image_media_file'] : MEDIA_TEMP_PATH."/".$image_row['image_media_file'];
      $file_src = get_file_path($image_row['image_media_file'], "media", 0, 1);

      echo "<td><input type=\"radio\" name=\"image_list[".$image_row['image_id']."]\" value=\"1\"></td>";
      echo "<td><input type=\"radio\" name=\"image_list[".$image_row['image_id']."]\" value=\"0\"></td>";
      echo "<td><a href=\"".$image_path."\" target=\"_blank\"><img src=\"".$file_src."\" border=\"1\" height=\"50\"></a></td>";
      echo "<td><b><a href=\"".$image_path."\" target=\"_blank\">".format_text($image_row['image_name'], 2)."</a></b> (".$image_row['image_media_file'];

      if (!get_file_path($image_row['image_media_file'], "media", 0, 0, 0)) {
        echo " <b class=\"marktext\">!</b>";
      }
      echo ")</td>\n";
      echo "<td><a href=\"".$site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$image_row['cat_id'])."\" target=\"_blank\">".format_text($cat_cache[$image_row['cat_id']]['cat_name'], 2)."</a></td>\n";
      $show_user_name = format_text($image_row[$user_table_fields['user_name']], 2);
      if ($image_row['user_id'] != GUEST && empty($url_show_profile)) {
        $show_user_name = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&".URL_USER_ID."=".$image_row['user_id'])."\" target=\"_blank\">$show_user_name</a>";
      }
      echo "<td>".$show_user_name."</td>\n";
      echo "<td>".format_date($config['date_format']." ".$config['time_format'], $image_row['image_date'])."</td>\n";
      echo "<td><p>";
      show_text_link($lang['edit'],"validateimages.php?action=editimage&image_id=".$image_row['image_id']);
      echo "</p></td>\n";
      echo "</tr>\n";
    }
    show_form_footer($lang['submit'], $lang['reset'], 8);
  }
  else {
    $bgcounter = 0;
    show_description_row($lang['no_search_results'], 8);
    show_table_footer();
  }
}

show_admin_footer();
?>