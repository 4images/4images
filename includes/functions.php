<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: functions.php                                        *
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
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

function set_download_token($token) {
  global $site_sess, $user_info;

  if ($user_info['user_level'] == ADMIN) {
    return;
  }

  $download_token = @unserialize($site_sess->get_session_var('download_token'));

  if (!$download_token) {
    $download_token = array();
  }

  $download_token[md5($token)] = 1;

  $site_sess->set_session_var('download_token', serialize($download_token));
}

function clear_download_token($token) {
  global $site_sess, $user_info;

  if ($user_info['user_level'] == ADMIN) {
    return;
  }

  $download_token = @unserialize($site_sess->get_session_var('download_token'));

  if (!$download_token) {
    return;
  }

  $token = md5($token);

  if (isset($download_token[$token])) {
    unset($download_token[$token]);
    $site_sess->set_session_var('download_token', serialize($download_token));
  }
}

function check_download_token($token) {
  global $site_sess, $user_info;

  if ($user_info['user_level'] == ADMIN) {
    return true;
  }

  $download_token = @unserialize($site_sess->get_session_var('download_token'));

  if (isset($download_token[md5($token)])) {
    return true;
  }

  return false;
}

function get_gallery_image($image_name) {
  global $config;
  if (file_exists(TEMPLATE_PATH."/images_".$config['language_dir']."/".$image_name)) {
    return TEMPLATE_PATH."/images_".$config['language_dir']."/".$image_name;
  }
  else {
    return TEMPLATE_PATH."/images/".$image_name;
  }
}

function get_basename($path) {
  $path = str_replace("\\", "/", $path);
  $name = substr(strrchr($path, "/"), 1);
  return $name ? $name : $path;
}

function get_basefile($path) {
  $basename = get_basename($path);
  preg_match("#(.+)\?(.+)#", $basename, $regs);
  return isset($regs[1]) ? $regs[1] : $basename;
}

function redirect($url) {
  global $script_url, $site_sess;
  if (strpos($url, '://') === false) {
    $url = $script_url.'/'.$url;
  }
  $location = @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ? 'Refresh: 0; URL=' : 'Location: ';
  if (is_object($site_sess)) {
    $url = $site_sess->url($url, "&");
  }
  header($location.$url);
  exit;
}

function is_remote($file_name) {
  return strpos($file_name, '://') > 0 ? 1 : 0;
}

function is_remote_file($file_name) {
  return is_remote($file_name) && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;
}

function is_local_file($file_name) {
  return !is_remote($file_name) && get_basefile($file_name) != $file_name && preg_match("#\.[a-zA-Z0-9]{1,4}$#", $file_name) ? 1 : 0;
}

function check_remote_media($remote_media_file) {
  global $config;
  return is_remote($remote_media_file) && preg_match("#\.[".$config['allowed_mediatypes_match']."]+$#i", $remote_media_file) ? 1 : 0;
}

function check_local_media($local_media_file) {
  global $config;
  return !is_remote($local_media_file) && get_basefile($local_media_file) != $local_media_file && preg_match("#\.[".$config['allowed_mediatypes_match']."]+$#i", $local_media_file) ? 1 : 0;
}

function check_remote_thumb($remote_thumb_file) {
  return is_remote($remote_thumb_file) && preg_match("#\.(gif|jpg|jpeg|png)$#is", $remote_thumb_file) ? 1 : 0;
}

function check_local_thumb($local_thumb_file) {
  return !is_remote($local_thumb_file) && get_basefile($local_thumb_file) != $local_thumb_file && preg_match("#\.(gif|jpg|jpeg|png)$#i", $local_thumb_file) ? 1 : 0;
}

function get_file_extension($file_name) {
  if (preg_match("#(.+)\.(.+)#", get_basefile($file_name), $regs)) {
    return strtolower($regs[2]);
  }
  return false;
}

function get_file_name($file_name) {
  if (preg_match("#(.+)\.(.+)#", get_basefile($file_name), $regs)) {
    return $regs[1];
  }
  return false;
}

function check_media_type($file_name) {
  global $config;
  return (in_array(get_file_extension($file_name), $config['allowed_mediatypes_array'])) ? 1 : 0;
}

function check_thumb_type($file_name) {
  return (preg_match("#\.(gif|jpg|jpeg|png)$#is", $file_name)) ? 1 : 0;
}

function check_executable($file_name) {
  if (substr(PHP_OS, 0, 3) == "WIN" && !preg_match("#\.exe$#i", $file_name)) {
    $file_name .= ".exe";
  }
  elseif (substr(PHP_OS, 0, 3) != "WIN") {
    $file_name = preg_replace("#\.exe$#i", "", $file_name);
  }
  return $file_name;
}

function get_file_path($file_name = "", $image_type = "media", $cat_id = 0, $in_admin = 0, $return_icon = 1, $check_remote = CHECK_REMOTE_FILES) {
  $return_code = ($return_icon) ? ICON_PATH."/404.gif" : 0;
  if (empty($file_name)) {
    return $return_code;
  }
  if (is_remote($file_name)) {
    $check_handle = "check_remote_".$image_type;
    return ($check_handle($file_name) && remote_file_exists($file_name, $check_remote)) ? (($in_admin && !preg_match("#\.(gif|jpg|jpeg|png)$#is", $file_name)) ? ICON_PATH."/".get_file_extension($file_name).".gif" : $file_name) : $return_code;
  }
  elseif (is_local_file($file_name)) {
    $check_handle = "check_local_".$image_type;
    $file_name = ($in_admin && preg_match("/^([\.]+|[^\/])/", $file_name)) ? "../".$file_name : $file_name;
    if (!file_exists($file_name)) {
      $file_path = preg_replace("/\/{2,}/", "/", get_document_root()."/".$file_name);
      return ($check_handle($file_name) && file_exists($file_path)) ? (($in_admin && !preg_match("#\.(gif|jpg|jpeg|png)$#is", $file_name)) ? ICON_PATH."/".get_file_extension($file_name).".gif" : $file_name) : $return_code;
    }
    else {
      return $file_name;
    }
  }
  else {
    $check_handle = "check_".$image_type."_type";
    $path = (($image_type == "media") ? (($cat_id) ? MEDIA_PATH."/".$cat_id : MEDIA_TEMP_PATH) : (($cat_id) ? THUMB_PATH."/".$cat_id : THUMB_TEMP_PATH))."/".$file_name;
    return ($check_handle($file_name) && file_exists($path)) ? (($in_admin && !preg_match("#\.(gif|jpg|jpeg|png)$#is", $file_name)) ? ICON_PATH."/".get_file_extension($file_name).".gif" : $path) : $return_code;
  }
}

function safe_htmlspecialchars($chars) {
  // Translate all non-unicode entities
  $chars = preg_replace(
    '/&(?!(#[0-9]+|[a-z]+);)/si',
    '&amp;',
    $chars
  );

  $chars = str_replace(">", "&gt;",   $chars);
  $chars = str_replace("<", "&lt;",   $chars);
  $chars = str_replace('"', "&quot;", $chars);
  return $chars;
}

function un_htmlspecialchars($text) {
  $text = str_replace(
    array('&lt;', '&gt;', '&quot;', '&amp;'),
    array('<',    '>',    '"',      '&'),
    $text
  );

  return $text;
}

function get_iptc_info($info) {
  $iptc_match = array();
  $iptc_match['2#120'] = "caption";
  $iptc_match['2#122'] = "caption_writer";
  $iptc_match['2#105'] = "headline";
  $iptc_match['2#040'] = "special_instructions";
  $iptc_match['2#080'] = "byline";
  $iptc_match['2#085'] = "byline_title";
  $iptc_match['2#110'] = "credit";
  $iptc_match['2#115'] = "source";
  $iptc_match['2#005'] = "object_name";
  $iptc_match['2#055'] = "date_created";
  $iptc_match['2#090'] = "city";
  $iptc_match['2#095'] = "state";
  $iptc_match['2#101'] = "country";
  $iptc_match['2#103'] = "original_transmission_reference";
  $iptc_match['2#015'] = "category";
  $iptc_match['2#020'] = "supplemental_category";
  $iptc_match['2#025'] = "keyword";
  $iptc_match['2#116'] = "copyright_notice";

  $iptc = iptcparse($info);
  $iptc_array = array();
  if (is_array($iptc)) {
    foreach ($iptc as $key => $val) {
      if (isset($iptc_match[$key])) {
        $iptc_info = "";
        foreach ($val as $val2) {
          $iptc_info .= (($iptc_info != "" ) ? ", " : "").$val2;
        }
        if ($key == "2#055") {
          $iptc_array[$iptc_match[$key]] = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\3.\\2.\\1", $iptc_info);
        }
        else {
          $iptc_array[$iptc_match[$key]] = $iptc_info;
        }
      }
    }
  }
  return $iptc_array;
}

