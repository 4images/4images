<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: images.php                                           *
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

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

include(ROOT_PATH.'includes/search_utils.php');

// Start Upload
include(ROOT_PATH.'includes/upload.php');
$site_upload = new Upload();

if ($action == "") {
  $action = "modifyimages";
}

$orderbyOptions = array(
  'i.image_name' => $lang['field_image_name'],
  'i.image_media_file' => $lang['field_image_file'],
  'i.image_thumb_file' => $lang['field_thumb_file'],
  'i.cat_id' => $lang['field_category'],
  'i.image_date' => $lang['field_date'],
  'i.image_downloads' => $lang['field_downloads'],
  'i.image_rating' => $lang['field_rating'],
  'i.image_votes' => $lang['field_votes'],
  'i.image_hits' => $lang['field_hits'],
);

function delete_images($image_ids, $delfromserver = 1) {
  global $site_db, $lang;
  if (empty($image_ids)) {
    echo $lang['no_search_results'];
    return false;
  }
  $error_log = array();
  echo "<br />";
  $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_media_file, i.image_thumb_file, l.lightbox_image_ids
          FROM ".IMAGES_TABLE." i
          LEFT JOIN ".LIGHTBOXES_TABLE." l ON (l.user_id = i.user_id)
          WHERE i.image_id IN ($image_ids)";
  $image_result = $site_db->query($sql);
  while ($image_row = $site_db->fetch_array($image_result)) {
    if ($image_row['user_id'] != GUEST) {
      $lightbox_array = explode(" ",$image_row['lightbox_image_ids']);
      foreach ($lightbox_array as $key => $val) {
        if ($val == $image_row['image_id']) {
          unset($lightbox_array[$key]);
        }
      }
      $lightbox_image_ids = trim(implode(" ", $lightbox_array));
      $sql = "UPDATE ".LIGHTBOXES_TABLE."
              SET lightbox_image_ids = '".$lightbox_image_ids."'
              WHERE user_id = ".$image_row['user_id'];
      $site_db->query($sql);
    }

    $sql = "DELETE FROM ".IMAGES_TABLE."
            WHERE image_id = ".$image_row['image_id'];
    if ($site_db->query($sql)) {
      echo "<b>".$lang['image_delete_success']."</b> ".format_text($image_row['image_name'], 2)." (ID: ".$image_row['image_id'].")<br />\n";
    }
    else {
      $error_log[] = "<b>".$lang['image_delete_error']."</b> ".format_text($image_row['image_name'], 2)." (ID: ".$image_row['image_id'].")<br />";
    }

    if ($delfromserver) {
      if (!is_remote($image_row['image_media_file']) && !is_local_file($image_row['image_media_file'])) {
        if (@unlink(MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file'])) {
          echo "&nbsp;&nbsp;".$lang['file_delete_success']." (".$image_row['image_media_file'].")<br />\n";
        }
        else {
          $error_log[] = "<b>".$lang['file_delete_error']." (".$image_row['image_media_file'].")<br />";
        }
      }
      if (!empty($image_row['image_thumb_file']) && !is_remote($image_row['image_thumb_file']) && !is_local_file($image_row['image_thumb_file'])) {
        if (@unlink(THUMB_PATH."/".$image_row['cat_id']."/".$image_row['image_thumb_file'])) {
          echo "&nbsp;&nbsp;".$lang['thumb_delete_success']." (".$image_row['image_thumb_file'].")<br />\n";
        }
        else {
          $error_log[] = "<b>".$lang['thumb_delete_error']." (".$image_row['image_thumb_file'].")<br />\n";
        }
      }
    }

    if (!empty($user_table_fields['user_comments'])) {
      $sql = "SELECT user_id
              FROM ".COMMENTS_TABLE."
              WHERE image_id = ".$image_row['image_id']." AND user_id <> ".GUEST;
      $result = $site_db->query($sql);

      while ($row = $site_db->fetch_array($result)) {
        $sql = "UPDATE ".USERS_TABLE."
                SET ".get_user_table_field("", "user_comments")." = ".get_user_table_field("", "user_comments")." - 1
                WHERE ".get_user_table_field("", "user_id")." = ".$row['user_id'];
        $site_db->query($sql);
      }
    }

    $sql = "DELETE FROM ".COMMENTS_TABLE."
            WHERE image_id = ".$image_row['image_id'];
    if ($site_db->query($sql)) {
      echo $lang['comments_delete_success']."<br />\n";
    }
    else {
      $error_log[] = "<b>".$lang['comments_delete_success']."</b> ".format_text($image_row['image_name'], 2).", (ID: ".$image_row['image_id'].")<br />\n";
    }
    echo "<br />\n";
  }
  remove_searchwords($image_ids);
  return $error_log;
}

show_admin_header();

if ($action == "deleteimage") {
  $deleteimages = (isset($HTTP_POST_VARS['deleteimages'])) ? $HTTP_POST_VARS['deleteimages'] : array();
  $delfromserver = (isset($HTTP_POST_VARS['delfromserver'])) ? intval($HTTP_POST_VARS['delfromserver']) : 1;
  $image_ids = "";
  if (!empty($deleteimages)) {
    foreach ($deleteimages as $val) {
      $image_ids .= (($image_ids != "") ? ", " : "").$val;
    }
  }
  $lang_key = (sizeof($deleteimages) > 1) ? 'images' : 'image';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 1);
  echo "<tr><td class=\"tablerow\">\n";
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
  $error_log = delete_images($image_ids, $delfromserver);
  echo "</td></tr></table>\n";
  echo "</td></tr>\n";
  show_table_footer();
  if (!empty($error_log)) {
    show_table_header("Error Log:", 1);
    echo "<tr><td class=\"tablerow\">\n";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
    echo "<b>".$lang['error_log_desc']."</b>\n<ul>\n";
    foreach ($error_log as $val) {
      printf("<li>%s</li>\n", $val);
    }
    echo "</ul>\n</td></tr></table>\n";
    echo "</td></tr>\n";
    show_table_footer();
  }
  echo "<p>";
  show_text_link($lang['back_overview'], "images.php?action=modifyimages");
}

