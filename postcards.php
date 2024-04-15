<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: postcards.php                                        *
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

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();

if (isset($HTTP_GET_VARS[URL_POSTCARD_ID]) || isset($HTTP_POST_VARS[URL_POSTCARD_ID])) {
  $postcard_id = (isset($HTTP_GET_VARS[URL_POSTCARD_ID])) ? trim($HTTP_GET_VARS[URL_POSTCARD_ID]) : trim($HTTP_POST_VARS[URL_POSTCARD_ID]);
}
else {
  $postcard_id = 0;
}

if ($action == "") {
  $action = ($postcard_id) ? "showcard" : "createcard";
}

if (isset($HTTP_GET_VARS['modifycard']) || isset($HTTP_POST_VARS['modifycard'])) {
  $action = "modifycard";
}

$main_template = ($action == "createcard" || $action == "modifycard") ? "postcard_create" : (($action == "previewcard") ? "postcard_create" : "postcard_send");
include(ROOT_PATH.'includes/page_header.php');

$sendprocess = 0;

if ($action != "showcard") {
  $sql = "SELECT i.cat_id, i.image_name, i.image_media_file, i.image_thumb_file, c.cat_name".get_user_table_field(", u.", "user_name")."
          FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
          WHERE i.image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (!$image_row) {
    redirect($url);
  }
  $cat_id = $image_row['cat_id'];
  $image_row['user_name'] = $image_row[$user_table_fields['user_name']];

  if (!check_permission("auth_viewcat", $cat_id) || !check_permission("auth_viewimage", $cat_id) || !check_permission("auth_sendpostcard", $cat_id)) {
    redirect($url);
  }
}