function get_exif_info($exif) {
  $exif_match = array();
  $exif_match['Make'] = "make";
  $exif_match['Model'] = "model";
  $exif_match['DateTimeOriginal'] = "datetime";
  $exif_match['ISOSpeedRatings'] = "isospeed";
  $exif_match['ExposureTime'] = "exposure";
  $exif_match['FNumber'] = "aperture";
  $exif_match['FocalLength'] = "focallen";

  $exif_array = array();
  if (is_array($exif)) {
    foreach ($exif as $key => $val) {
      if (isset($exif_match[$key])) {
        $exif_info = $val;
        if ($key == "DateTimeOriginal") {
          $exif_array[$exif_match[$key]] = preg_replace("/([0-9]{4}):([0-9]{2}):([0-9]{2})/", "\\3.\\2.\\1", $exif_info);
        }
        elseif ($key == "ExposureTime") {
          $ExposureTime = explode("/", $exif_info);
          if ((float)$ExposureTime[1] == 0) {
            $exif_array[$exif_match[$key]] = "0 sec(s)";
          }
          elseif (($ExposureTime[0]/$ExposureTime[1]) >= 0.3) {
            $exif_array[$exif_match[$key]] = round(($ExposureTime[0]/$ExposureTime[1]),1)." sec(s)";
          }
          else {
            $exif_array[$exif_match[$key]] = "1/".round((1/($ExposureTime[0]/$ExposureTime[1])),0)." sec(s)";
          }
        }
        elseif ($key == "FNumber") {
		  $aperture = explode("/", $exif_info);
          $exif_array[$exif_match[$key]] = "F/" . ($aperture[0] / $aperture[1]);
        }
        elseif ($key == "FocalLength") {
		  $focalLen = explode("/", $exif_info);
          $exif_array[$exif_match[$key]] = ($focalLen[0] / $focalLen[1]) . "mm";
        }
        else {
          $exif_array[$exif_match[$key]] = $exif_info;
        }
      }
    }
  }
  return $exif_array;
}

function show_image($image_row, $mode = "", $show_link = 1, $detailed_view = 0) {
  global $self_url, $site_template, $site_sess, $user_info, $config, $cat_cache, $lang, $additional_image_fields, $user_table_fields, $url_show_profile;

  $is_new = ($image_row['image_date'] >= (time() - 60 * 60 * 24 * $config['new_cutoff'])) ? 1 : 0;
  $description = (!empty($image_row['image_description'])) ? format_text($image_row['image_description'], 1, 0, 1) : REPLACE_EMPTY;

  if (!empty($image_row['image_keywords'])) {
    $split_keywords = explode(",", $image_row['image_keywords']);
    $keywords = "";
    foreach ($split_keywords as $key => $val) {
      $url_val = $val;
      if (preg_match('/[^a-z0-9]+/i', $url_val)) {
        $url_val = '"' . $url_val . '"';
      }
      $keywords .= (($keywords != "" ) ? ", " : "")."<a href=\"".$site_sess->url(ROOT_PATH."search.php?search_keywords=".urlencode($url_val))."\">".format_text($val, 2)."</a>";
    }
  }
  else {
    $keywords = REPLACE_EMPTY;
  }

  if (!check_permission("auth_readcomment", $image_row['cat_id'])) {
    $image_row['image_allow_comments'] = 0;
  }

  $num_comments = ($image_row['image_allow_comments'] == 1) ? $image_row['image_comments'] : "";

  if ($user_info['user_level'] != GUEST) {
    $lightbox_url = $self_url;
    $lightbox_url .= (!empty($mode)) ? ((strpos($lightbox_url, '?') !== false) ? "&amp;" : "?")."mode=".$mode : "";
    $lightbox_url .= strpos($lightbox_url, '?') !== false ? "&amp;" : "?";
    if (check_lightbox($image_row['image_id'])) {
      $lightbox_url .= "action=removefromlightbox&amp;id=".$image_row['image_id'];
      $lightbox_button = "<a href=\"".$site_sess->url($lightbox_url)."\"><img src=\"".get_gallery_image("lightbox_yes.gif")."\" border=\"0\" alt=\"\" /></a>";
    }
    else {
      $lightbox_url .= "action=addtolightbox&amp;id=".$image_row['image_id'];
      $lightbox_button = "<a href=\"".$site_sess->url($lightbox_url)."\"><img src=\"".get_gallery_image("lightbox_no.gif")."\" border=\"0\" alt=\"\" /></a>";
    }
  }
  else {
    $lightbox_button = "<img src=\"".get_gallery_image("lightbox_off.gif")."\" border=\"0\" alt=\"\" />";
  }

  if (!check_permission("auth_download", $image_row['cat_id'])) {
    $download_button = "<img src=\"".get_gallery_image("download_off.gif")."\" border=\"0\" alt=\"\" />";
    $download_zip_button = (function_exists("gzcompress") && function_exists("crc32")) ? "<img src=\"".get_gallery_image("download_zip_off.gif")."\" border=\"0\" alt=\"\" />" : "";
    $allow_download = 0;
    clear_download_token($image_row['image_id']);
  }
  else {
    $target = (!empty($image_row['image_download_url']) && !is_remote_file($image_row['image_download_url']) && !is_local_file($image_row['image_download_url'])) ? "target=\"_blank\"" : "";
    $download_button = "<a href=\"".$site_sess->url(ROOT_PATH."download.php?".URL_IMAGE_ID."=".$image_row['image_id'])."\"".$target."><img src=\"".get_gallery_image("download.gif")."\" border=\"0\" alt=\"\" /></a>";
    $download_zip_button = ($target == "" && function_exists("gzcompress") && function_exists("crc32")) ? "<a href=\"".$site_sess->url(ROOT_PATH."download.php?action=zip&amp;".URL_IMAGE_ID."=".$image_row['image_id'])."\"".$target."><img src=\"".get_gallery_image("download_zip.gif")."\" border=\"0\" alt=\"\" /></a>" : "";
    $allow_download = 1;
    set_download_token($image_row['image_id']);
  }

  if (!check_permission("auth_sendpostcard", $image_row['cat_id'])) {
    $postcard_button = "<img src=\"".get_gallery_image("postcard_off.gif")."\" border=\"0\" alt=\"\" />";
  }
  else {
    $postcard_button = "<a href=\"".$site_sess->url(ROOT_PATH."postcards.php?".URL_IMAGE_ID."=".$image_row['image_id'].((!empty($mode)) ? "&amp;mode=".$mode : ""))."\"><img src=\"".get_gallery_image("postcard.gif")."\" border=\"0\" alt=\"\" /></a>";
  }

  if (!check_permission("auth_viewimage", $image_row['cat_id']) || !check_permission("auth_viewcat", $image_row['cat_id'])) {
    $show_link = 0;
  }

  $file_size = "n/a";
  if (!is_remote($image_row['image_media_file'])) {
    if ($file_size = @filesize(MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file'])) {
      $file_size = format_file_size($file_size);
    }
  }
  elseif ($detailed_view) {
    $file_size = get_remote_file_size($image_row['image_media_file']);
  }

  if (isset($image_row[$user_table_fields['user_name']]) && $image_row['user_id'] != GUEST) {
    $user_name = format_text($image_row[$user_table_fields['user_name']], 2);

    $user_profile_link = (!empty($url_show_profile)) ? str_replace("{user_id}", $image_row['user_id'], $url_show_profile) : ROOT_PATH."member.php?action=showprofile&amp;".URL_USER_ID."=".$image_row['user_id'];
    $user_name_link = "<a href=\"".$site_sess->url($user_profile_link)."\">".$user_name."</a>";
  }
  else {
    $user_name = format_text($lang['userlevel_guest'], 2);
    $user_name_link = $user_name;
  }

  $site_template->register_vars(array(
    "image_id" => $image_row['image_id'],
    "user_id" => $image_row['user_id'],
    "user_name" => $user_name,
    "user_name_link" => $user_name_link,
    "image_name" => format_text($image_row['image_name'], 2),
    "image_description" => $description,
    "image_keywords" => $keywords,
    "image_date" => format_date($config['date_format']." ".$config['time_format'],$image_row['image_date']),
    "image_is_new" => $is_new,
    "lang_new" => $lang['new'],
    "image_active" => $image_row['image_active'],
    "cat_id" => $image_row['cat_id'],
    "cat_name" => format_text($image_row['cat_name'], 2),
    "cat_url" => $site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$image_row['cat_id']),
    "image_downloads" => $image_row['image_downloads'],
    "image_votes" => $image_row['image_votes'],
    "image_rating" => $image_row['image_rating'],
    "image_hits" => $image_row['image_hits'],
    "allow_comments" => $image_row['image_allow_comments'],
    "lang_comments" => $lang['comments'],
    "image_comments" => $num_comments,
    "lightbox_button" => $lightbox_button,
    "postcard_button" => $postcard_button,
    "download_button" => $download_button,
    "download_zip_button" => $download_zip_button,
    "image_download_url" => $image_row['image_download_url'],
    "allow_download" => $allow_download,
    "url_download" => $site_sess->url(ROOT_PATH."download.php?".URL_IMAGE_ID."=".$image_row['image_id']),
    "image_file_size" => $file_size,
    "image_url" => ($show_link) ? $site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_row['image_id'].((!empty($mode)) ? "&amp;mode=".$mode : "")) : "",
    "thumbnail" => get_thumbnail_code($image_row['image_media_file'], $image_row['image_thumb_file'], $image_row['image_id'], $image_row['cat_id'], $image_row['image_name'], $mode, $show_link),
    "thumbnail_openwindow" => get_thumbnail_code($image_row['image_media_file'], $image_row['image_thumb_file'], $image_row['image_id'], $image_row['cat_id'], $image_row['image_name'], $mode, $show_link, 1),
    "image_file_name" => $image_row['image_media_file'],
    "thumbnail_file_name" => $image_row['image_thumb_file']
  ));

  if (!empty($additional_image_fields)) {
    $additional_field_array = array();
    foreach ($additional_image_fields as $key => $val) {
      $additional_field_array[$key] = (!empty($image_row[$key])) ? format_text($image_row[$key], 1) : REPLACE_EMPTY;
      $additional_field_array['lang_'.$key] = $val[0];
    }
    if (!empty($additional_field_array)) {
      $site_template->register_vars($additional_field_array);
    }
  }

  $rate_form = "";
  if (check_permission("auth_vote", $image_row['cat_id'])) {
    $site_template->register_vars("rate", $lang['rate']);
    $rate_form = $site_template->parse_template("rate_form");
  }
  $site_template->register_vars("rate_form", $rate_form);
  $site_template->register_vars(array(
    "image" => get_media_code($image_row['image_media_file'], $image_row['image_id'], $image_row['cat_id'], $image_row['image_name'], $mode, $show_link, $detailed_view),
  ));
  return true;
}