if ($action == "removeimage") {
  $image_ids = array();
  if (isset($HTTP_GET_VARS['image_id']) || isset($HTTP_POST_VARS['image_id'])) {
    $image_id = (isset($HTTP_GET_VARS['image_id'])) ? intval($HTTP_GET_VARS['image_id']) : intval($HTTP_POST_VARS['image_id']);
    $image_ids[] = $image_id;
  }
  elseif (isset($HTTP_POST_VARS['deleteimages'])) {
    $image_ids = $HTTP_POST_VARS['deleteimages'];
  }
  else {
   $image_ids[] = 0;
  }

  show_form_header("images.php", "deleteimage");
  foreach ($image_ids as $val) {
    show_hidden_input("deleteimages[]", $val);
  }
  $lang_key = (sizeof($image_ids) > 1) ? 'images' : 'image';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 2);
  show_description_row($lang['delete_image_confirm']);
  show_radio_row($lang['delete_image_files_confirm'], "delfromserver", 1);
  show_form_footer($lang['yes'], "", 2, $lang['no']);
}

if ($action == "updateimage") {
  $error_msg = "";
  $error = array();

  $image_id = (isset($HTTP_POST_VARS['image_id'])) ? intval($HTTP_POST_VARS['image_id']) : intval($HTTP_GET_VARS['image_id']);
  $image_name = un_htmlspecialchars(trim($HTTP_POST_VARS['image_name']));
  $image_description = un_htmlspecialchars(trim($HTTP_POST_VARS['image_description']));

  $image_keywords = un_htmlspecialchars(trim($HTTP_POST_VARS['image_keywords']));
  $image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
  $image_keywords_arr = explode(',', $image_keywords);
  array_walk($image_keywords_arr, 'trim_value');
  $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

  $cat_id = intval($HTTP_POST_VARS['cat_id']);
  $old_cat_id = intval($HTTP_POST_VARS['old_cat_id']);

  $user_id = (intval($HTTP_POST_VARS['user_id']) != 0) ? intval($HTTP_POST_VARS['user_id']) : $user_info['user_id'];

  $image_date = (trim($HTTP_POST_VARS['image_date']) != "") ? "UNIX_TIMESTAMP('".trim($HTTP_POST_VARS['image_date'])."')" : time();
  $image_active = intval($HTTP_POST_VARS['image_active']);
  $image_allow_comments = intval($HTTP_POST_VARS['image_allow_comments']);
  $image_downloads = (trim($HTTP_POST_VARS['image_downloads']) != "") ? intval($HTTP_POST_VARS['image_downloads']) : 0;
  $image_votes = (trim($HTTP_POST_VARS['image_votes']) != "") ? intval($HTTP_POST_VARS['image_votes']) : 0;
  $image_rating = (trim($HTTP_POST_VARS['image_rating']) != "") ? sprintf("%.2f", trim($HTTP_POST_VARS['image_rating'])) : "0.00";
  $image_hits = (trim($HTTP_POST_VARS['image_hits']) != "") ? intval(trim($HTTP_POST_VARS['image_hits'])) : 0;

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
    @rename(MEDIA_PATH."/".$old_cat_id."/".$old_file_name, MEDIA_PATH."/".$old_cat_id."/".$old_file_name.".bak");
    $new_name = $site_upload->upload_file("file", "media", $cat_id);
    if (!$new_name) {
      $error_msg .= $lang['file_upload_error'].": <b>".$HTTP_POST_FILES['file']['name']."</b><br />".$site_upload->get_upload_errors();
      @rename(MEDIA_PATH."/".$old_cat_id."/".$old_file_name.".bak", MEDIA_PATH."/".$old_cat_id."/".$old_file_name);
      $error = 1;
    }
    else {
      unlink(MEDIA_PATH."/".$old_cat_id."/".$old_file_name.".bak");
      $log[] = $lang['file_upload_success'].": <b>$new_name</b>";
    }
  }
  elseif ((empty($HTTP_POST_FILES['file']['tmp_name']) || $HTTP_POST_FILES['file']['tmp_name'] == "none") && $remote_file != "" && (check_remote_media($remote_file) || check_local_media($remote_file))) {
    $new_name = $remote_file;
    if (file_exists(MEDIA_PATH."/".$old_cat_id."/".$old_file_name) && is_file(MEDIA_PATH."/".$old_cat_id."/".$old_file_name)) {
      unlink(MEDIA_PATH."/".$old_cat_id."/".$old_file_name);
    }
  }
  else {
    if ($cat_id != $old_cat_id && !empty($old_file_name)) {
      $new_name = copy_media($old_file_name, $old_cat_id, $cat_id);
    }
    else {
      $new_name = $old_file_name;
    }
  }

  if ($delete_thumb_file == 1) {
    if (!empty($old_thumb_file_name) && file_exists(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name)) {
      unlink(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name);
    }
    $new_thumb_name = "";
    unset($HTTP_POST_VARS['remote_thumb_file']);
  }
  elseif (!empty($HTTP_POST_FILES['thumb_file']['tmp_name']) && $HTTP_POST_FILES['thumb_file']['tmp_name'] != "none" && !$error) {
    unset($HTTP_POST_VARS['remote_thumb_file']);
    @rename(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name, THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name.".bak");
    $new_thumb_name = $site_upload->upload_file("thumb_file", "thumb", $cat_id, get_basefile($new_name));
    if (!$new_thumb_name) {
      $error_msg .= $lang['thumb_upload_error'].": <b>".$HTTP_POST_FILES['thumb_file']['name']."</b><br />".$site_upload->get_upload_errors();
      @rename(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name.".bak", THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name);
      @unlink(MEDIA_PATH."/".$old_cat_id."/".$new_name);
      $error = 1;
    }
    else {
      $log[] = $lang['thumb_upload_success'].": <b>$new_thumb_name</b>";
    }
  }
  elseif ((empty($HTTP_POST_FILES['thumb_file']['tmp_name']) || $HTTP_POST_FILES['thumb_file']['tmp_name'] == "none") && $remote_thumb_file != "" && (check_remote_thumb($remote_thumb_file) || check_local_thumb($remote_thumb_file))) {
    $new_thumb_name = $remote_thumb_file;
    if (file_exists(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name) && is_file(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name)) {
      unlink(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name);
    }
  }
  else {
    if ($cat_id != $old_cat_id && !empty($old_thumb_file_name)) {
      $new_thumb_name = copy_thumbnail($new_name, $old_thumb_file_name, $old_cat_id, $cat_id);
    }
    else {
      $new_thumb_name = $old_thumb_file_name;
    }
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

    $sql = "UPDATE ".IMAGES_TABLE."
            SET cat_id = $cat_id, user_id = $user_id, image_name = '$image_name', image_description = '$image_description', image_keywords = '$image_keywords', image_date = $image_date, image_active = $image_active, image_media_file = '$new_name', image_thumb_file = '$new_thumb_name', image_download_url = '$image_download_url', image_allow_comments = $image_allow_comments, image_downloads = $image_downloads, image_votes = $image_votes, image_rating = '$image_rating', image_hits = $image_hits".$additional_sql."
            WHERE image_id = $image_id";
    $result = $site_db->query($sql);

    @unlink(MEDIA_PATH."/".$old_cat_id."/".$old_file_name.".bak");
    @unlink(THUMB_PATH."/".$old_cat_id."/".$old_thumb_file_name.".bak");
    update_comment_count($image_id);

    if ($result) {
      $search_words = array();
      foreach ($search_match_fields as $image_column => $match_column) {
        if (isset($HTTP_POST_VARS[$image_column])) {
          $search_words[$image_column] = stripslashes($HTTP_POST_VARS[$image_column]);
        }
      }
      remove_searchwords($image_id);
      add_searchwords($image_id, $search_words);
      $msg = $lang['image_edit_success'];
    }
    else {
      $msg = $lang['image_edit_error'];
    }
  }
  else {
    $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
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
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);

  show_form_header("images.php", "updateimage", "form", 1);
  show_hidden_input("image_id", $image_id);
  show_hidden_input("old_file_name", $image_row['image_media_file']);
  show_hidden_input("old_thumb_file_name", $image_row['image_thumb_file']);
  show_hidden_input("old_cat_id", $image_row['cat_id']);
  show_table_header($lang['nav_images_edit'].": ".format_text($image_row['image_name'], 2), 2);

  $file_src = get_file_path($image_row['image_media_file'], "media", $image_row['cat_id'], 1);
  show_image_row($lang['image']."<br /><span class=\"smalltext\">(".$image_row['image_media_file'].")</span>", $file_src, 1);

  $value = (is_remote($image_row['image_media_file']) || is_local_file($image_row['image_media_file'])) ? $image_row['image_media_file'] : "";
  show_upload_row($lang['image_file'], "file", "<br /><span class=\"smalltext\">".$lang['allowed_mediatypes_desc'].str_replace(",",", ",$config['allowed_mediatypes'])."</span>", $value);

  if (!empty($image_row['image_thumb_file'])) {
    $thumb_src = get_file_path($image_row['image_thumb_file'], "thumb", $image_row['cat_id'], 1);
    show_image_row($lang['thumb']."<br /><span class=\"smalltext\">(".$image_row['image_thumb_file'].")</span>", $thumb_src, 1, "delete_thumb_file");
  }
  else {
    $file_type = get_file_extension($image_row['image_media_file']);
    show_image_row($lang['thumb']."<br /><span class=smalltext>(".$lang['no_thumb_found'].")</span>", ICON_PATH."/".$file_type.".gif", 1);
  }

  $value = (is_remote($image_row['image_thumb_file']) || is_local_file($image_row['image_thumb_file'])) ? $image_row['image_thumb_file'] : "";
  show_upload_row($lang['thumb_file'], "thumb_file", "<br /><span class=\"smalltext\">".$lang['allowed_mediatypes_desc']." jpg, gif, png</span>", $value);

  show_input_row($lang['field_download_url'].$lang['download_url_desc'], "image_download_url", $image_row['image_download_url'], $textinput_size);

  $title = $lang['field_image_name'].((isset($file_src)) ? get_iptc_insert_link($file_src, "object_name", "image_name", 0) : "");
  show_input_row($title, "image_name", $image_row['image_name'], $textinput_size);

  $title = $lang['field_description_ext'].((isset($file_src)) ? get_iptc_insert_link($file_src, "caption", "image_description") : "");
  show_textarea_row($title, "image_description", $image_row['image_description'], $textarea_size);

  $title = $lang['field_keywords_ext'].((isset($file_src)) ? get_iptc_insert_link($file_src, "keyword", "image_keywords") : "");
  show_textarea_row($title, "image_keywords", $image_row['image_keywords'], $textarea_size);

  show_cat_select_row($lang['field_category'], $image_row['cat_id'], 3);
  show_user_select_row($lang['user'], $image_row['user_id']);

  $title = $lang['field_date'].$lang['date_desc'].$lang['date_format'].((isset($file_src)) ? get_iptc_insert_link($file_src, "date_created", "image_date", 0) : "");
  show_date_input_row($title, "image_date", $image_row['image_date'], $textinput_size);

  show_radio_row($lang['field_free'], "image_active", $image_row['image_active']);
  show_radio_row($lang['field_allow_comments'], "image_allow_comments", $image_row['image_allow_comments']);
  show_input_row($lang['field_downloads'], "image_downloads", $image_row['image_downloads'], 10);
  show_input_row($lang['field_votes'], "image_votes", $image_row['image_votes'], 10);
  show_input_row($lang['field_rating'], "image_rating", $image_row['image_rating'], 10);
  show_input_row($lang['field_hits'], "image_hits", $image_row['image_hits'], 10);
  show_additional_fields("image", $image_row, IMAGES_TABLE);
  show_form_footer($lang['save_changes'], $lang['reset'], 2, "", " onClick='showProgress()'");
}