if ($action == "sendcard") {
  $expiry = time() - 60 * 60 * 24 * POSTCARD_EXPIRY;
  $sql = "DELETE FROM ".POSTCARDS_TABLE."
          WHERE (postcard_date < $expiry)";
  $site_db->query($sql);

  $bg_color = un_htmlspecialchars(trim($HTTP_POST_VARS['bg_color']));
  $border_color = un_htmlspecialchars(trim($HTTP_POST_VARS['border_color']));
  $font_color = un_htmlspecialchars(trim($HTTP_POST_VARS['font_color']));
  $font_face = un_htmlspecialchars(trim($HTTP_POST_VARS['font_face']));

  $sender_name = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_name']));
  $sender_email = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_email']));
  $recipient_name = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_name']));
  $recipient_email = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_email']));

  $headline = un_htmlspecialchars(trim($HTTP_POST_VARS['headline']));
  $message = un_htmlspecialchars(trim($HTTP_POST_VARS['message']));
  $message = strip_tags($message);

  $captcha = (isset($HTTP_POST_VARS['captcha'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['captcha'])) : "";

  $back_url = (!empty($HTTP_POST_VARS['back_url'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['back_url']))) : $site_sess->url(ROOT_PATH."index.php", "&");

  $postcard_id = get_random_key(POSTCARDS_TABLE, "postcard_id");
  $current_time = time();

  if ($captcha_enable_postcards && !captcha_validate($captcha)) {
      $msg .= (($msg != "") ? "<br />" : "").$lang['captcha_required'];
      $action = "previewcard";
      $main_template = "postcard_preview";
  } else {
    $sql = "INSERT INTO ".POSTCARDS_TABLE."
            (postcard_id, image_id, postcard_date, postcard_bg_color, postcard_border_color, postcard_font_color, postcard_font_face, postcard_sender_name, postcard_sender_email, postcard_recipient_name, postcard_recipient_email, postcard_headline, postcard_message)
            VALUES
            ('$postcard_id', $image_id, $current_time, '$bg_color', '$border_color', '$font_color', '$font_face', '$sender_name', '$sender_email', '$recipient_name', '$recipient_email', '$headline', '$message')";
    $result = $site_db->query($sql);

    if ($result) {
      $postcard_url = $script_url."/postcards.php?".URL_POSTCARD_ID."=".$postcard_id;

      include(ROOT_PATH.'includes/email.php');
      $site_email = new Email();
      $site_email->set_to(stripslashes($recipient_email));
      $site_email->set_from(stripslashes($sender_email), stripslashes($sender_name));
      $site_email->set_subject($lang['send_postcard_emailsubject']);
      $site_email->register_vars(array(
        "sender_name" => stripslashes($sender_name),
        "sender_email" => stripslashes($sender_email),
        "recipient_name" => stripslashes($recipient_name),
        "postcard_url" => stripslashes($postcard_url),
        "postcard_send_date" => format_date($config['date_format']." ".$config['time_format'], $current_time),
        "site_name" => $config['site_name']
      ));
      $site_email->set_body("postcard_message", $config['language_dir']);
      $site_email->send_email();

      $msg .= $lang['send_postcard_success'];
      $msg .= "<br /><a href=\"".$back_url."\">".$lang['back_to_gallery']."</a>";
      $action = "showcard";
    }
    else {
      $msg = $lang['general_error'];
      $action = "previewcard";
      $main_template = "postcard_preview";
    }
  }
}

if ($action == "showcard") {
  $expiry = time() - 60 * 60 * 24 * POSTCARD_EXPIRY;
  $sql = "DELETE FROM ".POSTCARDS_TABLE."
          WHERE (postcard_date < $expiry)";
  $site_db->query($sql);

  if (!$postcard_id){
    redirect("index.php");
  }
  else {
    $sql = "SELECT p.postcard_id, p.image_id, p.postcard_date, p.postcard_bg_color, p.postcard_border_color, p.postcard_font_color, p.postcard_font_face, p.postcard_sender_name, p.postcard_sender_email, p.postcard_recipient_name, p.postcard_recipient_email, p.postcard_headline, p.postcard_message, i.image_name, i.cat_id, i.image_media_file, i.image_thumb_file
            FROM (".POSTCARDS_TABLE." p, ".IMAGES_TABLE." i)
            WHERE p.postcard_id = '$postcard_id' AND p.image_id = i.image_id";
    $image_row = $site_db->query_firstrow($sql);

    if (!$image_row) {
      show_error_page($lang['invalid_postcard_id']);
    }
    else {
      $image = get_media_code($image_row['image_media_file'], $image_row['image_id'], $image_row['cat_id'], $image_row['image_name'], $mode, 1);
      $thumbnail = get_thumbnail_code($image_row['image_media_file'], $image_row['image_thumb_file'], $image_row['image_id'], $image_row['cat_id'], $image_row['image_name'], $mode);
      $image_name_link = "<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_row['image_id'])."\">".format_text($image_row['image_name'])."</a>";
      $site_template->register_vars(array(
        "image" => $image,
        "thumbnail" => $thumbnail,
        "image_name_link" => $image_name_link,
        "bg_color" => format_text($image_row['postcard_bg_color'], 2),
        "border_color" => format_text($image_row['postcard_border_color'], 2),
        "font_color" => format_text($image_row['postcard_font_color'], 2),
        "font_face" => format_text($image_row['postcard_font_face'], 2),
        "sender_name" => format_text($image_row['postcard_sender_name'], 2),
        "sender_email" => format_text($image_row['postcard_sender_email'], 2),
        "recipient_name" => format_text($image_row['postcard_recipient_name'], 2),
        "recipient_email" => format_text($image_row['postcard_recipient_email'], 2),
        "headline" => format_text($image_row['postcard_headline'], 2),
        "message" => format_text($image_row['postcard_message'], 0)
      ));
    }
  }
}

if ($action == "previewcard") {
  $error = 0;
  $bg_color = un_htmlspecialchars(trim($HTTP_POST_VARS['bg_color']));
  $border_color = un_htmlspecialchars(trim($HTTP_POST_VARS['border_color']));
  $font_color = un_htmlspecialchars(trim($HTTP_POST_VARS['font_color']));
  $font_face = un_htmlspecialchars(trim($HTTP_POST_VARS['font_face']));

  $sender_name = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_name']));
  $sender_email = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_email']));
  $recipient_name = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_name']));
  $recipient_email = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_email']));

  $headline = un_htmlspecialchars(trim($HTTP_POST_VARS['headline']));
  $message = un_htmlspecialchars(trim($HTTP_POST_VARS['message']));

  $back_url = (!empty($HTTP_POST_VARS['back_url'])) ? un_htmlspecialchars(stripslashes(trim($HTTP_POST_VARS['back_url']))) : $site_sess->url(ROOT_PATH."index.php", "&");

  if ($sender_name == "" || $sender_email == "" || $recipient_name == "" || $recipient_email == "" || $headline == "" || $message == "") {
    $msg .= $lang['lostfield_error'];
    $error = 1;
  }
  if (($sender_email != "" && !check_email($sender_email)) || ($recipient_email != "" && !check_email($recipient_email))) {
    $msg .= (($msg != "") ? "<br />" : "").$lang['invalid_email_format'];
    $error = 1;
  }

  if (!$error) {
    $main_template = "postcard_preview";
    $image = get_media_code($image_row['image_media_file'], $image_id, $cat_id, $image_row['image_name'], $mode, 1);
    $thumbnail = get_thumbnail_code($image_row['image_media_file'], $image_row['image_thumb_file'], $image_id, $cat_id, $image_row['image_name'], $mode);
    $site_template->register_vars(array(
      "image" => $image,
      "thumbnail" => $thumbnail,
      "image_name" => format_text($image_row['image_name']),
      "url_postcard" => $site_sess->url(ROOT_PATH."postcards.php?".URL_IMAGE_ID."=".$image_id),
      "bg_color" => format_text(stripslashes($bg_color), 2),
      "border_color" => format_text(stripslashes($border_color), 2),
      "font_color" => format_text(stripslashes($font_color), 2),
      "font_face" => format_text(stripslashes($font_face), 2),
      "sender_name" => format_text(stripslashes($sender_name), 2),
      "sender_email" => format_text(stripslashes($sender_email), 2),
      "recipient_name" => format_text(stripslashes($recipient_name), 2),
      "recipient_email" => format_text(stripslashes($recipient_email), 2),
      "headline" => format_text(stripslashes($headline), 2),
      "message" => format_text(stripslashes($message), 0),
      "message_hidden" => format_text(stripslashes($message), 2),
      "image_id" => $image_id,
      "lang_sender" => $lang['sender'],
      "lang_recipient" => $lang['recipient'],
      "lang_edit_postcard" => $lang['edit_postcard'],
      "lang_send_postcard" => $lang['send_postcard'],
      "back_url" => format_text($back_url),
      "lang_captcha" => $lang['captcha'],
      "lang_captcha_desc" => $lang['captcha_desc'],
      "captcha_postcards" => (bool)$captcha_enable_postcards
    ));
  }
  else {
    $action = "createcard";
    $main_template = "postcard_create";
    $sendprocess = 1;
  }
}

if ($action == "createcard" || $action == "modifycard") {
  if (!$sendprocess) {
    $bg_color = "";
    $border_color = "";
    $font_color = "";
    $font_face = "";
    $sender_name = ($user_info['user_level'] != GUEST) ? $user_info['user_name'] : "";
    $sender_email = ($user_info['user_level'] != GUEST) ? $user_info['user_email'] : "";
    $recipient_name = "";
    $recipient_email = "";
    $headline = "";
    $message = "";
  }

  if ($action == "modifycard") {
    $bg_color = un_htmlspecialchars(trim($HTTP_POST_VARS['bg_color']));
    $border_color = un_htmlspecialchars(trim($HTTP_POST_VARS['border_color']));
    $font_color = un_htmlspecialchars(trim($HTTP_POST_VARS['font_color']));
    $font_face = un_htmlspecialchars(trim($HTTP_POST_VARS['font_face']));

    $sender_name = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_name']));
    $sender_email = un_htmlspecialchars(trim($HTTP_POST_VARS['sender_email']));
    $recipient_name = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_name']));
    $recipient_email = un_htmlspecialchars(trim($HTTP_POST_VARS['recipient_email']));

    $headline = un_htmlspecialchars(trim($HTTP_POST_VARS['headline']));
    $message = un_htmlspecialchars(trim($HTTP_POST_VARS['message']));
    $message = strip_tags($message);
  }

  $image = get_media_code($image_row['image_media_file'], $image_id, $cat_id, $image_row['image_name'], $mode, 1);
  $thumbnail = get_thumbnail_code($image_row['image_media_file'], $image_row['image_thumb_file'], $image_id, $cat_id, $image_row['image_name'], $mode);
  $site_template->register_vars(array(
    "image" => $image,
    "thumbnail" => $thumbnail,
    "image_name" => format_text($image_row['image_name']),
    "lang_bg_color" => $lang['bg_color'],
    "bg_color" => $bg_color,
    "lang_border_color" => $lang['border_color'],
    "border_color" => $border_color,
    "lang_font_color" => $lang['font_color'],
    "font_color" => $font_color,
    "lang_font_face" => $lang['font_face'],
    "font_face" => $font_face,
    "image_id" => $image_id,
      "lang_sender" => $lang['sender'],
    "lang_recipient" => $lang['recipient'],
    "lang_email" => $lang['email'],
    "lang_name" => $lang['name'],
    "lang_headline" => $lang['headline'],
    "lang_message" => $lang['message'],
    "lang_preview_postcard" => $lang['preview_postcard'],
    "url_postcard" => $site_sess->url(ROOT_PATH."postcards.php?".URL_IMAGE_ID."=".$image_id),
    "sender_name" => format_text(stripslashes($sender_name), 2),
    "sender_email" => format_text(stripslashes($sender_email), 2),
    "recipient_name" => format_text(stripslashes($recipient_name), 2),
    "recipient_email" => format_text(stripslashes($recipient_email), 2),
    "headline" => format_text(stripslashes($headline), 2),
    "message" => format_text(stripslashes($message), 2),
    "lang_send_postcard" => $lang['send_postcard'],
    "back_url" => format_text(stripslashes($url), 2)
  ));
}

//-----------------------------------------------------
//--- Clickstream -------------------------------------
//-----------------------------------------------------
$clickstream = "<span class=\"clickstream\"><a href=\"".$site_sess->url(ROOT_PATH."index.php")."\" class=\"clickstream\">".$lang['home']."</a>".$config['category_separator'];
if ($mode == "lightbox" && !empty($user_info['lightbox_image_ids'])) {
  $clickstream .= "<a href=\"".$site_sess->url(ROOT_PATH."lightbox.php")."\" class=\"clickstream\">".$lang['lightbox']."</a>".$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id."&amp;mode=".$mode)."\" class=\"clickstream\">".$image_row['image_name']."</a>".$config['category_separator'];
}
elseif ($mode == "search" && !empty($session_info['search_id'])) {
  $clickstream .= "<a href=\"".$site_sess->url(ROOT_PATH."search.php?show_result=1")."\" class=\"clickstream\">".$lang['search']."</a>".$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id."&amp;mode=".$mode)."\" class=\"clickstream\">".$image_row['image_name']."</a>".$config['category_separator'];
}
else {
  $clickstream .= get_category_path($cat_id, 1).$config['category_separator']."<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id)."\" class=\"clickstream\">".$image_row['image_name']."</a>".$config['category_separator'];
}
$clickstream .= $lang['send_postcard']."</span>";

//-----------------------------------------------------
//--- Print Out ---------------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "msg" => $msg,
  "clickstream" => $clickstream,
));
$site_template->print_template($site_template->parse_template($main_template));
include(ROOT_PATH.'includes/page_footer.php');
?>