function get_thumbnail_code($media_file_name, $thumb_file_name = "", $image_id, $cat_id, $image_name = "", $mode = "", $show_link = 1, $open_window = 0) {
  global $site_sess, $config;

  if (!check_media_type($media_file_name)) {
    $thumb = "<img src=\"".ICON_PATH."/404.gif\" border=\"0\" alt=\"\" />";
  }
  else {
    if (!get_file_path($thumb_file_name, "thumb", $cat_id, 0, 0)) {
      $file_src = ICON_PATH."/".get_file_extension($media_file_name).".gif";
      $image_info = @getimagesize($file_src);
      $width_height = (!empty($image_info[3])) ? " ".$image_info[3] : "";
      $thumb = "<img src=\"".$file_src."\" border=\"0\"".$width_height." alt=\"".format_text($image_name, 2)."\" title=\"".format_text($image_name, 2)."\" />";
    }
    else {
      $file_src = get_file_path($thumb_file_name, "thumb", $cat_id, 0, 1);
      $image_info = @getimagesize($file_src);
      $width_height = (!empty($image_info[3])) ? " ".$image_info[3] : "";
      $thumb = "<img src=\"".$file_src."\" border=\"".$config['image_border']."\"".$width_height." alt=\"".format_text($image_name, 2)."\" title=\"".format_text($image_name, 2)."\" />";
    }
  }

  if ($show_link) {
    if ($open_window) {
      $thumb = "<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id.((!empty($mode)) ? "&amp;mode=".$mode : ""))."\" onclick=\"opendetailwindow()\" target=\"detailwindow\">".$thumb."</a>";
    }
    else {
      $thumb = "<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id.((!empty($mode)) ? "&amp;mode=".$mode : ""))."\">".$thumb."</a>";
    }
  }
  return $thumb;
}

function get_media_code($media_file_name, $image_id = 0, $cat_id = 0, $image_name = "", $mode = "", $show_link = 0, $detailed_view = 0) {
  global $site_template, $site_sess, $lang, $mode;

  if (!get_file_path($media_file_name, "media", $cat_id, 0, 0)) {
    $media = "<img src=\"".ICON_PATH."/404.gif\" border=\"0\" alt=\"\" />";
    $site_template->register_vars("iptc_info", "");
    $site_template->register_vars("exif_info", "");
  }
  else {
    $media_src = get_file_path($media_file_name, "media", $cat_id, 0, 1);
    $file_extension = get_file_extension($media_file_name);
    $media_icon = "<img src=\"".ICON_PATH."/".$file_extension.".gif\" border=\"0\" alt=\"".format_text($image_name, 2)."\" />";
    if ($show_link) {
      $media_icon = "<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$image_id.((!empty($mode)) ? "&amp;mode=".$mode : ""))."\">".$media_icon."</a>";
    }
    $width_height = "";
    $width = "";
    $height = "";
    $iptc_info = "";
    $exif_info = "";
    if (!is_remote($media_src)) {
      $src = (!file_exists($media_src) && file_exists(preg_replace("/\/{2,}/", "/", get_document_root()."/".$media_src))) ? preg_replace("/\/{2,}/", "/", get_document_root()."/".$media_src) : $media_src;
      if (in_array(strtolower($file_extension), array('gif','jpg','jpeg','png','swf')) && $image_info = @getimagesize($src, $info)) {
        $width_height = " ".$image_info[3];
        $width = $image_info[0];
        $height = $image_info[1];
        if ($detailed_view && isset($info['APP13'])) {
          $iptc_array = get_iptc_info($info['APP13']);
          $bgcounter = 0;
          foreach ($iptc_array as $key => $val) {
            $row_bg_number = ($bgcounter++ % 2 == 0) ? 1 : 2;
            $site_template->register_vars(array(
              "iptc_value" => format_text($val),
              "iptc_name" => $lang['iptc_'.$key],
              "row_bg_number" => $row_bg_number
            ));
            $iptc_info .= $site_template->parse_template("iptc_bit");
          }
        }
        if ($detailed_view && $image_info[2] == 2 && function_exists('exif_read_data') && $exif_data = @exif_read_data($src, 'EXIF')) {
          $exif_array = get_exif_info($exif_data);
          $bgcounter = 0;
          foreach ($exif_array as $key => $val) {
            $row_bg_number = ($bgcounter++ % 2 == 0) ? 1 : 2;
            $site_template->register_vars(array(
              "exif_value" => format_text($val),
              "exif_name" => $lang['exif_'.$key],
              "row_bg_number" => $row_bg_number
            ));
            $exif_info .= $site_template->parse_template("exif_bit");
          }
        }
      }
    }
    $site_template->register_vars(array(
      "media_src" => $media_src,
      "media_icon" => $media_icon,
      "image_name" => format_text($image_name, 2),
      "width_height" => $width_height,
      "width" => $width,
      "height" => $height,
      "iptc_info" => $iptc_info,
      "exif_info" => $exif_info
    ));
    $media = $site_template->parse_template("media/".$file_extension);
  }
  return $media;
}

function get_random_image_cache() {
  global $site_db, $cat_cache, $total_images;

  $random_image_cache = array();
  $cat_id_sql = get_auth_cat_sql("auth_viewcat", "NOTIN");

  if (SHOW_RANDOM_CAT_IMAGE) {
    $sql = "SELECT DISTINCT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits, c.cat_name".get_user_table_field(", u.", "user_name")."
            FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE i.image_active = 1 AND i.cat_id NOT IN ($cat_id_sql) AND c.cat_id = i.cat_id
            ORDER BY RAND()";
    $result = $site_db->query($sql);
    while ($row = $site_db->fetch_array($result)) {
      $random_image_cache[$row['cat_id']] = $row;
    }
  }
  else {
    if (empty($total_images)) {
      $sql = "SELECT COUNT(*) as total_images
              FROM ".IMAGES_TABLE."
              WHERE image_active = 1 AND cat_id NOT IN ($cat_id_sql)";
      $row = $site_db->query_firstrow($sql);
      $total_images = $row['total_images'];
    }
    if (empty($total_images)) {
      return $random_image_cache;
    }
    mt_srand((double)microtime() * 1000000);
    $number = ($total_images > 1) ? mt_rand(0, $total_images - 1) : 0;

    $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits, c.cat_name".get_user_table_field(", u.", "user_name")."
            FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE i.image_active = 1 AND i.cat_id NOT IN ($cat_id_sql) AND c.cat_id = i.cat_id
            LIMIT $number, 1";
    $random_image_cache[0] = $site_db->query_firstrow($sql);
  }
  return $random_image_cache;
}

function get_random_image($cat_id = 0, $show_link = 1, $return_file = 0) {
  global $site_template, $random_image_cache;

  if (!isset($random_image_cache)) {
    $random_image_cache = get_random_image_cache();
  }

  if ($cat_id && SHOW_RANDOM_CAT_IMAGE) {
    $template = 'random_cat_image';
    $category_id = $cat_id;
  }
  else {
    $template = 'random_image';
    if (SHOW_RANDOM_CAT_IMAGE) {
      srand((float)microtime() * 1000000);
      $category_id = array_rand($random_image_cache);
    }
    else {
      $category_id = 0;
    }
  }

  if (!empty($random_image_cache[$category_id])) {
    if (!$return_file) {
      show_image($random_image_cache[$category_id], "", $show_link);
      $random_image = $site_template->parse_template($template);
      return $random_image;
    }
    else {
      return get_file_path($random_image_cache[$category_id]['image_thumb_file'], "thumb", $category_id, 0, 1);
    }
  }
}

