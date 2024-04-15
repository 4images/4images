<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: member.php                                           *
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

$main_template = "member";

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();
include(ROOT_PATH.'includes/page_header.php');

if ($action == "") {
  $action = "lostpassword";
}
$content = "";
$txt_clickstream = "";

$sendprocess = 0;

if (isset($HTTP_GET_VARS[URL_COMMENT_ID]) || isset($HTTP_POST_VARS[URL_COMMENT_ID])) {
  $comment_id = (isset($HTTP_GET_VARS[URL_COMMENT_ID])) ? intval($HTTP_GET_VARS[URL_COMMENT_ID]) : intval($HTTP_POST_VARS[URL_COMMENT_ID]);
}
else {
  $comment_id = 0;
}

if ($action == "deletecomment") {
  if (!$comment_id || ($config['user_delete_comments'] != 1 && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $sql = "SELECT c.comment_id, c.user_id AS comment_user_id, i.image_id, i.cat_id, i.user_id, i.image_name
          FROM (".COMMENTS_TABLE." c, ".IMAGES_TABLE." i)
          WHERE c.comment_id = $comment_id AND i.image_id = c.image_id";
  $comment_row = $site_db->query_firstrow($sql);
  if (!$comment_row || $comment_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $comment_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = get_category_path($comment_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$comment_row['image_id'])."\" class=\"clickstream\">".format_text($comment_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['comment_delete'];

  $sql = "UPDATE ".IMAGES_TABLE."
          SET image_comments = image_comments - 1
          WHERE image_id = ".$comment_row['image_id'];
  $site_db->query($sql);

  if ($comment_row['comment_user_id'] != GUEST) {
    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_comments")." = ".get_user_table_field("", "user_comments")." - 1
            WHERE ".get_user_table_field("", "user_id")." = ".$comment_row['comment_user_id'];
    $site_db->query($sql);
  }

  $sql = "DELETE FROM ".COMMENTS_TABLE."
          WHERE comment_id = $comment_id";
  $result = $site_db->query($sql);
  $msg = ($result) ? $lang['comment_delete_success'] : $lang['comment_delete_error'];
}

if ($action == "removecomment") {
  if (!$comment_id || ($config['user_delete_comments'] != 1 && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }

  $sql = "SELECT c.comment_id, c.image_id, c.user_id AS comment_user_id, c.user_name AS comment_user_name, c.comment_headline, c.comment_text, i.image_name, i.cat_id, i.user_id".get_user_table_field(", u.", "user_name")."
          FROM (".COMMENTS_TABLE." c, ".IMAGES_TABLE." i)
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = c.user_id)
          WHERE c.comment_id = $comment_id AND i.image_id = c.image_id";
  $comment_row = $site_db->query_firstrow($sql);
  if (!$comment_row || $comment_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $comment_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }

  $txt_clickstream = get_category_path($comment_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$comment_row['image_id'])."\" class=\"clickstream\">".format_text($comment_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['comment_delete'];

  if (isset($comment_row[$user_table_fields['user_name']]) && $comment_row['comment_user_id'] != GUEST) {
    $user_name = $comment_row[$user_table_fields['user_name']];
  }
  else {
    $user_name = $comment_row['comment_user_name'];
  }

  $site_template->register_vars(array(
    "comment_id" => $comment_id,
    "image_name" => format_text($comment_row['image_name']),
    "user_name" => format_text($user_name),
    "comment_headline" => format_text($comment_row['comment_headline'], 0, $config['wordwrap_comments'], 0, 0),
    "comment_text" => format_text($comment_row['comment_text'], $config['html_comments'], $config['wordwrap_comments'], $config['bb_comments'], $config['bb_img_comments']),
    "lang_delete_comment" => $lang['comment_delete'],
    "lang_delete_comment_confirm" => $lang['comment_delete_confirm'],
    "lang_image_name" => $lang['image_name'],
    "lang_name" => $lang['name'],
    "lang_headline" => $lang['headline'],
    "lang_comment" => $lang['comment'],
    "lang_submit" => $lang['submit'],
    "lang_reset" => $lang['reset'],
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no']
  ));
  $content = $site_template->parse_template("member_deletecomment");
}

if ($action == "updatecomment") {
  if (!$comment_id || ($config['user_edit_comments'] != 1 && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $sql = "SELECT c.comment_id, c.image_id, i.image_name, i.cat_id, i.user_id".get_user_table_field(", u.", "user_name")."
          FROM (".COMMENTS_TABLE." c, ".IMAGES_TABLE." i)
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = c.user_id)
          WHERE c.comment_id = $comment_id AND i.image_id = c.image_id";
  $comment_row = $site_db->query_firstrow($sql);
  if (!$comment_row || $comment_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $comment_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = get_category_path($comment_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$comment_row['image_id'])."\" class=\"clickstream\">".format_text($comment_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['comment_edit'];

  $error = 0;

  $comment_headline = un_htmlspecialchars(trim($HTTP_POST_VARS['comment_headline']));
  $comment_text = un_htmlspecialchars(trim($HTTP_POST_VARS['comment_text']));

  if ($comment_headline == "")  {
    $error = 1;
    $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['headline']), $lang['field_required']);
    $msg .= (($msg != "") ? "<br />" : "").$field_error;
  }
  if ($comment_text == "")  {
    $error = 1;
    $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['comment']), $lang['field_required']);
    $msg .= (($msg != "") ? "<br />" : "").$field_error;
  }

  if (!$error) {
    $sql = "UPDATE ".COMMENTS_TABLE."
            SET comment_headline = '$comment_headline', comment_text = '$comment_text'
            WHERE comment_id = $comment_id";
    $result = $site_db->query($sql);
    $msg = ($result) ? $lang['comment_edit_success'] : $lang['comment_edit_error'];
  }
  else {
    $action = "editcomment";
    $sendprocess = 1;
  }
}

if ($action == "editcomment") {
  if (!$comment_id || ($config['user_edit_comments'] != 1 && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }

  $sql = "SELECT c.comment_id, c.image_id, c.user_id AS comment_user_id, c.user_name AS comment_user_name, c.comment_headline, c.comment_text, i.image_name, i.cat_id, i.user_id".get_user_table_field(", u.", "user_name")."
          FROM (".COMMENTS_TABLE." c, ".IMAGES_TABLE." i)
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = c.user_id)
          WHERE c.comment_id = $comment_id AND i.image_id = c.image_id";
  $comment_row = $site_db->query_firstrow($sql);
  if (!$comment_row || $comment_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $comment_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    header("Location: ".$site_sess->url($url, "&"));
    exit;
  }

  $txt_clickstream = get_category_path($comment_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$comment_row['image_id'])."\" class=\"clickstream\">".format_text($comment_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['comment_edit'];

  $comment_headline = (isset($HTTP_POST_VARS['comment_headline'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['comment_headline']))) : $comment_row['comment_headline'];
  $comment_text = (isset($HTTP_POST_VARS['comment_text'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['comment_text']))) : $comment_row['comment_text'];

  if (isset($comment_row[$user_table_fields['user_name']]) && $comment_row['comment_user_id'] != GUEST) {
    $user_name = $comment_row[$user_table_fields['user_name']];
  }
  else {
    $user_name = $comment_row['comment_user_name'];
  }

  $bbcode = "";
  if ($config['bb_comments'] == 1) {
    $site_template->register_vars(array(
      "lang_bbcode" => $lang['bbcode'],
      "lang_tag_prompt" => $lang['tag_prompt'],
      "lang_link_text_prompt" => $lang['link_text_prompt'],
      "lang_link_url_prompt" => $lang['link_url_prompt'],
      "lang_link_email_prompt" => $lang['link_email_prompt'],
      "lang_list_type_prompt" => $lang['list_type_prompt'],
      "lang_list_item_prompt" => $lang['list_item_prompt']
    ));
    $bbcode = $site_template->parse_template("bbcode");
  }

  $site_template->register_vars(array(
    "bbcode" => $bbcode,
    "comment_id" => $comment_id,
    "image_name" => format_text($comment_row['image_name'], 2),
    "user_name" => format_text($user_name, 2),
    "comment_headline" => format_text($comment_headline, 2),
    "comment_text" => format_text($comment_text, 2),
    "lang_edit_comment" => $lang['comment_edit'],
    "lang_image_name" => $lang['image_name'],
    "lang_name" => $lang['name'],
    "lang_headline" => $lang['headline'],
    "lang_comment" => $lang['comment'],
    "lang_submit" => $lang['submit'],
    "lang_reset" => $lang['reset'],
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no']
  ));
  $content = $site_template->parse_template("member_editcomment");
}

if ($action == "deleteimage") {
  if (!$image_id || ($config['user_delete_image'] != 1 && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $sql = "SELECT image_id, cat_id, user_id, image_name, image_media_file, image_thumb_file
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (!$image_row || $image_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $image_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = $lang['image_delete'];

  $sql = "DELETE FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $del_img = $site_db->query($sql);

  if (!is_remote($image_row['image_media_file']) && !is_local_file($image_row['image_media_file'])) {
    @unlink(MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file']);
  }
  if (!empty($image_row['image_thumb_file']) && !is_remote($image_row['image_thumb_file']) && !is_local_file($image_row['image_thumb_file'])) {
    @unlink(THUMB_PATH."/".$image_row['cat_id']."/".$image_row['image_thumb_file']);
  }

  include(ROOT_PATH.'includes/search_utils.php');
  remove_searchwords($image_id);

  if (!empty($user_table_fields['user_comments'])) {
    $sql = "SELECT user_id
            FROM ".COMMENTS_TABLE."
            WHERE image_id = $image_id";
    $result = $site_db->query($sql);
    $user_id_sql = "";
    while ($row = $site_db->fetch_array($result)) {
      if ($row['user_id'] != GUEST) {
        $sql = "UPDATE ".USERS_TABLE."
                SET ".get_user_table_field("", "user_comments")." = ".get_user_table_field("", "user_comments")." - 1
                WHERE ".get_user_table_field("", "user_id")." = ".$row['user_id'];
        $site_db->query($sql);
      }
    }
  }

  $sql = "DELETE FROM ".COMMENTS_TABLE."
          WHERE image_id = $image_id";
  $del_com = $site_db->query($sql);

  if ($del_img) {
    $msg = $lang['image_delete_success'];
  }
  else {
    $msg = $lang['image_delete_error'];
  }
}

if ($action == "removeimage") {
  if (!$image_id || ($config['user_delete_image'] != 1 && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }
  $sql = "SELECT image_id, cat_id, user_id, image_name
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (!$image_row || $image_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $image_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = get_category_path($image_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id)."\" class=\"clickstream\">".format_text($image_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['image_delete'];

  $site_template->register_vars(array(
    "image_id" => $image_id,
    "image_name" => format_text($image_row['image_name'], 2),
    "lang_delete_image" => $lang['image_delete'],
    "lang_delete_image_confirm" => $lang['image_delete_confirm'],
    "lang_submit" => $lang['submit'],
    "lang_reset" => $lang['reset'],
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no']
  ));
  $content = $site_template->parse_template("member_deleteimage");
}

if ($action == "updateimage") {
  if (!$image_id || ($config['user_edit_image'] != 1 && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
  }
  $sql = "SELECT image_id, cat_id, user_id, image_name
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (!$image_row || $image_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $image_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = get_category_path($image_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id)."\" class=\"clickstream\">".format_text($image_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['image_edit'];

  $error = 0;

  $image_name = un_htmlspecialchars(trim($HTTP_POST_VARS['image_name']));
  $image_description = un_htmlspecialchars(trim($HTTP_POST_VARS['image_description']));
  $image_keywords = un_htmlspecialchars(trim($HTTP_POST_VARS['image_keywords']));

  $image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
  $image_keywords_arr = explode(',', $image_keywords);
  array_walk($image_keywords_arr, 'trim_value');
  $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

  if ($image_name == "")  {
    $error = 1;
    $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['image_name']), $lang['field_required']);
    $msg .= (($msg != "") ? "<br />" : "").$field_error;
  }

  if (!empty($additional_image_fields)) {
    foreach ($additional_image_fields as $key => $val) {
      if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
        $error = 1;
        $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $val[0]), $lang['field_required']);
        $msg .= (($msg != "") ? "<br />" : "").$field_error;
      }
    }
  }

  if (!$error) {
    $additional_sql = "";

    if (isset($HTTP_POST_VARS['image_allow_comments'])) {
      $additional_sql .= ", image_allow_comments = ".intval($HTTP_POST_VARS['image_allow_comments']);
    }

    if (!empty($additional_image_fields)) {
      $table_fields = $site_db->get_table_fields(IMAGES_TABLE);
      foreach ($additional_image_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
          $additional_sql .= ", $key = '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
        }
      }
    }

    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_name = '$image_name', image_description = '$image_description', image_keywords = '$image_keywords'".$additional_sql."
            WHERE image_id = $image_id";
    $result = $site_db->query($sql);
    if ($result) {
      include(ROOT_PATH.'includes/search_utils.php');
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
    $action = "editimage";
    $sendprocess = 1;
  }
}

if ($action == "editimage") {
  if (!$image_id || ($config['user_edit_image'] != 1 && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }

  $additional_sql = "";
  if (!empty($additional_image_fields)) {
    foreach ($additional_image_fields as $key => $val) {
      $additional_sql .= ", ".$key;
    }
  }
  $sql = "SELECT image_id, cat_id, user_id, image_name, image_description, image_keywords, image_allow_comments".$additional_sql."
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (!$image_row || $image_row['user_id'] <= USER_AWAITING || ($user_info['user_id'] != $image_row['user_id'] && $user_info['user_level'] != ADMIN)) {
    redirect($url);
  }

  $txt_clickstream = get_category_path($image_row['cat_id'], 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id)."\" class=\"clickstream\">".format_text($image_row['image_name'], 2)."</a>".$config['category_separator'];
  $txt_clickstream .= $lang['image_edit'];

  $image_name = (isset($HTTP_POST_VARS['image_name'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['image_name']))) : $image_row['image_name'];
  $image_description = (isset($HTTP_POST_VARS['image_description'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['image_description']))) : $image_row['image_description'];
  $image_keywords = (isset($HTTP_POST_VARS['image_keywords'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['image_keywords']))) : $image_row['image_keywords'];
  $image_allow_comments = (isset($HTTP_POST_VARS['image_allow_comments'])) ? intval($HTTP_POST_VARS['image_allow_comments']) : $image_row['image_allow_comments'];

  $site_template->register_vars(array(
    "image_id" => $image_id,
    "image_name" => format_text($image_name, 2),
    "image_description" => format_text($image_description, 2),
    "image_keywords" => format_text($image_keywords, 2),
    "image_allow_comments_yes" => ($image_allow_comments) ? " checked=\"checked\"" : "",
    "image_allow_comments_no" => (!$image_allow_comments) ? " checked=\"checked\"" : "",
    "lang_edit_image" => $lang['image_edit'],
    "lang_image_name" => $lang['image_name'],
    "lang_description" => $lang['description'],
    "lang_keywords" => $lang['keywords_ext'],
    "lang_allow_comments" => isset($lang['allow_comments']) ? $lang['allow_comments'] : "",
    "lang_submit" => $lang['submit'],
    "lang_reset" => $lang['reset'],
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no']
  ));

  if (!empty($additional_image_fields)) {
    $additional_field_array = array();
    foreach ($additional_image_fields as $key => $val) {
      if ($val[1] == "radio") {
        $value = (isset($HTTP_POST_VARS[$key])) ? intval($HTTP_POST_VARS[$key]) : $image_row[$key];
        if ($value == 1) {
          $additional_field_array[$key.'_yes'] = " checked=\"checked\"";
          $additional_field_array[$key.'_no'] = "";
        }
        else {
          $additional_field_array[$key.'_yes'] = "";
          $additional_field_array[$key.'_no'] = " checked=\"checked\"";
        }
      }
      else {
        $value = (isset($HTTP_POST_VARS[$key])) ? format_text(stripslashes(trim($HTTP_POST_VARS[$key]))) : $image_row[$key];
      }
      $additional_field_array[$key] = $value;
      $additional_field_array['lang_'.$key] = $val[0];
    }
    if (!empty($additional_field_array)) {
      $site_template->register_vars($additional_field_array);
    }
  }
  $content = $site_template->parse_template("member_editimage");
}

if ($action == "uploadimage") {
  if ($cat_id != 0 && (!isset($cat_cache[$cat_id]) || !check_permission("auth_upload", $cat_id))) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = "";
  if ($cat_id && isset($cat_cache[$cat_id])) {
    $txt_clickstream .= get_category_path($cat_id, 1).$config['category_separator'];
  }
  $txt_clickstream .= $lang['user_upload'];

  $remote_media_file = format_url(un_htmlspecialchars(trim($HTTP_POST_VARS['remote_media_file'])));
  $remote_thumb_file = format_url(un_htmlspecialchars(trim($HTTP_POST_VARS['remote_thumb_file'])));

  $image_name = un_htmlspecialchars(trim($HTTP_POST_VARS['image_name']));
  $image_description = un_htmlspecialchars(trim($HTTP_POST_VARS['image_description']));
  $image_keywords = un_htmlspecialchars(trim($HTTP_POST_VARS['image_keywords']));

  $image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
  $image_keywords_arr = explode(',', $image_keywords);
  array_walk($image_keywords_arr, 'trim_value');
  $image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));

  $image_active = (isset($HTTP_POST_VARS['image_active']) && $HTTP_POST_VARS['image_active'] == 0) ? 0 : 1;
  $image_allow_comments = (isset($HTTP_POST_VARS['image_allow_comments']) && $HTTP_POST_VARS['image_allow_comments'] == 0) ? 0 : 1;
  $image_download_url = (isset($HTTP_POST_VARS['image_download_url'])) ? format_url(un_htmlspecialchars(trim($HTTP_POST_VARS['image_download_url']))) : "";

  $captcha = (isset($HTTP_POST_VARS['captcha'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['captcha'])) : "";

  $direct_upload = (check_permission("auth_directupload", $cat_id)) ? 1 : 0;
  $upload_cat = ($direct_upload) ? $cat_id : 0;

  $error = 0;
  $uploaderror = 0;

  if ($cat_id == 0)  {
    $error = 1;
    $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['category']), $lang['field_required']);
    $msg .= (($msg != "") ? "<br />" : "").$field_error;
  }
  if ((empty($HTTP_POST_FILES['media_file']['tmp_name']) || $HTTP_POST_FILES['media_file']['tmp_name'] == "none") && ($remote_media_file == "" || !check_remote_media($remote_media_file))) {
    $error = 1;
    $msg .= (($msg != "") ? "<br />" : "").$lang['image_file_required'];
  }
  if ($image_name == "")  {
    $error = 1;
    $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['image_name']), $lang['field_required']);
    $msg .= (($msg != "") ? "<br />" : "").$field_error;
  }

  if ($captcha_enable_upload && !captcha_validate($captcha)) {
    $msg .= (($msg != "") ? "<br />" : "").$lang['captcha_required'];
    $error = 1;
  }

  if (!empty($additional_image_fields)) {
    foreach ($additional_image_fields as $key => $val) {
      if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
        $error = 1;
        $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $val[0]), $lang['field_required']);
        $msg .= (($msg != "") ? "<br />" : "").$field_error;
      }
    }
  }

  if (!$error) {
    // Start Upload
    include(ROOT_PATH.'includes/upload.php');
    $site_upload = new Upload();

    // Upload Media file
    if (!empty($HTTP_POST_FILES['media_file']['tmp_name']) && $HTTP_POST_FILES['media_file']['tmp_name'] != "none") {
      $new_name = $site_upload->upload_file("media_file", "media", $upload_cat);
      if (!$new_name) {
        $msg .= (($msg != "") ? "<br />" : "")."<b>".$lang['file_upload_error'].": ".$new_name."</b><br />".$site_upload->get_upload_errors();
        $uploaderror = 1;
      }
    }
    else {
      $new_name = $remote_media_file;
    }

    // Upload thumb file
    $new_thumb_name = "";
    if (!empty($HTTP_POST_FILES['thumb_file']['tmp_name']) && $HTTP_POST_FILES['thumb_file']['tmp_name'] != "none" && !$uploaderror) {
      $new_thumb_name = $site_upload->upload_file("thumb_file", "thumb", $upload_cat, get_basefile($new_name));
      if (!$new_thumb_name) {
        $msg .= (($msg != "") ? "<br />" : "")."<b>".$lang['thumb_upload_error'].": ".$new_thumb_name."</b><br />".$site_upload->get_upload_errors();
        @unlink(MEDIA_TEMP_PATH."/".$new_name);
        $uploaderror = 1;
      }
    }
    elseif (check_remote_thumb($remote_thumb_file)) {
      $new_thumb_name = $remote_thumb_file;
    }
    elseif ($config['auto_thumbnail'] == 1 && !empty($new_name) && !$uploaderror && ((!empty($HTTP_POST_FILES['media_file']['tmp_name']) && $HTTP_POST_FILES['media_file']['tmp_name'] != "none") || is_remote($new_name))) {
      if ($direct_upload) {
        if (is_remote($new_name)) {
          $src = $new_name;
          $thumb = create_unique_filename(THUMB_PATH."/".$cat_id, filterFileName($new_name));
        } else {
          $src = MEDIA_PATH."/".$cat_id."/".$new_name;
          $thumb = $new_name;
        }
        $dest = THUMB_PATH."/".$cat_id."/".$thumb;
      }
      else {
        if (is_remote($new_name)) {
          $src = $new_name;
          $thumb = create_unique_filename(THUMB_TEMP_PATH, filterFileName($new_name));
        } else {
          $src = MEDIA_TEMP_PATH."/".$new_name;
          $thumb = $new_name;
        }
        $dest = THUMB_TEMP_PATH."/".$thumb;
      }
      $do_create = 0;
      if ($image_info = @getimagesize($src)) {
        if ($image_info[2] == 1 || $image_info[2] == 2 || $image_info[2] == 3) {
          $do_create = 1;
        }
      }
      if ($do_create) {
        require(ROOT_PATH.'includes/image_utils.php');
        $convert_options = init_convert_options();
        if (!$convert_options['convert_error']) {
          $dimension = (intval($config['auto_thumbnail_dimension'])) ? intval($config['auto_thumbnail_dimension']) : 100;
          $resize_type = (intval($config['auto_thumbnail_resize_type'])) ? intval($config['auto_thumbnail_resize_type']) : 1;
          $quality = (intval($config['auto_thumbnail_quality']) && intval($config['auto_thumbnail_quality']) <= 100) ? intval($config['auto_thumbnail_quality']) : 100;

          if (create_thumbnail($src, $dest, $quality, $dimension, $resize_type)) {
            $new_thumb_name = $thumb;
          }
        }
      }
    }

    if (!$uploaderror) {
      $additional_field_sql = "";
      $additional_value_sql = "";
      if (!empty($additional_image_fields)) {
        $table = ($direct_upload) ? IMAGES_TABLE : IMAGES_TEMP_TABLE;
        $table_fields = $site_db->get_table_fields($table);
        foreach ($additional_image_fields as $key => $val) {
          if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
            $additional_field_sql .= ", $key";
            $additional_value_sql .= ", '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
          }
        }
      }

      $current_time = time();
      if ($direct_upload) {
        $sql = "INSERT INTO ".IMAGES_TABLE."
                (cat_id, user_id, image_name, image_description, image_keywords, image_date, image_active, image_media_file, image_thumb_file, image_download_url, image_allow_comments".$additional_field_sql.")
                VALUES
                ($cat_id, ".$user_info['user_id'].", '$image_name', '$image_description', '$image_keywords', $current_time, $image_active, '$new_name', '$new_thumb_name', '$image_download_url', $image_allow_comments".$additional_value_sql.")";
        $result = $site_db->query($sql);
        $image_id = $site_db->get_insert_id();
        if ($result) {
          include(ROOT_PATH.'includes/search_utils.php');
          $search_words = array();
          foreach ($search_match_fields as $image_column => $match_column) {
            if (isset($HTTP_POST_VARS[$image_column])) {
              $search_words[$image_column] = stripslashes($HTTP_POST_VARS[$image_column]);
            }
          }
          add_searchwords($image_id, $search_words);
        }
      }
      else {
        $sql = "INSERT INTO ".IMAGES_TEMP_TABLE."
                (cat_id, user_id, image_name, image_description, image_keywords, image_date, image_media_file, image_thumb_file, image_download_url".$additional_field_sql.")
                VALUES
                ($cat_id, ".$user_info['user_id'].", '$image_name', '$image_description', '$image_keywords', $current_time, '$new_name', '$new_thumb_name', '$image_download_url'".$additional_value_sql.")";
        $result = $site_db->query($sql);
      }

      if ($config['upload_notify'] == 1 && !$direct_upload) {
        include(ROOT_PATH.'includes/email.php');
        $site_email = new Email();

        $config['upload_emails'] = str_replace(" ", "", $config['upload_emails']);
        $emails = explode(",", $config['upload_emails']);

        $validation_url = $script_url."/admin/index.php?goto=".urlencode("validateimages.php?action=validateimages");

        $site_email->set_to($config['site_email']);
        $site_email->set_subject($lang['new_upload_emailsubject']);
        $site_email->register_vars(array(
          "image_name" => stripslashes($image_name),
          "file_name" => $new_name,
          "cat_name" => $cat_cache[$cat_id]['cat_name'],
          "validation_url" => $validation_url,
          "site_name" => $config['site_name']
        ));
        $site_email->set_body("upload_notify", $config['language_dir_default']);
        $site_email->set_bcc($emails);
        $site_email->send_email();
      }

      $msg .= $lang['image_add_success'].": <b>".format_text(stripslashes($image_name))."</b> (".$new_name.")";
      $msg .= (!$direct_upload) ? "<br />".$lang['new_upload_validate_desc'] : "";

      $file_extension = get_file_extension($new_name);
      $file = (is_remote($new_name)) ? $new_name : (($direct_upload) ? MEDIA_PATH."/".$cat_id."/".$new_name : MEDIA_TEMP_PATH."/".$new_name);
      $width_height = "";
      if (!is_remote($file) && $imageinfo = @getimagesize($file)) {
        $width_height = " ".$imageinfo[3];
      }
      $media_icon = "<img src=\"".ICON_PATH."/".$file_extension.".gif\" border=\"0\" alt=\"\" />";
      $site_template->register_vars(array(
        "media_src" => $file,
        "media_icon" => $media_icon,
        "image_name" => format_text(stripslashes($image_name)),
        "width_height" => $width_height
      ));
      $media = $site_template->parse_template("media/".$file_extension);
      $content .= "<table border=\"0\" align=\"center\">\n<tr>\n<td>\n".$media."\n</td>\n</tr>\n</table>\n";
    }
    else {
      $action = "uploadform";
      $sendprocess = 1;
    }
  }
  else {
    $action = "uploadform";
    $sendprocess = 1;
  }
}

if ($action == "uploadform") {
  if ($cat_id != 0 && (!isset($cat_cache[$cat_id]) || !check_permission("auth_upload", $cat_id))) {
    show_error_page($lang['no_permission']);
    exit;
  }

  $txt_clickstream = "";
  if ($cat_id && isset($cat_cache[$cat_id])) {
    $txt_clickstream .= get_category_path($cat_id, 1).$config['category_separator'];
  }
  $txt_clickstream .= $lang['user_upload'];

  if (!$sendprocess) {
    $remote_media_file = "";
    $remote_thumb_file = "";
    $image_name = "";
    $image_description = "";
    $image_keywords = "";
    $image_download_url = "";
    $image_allow_comments = 1;
  }

  $site_template->register_vars(array(
    "cat_id" => $cat_id,
    "cat_name" => ($cat_id != 0) ? format_text($cat_cache[$cat_id]['cat_name'], 2) : get_category_dropdown($cat_id),
    "remote_media_file" => format_text(stripslashes($remote_media_file), 2),
    "remote_thumb_file" => format_text(stripslashes($remote_thumb_file), 2),
    "image_name" => format_text(stripslashes($image_name), 2),
    "image_description" => format_text(stripslashes($image_description), 2),
    "image_keywords" => format_text(stripslashes($image_keywords), 2),
    "image_allow_comments_yes" => ($image_allow_comments) ? " checked=\"checked\"" : "",
    "image_allow_comments_no" => (!$image_allow_comments) ? " checked=\"checked\"" : "",
    "image_download_url" => format_text(stripslashes($image_download_url), 2),
    "lang_category" => $lang['category'],
    "lang_user_upload" => $lang['user_upload'],
    "lang_media_file" => $lang['media_file'],
    "lang_thumb_file" => $lang['thumb_file'],
    "lang_allowed_file_types" => $lang['allowed_mediatypes_desc'],
    "allowed_media_types" => str_replace(",",", ",$config['allowed_mediatypes']),
    "allowed_thumb_types" => "jpg, gif, png",
    "lang_max_filesize" => $lang['max_filesize'],
    "lang_max_imagewidth" => $lang['max_imagewidth'],
    "lang_max_imageheight" => $lang['max_imageheight'],
    "max_thumb_filsize" => $config['max_thumb_size']."&nbsp;".$lang['kb'],
    "max_thumb_imagewidth" => $config['max_thumb_width']."&nbsp;".$lang['px'],
    "max_thumb_imageheight" => $config['max_thumb_height']."&nbsp;".$lang['px'],
    "max_media_filsize" => $config['max_media_size']."&nbsp;".$lang['kb'],
    "max_media_imagewidth" => $config['max_image_width']."&nbsp;".$lang['px'],
    "max_media_imageheight" => $config['max_image_height']."&nbsp;".$lang['px'],
    "lang_image_name" => $lang['image_name'],
    "lang_description" => $lang['description'],
    "lang_keywords" => $lang['keywords_ext'],
    "lang_allow_comments" => isset($lang['allow_comments']) ? $lang['allow_comments'] : "",
    "lang_submit" => $lang['submit'],
    "lang_reset" => $lang['reset'],
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no'],
    "lang_captcha" => $lang['captcha'],
    "lang_captcha_desc" => $lang['captcha_desc'],
    "captcha_upload" => (bool)$captcha_enable_upload
  ));

  if (!empty($additional_image_fields)) {
    $additional_field_array = array();
    foreach ($additional_image_fields as $key => $val) {
      if ($val[1] == "radio") {
        $value = (isset($HTTP_POST_VARS[$key])) ? intval($HTTP_POST_VARS[$key]) : 1;
        if ($value == 1) {
          $additional_field_array[$key.'_yes'] = " checked=\"checked\"";
          $additional_field_array[$key.'_no'] = "";
        }
        else {
          $additional_field_array[$key.'_yes'] = "";
          $additional_field_array[$key.'_no'] = " checked=\"checked\"";
        }
      }
      else {
        $value = (isset($HTTP_POST_VARS[$key])) ? format_text(stripslashes(trim($HTTP_POST_VARS[$key]))) : "";
      }
      $additional_field_array[$key] = $value;
      $additional_field_array['lang_'.$key] = $val[0];
    }
    if (!empty($additional_field_array)) {
      $site_template->register_vars($additional_field_array);
    }
  }
  $content = $site_template->parse_template("member_uploadform");
}

if ($action == "emailuser") {
  $txt_clickstream = $lang['profile'];
  $user_id = (isset($HTTP_POST_VARS[URL_USER_ID])) ? intval($HTTP_POST_VARS[URL_USER_ID]) : GUEST;
  $error = 0;

  if ($user_info['user_level'] == GUEST || $user_info['user_level'] == USER_AWAITING) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $subject = stripslashes(trim($HTTP_POST_VARS['subject']));
  $message = stripslashes(trim($HTTP_POST_VARS['message']));

  if ($subject == "" || $message == "") {
    $msg = $lang['lostfield_error'];
    $sendprocess = 1;
    $error = 1;
  }

  if (!$error) {
    if ($user_row = get_user_info($user_id)) {
      if (isset($user_row['user_showemail']) && $user_row['user_showemail'] == 0) {
        $content = $lang['invalid_user_id'];
      }
      else {
        $sender_user_name = ($user_info['user_level'] != GUEST) ? (isset($user_info['user_name']) ? $user_info['user_name'] : $lang['userlevel_user']) : $lang['userlevel_guest'];
        $sender_user_email = ($user_info['user_level'] != GUEST && isset($user_info['user_email'])) ? $user_info['user_email'] : $config['site_email'];

        // Start Emailer
        include(ROOT_PATH.'includes/email.php');
        $site_email = new Email();
        $site_email->set_from($sender_user_email, $sender_user_name);
        $site_email->set_to($user_row['user_email']);
        $site_email->set_subject($subject);
        $site_email->register_vars(array(
          "sender_user_name" => $sender_user_name,
          "sender_user_email" => $sender_user_email,
          "message" => $message,
          "site_name" => $config['site_name']
        ));
        $site_email->set_body("mailform_message", $config['language_dir']);
        $site_email->send_email();
        $msg = $lang['emailuser_success'];
      }
    }
    else {
      $content = $lang['invalid_user_id'];
    }
  }
  else {
    $action = "mailform";
  }
}

if ($action == "mailform") {
  $txt_clickstream = $lang['profile'];
  if (isset($HTTP_GET_VARS[URL_USER_ID]) || isset($HTTP_POST_VARS[URL_USER_ID])) {
    $user_id = (isset($HTTP_GET_VARS[URL_USER_ID])) ? intval($HTTP_GET_VARS[URL_USER_ID]) : intval($HTTP_POST_VARS[URL_USER_ID]);
    if (!$user_id) {
      $user_id = GUEST;
    }
  }
  else {
    $user_id = GUEST;
  }

  if ($user_info['user_level'] == GUEST || $user_info['user_level'] == USER_AWAITING) {
    show_error_page($lang['no_permission']);
    exit;
  }

  if (!$sendprocess) {
    $subject = "";
    $message = "";
  }

  if ($user_row = get_user_info($user_id)) {
    if (isset($user_row['user_showemail']) && $user_row['user_showemail'] == 0) {
      $content = $lang['invalid_user_id'];
    }
    else {
      $site_template->register_vars(array(
        "user_id" => $user_row['user_id'],
        "user_name" => format_text($user_row['user_name'], 2),
        "subject" => format_text($subject, 2),
        "message" => format_text($message, 2),
        "lang_send_email_to" => $lang['send_email_to'],
        "lang_subject" => $lang['subject'],
        "lang_message" => $lang['message'],
        "lang_submit" => $lang['submit'],
        "lang_reset" => $lang['reset']
      ));
      $content = $site_template->parse_template("member_mailform");
    }
  }
  else {
    $content = $lang['invalid_user_id'];
  }
}

//-----------------------------------------------------
//--- Show Profile ------------------------------------
//-----------------------------------------------------
if ($action == "showprofile") {
  $txt_clickstream = $lang['profile'];
  if (isset($HTTP_GET_VARS[URL_USER_ID]) || isset($HTTP_POST_VARS[URL_USER_ID])) {
    $user_id = (isset($HTTP_GET_VARS[URL_USER_ID])) ? intval($HTTP_GET_VARS[URL_USER_ID]) : intval($HTTP_POST_VARS[URL_USER_ID]);
    if (!$user_id) {
      $user_id = GUEST;
    }
  }
  else {
    $user_id = GUEST;
  }

  if ($user_row = get_user_info($user_id)) {
    $user_homepage = (isset($user_row['user_homepage'])) ? format_text(format_url($user_row['user_homepage']), 2) : REPLACE_EMPTY;
    if (!empty($user_homepage) && $user_homepage != REPLACE_EMPTY) {
      $user_homepage_button = "<a href=\"".$user_homepage."\" target=\"_blank\"><img src=\"".get_gallery_image("homepage.gif")."\" border=\"0\" alt=\"".$user_homepage."\" /></a>";
    }
    else {
      $user_homepage_button = REPLACE_EMPTY;
    }

    $user_icq = (isset($user_row['user_icq'])) ? $user_row['user_icq'] : REPLACE_EMPTY;
    if (!empty($user_icq) && $user_icq != REPLACE_EMPTY) {
      $user_icq_button = "<a href=\"http://www.icq.com/people/about_me.php?uin=".$user_icq."\" target=\"_blank\"><img src=\"http://status.icq.com/online.gif?icq=".$user_icq."&img=5\" width=\"18\" height=\"18\" border=\"0\" alt=\"".$user_icq."\" /></a>";
    }
    else {
      $user_icq_button = REPLACE_EMPTY;
    }

    if (!empty($user_row['user_email']) && (!isset($user_row['user_showemail']) || (isset($user_row['user_showemail']) && $user_row['user_showemail'] == 1))) {
      $user_email = $user_row['user_email'];
      $user_email_save = str_replace("@", " at ", $user_row['user_email']);
      $user_email_save = str_replace(".", " dot ", $user_row['user_email']);
      if (!empty($url_mailform)) {
        $user_mailform_link = $site_sess->url(preg_replace("/{user_id}/", $user_row['user_id'], $url_mailform));
      }
      else {
        $user_mailform_link = $site_sess->url(ROOT_PATH."member.php?action=mailform&amp;".URL_USER_ID."=".$user_row['user_id']);
      }
      $user_email_button = "<a href=\"".$user_mailform_link."\"><img src=\"".get_gallery_image("email.gif")."\" border=\"0\" alt=\"".$user_email_save."\" /></a>";
    }
    else {
      $user_email = REPLACE_EMPTY;
      $user_email_save = REPLACE_EMPTY;
      $user_mailform_link = REPLACE_EMPTY;
      $user_email_button = REPLACE_EMPTY;
    }
    $site_template->register_vars(array(
      "user_id" => $user_row['user_id'],
      "user_name" => (isset($user_row['user_name'])) ? format_text($user_row['user_name'], 2) : REPLACE_EMPTY,
      "user_email" => $user_email,
      "user_email_save" => $user_email_save,
      "user_mailform_link" => $user_mailform_link,
      "user_email_button" => $user_email_button,
      "user_join_date" => (isset($user_row['user_joindate'])) ? format_date($config['date_format'], $user_row['user_joindate']) : REPLACE_EMPTY,
      "user_last_action" => (isset($user_row['user_lastaction'])) ? format_date($config['date_format']." ".$config['time_format'], $user_row['user_lastaction']) : REPLACE_EMPTY,
      "user_homepage" => $user_homepage,
      "user_homepage_button" => $user_homepage_button,
      "user_icq" => $user_icq,
      "user_icq_button" => $user_icq_button,
      "user_icq_status" => (isset($user_row['user_icq'])) ? get_icq_status($user_row['user_icq']) : REPLACE_EMPTY,
      "user_comments" => (isset($user_row['user_comments'])) ? $user_row['user_comments'] : REPLACE_EMPTY,
      "lang_profile_of" => $lang['profile_of'],
      "lang_show_user_images" => preg_replace("/".$site_template->start."user_name".$site_template->end."/siU", format_text($user_row['user_name'], 2), $lang['show_user_images']),
      "url_show_user_images" => $site_sess->url(ROOT_PATH."search.php?search_user=".urlencode($user_row['user_name'])),
      "lang_join_date" => $lang['join_date'],
      "lang_last_action" => $lang['last_action'],
      "lang_comments" => $lang['comments'],
      "lang_email" => $lang['email'],
      "lang_homepage" => $lang['homepage'],
      "lang_icq" => $lang['icq']
    ));

    if (!empty($additional_user_fields)) {
      $additional_field_array = array();
      foreach ($additional_user_fields as $key => $val) {
        $additional_field_array[$key] = (!empty($user_row[$key])) ? format_text($user_row[$key], 1) : REPLACE_EMPTY;
        $additional_field_array['lang_'.$key] = $val[0];
      }
      if (!empty($additional_field_array)) {
        $site_template->register_vars($additional_field_array);
      }
    }
    $content = $site_template->parse_template("member_profile");
  }
  else {
    $content = $lang['invalid_user_id'];
  }
}

//-----------------------------------------------------
//--- Send Password -----------------------------------
//-----------------------------------------------------
if ($action == "sendpassword") {
  $txt_clickstream = $lang['lost_password'];
  $user_email = un_htmlspecialchars(trim($HTTP_POST_VARS['user_email']));

  if ($user_email != "") {
    $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_name").get_user_table_field(", ", "user_password")."
            FROM ".USERS_TABLE."
            WHERE ".get_user_table_field("", "user_email")." = '$user_email'";
    if ($checkuser = $site_db->query_firstrow($sql)) {
      $user_password = random_string(8);
      $user_password_hashed = salted_hash($user_password);

      $sql = "UPDATE ".USERS_TABLE."
              SET ".get_user_table_field("", "user_password")." = '".$user_password_hashed."'
              WHERE ".get_user_table_field("", "user_id")." = ".$checkuser[$user_table_fields['user_id']];
      $site_db->query($sql);

      // Start Emailer
      include(ROOT_PATH.'includes/email.php');
      $site_email = new Email();
      $site_email->set_to($user_email);
      $site_email->set_subject($lang['send_password_emailsubject']);
      $site_email->register_vars(array(
        "user_name" => $checkuser[$user_table_fields['user_name']],
        "user_password" => stripslashes($user_password),
        "site_name" => $config['site_name']
      ));
      $site_email->set_body("lost_password", $config['language_dir']);
      $site_email->send_email();

      $msg = $lang['send_password_success'];
      $HTTP_POST_VARS['user_email'] = "";
    }
    else {
      $msg = $lang['invalid_email'];
    }
  }

  $action = "lostpassword";
}

if ($action == "lostpassword") {
  $txt_clickstream = $lang['lost_password'];
  $user_email = (isset($HTTP_POST_VARS['user_email'])) ? format_text(stripslashes($HTTP_POST_VARS['user_email']), 2) : "";
  $site_template->register_vars(array(
    "lang_email" => $lang['email'],
    "lang_lost_password" => $lang['lost_password'],
    "lang_lost_password_msg" => $lang['lost_password_msg'],
    "lang_submit" => $lang['submit'],
    "user_email" => $user_email,
  ));
  $content = $site_template->parse_template("member_lostpassword");
}

//-----------------------------------------------------
//--- Edit Profile ------------------------------------
//-----------------------------------------------------
$update_process = 0;
$new_email_msg = "";
if ($action == "updateprofile") {
  $txt_clickstream = $lang['control_panel'];
  if ($user_info['user_level'] == GUEST) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $user_email = (isset($HTTP_POST_VARS['user_email'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['user_email'])) : "";
  $user_email2 = (isset($HTTP_POST_VARS['user_email2'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['user_email2'])) : "";
  $user_homepage = (isset($HTTP_POST_VARS['user_homepage'])) ? format_url(un_htmlspecialchars(trim($HTTP_POST_VARS['user_homepage']))) : "";
  $user_icq = (isset($HTTP_POST_VARS['user_icq'])) ? ((intval(trim($HTTP_POST_VARS['user_icq']))) ? intval(trim($HTTP_POST_VARS['user_icq'])) : "") : "";
  $user_showemail = (isset($HTTP_POST_VARS['user_showemail'])) ? intval($HTTP_POST_VARS['user_showemail']) : 0;
  $user_allowemails = (isset($HTTP_POST_VARS['user_allowemails'])) ? intval($HTTP_POST_VARS['user_allowemails']) : 0;
  $user_invisible = (isset($HTTP_POST_VARS['user_invisible'])) ? intval($HTTP_POST_VARS['user_invisible']) : 0;

  $error = 0;
  if ($user_info['user_email'] != $user_email && $checkuser = $site_db->query_firstrow("SELECT ".get_user_table_field("", "user_id")." FROM ".USERS_TABLE." WHERE ".get_user_table_field("", "user_email")." = '$user_email' AND ".get_user_table_field("", "user_id")." <> '".$user_info['user_id']."'")) {
    if ($checkuser[$user_table_fields['user_id']] != $user_info['user_id']) {
      $msg .= (($msg != "") ? "<br />" : "").$lang['email_exists'];
      $error = 1;
    }
  }
  if ($user_email != $user_email2) {
    $msg .= (($msg != "") ? "<br />" : "").$lang['update_email_confirm_error'];
    $error = 1;
  }
  if ($user_email == "" || $user_email2 == "") {
    $msg .= (($msg != "") ? "<br />" : "").$lang['update_email_error'];
    $error = 1;
  }
  if (!check_email($user_email)) {
    $msg .= (($msg != "") ? "<br />" : "").$lang['invalid_email_format'];
    $error = 1;
  }

  if (!empty($additional_user_fields)) {
    foreach ($additional_user_fields as $key => $val) {
      if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
        $error = 1;
        $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $val[0]), $lang['field_required']);
        $msg .= (($msg != "") ? "<br />" : "").$field_error;
      }
    }
  }

  if (!$error && $user_email != $user_info['user_email'] && $user_info['user_level'] != ADMIN && $config['account_activation'] != 0) {
    $activationkey = get_random_key(USERS_TABLE, $user_table_fields['user_activationkey']);

    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_level")." = ".USER_AWAITING.", ".get_user_table_field("", "user_activationkey")." = '$activationkey'
            WHERE ".get_user_table_field("", "user_id")." = ".$user_info['user_id'];
    $result = $site_db->query($sql);

    if ($result) {
      $activation_url = $script_url."/register.php?action=activate&activationkey=".$activationkey;

      include(ROOT_PATH.'includes/email.php');
      $site_email = new Email();

      switch($config['account_activation']) {
      case 2:
        $user_details_url = $script_url."/admin/index.php?goto=".urlencode("users.php?action=edituser&user_id=".$user_info['user_id']."&activation=1");
        $email_to = $config['site_email'];
        $email_subject = $lang['admin_activation_emailsubject'];
        $email_template = "admin_activation";
        $new_email_msg = $lang['update_email_instruction_admin'];
        break;
      case 1:
        if ($config['language_dir_default'] != $config['language_dir']) {
          $activation_url .= "&l=".$config['language_dir'];
        }
        $user_details_url = "";
        $email_to = $user_email;
        $email_subject = $lang['update_email_emailsubject'];
        $email_template = "newemail_activation";
        $new_email_msg = $lang['update_email_instruction'];
        break;
      case 0:
      default:
        break;
      }

      if (!empty($email_to)) {
        $site_email->set_to($email_to);
        $site_email->set_subject($email_subject);
        $site_email->register_vars(array(
          "user_details_url" => $user_details_url,
          "activation_url" => $activation_url,
          "user_name" => $user_info['user_name'],
          "site_name" => $config['site_name']
        ));
        $site_email->set_body($email_template, $config['language_dir']);
        $site_email->send_email();
      }
    }
    else {
      $msg = $lang['general_error'];
      $error = 1;
    }
  }

  if (!$error) {
    $additional_sql = "";
    if (!empty($additional_user_fields)) {
      $table_fields = $site_db->get_table_fields(USERS_TABLE);
      foreach ($additional_user_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
          $additional_sql .= ", $key = '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
        }
      }
    }

    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_email")." = '$user_email', ".get_user_table_field("", "user_showemail")." = $user_showemail, ".get_user_table_field("", "user_allowemails")." = $user_allowemails, ".get_user_table_field("", "user_invisible")." = $user_invisible, ".get_user_table_field("", "user_homepage")." = '$user_homepage', ".get_user_table_field("", "user_icq")." = '$user_icq'".$additional_sql."
            WHERE ".get_user_table_field("", "user_id")." = ".$user_info['user_id'];
    $site_db->query($sql);

    $msg = $lang['update_profile_success'];
    if (!empty($new_email_msg)) {
      $msg .= "<br />".$new_email_msg;
    }
    $user_info = $site_sess->load_user_info($user_info['user_id']);
  }
  else {
    $update_process = 1;
  }
  $action = "editprofile";
}