if ($action == "saveimages") {
  $date = time();
  $ip = getenv("REMOTE_ADDR");
  $error_msg = "";
  $num_newimages = $HTTP_POST_VARS['num_newimages'];

  $error = array();
  for ($i = 1; $i <= $num_newimages; $i++) {
    $image_name = un_htmlspecialchars(trim($HTTP_POST_VARS['image_name_'.$i]));
    $cat_id = intval($HTTP_POST_VARS['cat_id_'.$i]);
    $user_id = (intval($HTTP_POST_VARS['user_id_'.$i]) != 0) ? intval($HTTP_POST_VARS['user_id_'.$i]) : $user_info['user_id'];
    $remote_file = trim($HTTP_POST_VARS['remote_file_'.$i]);
    $remote_thumb_file = trim($HTTP_POST_VARS['remote_thumb_file_'.$i]);
    $image_download_url = trim($HTTP_POST_VARS['image_download_url_'.$i]);

    if ($image_name == "") {
      $error['image_name_'.$i] = 1;
    }
    if ($cat_id == 0) {
      $error['cat_id_'.$i] = 1;
    }
    if (((empty($HTTP_POST_FILES['file_'.$i]['tmp_name']) || $HTTP_POST_FILES['file_'.$i]['tmp_name'] == "none") && $remote_file == "") || ($remote_file != "" && !check_remote_media($remote_file) && !check_local_media($remote_file))) {
      $error['file_'.$i] = 1;
    }
    if ($remote_thumb_file != "" && !check_remote_thumb($remote_thumb_file) && !check_local_thumb($remote_thumb_file)) {
      $error['remote_thumb_file_'.$i] = 1;
    }
    if ($image_download_url != "" && !is_remote($image_download_url) && !is_local_file($image_download_url)) {
      $error['image_download_url_'.$i] = 1;
    }

    if (!empty($additional_image_fields)) {
      foreach ($additional_image_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key.'_'.$i]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key.'_'.$i]) == "") {
          $error[$key.'_'.$i] = 1;
        }
      }
    }
  }

  if (empty($error)) {
    for ($i = 1; $i <= $num_newimages; $i++) {
      $log = array();
      $uploaderror = 0;
      $image_name = un_htmlspecialchars(trim($HTTP_POST_VARS['image_name_'.$i]));
      $cat_id = intval($HTTP_POST_VARS['cat_id_'.$i]);
      $remote_file = trim($HTTP_POST_VARS['remote_file_'.$i]);
      $remote_thumb_file = trim($HTTP_POST_VARS['remote_thumb_file_'.$i]);

      //Upload Image
      $file = "file_".$i;
      $remote_file = trim($HTTP_POST_VARS['remote_file_'.$i]);
      if (!empty($HTTP_POST_FILES[$file]['tmp_name']) && $HTTP_POST_FILES[$file]['tmp_name'] != "none") {
        $new_name = $site_upload->upload_file($file, "media", $cat_id);
        if (!$new_name) {
          $log[] = "<b>".$lang['file_upload_error'].": ".$HTTP_POST_FILES[$file]['name']."</b><br />".$site_upload->get_upload_errors();
          $uploaderror = 1;
        }
        else {
          $log[] = "<b>".$lang['file_upload_success'].": ".$new_name."</b>";
        }
      }
      else {
        $new_name = $remote_file;
      }

      //Upload Thumbnail if exists
      $thumb_file = "thumb_file_".$i;
      $remote_thumb_file = trim($HTTP_POST_VARS['remote_thumb_file_'.$i]);
      $new_thumb_name = "";
      if (!empty($HTTP_POST_FILES[$thumb_file]['tmp_name']) && $HTTP_POST_FILES[$thumb_file]['tmp_name'] != "none" && !$uploaderror) {
        $new_thumb_name = $site_upload->upload_file($thumb_file, "thumb", $cat_id, get_basefile($new_name));
        if (!$new_thumb_name) {
          $log[] = "<b>".$lang['thumb_upload_error'].": ".$HTTP_POST_FILES[$thumb_file]['name']."</b><br />".$site_upload->get_upload_errors();
          @unlink(MEDIA_PATH."/".$cat_id."/".$new_name);
          $log[] = $lang['error_image_deleted'];
          $uploaderror = 1;
        }
        else {
          $log[] = "<b>".$lang['thumb_upload_success'].": ".$new_thumb_name."</b>";
        }
      }
      else {
        $new_thumb_name = $remote_thumb_file;
      }

      //Save to Database
      if (!$uploaderror) {
        $image_description = un_htmlspecialchars(trim($HTTP_POST_VARS['image_description_'.$i]));

        $image_keywords = un_htmlspecialchars(trim($HTTP_POST_VARS['image_keywords_'.$i]));
        $image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
        $image_keywords_arr = explode(',', $image_keywords);
        array_walk($image_keywords_arr, 'trim_value');
        $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

        $image_active = trim($HTTP_POST_VARS['image_active_'.$i]);
        $image_allow_comments = trim($HTTP_POST_VARS['image_allow_comments_'.$i]);

        $image_download_url = trim($HTTP_POST_VARS['image_download_url_'.$i]);

        $additional_field_sql = "";
        $additional_value_sql = "";
        if (!empty($additional_image_fields)) {
          $table_fields = $site_db->get_table_fields(IMAGES_TABLE);
          foreach ($additional_image_fields as $key => $val) {
            if (isset($HTTP_POST_VARS[$key.'_'.$i]) && isset($table_fields[$key])) {
              $additional_field_sql .= ", $key";
              $additional_value_sql .= ", '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key.'_'.$i]))."'";
            }
          }
        }

        $current_time = time();
        $sql = "INSERT INTO ".IMAGES_TABLE."
                (cat_id, user_id, image_name, image_description, image_keywords, image_date, image_active, image_media_file, image_thumb_file, image_download_url, image_allow_comments".$additional_field_sql.")
                VALUES
                ($cat_id, $user_id, '$image_name', '$image_description', '$image_keywords', $current_time, $image_active, '$new_name', '$new_thumb_name', '$image_download_url', $image_allow_comments".$additional_value_sql.")";
        $result = $site_db->query($sql);
        $image_id = $site_db->get_insert_id();

        if ($result) {
          $search_words = array();
          foreach ($search_match_fields as $image_column => $match_column) {
            if (isset($HTTP_POST_VARS[$image_column.'_'.$i])) {
              $search_words[$image_column] = stripslashes($HTTP_POST_VARS[$image_column.'_'.$i]);
            }
          }
          add_searchwords($image_id, $search_words);
          $log[] = $lang['image_add_success'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$new_name.")";
        }
        else {
          $log[] = $lang['image_add_error'].": <b>".format_text(stripslashes($image_name), 2)."</b> (".$new_name.")";
        }
      }
      else {
        $log[] = $lang['no_db_entry'];
      }
      echo "<script language=javascript>\n showProgress();\n hideProgress();\n</script>";
      show_table_header($lang['image']." $i", 1);
      echo "<tr><td class=\"tablerow\">\n";
      echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
      foreach ($log as $val) {
        echo $val."<br />";
      }
      echo "</td></tr></table>\n";
      echo "</td></tr>\n";
      show_table_footer();
      echo "<br />";
    }
  }
  else {
    echo "<script language=javascript>\n showProgress();\n hideProgress();\n</script>";
    $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "addimages";
  }
}