function format_file_size($file_size = 0) {
  //$file_size = intval($file_size);
  if (!$file_size) {
    return "n/a";
  }
  if (strlen($file_size) <= 9 && strlen($file_size) >= 7) {
    $file_size = number_format($file_size / 1048576,1);
    return $file_size."&nbsp;MB";
  }
  elseif (strlen($file_size) >= 10) {
    $file_size = number_format($file_size / 1073741824,1);
    return $file_size."&nbsp;GB";
  }
  else {
    $file_size = number_format($file_size / 1024,1);
    return $file_size."&nbsp;KB";
  }
}

function get_remote_file_size($file_path) {
  if (!CHECK_REMOTE_FILES) {
    return 'n/a';
  }
  ob_start();
  @readfile($file_path);
  $file_data = ob_get_contents();
  ob_end_clean();
  return format_file_size(strlen($file_data));
}

function update_comment_count($image_id = 0, $user_id = 0) {
  global $site_db, $user_table_fields;
  if ($image_id) {
    $sql = "SELECT COUNT(comment_id) AS comments
            FROM ".COMMENTS_TABLE."
            WHERE image_id = $image_id";
    $countcomments = $site_db->query_firstrow($sql);
    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_comments = ".$countcomments['comments']."
            WHERE image_id = $image_id";
    $site_db->query($sql);
  }
  if ($user_id != GUEST && $user_id && !empty($user_table_fields['user_comments'])) {
    $sql = "SELECT COUNT(comment_id) AS comments
            FROM ".COMMENTS_TABLE."
            WHERE user_id = $user_id";
    $countcomments = $site_db->query_firstrow($sql);
    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_comments")." = ".$countcomments['comments']."
            WHERE ".get_user_table_field("", "user_id")." = $user_id";
    $site_db->query($sql);
  }
}

function update_image_rating($image_id, $rating) {
  global $site_db;
  $sql = "SELECT cat_id, image_votes, image_rating
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id";
  $image_row = $site_db->query_firstrow($sql);
  if (check_permission("auth_vote", $image_row['cat_id'])) {
    $old_votes = $image_row['image_votes'];
    $old_rating = $image_row['image_rating'];
    $new_rating = (($old_rating * $old_votes) + $rating) / ($old_votes + 1);
    $new_rating = sprintf("%.2f", $new_rating);
    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_votes = ($old_votes + 1), image_rating = '$new_rating'
            WHERE image_id = $image_id";
    $site_db->query($sql);
  }
}

function check_email($email) {
  return (preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,}$/i', $email)) ? 1 : 0;
}

function format_date($format, $timestamp) {
  global $user_info;
  $timezone_offset = (defined("TIME_OFFSET")) ? TIME_OFFSET : 0;
  return date($format, $timestamp + (3600 * $timezone_offset));
}

function format_url($url) {
  if (empty($url)) {
    return '';
  }

  if (!preg_match("/^https?:\/\//i", $url)) {
    $url = "http://".$url;
  }

  return htmlspecialchars($url);
}

function replace_url($text) {
  $text = " ".$text." ";
  $url_search_array = array(
    "#([^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \(\)<>\n\r]*)?)#si",
    "#([^]_a-z0-9-=\"'\/])([a-z]+?)://([^, \(\)<>\n\r]+)#si"
  );

  $url_replace_array = array(
    "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\" rel=\"nofollow\">www.\\2.\\3\\4</a>",
    "\\1<a href=\"\\2://\\3\" target=\"_blank\" rel=\"nofollow\">\\2://\\3</a>",
  );
  $text = preg_replace($url_search_array, $url_replace_array, $text);

  if (strpos($text, "@")) {
    $text = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);
  }
  return substr($text, 1, -1);
}

function replace_badwords($text) {
  global $config, $split_badwords;
  if ($config['badword_list'] != "") {
    if (!isset($split_badwords)) {
      $badwords = trim($config['badword_list']);
      $badwords = preg_replace("/[\n\r]/is", " ", $badwords);
      $badwords = str_replace(","," ",$badwords);
      $badwords = preg_quote($badwords);
      $badwords = str_replace('/', '\\/', $badwords);
      $split_badwords = preg_split("/\s+/", $badwords);
    }

    foreach ($split_badwords as $key => $val) {
      if ($val != "") {
        if (substr($val, 0, 2) == "\\{") {
          $val = substr($val, 2, -2);
          $text = trim(preg_replace("/([^A-Za-z])".$val."(?=[^A-Za-z])/si", "\\1".str_repeat($config['badword_replace_char'], strlen($val)), " $text "));
        }
        else {
          $text = trim(preg_replace("/$val/si", str_repeat($config['badword_replace_char'], strlen($val)), " $text "));
        }
      }
    }
  }
  return $text;
}

function format_text($text, $html = 0, $word_wrap = 0, $bbcode = 0, $bbcode_img = 0) {
  if ($word_wrap && $text != "") {
    $text = preg_replace("/([^\n\r ?&\.\/<>\"\\-]{".$word_wrap."})/i", " \\1\n", $text);
  }

  if ($html == 0 || $html == 2) {
    $text = safe_htmlspecialchars($text);
  }

  // Replace { to prevent parsing in templates
  global $site_template;
  $text = preg_replace(
    '='.preg_quote($site_template->start).'([A-Z0-9_]+)'.preg_quote($site_template->end).'=Usi',
    '&#123;\1&#125;',
    $text
  );

  if ($html !== 2) {
    $text = nl2br(trim($text));
    $text = replace_url($text);
  }

  if ($bbcode == 1) {
    $search_array = array(
      "/(\[)(list)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/list)(((=)(\\4)([^\"']*)(\\4]))|(\]))/siU",
      "/(\[)(list)(])(.*)(\[\/list\])/siU",
      "/(\[\*\])/siU",
      "/(\[\/\*\])/siU",
      "/(\[)(url)(=)(['\"]?)(www\.)([^\"']*)(\\4])(.*)(\[\/url\])/siU",
      "/(\[)(url)(=)(['\"]?)([^\"']*)(\\4])(.*)(\[\/url\])/siU",
      "/(\[)(url)(])(www\.)([^\"]*)(\[\/url\])/siU",
      "/(\[)(url)(])([^\"]*)(\[\/url\])/siU",
      "/(\[)(code)(])(\r\n)*(.*)(\[\/code\])/siU",
      "/javascript:/si",
      "/about:/si"
    );
    $replace_array = array(
      "<ol type=\"\\5\">\\7</ol>",
      "<ul>\\4</ul>",
      "<li>",
      "</li>",
      "<a href=\"http://www.\\6\" target=\"_blank\" rel=\"nofollow\">\\8</a>",
      "<a href=\"\\5\" target=\"_blank\" rel=\"nofollow\">\\7</a>",
      "<a href=\"http://www.\\5\" target=\"_blank\" rel=\"nofollow\">www.\\5</a>",
      "<a href=\"\\4\" target=\"_blank\" rel=\"nofollow\">\\4</a>",
      "<pre>Code:<hr size=1>\\5<hr size=1></pre>",
      "java script:",
      "about :"
    );
    $text = preg_replace($search_array, $replace_array, $text);
    if (!$bbcode_img)  {
      $text = preg_replace("/(\[)(img)(])(\r\n)*([^\"]*)(\[\/img\])/siU", "<a href=\"\\5\" target=\"_blank\">\\5</a>", $text);
    }
    else  {
      $text = preg_replace("/(\[)(img)(])(\r\n)*([^\"]*)(\[\/img\])/siU", "<img src=\"\\5\">", $text);
    }
    $text = preg_replace("/(\[)(b)(])(\r\n)*([^\"]*)(\[\/b\])/siU", "<b>\\5</b>", $text);
    $text = preg_replace("/(\[)(i)(])(\r\n)*([^\"]*)(\[\/i\])/siU", "<i>\\5</i>", $text);
    $text = preg_replace("/(\[)(u)(])(\r\n)*([^\"]*)(\[\/u\])/siU", "<u>\\5</u>", $text);

    $text = replace_badwords($text);
  }

  $text = str_replace("\\'", "'", $text);

  return $text;
}