if ($action == "updatepassword") {
  $txt_clickstream = $lang['control_panel'];
  if ($user_info['user_level'] == GUEST) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $error = 0;
  $current_user_password = trim($HTTP_POST_VARS['current_user_password']);
  $user_password = trim($HTTP_POST_VARS['user_password']);
  $user_password2 = trim($HTTP_POST_VARS['user_password2']);
  if (!compare_passwords($current_user_password, $user_info['user_password'])) {
    $msg .= (($msg != "") ? "<br />" : "").$lang['update_password_error'];
    $error = 1;
  }
  if ($user_password != $user_password2 || $user_password == "") {
    $msg .= (($msg != "") ? "<br />" : "").$lang['update_password_confirm_error'];
    $error = 1;
  }
  if (!$error) {
    $user_password_hashed = salted_hash($user_password);
    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_password")." = '".$user_password_hashed."'
            WHERE ".get_user_table_field("", "user_id")." = ".$user_info['user_id'];
    $site_db->query($sql);

    $msg = $lang['update_password_success'];
    $user_info = $site_sess->load_user_info($user_info['user_id']);
  }
  $action = "editprofile";
}

if ($action == "editprofile") {
  $txt_clickstream = $lang['control_panel'];
  if ($user_info['user_level'] == GUEST) {
    show_error_page($lang['no_permission']);
    exit;
  }
  $user_name = $user_info['user_name'];
  if (!$update_process) {
    $user_email = $user_info['user_email'];
    $user_email2 = $user_info['user_email'];
    $user_showemail = $user_info['user_showemail'];
    $user_allowemails = $user_info['user_allowemails'];
    $user_invisible = $user_info['user_invisible'];
    $user_homepage = $user_info['user_homepage'];
    $user_icq = $user_info['user_icq'];
  }

  if ($user_showemail == 1) {
    $user_showemail_yes = " checked=\"checked\"";
    $user_showemail_no = "";
  }
  else {
    $user_showemail_yes = "";
    $user_showemail_no = " checked=\"checked\"";
  }
  if ($user_allowemails == 1) {
    $user_allowemails_yes = " checked=\"checked\"";
    $user_allowemails_no = "";
  }
  else {
    $user_allowemails_yes = "";
    $user_allowemails_no = " checked=\"checked\"";
  }
  if ($user_invisible == 1) {
    $user_invisible_yes = " checked=\"checked\"";
    $user_invisible_no = "";
  }
  else {
    $user_invisible_yes = "";
    $user_invisible_no = " checked=\"checked\"";
  }

  $edit_profile_msg = $lang['edit_profile_msg'];
  if ($config['account_activation'] == 1 && $user_info['user_level'] != ADMIN) {
    $edit_profile_msg .= $lang['edit_profile_email_msg'];
  }
  if ($config['account_activation'] == 2 && $user_info['user_level'] != ADMIN) {
    $edit_profile_msg .= $lang['edit_profile_email_msg_admin'];
  }

  $site_template->register_vars(array(
    "user_name" => format_text(stripslashes($user_name), 2),
    "user_email" => format_text(stripslashes($user_email), 2),
    "user_email2" => format_text(stripslashes($user_email2), 2),
    "user_homepage" => format_text(stripslashes($user_homepage), 2),
    "user_icq" => $user_icq,
    "user_showemail_yes" => $user_showemail_yes,
    "user_showemail_no" => $user_showemail_no,
    "user_allowemails_yes" => $user_allowemails_yes,
    "user_allowemails_no" => $user_allowemails_no,
    "user_invisible_yes" => $user_invisible_yes,
    "user_invisible_no" => $user_invisible_no,
    "lang_profile_of" => $lang['profile_of'],
    "lang_email" => $lang['email'],
    "lang_email_confirm" => $lang['email_confirm'],
    "lang_show_email" => $lang['show_email'],
    "lang_allow_emails" => $lang['allow_emails'],
    "lang_invisible" => $lang['invisible'],
    "lang_optional_infos" => $lang['optional_infos'],
    "lang_homepage" => $lang['homepage'],
    "lang_icq" => $lang['icq'],
    "lang_save" => $lang['save'],
    "lang_reset" => $lang['reset'],
    "lang_change_password" => $lang['change_password'],
    "lang_old_password" => $lang['old_password'],
    "lang_new_password" => $lang['new_password'],
    "lang_new_password_confirm" => $lang['new_password_confirm'],
    "lang_edit_profile_msg" => $edit_profile_msg,
    "lang_yes" => $lang['yes'],
    "lang_no" => $lang['no']
  ));

  if (!empty($additional_user_fields)) {
    $additional_field_array = array();
    foreach ($additional_user_fields as $key => $val) {
      if ($val[1] == "radio") {
        $value = (isset($HTTP_POST_VARS[$key])) ? intval($HTTP_POST_VARS[$key]) : intval($user_info[$key]);
        if ($value == 1) {
          $additional_field_array[$key.'_yes'] = " checked=\"checked\"";
          $additional_field_array[$key.'_no'] = "";
        }
        else {
          $additional_field_array[$key.'_yes'] = "";
          $additional_field_array[$key.'_no'] = " checked=\"checked\"";
        }
      }
      else {
        $value = (isset($HTTP_POST_VARS[$key])) ? format_text(trim($HTTP_POST_VARS[$key]), 2) : $user_info[$key];
      }
      $additional_field_array[$key] = $value;
      $additional_field_array['lang_'.$key] = $val[0];
    }
    if (!empty($additional_field_array)) {
      $site_template->register_vars($additional_field_array);
    }
  }

  $content = $site_template->parse_template("member_editprofile");
  if (!empty($new_email_msg)) {
    $site_sess->logout($user_info['user_id']);
  }
}

//-----------------------------------------------------
//--- Clickstream -------------------------------------
//-----------------------------------------------------
$clickstream = "<span class=\"clickstream\"><a href=\"".$site_sess->url(ROOT_PATH."index.php")."\" class=\"clickstream\">".$lang['home']."</a>".$config['category_separator'].$txt_clickstream."</span>";

//-----------------------------------------------------
//--- Print Out ---------------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "content" => $content,
  "msg" => $msg,
  "clickstream" => $clickstream,
  "lang_control_panel" => $lang['control_panel']
));
$site_template->print_template($site_template->parse_template($main_template));
include(ROOT_PATH.'includes/page_footer.php');
?>