if ($action == "addimages") {
  if (isset($HTTP_GET_VARS['num_newimages']) || isset($HTTP_POST_VARS['num_newimages'])) {
    $num_newimages = (isset($HTTP_GET_VARS['num_newimages'])) ? intval($HTTP_GET_VARS['num_newimages']) : intval($HTTP_POST_VARS['num_newimages']);
  }
  else {
    $num_newimages = 1;
  }

  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }

  printf("<p>%s</p>\n", $lang['upload_note']);

  show_form_header("images.php", "saveimages", "form", 1);
  show_table_header($lang['nav_images_add'], 2);
  show_num_select_row("&nbsp;", "num_newimages", $lang['num_addnewimages_desc']);

  for ($i = 1; $i <= $num_newimages; $i++) {
    show_table_separator($lang['image']." ".$i, 2);
    show_upload_row($lang['image'], "file_".$i, "<br /><span class=smalltext>".$lang['allowed_mediatypes_desc'].str_replace(",",", ",$config['allowed_mediatypes'])."</span>");
    show_upload_row($lang['thumb'], "thumb_file_".$i, "<br /><span class=smalltext>".$lang['allowed_mediatypes_desc']." jpg, gif, png</span>");
    show_input_row($lang['field_download_url'].$lang['download_url_desc'], "image_download_url_".$i, "", $textinput_size);
    show_input_row($lang['field_image_name'], "image_name_".$i, "", $textinput_size);
    show_textarea_row($lang['field_description_ext'],"image_description_".$i, "", $textarea_size);
    show_textarea_row($lang['field_keywords_ext'], "image_keywords_".$i, "", $textarea_size);
    show_cat_select_row($lang['field_category'], 0, 3, $i);
    show_user_select_row($lang['user'], $user_info['user_id'], $i);
    show_radio_row($lang['field_free'], "image_active_".$i, 1);
    show_radio_row($lang['field_allow_comments'], "image_allow_comments_".$i, 1);
    show_additional_fields("image", array(), IMAGES_TABLE, $i);
  }
  show_hidden_input("num_newimages", $num_newimages);
  show_form_footer($lang['add'], $lang['reset'], 2, "", " onClick='showProgress()'");
}