function utf8_to_htmlentities($source) {
  // array used to figure what number to decrement from character order
  // value
  // according to number of characters used to map unicode to ascii by
  // utf-8
  $decrement = array();
  $decrement[4] = 240;
  $decrement[3] = 224;
  $decrement[2] = 192;
  $decrement[1] = 0;

  // the number of bits to shift each charNum by
  $shift = array();
  $shift[1][0] = 0;
  $shift[2][0] = 6;
  $shift[2][1] = 0;
  $shift[3][0] = 12;
  $shift[3][1] = 6;
  $shift[3][2] = 0;
  $shift[4][0] = 18;
  $shift[4][1] = 12;
  $shift[4][2] = 6;
  $shift[4][3] = 0;

  $pos = 0;
  $len = strlen($source);
  $str = '';
  while ($pos < $len) {
    $asciiPos = ord(substr($source, $pos, 1));
    if (($asciiPos >= 240) && ($asciiPos <= 255)) {
      // 4 chars representing one unicode character
      $thisLetter = substr($source, $pos, 4);
      $pos += 4;
    }
    elseif (($asciiPos >= 224) && ($asciiPos <= 239)) {
      // 3 chars representing one unicode character
      $thisLetter = substr($source, $pos, 3);
      $pos += 3;
    }
    else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
      // 2 chars representing one unicode character
      $thisLetter = substr($source, $pos, 2);
      $pos += 2;
    }
    else {
      // 1 char (lower ascii)
      $thisLetter = substr($source, $pos, 1);
      $pos += 1;
    }

    // process the string representing the letter to a unicode entity
    $thisLen = strlen($thisLetter);
    $thisPos = 0;
    $decimalCode = 0;

    while ($thisPos < $thisLen) {
      $thisCharOrd = ord(substr($thisLetter, $thisPos, 1));
      if ($thisPos == 0) {
        $charNum = intval($thisCharOrd - $decrement[$thisLen]);
        $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
      } else {
        $charNum = intval($thisCharOrd - 128);
        $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
      }
      $thisPos++;
    }
    if (($thisLen == 1) && ($decimalCode <= 128)) {
      $encodedLetter = $thisLetter;
    }
    else {
      $encodedLetter = '&#' . $decimalCode . ';';
    }
    $str .= $encodedLetter;
  }
  return $str;
}

function uni_to_utf8($char) {
  $char = intval($char);

  switch ($char) {
    case ($char < 128) :
      // its an ASCII char no encoding needed
      return chr($char);

    case ($char < 1 << 11) :
      // its a 2 byte UTF-8 char
      return chr(192 + ($char >> 6)) .
             chr(128 + ($char & 63));

    case ($char < 1 << 16) :
      // its a 3 byte UTF-8 char
      return chr(224 + ($char >> 12)) .
             chr(128 + (($char >> 6) & 63)) .
             chr(128 + ($char & 63));

    case ($char < 1 << 21) :
      // its a 4 byte UTF-8 char
      return chr(240 + ($char >> 18)) .
             chr(128 + (($char >> 12) & 63)) .
             chr(128 + (($char >>  6) & 63)) .
             chr(128 + ($char & 63));

    case ($char < 1 << 26) :
      // its a 5 byte UTF-8 char
      return chr(248 + ($char >> 24)) .
             chr(128 + (($char >> 18) & 63)) .
             chr(128 + (($char >> 12) & 63)) .
             chr(128 + (($char >> 6) & 63)) .
             chr(128 + ($char & 63));
    default:
      // its a 6 byte UTF-8 char
      return chr(252 + ($char >> 30)) .
             chr(128 + (($char >> 24) & 63)) .
             chr(128 + (($char >> 18) & 63)) .
             chr(128 + (($char >> 12) & 63)) .
             chr(128 + (($char >> 6) & 63)) .
             chr(128 + ($char & 63));
  }
}

function get_user_info($user_id = 0) {
  global $site_db, $user_table_fields;
  $user_info = 0;
  if ($user_id != 0 && $user_id != GUEST) {
    $sql = "SELECT *
            FROM ".USERS_TABLE."
            WHERE ".get_user_table_field("", "user_id")." = $user_id";
    if ($user_info = $site_db->query_firstrow($sql)) {
      foreach ($user_table_fields as $key => $val) {
        if (isset($user_info[$val])) {
          $user_info[$key] = $user_info[$val];
        }
        elseif (!isset($user_info[$key])) {
          $user_info[$key] = "";
        }
      }
    }
  }
  return $user_info;
}

function get_icq_status($uin) {
  if (!is_numeric($uin)) return false;

  $fp = @fsockopen('status.icq.com', 80, $errno, $errstr, 8);
  if (!$fp) return false;

  $request = "HEAD /online.gif?icq=$uin&img=3 HTTP/1.0\r\n"
            ."Host: status.icq.com\r\n"
            ."Connection: close\r\n\r\n";
  fputs($fp, $request);

  do {
     $response = fgets($fp, 1024);
  }
  while (!feof($fp) && !stristr($response, 'Location'));

  fclose($fp);
  if (strstr($response, 'online1')) return 'online';
  if (strstr($response, 'online0')) return 'offline';
  if (strstr($response, 'online2')) return 'disabled';
  return FALSE;
}

function add_to_lightbox($id) {
  global $user_info, $site_db;
  $id = intval($id);
  if (!$id) {
    return false;
  }
  $lightbox_ids = $user_info['lightbox_image_ids'];
  $lightbox_array = explode(" ", $lightbox_ids);
  if (!in_array($id, $lightbox_array)) {
    $lightbox_ids .= " ".$id;
  }
  $user_info['lightbox_image_ids'] = trim($lightbox_ids);
  $user_info['lightbox_lastaction'] = time();
  $sql = "UPDATE ".LIGHTBOXES_TABLE."
          SET lightbox_lastaction = ".$user_info['lightbox_lastaction'].", lightbox_image_ids = '".$user_info['lightbox_image_ids']."'
          WHERE user_id = ".$user_info['user_id'];
  return ($site_db->query($sql)) ? 1 : 0;
}

function remove_from_lightbox($id) {
  global $user_info, $site_db;
  $lightbox_array = explode(" ",$user_info['lightbox_image_ids']);
  foreach ($lightbox_array as $key => $val) {
    if ($val == $id) {
      unset($lightbox_array[$key]);
    }
  }
  $user_info['lightbox_image_ids'] = trim(implode(" ", $lightbox_array));
  $user_info['lightbox_lastaction'] = time();
  $sql = "UPDATE ".LIGHTBOXES_TABLE."
          SET lightbox_lastaction = ".$user_info['lightbox_lastaction'].", lightbox_image_ids = '".$user_info['lightbox_image_ids']."'
          WHERE user_id = ".$user_info['user_id'];
  return ($site_db->query($sql)) ? 1 : 0;
}

function clear_lightbox() {
  global $user_info, $site_db;
  $current_time = time();
  $sql = "UPDATE ".LIGHTBOXES_TABLE."
          SET lightbox_image_ids = '', lightbox_lastaction = $current_time
          WHERE user_id = ".$user_info['user_id'];
  if ($site_db->query($sql)) {
    $user_info['lightbox_image_ids'] = "";
    $user_info['lightbox_lastaction'] = $current_time;
    return true;
  }
  else {
    return false;
  }
}

function check_lightbox($id) {
  global $user_info;
  $lightbox_array = explode(" ", $user_info['lightbox_image_ids']);
  return in_array($id, $lightbox_array);
}

function get_random_key($db_table = "", $db_column = "") {
  global $site_db;
  $key = md5(uniqid(microtime()));
  if ($db_table != "" && $db_column != "") {
    $i = 0;
    while ($i == 0) {
      $sql = "SELECT ".$db_column."
              FROM ".$db_table."
              WHERE ".$db_column." = '$key'";
      if ($site_db->is_empty($sql)) {
        $i = 1;
      }
      else {
        $i = 0;
        $key = md5(uniqid(microtime()));
      }
    }
  }
  return $key;
}

function get_subcat_ids($cid = 0, $cat_id = 0, $cat_parent_cache) {
  global $subcat_ids;

  if (!isset($cat_parent_cache[$cid])) {
    return false;
  }
  foreach ($cat_parent_cache[$cid] as $key => $val) {
    if (check_permission("auth_viewcat", $val)) {
      $subcat_ids[$cat_id][] = $val;
      get_subcat_ids($val, $cat_id, $cat_parent_cache);
    }
  }
  return $subcat_ids;
}

function get_subcategories($parent_id) {
  global $cat_parent_cache, $cat_cache, $site_sess, $config;

  if (!isset($cat_parent_cache[$parent_id]) || $config['num_subcats'] < 1) {
    return "";
  }

  $visible_cat_cache = array();
  foreach ($cat_parent_cache[$parent_id] as $key => $val) {
    if (check_permission("auth_viewcat", $val)) {
      $visible_cat_cache[$key] = $val;
    }
  }

  $num_subs = sizeof($visible_cat_cache);
  $sub_cat_list = "";
  $i = 1;
  foreach ($visible_cat_cache as $subcat_id) {
    if ($i <= $num_subs && $i <= $config['num_subcats']) {
      $sub_url = $site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$subcat_id);
      $sub_cat_list .= "<a href=\"".$sub_url."\" class=\"subcat\">".format_text($cat_cache[$subcat_id]['cat_name'], 2)."</a>";
      if ($i != $config['num_subcats'] && $i < $config['num_subcats'] && $i < $num_subs) {
        $sub_cat_list .= ", ";
      }
      if ($i == $config['num_subcats'] && $i < $num_subs) {
        $sub_cat_list .= " ...\n";
      }
    }
    $i++;
  }
  return $sub_cat_list;
}

function get_categories($cat_id = 0) {
  global $site_template, $site_db, $site_sess, $config, $lang;
  global $cat_cache, $cat_parent_cache, $new_image_cache, $subcat_ids;

  $cattable_width = ceil((intval($config['cat_table_width'])) / $config['cat_cells']);
  if ((substr($config['cat_table_width'],-1)) == "%") {
    $cattable_width .= "%";
  }

  if (!isset($cat_parent_cache[$cat_id])) {
    return "";
  }

  $visible_cat_cache = array();
  foreach ($cat_parent_cache[$cat_id] as $key => $val) {
    if (check_permission("auth_viewcat", $val)) {
      $visible_cat_cache[$key] = $val;
    }
  }

  if (empty($visible_cat_cache)) {
    return "";
  }

  $total = sizeof($visible_cat_cache);
  $table_columns = (intval($config['cat_cells'])) ? intval($config['cat_cells']) : 2;
  if ($total <= $table_columns) {
    $table_rows = 1;
  }
  else {
    $table_rows = $total / $table_columns;
    if ($total >= $table_columns && !is_integer($table_rows)) {
      $table_rows = intval($table_rows) + 1;
    }
  }

  $categories = "\n<table width=\"".$config['cat_table_width']."\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n<tr>\n<td valign=\"top\" width=\"".$cattable_width."\" class=\"catbgcolor\">\n";
  $categories .= "<table border=\"0\" cellpadding=\"".$config['cat_table_cellpadding']."\" cellspacing=\"".$config['cat_table_cellspacing']."\">\n";
  $count = 0;
  $count2 = 0;
  foreach ($visible_cat_cache as $key => $category_id) {
    $categories .= "<tr>\n<td valign=\"top\">\n";

    $is_new = (isset($new_image_cache[$category_id]) && $new_image_cache[$category_id] > 0) ? 1 : 0;
    $num_images = (isset($cat_cache[$category_id]['num_images'])) ? $cat_cache[$category_id]['num_images'] : 0;

    $subcat_ids = array();
    get_subcat_ids($category_id, $category_id, $cat_parent_cache);

    if (isset($subcat_ids[$category_id])) {
      foreach ($subcat_ids[$category_id] as $val) {
        if (isset($new_image_cache[$val]) && $new_image_cache[$val] > 0) {
          $is_new = 1;
        }
        if (isset($cat_cache[$val]['num_images'])) {
          $num_images += $cat_cache[$val]['num_images'];
        }
      }
    }

    if (defined("SHOW_RANDOM_IMAGE") && SHOW_RANDOM_IMAGE == 0 || defined("SHOW_RANDOM_CAT_IMAGE") && SHOW_RANDOM_CAT_IMAGE == 0) {
      $random_cat_image_file = "";
    }
    else {
      $random_cat_image_file = get_random_image($category_id, 0, 1);
    }

    $site_template->register_vars(array(
      "cat_id" => $category_id,
      "cat_name" => format_text($cat_cache[$category_id]['cat_name'], 2),
      "cat_description" => htmlspecialchars(format_text($cat_cache[$category_id]['cat_description'], 1)),
      "cat_hits" => $cat_cache[$category_id]['cat_hits'],
      "cat_is_new" => $is_new,
      "lang_new" => $lang['new'],
      "sub_cats" => get_subcategories($category_id),
      "cat_url" => $site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$category_id),
      "random_cat_image_file" => $random_cat_image_file,
      "num_images" => $num_images
    ));
    $categories .= $site_template->parse_template("category_bit");
    $count++;
    $count2++;
    $categories .= "</td>\n</tr>\n";

    if ($count == $table_rows && $count2 < sizeof($visible_cat_cache)) {
      $categories .= "</table></td>\n";
      $categories .= "<td valign=\"top\" width=\"".$cattable_width."\" class=\"catbgcolor\">\n";
      $categories .= "<table border=\"0\" cellpadding=\"".$config['cat_table_cellpadding']."\" cellspacing=\"".$config['cat_table_cellspacing']."\">\n";

      $total = $total - $count2;
      $table_columns = $table_columns - 1;
      /*if ($total <= $table_columns && $table_columns > 1) {
        $table_rows = 1;
      }
      else {
        $table_rows = $total / $table_columns;
        if ($total >= $table_columns && !is_integer($table_rows)) {
          $table_rows = intval($table_rows) + 1;
        }
      }*/
      $count = 0;
    }
  }

  $categories .= "</table>\n</td>\n</tr>\n</table>\n";
  return $categories;
}

function get_category_path($cat_id = 0, $detail_path = 0) {
  global $site_sess, $config, $cat_cache, $url;
  $parent_id = 1;
  while ($parent_id) {
    if (!isset($cat_cache[$cat_id]['cat_parent_id'])) {
      return false;
    }
    $parent_id = $cat_cache[$cat_id]['cat_parent_id'];

    if (empty($path)) {
      if ($detail_path) {
        $cat_url = ROOT_PATH."categories.php?".URL_CAT_ID."=".$cat_id;
        if (preg_match("/".URL_PAGE."=([0-9]+)/", $url, $regs)) {
          if (!empty($regs[1]) && $regs[1] != 1) {
            $cat_url .= "&amp;".URL_PAGE."=".$regs[1];
          }
        }
        $path = "<a href=\"".$site_sess->url($cat_url)."\" class=\"clickstream\">".format_text($cat_cache[$cat_id]['cat_name'], 2)."</a>";
      }
      else  {
        $path = format_text($cat_cache[$cat_id]['cat_name'], 2);
      }
    }
    else {
      $path = "<a href=\"".$site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$cat_id)."\" class=\"clickstream\">".format_text($cat_cache[$cat_id]['cat_name'], 2)."</a>".$config['category_separator'].$path;
    }
    $cat_id = $parent_id;
  } // end while
  return $path;
}

function get_category_dropdown_bits($cat_id, $cid = 0, $depth = 1) {
  global $site_db, $drop_down_cat_cache, $cat_cache;

  if (!isset($drop_down_cat_cache[$cid])) {
    return "";
  }
  $category_list = "";
  foreach ($drop_down_cat_cache[$cid] as $key => $category_id) {
    if (check_permission("auth_viewcat", $category_id)) {
      $category_list .= "<option value=\"".$category_id."\"";
      if ($cat_id == $category_id) {
        $category_list .= " selected=\"selected\"";
      }
      if ($cat_cache[$category_id]['cat_parent_id'] == 0) {
        $category_list .= " class=\"dropdownmarker\"";
      }

      if ($depth > 1) {
        $category_list .= ">".str_repeat("--", $depth - 1)." ".format_text($cat_cache[$category_id]['cat_name'], 2)."</option>\n";
      }
      else {
        $category_list .= ">".format_text($cat_cache[$category_id]['cat_name'], 2)."</option>\n";
      }
      $category_list .= get_category_dropdown_bits($cat_id, $category_id, $depth + 1);
    }
  }
  unset($drop_down_cat_cache[$cid]);
  return $category_list;
}

function get_category_dropdown($cat_id, $jump = 0, $admin = 0, $i = 0) {
  global $lang, $drop_down_cat_cache, $cat_parent_cache;
  // $admin = 1  Main Cat (update/add cats)
  // $admin = 2  All Cats (find/validate images...)
  // $admin = 3  Select Cat (update/add image)
  // $admin = 4  No Cat (check new images)

  switch ($admin) {
  case 1:
    $category = "\n<select name=\"cat_parent_id\" class=\"categoryselect\">\n";
    $category .= "<option value=\"0\">".$lang['main_category']."</option>\n";
    $category .= "<option value=\"0\">--------------</option>\n";
    break;

  case 2:
    $category = "\n<select name=\"cat_id\" class=\"categoryselect\">\n";
    $category .= "<option value=\"0\">".$lang['all_categories']."</option>\n";
    $category .= "<option value=\"0\">-------------------------------</option>\n";
    break;

  case 3:
    $i = ($i) ? "_".$i : "";
    $category = "\n<select name=\"cat_id".$i."\" class=\"categoryselect\">\n";
    $category .= "<option value=\"0\">".$lang['select_category']."</option>\n";
    $category .= "<option value=\"0\">-------------------------------</option>\n";
    break;

  case 4:
    $category = "\n<select name=\"cat_id\" class=\"categoryselect\">\n";
    $category .= "<option value=\"0\">".$lang['no_category']."</option>\n";
    $category .= "<option value=\"0\">-------------------------------</option>\n";
    break;

  case 0:
  default:
    if ($jump) {
      $category = "\n<select name=\"".URL_CAT_ID."\" onchange=\"if (this.options[this.selectedIndex].value != 0){ forms['jumpbox'].submit() }\" class=\"categoryselect\">\n";
    }
    else {
      $category = "\n<select name=\"".URL_CAT_ID."\" class=\"categoryselect\">\n";
    }
    $category .= "<option value=\"0\">".$lang['select_category']."</option>\n";
    $category .= "<option value=\"0\">-------------------------------</option>\n";
  } // end switch

  $drop_down_cat_cache = array();
  $drop_down_cat_cache = $cat_parent_cache;
  $category .= get_category_dropdown_bits($cat_id);
  $category .= "</select>\n";
  return $category;
}