if ($action == "modifyimages") {
  show_form_header("images.php", "findimages", "form");
  show_table_header($lang['nav_images_edit'], 2);
  show_input_row($lang['field_image_id_contains'], "image_id", "", $textinput_size);
  show_input_row($lang['field_image_name_contains'], "image_name", "", $textinput_size);
  show_input_row($lang['field_description_contains'], "image_description", "", $textinput_size);
  show_input_row($lang['field_keywords_contains'], "image_keywords", "", $textinput_size);
  show_cat_select_row($lang['field_category'], 0, 2);
  show_input_row($lang['field_image_file_contains'], "image_media_file", "", $textinput_size);
  show_input_row($lang['field_thumb_file_contains'], "image_thumb_file", "", $textinput_size);
  show_date_input_row($lang['field_date_after'].$lang['date_format'], "dateafter", "", $textinput_size);
  show_date_input_row($lang['field_date_before'].$lang['date_format'], "datebefore", "", $textinput_size);
  show_input_row($lang['field_downloads_upper'], "downloadsupper", "", $textinput_size);
  show_input_row($lang['field_downloads_lower'], "downloadslower", "", $textinput_size);
  show_input_row($lang['field_rating_upper'], "ratingupper", "", $textinput_size);
  show_input_row($lang['field_rating_lower'], "ratinglower", "", $textinput_size);
  show_input_row($lang['field_votes_upper'], "votesupper", "", $textinput_size);
  show_input_row($lang['field_votes_lower'], "voteslower", "", $textinput_size);
  show_input_row($lang['field_hits_upper'], "hitsupper", "", $textinput_size);
  show_input_row($lang['field_hits_lower'], "hitslower", "", $textinput_size);
  show_table_separator($lang['sort_options'], 2);
  ?>
  <tr class="<?php echo get_row_bg(); ?>"><td><p><b><?php echo $lang['order_by'] ?></b></p></td><td><p>
  <select name="orderby">
  <?php foreach ($orderbyOptions as $field => $label): ?>
  <option value="<?php echo $field; ?>"><?php echo $label; ?></option>
  <?php endforeach; ?>
  </select>
  <select name="direction">
  <option selected value="ASC"><?php echo $lang['asc'] ?></option>
  <option value="DESC"><?php echo $lang['desc'] ?></option>
  </select>
  </p></td></tr>
  <?php
  show_input_row($lang['results_per_page'], "limitnumber", 50);
  show_form_footer($lang['search'], $lang['reset'], 2);
}