function show_error_page($error_msg, $clickstream = "") {
  global $site_template, $site_sess, $lang, $config;
  if (empty($clickstream)) {
    $clickstream = "<a href=\"".$site_sess->url(ROOT_PATH."index.php")."\">".$lang['home']."</a>".$config['category_separator'].$lang['error'];
  }
  $site_template->register_vars(array(
    "error_msg" => $error_msg,
    "lang_error" => $lang['error'],
    "clickstream" => $clickstream,
    "random_image" => ""
  ));
  $site_template->print_template($site_template->parse_template("error"));
  exit;
}

function get_mysql_version() {
  global $global_info, $site_db;
  if (!empty($global_info['mysql_version'])) {
    return $global_info['mysql_version'];
  }
  $split_info = array();
  if ($row = $site_db->query_firstrow("SELECT VERSION() AS version")) {
    $split_info = explode('.', $row['version']);
  }
  else {
    if ($row = $site_db->query_firstrow("SHOW VARIABLES LIKE 'version'")){
      $split_info = explode('.', $row[1]);
    }
  }
  $first  = (empty($split_info) || empty($split_info[0])) ? 3 : intval($split_info[0]);
  $second = (empty($split_info[1])) ? 21 : intval($split_info[1]);
  $third  = (empty($split_info[2])) ? 0 : intval($split_info[2]);
  $global_info['mysql_version'] = sprintf('%d%02d%02d', $first, $second, $third);
  return $global_info['mysql_version'];
}

function get_php_version() {
  global $global_info;
  if (!empty($global_info['php_version'])) {
    return $global_info['php_version'];
  }
  $split_info = array();
  preg_match("/([0-9]{1,2})\.([0-9]{1,2})(\.([0-9]{1,2}))?/", phpversion(), $split_info);

  $global_info['php_version'] = 0;
  if (!empty($split_info) && !empty($split_info[1])) {
    $first  = intval($split_info[1]);
    $second = (empty($split_info[2])) ? 0 : intval($split_info[2]);
    $third  = (empty($split_info[4])) ? 0 : intval($split_info[4]);
    $global_info['php_version'] = sprintf('%d%02d%02d', $first, $second, $third);
  }
  return $global_info['php_version'];
}

function get_user_os() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (!empty($global_info['user_os'])) {
    return $global_info['user_os'];
  }
  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (preg_match("#Win#i", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "WIN";
  }
  elseif (preg_match("#Mac#i", $HTTP_USER_AGENT)) {
    $global_info['user_os'] = "MAC";
  }
  else {
    $global_info['user_os'] = "OTHER";
  }
  return $global_info['user_os'];
}

function get_browser_info() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;
  if (!empty($global_info['browser_agent'])) {
    return $global_info['browser_agent'];
  }
  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (preg_match("#MSIE ([0-9].[0-9]{1,2})#i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MSIE";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (preg_match("#Mozilla/([0-9].[0-9]{1,2})#i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MOZILLA";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (preg_match("#Opera(/| )([0-9].[0-9]{1,2})#i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "OPERA";
    $global_info['browser_version'] = $regs[2];
  }
  else {
    $global_info['browser_agent'] = "OTHER";
    $global_info['browser_version'] = 0;
  }
  return $global_info['browser_agent'];
}

function get_document_root() {
  global $global_info, $DOCUMENT_ROOT, $HTTP_SERVER_VARS;
  if (!empty($global_info['document_root'])) {
    return $global_info['document_root'];
  }
  if (!empty($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
    $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
  }
  elseif (getenv("DOCUMENT_ROOT")) {
    $DOCUMENT_ROOT = getenv("DOCUMENT_ROOT");
  }
  elseif (empty($DOCUMENT_ROOT)) {
    $DOCUMENT_ROOT = "";
  }
  return $global_info['document_root'] = $DOCUMENT_ROOT;
}

function remote_file_exists($url, $check_remote = CHECK_REMOTE_FILES) { // similar to file_exists(), checks existence of remote files
  if (!$check_remote || !CHECK_REMOTE_FILES) {
    return true;
  }
  $url = trim($url);
  if (!preg_match("=://=", $url)) $url = "http://$url";
  if (!($url = @parse_url($url))) {
    return false;
  }
  if (!preg_match("#http#i", $url['scheme'])) {
    return false;
  }
  $url['port'] = (!isset($url['port'])) ? 80 : $url['port'];
  $url['path'] = (!isset($url['path'])) ? "/" : $url['path'];
  $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
  if (!$fp) {
    return false;
  }
  else {
    $head = "";
    $httpRequest = "HEAD ".$url['path']." HTTP/1.1\r\n"
                  ."HOST: ".$url['host']."\r\n"
                  ."Connection: close\r\n\r\n";
    fputs($fp, $httpRequest);
    while (!feof($fp)) {
      $head .= fgets($fp, 1024);
    }
    fclose($fp);

    preg_match("=^(HTTP/\d+\.\d+) (\d{3}) ([^\r\n]*)=", $head, $matches);
    if ($matches[2] == 200) {
      return true;
    }
  }
}

if (!function_exists('is_executable')) {
  function is_executable($file) {
    return is_file($file);
  }
}

if (!function_exists('session_regenerate_id')) {
  function session_regenerate_id() {
   $id = md5(uniqid(microtime()));
   if (session_id($id)) {
     return true;
   } else {
     return false;
   }
  }
}

function get_mime_content_type($file) {
    if (function_exists('mime_content_type')) {
      $type = mime_content_type($file);
      if ($type) {
        return $type;
      }
    }

    $info = @getimagesize($file);

    if (isset($info['mime'])) {
      return $info['mime'];
    }

    $type = @exec(trim('file -bi '.escapeshellarg($file)));

    if (strpos($type, ';') !== false) {
      list($type) = explode(';', $type);
    }

    if ($type) {
      return $type;
    }

    static $types = array(
      'ai' => 'application/postscript',
     'aif' => 'audio/x-aiff',
    'aifc' => 'audio/x-aiff',
    'aiff' => 'audio/x-aiff',
     'asc' => 'text/plain',
      'au' => 'audio/basic',
     'avi' => 'video/x-msvideo',
   'bcpio' => 'application/x-bcpio',
     'bin' => 'application/octet-stream',
       'c' => 'text/plain',
      'cc' => 'text/plain',
    'ccad' => 'application/clariscad',
     'cdf' => 'application/x-netcdf',
   'class' => 'application/octet-stream',
    'cpio' => 'application/x-cpio',
     'cpt' => 'application/mac-compactpro',
     'csh' => 'application/x-csh',
     'css' => 'text/css',
     'dcr' => 'application/x-director',
     'dir' => 'application/x-director',
     'dms' => 'application/octet-stream',
     'doc' => 'application/msword',
     'drw' => 'application/drafting',
     'dvi' => 'application/x-dvi',
     'dwg' => 'application/acad',
     'dxf' => 'application/dxf',
     'dxr' => 'application/x-director',
     'eps' => 'application/postscript',
     'etx' => 'text/x-setext',
     'exe' => 'application/octet-stream',
      'ez' => 'application/andrew-inset',
       'f' => 'text/plain',
     'f90' => 'text/plain',
     'fli' => 'video/x-fli',
     'gif' => 'image/gif',
    'gtar' => 'application/x-gtar',
      'gz' => 'application/x-gzip',
       'h' => 'text/plain',
     'hdf' => 'application/x-hdf',
      'hh' => 'text/plain',
     'hqx' => 'application/mac-binhex40',
     'htm' => 'text/html',
    'html' => 'text/html',
     'ice' => 'x-conference/x-cooltalk',
     'ief' => 'image/ief',
    'iges' => 'model/iges',
     'igs' => 'model/iges',
     'ips' => 'application/x-ipscript',
     'ipx' => 'application/x-ipix',
     'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
     'jpg' => 'image/jpeg',
      'js' => 'application/x-javascript',
     'kar' => 'audio/midi',
   'latex' => 'application/x-latex',
     'lha' => 'application/octet-stream',
     'lsp' => 'application/x-lisp',
     'lzh' => 'application/octet-stream',
       'm' => 'text/plain',
     'man' => 'application/x-troff-man',
      'me' => 'application/x-troff-me',
    'mesh' => 'model/mesh',
     'mid' => 'audio/midi',
    'midi' => 'audio/midi',
     'mif' => 'application/vnd.mif',
    'mime' => 'www/mime',
     'mov' => 'video/quicktime',
   'movie' => 'video/x-sgi-movie',
     'mp2' => 'audio/mpeg',
     'mp3' => 'audio/mpeg',
     'mpe' => 'video/mpeg',
    'mpeg' => 'video/mpeg',
     'mpg' => 'video/mpeg',
    'mpga' => 'audio/mpeg',
      'ms' => 'application/x-troff-ms',
     'msh' => 'model/mesh',
      'nc' => 'application/x-netcdf',
     'oda' => 'application/oda',
     'pbm' => 'image/x-portable-bitmap',
     'pdb' => 'chemical/x-pdb',
     'pdf' => 'application/pdf',
     'pgm' => 'image/x-portable-graymap',
     'pgn' => 'application/x-chess-pgn',
     'png' => 'image/png',
     'pnm' => 'image/x-portable-anymap',
     'pot' => 'application/mspowerpoint',
     'ppm' => 'image/x-portable-pixmap',
     'pps' => 'application/mspowerpoint',
     'ppt' => 'application/mspowerpoint',
     'ppz' => 'application/mspowerpoint',
     'pre' => 'application/x-freelance',
     'prt' => 'application/pro_eng',
      'ps' => 'application/postscript',
      'qt' => 'video/quicktime',
      'ra' => 'audio/x-realaudio',
     'ram' => 'audio/x-pn-realaudio',
     'ras' => 'image/cmu-raster',
     'rgb' => 'image/x-rgb',
      'rm' => 'audio/x-pn-realaudio',
    'roff' => 'application/x-troff',
     'rpm' => 'audio/x-pn-realaudio-plugin',
     'rtf' => 'text/rtf',
     'rtx' => 'text/richtext',
     'scm' => 'application/x-lotusscreencam',
     'set' => 'application/set',
     'sgm' => 'text/sgml',
    'sgml' => 'text/sgml',
      'sh' => 'application/x-sh',
    'shar' => 'application/x-shar',
    'silo' => 'model/mesh',
     'sit' => 'application/x-stuffit',
     'skd' => 'application/x-koan',
     'skm' => 'application/x-koan',
     'skp' => 'application/x-koan',
     'skt' => 'application/x-koan',
     'smi' => 'application/smil',
    'smil' => 'application/smil',
     'snd' => 'audio/basic',
     'sol' => 'application/solids',
     'spl' => 'application/x-futuresplash',
     'src' => 'application/x-wais-source',
    'step' => 'application/STEP',
     'stl' => 'application/SLA',
     'stp' => 'application/STEP',
'sv4cpio' => 'application/x-sv4cpio',
  'sv4crc' => 'application/x-sv4crc',
     'swf' => 'application/x-shockwave-flash',
       't' => 'application/x-troff',
     'tar' => 'application/x-tar',
     'tcl' => 'application/x-tcl',
     'tex' => 'application/x-tex',
    'texi' => 'application/x-texinfo',
  'texinfo -  application/x-texinfo',
     'tif' => 'image/tiff',
    'tiff' => 'image/tiff',
      'tr' => 'application/x-troff',
     'tsi' => 'audio/TSP-audio',
     'tsp' => 'application/dsptype',
     'tsv' => 'text/tab-separated-values',
     'txt' => 'text/plain',
     'unv' => 'application/i-deas',
   'ustar' => 'application/x-ustar',
     'vcd' => 'application/x-cdlink',
     'vda' => 'application/vda',
     'viv' => 'video/vnd.vivo',
    'vivo' => 'video/vnd.vivo',
    'vrml' => 'model/vrml',
     'wav' => 'audio/x-wav',
     'wrl' => 'model/vrml',
     'xbm' => 'image/x-xbitmap',
     'xlc' => 'application/vnd.ms-excel',
     'xll' => 'application/vnd.ms-excel',
     'xlm' => 'application/vnd.ms-excel',
     'xls' => 'application/vnd.ms-excel',
     'xlw' => 'application/vnd.ms-excel',
     'xml' => 'text/xml',
     'xpm' => 'image/x-xpixmap',

     'xwd' => 'image/x-xwindowdump',
     'xyz' => 'chemical/x-pdb',
     'zip' => 'application/zip',
    );

    $ext = get_file_extension($file);

    if (isset($types[$ext])) {
        return $types[$ext];
    }

    return 'application/octet-stream';
}

function trim_value(&$value)
{
    $value = trim($value);
}

/*
    create directory tree recursively
    backward compatibility for php4
*/
function _mkdir($dir, $chmod = CHMOD_DIRS)
{
	if (is_dir($dir) || @mkdir($dir, $chmod)) return true;
	if (!_mkdir(dirname($dir), $chmod)) return false;
	return @mkdir($dir, $chmod);
}

function filterFileName($text, $tolower = 1, $transl = null)
{
	global $translit;
	$transl = ($transl !== null) ? $transl : @$translit;
	if ($transl)
		$text = strtr(
			$text,
			 array(
				// russian Windows-1251
				"\xc0" => "a",
				"\xc1" => "b",
				"\xc2" => "v",
				"\xc3" => "g",
				"\xc4" => "d",
				"\xc5" => "e",
				"\xa8" => "e",
				"\xc6" => "zh",
				"\xc7" => "z",
				"\xc8" => "i",
				"\xc9" => "j",
				"\xca" => "k",
				"\xcb" => "l",
				"\xcc" => "m",
				"\xcd" => "n",
				"\xce" => "o",
				"\xcf" => "p",
				"\xd0" => "r",
				"\xd1" => "s",
				"\xd2" => "t",
				"\xd3" => "u",
				"\xd4" => "f",
				"\xd5" => "h",
				"\xd6" => "c",
				"\xd7" => "ch",
				"\xd8" => "sh",
				"\xd9" => "sch",
				"\xda" => "",
				"\xdb" => "i",
				"\xdc" => "",
				"\xdd" => "e",
				"\xde" => "yu",
				"\xdf" => "ya",
				"\xe0" => "a",
				"\xe1" => "b",
				"\xe2" => "v",
				"\xe3" => "g",
				"\xe4" => "d",
				"\xe5" => "e",
				"\xb8" => "e",
				"\xe6" => "zh",
				"\xe7" => "z",
				"\xe8" => "i",
				"\xe9" => "j",
				"\xea" => "k",
				"\xeb" => "l",
				"\xec" => "m",
				"\xed" => "n",
				"\xee" => "o",
				"\xef" => "p",
				"\xf0" => "r",
				"\xf1" => "s",
				"\xf2" => "t",
				"\xf3" => "u",
				"\xf4" => "f",
				"\xf5" => "h",
				"\xf6" => "c",
				"\xf7" => "ch",
				"\xf8" => "sh",
				"\xf9" => "sch",
				"\xfa" => "",
				"\xfb" => "i",
				"\xfc" => "",
				"\xfd" => "e",
				"\xfe" => "yu",
				"\xff" => "ya",
				// russian KOI8
/*
				"\xe1" => "a",
				"\xe2" => "b",
				"\xf7" => "v",
				"\xe7" => "g",
				"\xe4" => "d",
				"\xe5" => "e",
				"\xb3" => "e",
				"\xf6" => "zh",
				"\xfa" => "z",
				"\xe9" => "i",
				"\xea" => "j",
				"\xeb" => "k",
				"\xec" => "l",
				"\xed" => "m",
				"\xee" => "n",
				"\xef" => "o",
				"\xf0" => "p",
				"\xf2" => "r",
				"\xf3" => "s",
				"\xf4" => "t",
				"\xf5" => "u",
				"\xe6" => "f",
				"\xe8" => "h",
				"\xe3" => "c",
				"\xfe" => "ch",
				"\xfb" => "sh",
				"\xfd" => "sch",
				"\xff" => "",
				"\xf9" => "i",
				"\xf8" => "",
				"\xfc" => "e",
				"\xe0" => "yu",
				"\xf1" => "ya",
				"\xc1" => "a",
				"\xc2" => "b",
				"\xd7" => "v",
				"\xc7" => "g",
				"\xc4" => "d",
				"\xc5" => "e",
				"\xa3" => "e",
				"\xd6" => "zh",
				"\xda" => "z",
				"\xc9" => "i",
				"\xca" => "j",
				"\xcb" => "k",
				"\xcc" => "l",
				"\xcd" => "m",
				"\xce" => "n",
				"\xcf" => "o",
				"\xd0" => "p",
				"\xd2" => "r",
				"\xd3" => "s",
				"\xd4" => "t",
				"\xd5" => "u",
				"\xc6" => "f",
				"\xc8" => "h",
				"\xc3" => "c",
				"\xde" => "ch",
				"\xdb" => "sh",
				"\xdd" => "sch",
				"\xdf" => "",
				"\xd9" => "i",
				"\xd8" => "",
				"\xdc" => "e",
				"\xc0" => "yu",
				"\xd1" => "ya",
*/
		));

	if ($tolower)
	  $text = strtolower($text);

	$text = str_replace(" ", "_", $text);
	$text = str_replace("%20", "_", $text);
	$text = preg_replace("/[^\-\._a-z0-9]/i", "_", $text);
	return $text;
}

function create_unique_filename($base, $file)
{
  $ext = get_file_extension($file);
  $name = get_file_name($file);
  $n = 2;
  $copy = "";
  while (file_exists($base."/".$name.$copy.".".$ext)) {
    $copy = "_".$n;
    $n++;
  }
  return $name.$copy.".".$ext;
}

?>