if ($action == "findimages") {
  $site_sess->set_session_var('back_url', $self_url);

	$condition = "1=1";

  if (array_key_exists('image_id', $HTTP_POST_VARS) && is_numeric($HTTP_POST_VARS['image_id'])) {
      $image_id = intval($HTTP_POST_VARS['image_id']);

      $condition .= " AND INSTR(LCASE(i.image_id),'$image_id')>0";
  } else {
      $image_id = '';
  }
  $image_name = trim($HTTP_POST_VARS['image_name']);
  if ($image_name != "") {
    $condition .= " AND INSTR(LCASE(i.image_name),'".strtolower($image_name)."')>0";
  }
  $image_description = trim($HTTP_POST_VARS['image_description']);
  if ($image_description != "") {
    $condition .= " AND INSTR(LCASE(i.image_description),'".strtolower($image_description)."')>0";
  }
  $image_keywords = trim($HTTP_POST_VARS['image_keywords']);
  if ($image_keywords != "") {
    $condition .= " AND INSTR(LCASE(i.image_keywords),'".strtolower($image_keywords)."')>0";
  }
  $cat_id = intval(trim($HTTP_POST_VARS['cat_id']));
  if ($cat_id != 0 && $cat_id != "") {
    $condition .= " AND i.cat_id = '$cat_id'";
  }
  $image_media_file = trim($HTTP_POST_VARS['image_media_file']);
  if ($image_media_file != "") {
    $condition .= " AND INSTR(LCASE(i.image_media_file),'".strtolower($image_media_file)."')>0";
  }
  $image_thumb_file = trim($HTTP_POST_VARS['image_thumb_file']);
  if ($image_thumb_file != "") {
    $condition .= " AND INSTR(LCASE(i.image_thumb_file),'".strtolower($image_thumb_file)."')>0";
  }
  $dateafter = trim($HTTP_POST_VARS['dateafter']);
  if ($dateafter != "") {
    $condition .= " AND i.image_date > UNIX_TIMESTAMP('$dateafter')";
  }
  $datebefore = trim($HTTP_POST_VARS['datebefore']);
  if ($datebefore != "") {
    $condition .= " AND i.image_date < UNIX_TIMESTAMP('$datebefore')";
  }
  $downloadslower = trim($HTTP_POST_VARS['downloadslower']);
  if ($downloadslower != "") {
    $condition .= " AND i.image_downloads < '$downloadslower'";
  }
  $downloadsupper = trim($HTTP_POST_VARS['downloadsupper']);
  if ($downloadsupper != "") {
    $condition .= " AND i.image_downloads > '$downloadsupper'";
  }
  $ratinglower = trim($HTTP_POST_VARS['ratinglower']);
  if ($ratinglower != "") {
    $condition .= " AND i.image_rating < '$ratinglower'";
  }
  $ratingupper = trim($HTTP_POST_VARS['ratingupper']);
  if ($ratingupper != "") {
    $condition .= " AND i.image_rating > '$ratingupper'";
  }
  $voteslower = trim($HTTP_POST_VARS['voteslower']);
  if ($voteslower != "") {
    $condition .= " AND i.image_votes < '$voteslower'";
  }
  $votesupper = trim($HTTP_POST_VARS['votesupper']);
  if ($votesupper != "") {
    $condition .= " AND i.image_votes > '$votesupper'";
  }
  $hitslower = trim($HTTP_POST_VARS['hitslower']);
  if ($hitslower != "") {
    $condition .= " AND i.image_hits < '$hitslower'";
  }
  $hitsupper = trim($HTTP_POST_VARS['hitsupper']);
  if ($hitsupper != "") {
    $condition .= " AND i.image_hits > '$hitsupper'";
  }
  $orderby = trim($HTTP_POST_VARS['orderby']);
  if (!isset($orderbyOptions[$orderby])) {
    $orderby = "i.image_name";
  }

  $limitstart = (isset($HTTP_POST_VARS['limitstart'])) ? trim($HTTP_POST_VARS['limitstart']) : "";
  if ($limitstart == "" || !is_numeric($limitstart)) {
    $limitstart = 0;
  }
  else {
    $limitstart--;
  }
  $limitnumber = trim($HTTP_POST_VARS['limitnumber']);
  if ($limitnumber == "" || !is_numeric($limitnumber)) {
    $limitnumber = 5000;
  }

  $direction = "ASC";
  if (isset($HTTP_GET_VARS['direction']) || isset($HTTP_POST_VARS['direction'])) {
    $requestedDirection = (isset($HTTP_GET_VARS['direction'])) ? trim($HTTP_GET_VARS['direction']) : trim($HTTP_POST_VARS['direction']);

    if ('DESC' === $requestedDirection) {
      $direction = "DESC";
    }
  }

  $sql = "SELECT COUNT(*) AS images
          FROM ".IMAGES_TABLE." i
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
          WHERE ".$condition;
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

  show_form_header("images.php", "removeimage", "form");
  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  if ($countimages['images'] > 0) {
    echo "<tr class=\"tableseparator\">\n";
    echo "<td class=\"tableseparator\"><input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\" /></td>\n";
    echo "<td class=\"tableseparator\">".$lang['field_image_name']."</td>\n<td class=\"tableseparator\">".$lang['field_category']."</td>\n<td class=\"tableseparator\">".$lang['field_username']."</td>\n<td class=\"tableseparator\">".$lang['field_date']."</td>\n<td class=\"tableseparator\">".$lang['options']."</td>\n</tr>\n";

    $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_media_file, i.image_date".get_user_table_field(", u.", "user_name")."
            FROM ".IMAGES_TABLE." i
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE $condition
            ORDER BY $orderby $direction
            LIMIT $limitstart, $limitnumber";
    $result = $site_db->query($sql);

    while ($image_row = $site_db->fetch_array($result)) {
      echo "<tr class=\"".get_row_bg()."\">";
      echo "<td><input type=\"checkbox\" name=\"deleteimages[]\" value=\"".$image_row['image_id']."\" /></td>";
      echo "<td><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_row['image_id'])."\" target=_blank>".format_text($image_row['image_name'], 2)."</a></b> (".$image_row['image_media_file'];

      if (!get_file_path($image_row['image_media_file'], "media", $image_row['cat_id'], 1, 0)) {
        echo " <b class=\"marktext\">!</b>";
      }
      echo ")</td>\n";
      echo "<td><a href=\"".$site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$image_row['cat_id'])."\" target=\"_blank\">".format_text($cat_cache[$image_row['cat_id']]['cat_name'], 2)."</a></td>\n";
      $show_user_name = format_text($image_row[$user_table_fields['user_name']], 2);
      if ($image_row['user_id'] != GUEST && empty($url_show_profile)) {
        $show_user_name = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&".URL_USER_ID."=".$image_row['user_id'])."\" target=\"_blank\">$show_user_name</a>";
      }
      echo "<td>".$show_user_name."</a></td>\n";
      echo "<td>".format_date($config['date_format'], $image_row['image_date'])."</td>\n";
      echo "<td><p>";
      show_text_link($lang['edit'],"images.php?action=editimage&image_id=".$image_row['image_id']);
      show_text_link($lang['delete'],"images.php?action=removeimage&image_id=".$image_row['image_id']);
      echo "</p></td>\n";
      echo "</tr>\n";
    }

    echo "<tr class=\"tablefooter\">\n<td colspan=\"6\" align=\"left\">\n&nbsp;";
    echo "<input type=\"submit\" value=\"  ".$lang['delete']."   \" class=\"button\">\n";
    echo "&nbsp;\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n</form>\n";
  }
  else {
    show_description_row($lang['no_search_results'], 6);
    show_form_footer("", "");
  }

  echo "<div align=\"right\">";
  echo "<form action=\"".$site_sess->url("images.php")."\" name=\"form2\" method=\"post\">\n";

  //if ($limitnumber != 5000 && $limitfinish < $countimages['images']) {
    show_hidden_input("action", "findimages");
    show_hidden_input("image_id", $image_id);
    show_hidden_input("image_name", $image_name, 1);
    show_hidden_input("image_description", $image_description, 1);
    show_hidden_input("image_keywords", $image_keywords, 1);
    show_hidden_input("cat_id", $cat_id);
    show_hidden_input("image_media_file", $image_media_file, 1);
    show_hidden_input("image_thumb_file", $image_thumb_file, 1);
    show_hidden_input("dateafter", $dateafter);
    show_hidden_input("datebefore", $datebefore);
    show_hidden_input("downloadsupper", $downloadsupper);
    show_hidden_input("downloadslower", $downloadslower);
    show_hidden_input("ratingupper", $ratingupper);
    show_hidden_input("ratinglower", $ratinglower);
    show_hidden_input("votesupper", $votesupper);
    show_hidden_input("voteslower", $voteslower);
    show_hidden_input("hitsupper", $hitsupper);
    show_hidden_input("hitslower", $hitslower);

    show_hidden_input("orderby", $orderby, 1);
    show_hidden_input("direction", $direction, 1);
    show_hidden_input("limitstart", $limitstart + $limitnumber + 1);
    show_hidden_input("limitnumber", $limitnumber);

  if ($limitstart > 1) {
    echo "<input type=\"button\" value=\"   ".$lang['back']."   \" onclick=\"limitstart.value=limitstart.value-limitnumber.value*2;submit();\" class=\"button\">\n";
  }

  if ($limitnumber != 5000 && $limitfinish < $countimages['images']) {
    echo "<input type=\"submit\" value=\"   ".$lang['search_next_page']."   \" class=\"button\">\n";
  }
  //echo "<input type=\"button\" value=\"   ".$lang['back']."   \" onclick=\"history.go(-1)\" class=\"button\">\n";
  echo "</form>";
  echo "</div>";
}

show_admin_footer();
?>