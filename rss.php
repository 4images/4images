<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: rss.php                                              *
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

$main_template = 'rss';

$nozip = 1;
define('GET_CACHES', 1);
define('ROOT_PATH', './');
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();
include(ROOT_PATH.'includes/page_header.php');

$site_template->template_extension = 'xml';

@define('RSS_DEFAULT_ITEMS', 10);
@define('RSS_MAX_ITEMS', 30);

if (isset($HTTP_GET_VARS['items']) || isset($HTTP_POST_VARS['items'])) {
  $num_items = (isset($HTTP_POST_VARS['items'])) ? intval($HTTP_POST_VARS['items']) : intval($HTTP_GET_VARS['items']);
  if (!$num_items) {
    $num_items = RSS_DEFAULT_ITEMS;
  }

  if ($num_items > RSS_MAX_ITEMS) {
    $num_items = RSS_MAX_ITEMS;
  }
}
else {
  $num_items = RSS_DEFAULT_ITEMS;
}

if ($action == '') {
  $action = 'images';
}

function cut_at_word($text, $length, $suffix = '...') {
  if (strlen($text) <= $length) {
    return $text;
  }

  $delims = array(' ', '.', ',', '!', '?', '-', ':', '_', '/');
  $text = substr($text, 0, $length + 1);

  $positions = array();

  for ($i = 0; isset($delims[$i]); $i++) {
    $pos = strrpos($text, $delims[$i]);
    if ($pos) {
      $positions[] = $pos;
    }
  }

  if (sizeof($positions) > 0) {
    rsort($positions);
    $text = substr($text, 0, $positions[0]);
  }

  $text .= $suffix;

  return $text;
}

function format_rss_text($text) {
  $text = format_text(trim($text), 1, 0, 1);
  $text = strip_tags($text);
  $text = safe_htmlspecialchars($text);

  $text = cut_at_word($text, 250);

  return $text;
}

function format_rss_html($text) {
  $text = format_text(trim($text), 2, 0, 1);

  return $text;
}

function get_file_url($file_name, $image_type, $cat_id)
{
    $url = get_file_path($file_name, $image_type, $cat_id, 0, 1);

    if (!is_remote($file_name)) {
        global $script_url;
        $url = $script_url.'/'.$url;
    }

    return str_replace('./', '', $url);
}

function get_rss_enclosure($file_name, $image_type, $cat_id) {
  if (!get_file_path($file_name, $image_type, $cat_id, 0, 0)) {
    return array();
  }

  $file = get_file_path($file_name, $image_type, $cat_id, 0, 1);
  $url = get_file_url($file_name, $image_type, $cat_id);

  return array(
    'url' => $url,
    'length' => @filesize($file),
    'type' => get_mime_content_type($file)
  );
}

$cache_id = create_cache_id(
  'page.rss',
  array(
    $user_info[$user_table_fields['user_id']],
	$action,
    $image_id,
    $cat_id,
    $num_items
  )
);

if (!$cache_page_rss || !$content = get_cache_file($cache_id)) {
  $old_session_mode = $site_sess->mode;
  $site_sess->mode = 'cookie';

ob_start();

$rss_title = format_rss_text($config['site_name']);
$rss_link  = $site_sess->url($script_url);
$rss_desc  = format_rss_text($config['site_name']);
$rss_lang  = "";
$rss_image = array();
$rss_ttl   = $cache_page_rss ? $cache_lifetime : 0;
$rss_cat   = array();
$rss_items = array();

switch ($action) {
  case 'comments':
    if (!$image_id) {
      exit;
    }

    $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits, c.cat_name".get_user_table_field(", u.", "user_name").get_user_table_field(", u.", "user_email")."
            FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE i.image_id = $image_id AND c.cat_id = i.cat_id";
    $image_row = $site_db->query_firstrow($sql);

    if (!isset($image_row['image_id'])) {
      exit;
    }

    $cat_id = (isset($image_row['cat_id'])) ? $image_row['cat_id'] : 0;

    $rss_title .= " - " . format_rss_text($image_row['image_name']);
    $rss_link  = $site_sess->url($script_url."/details.php?".URL_IMAGE_ID."=".$image_id);
    $rss_desc  = format_rss_html($image_row['image_description']);
    if (get_file_path($image_row['image_thumb_file'], "thumb", $cat_id, 0, 0)) {
      $rss_image = array(
        'url' => get_file_url($image_row['image_thumb_file'], "thumb", $cat_id),
        'title' => format_rss_text($image_row['image_name']),
        'link' => $rss_link
      );
    }

    $rss_cat = array(
      'name' => format_rss_text($cat_cache[$cat_id]['cat_name']),
      'domain' => $site_sess->url($script_url."/categories.php?".URL_CAT_ID."=".$cat_id)
    );

    $image_allow_comments = (check_permission("auth_readcomment", $cat_id)) ? $image_row['image_allow_comments'] : 0;

    $sql = "SELECT c.comment_id, c.image_id, c.user_id, c.user_name AS comment_user_name, c.comment_headline, c.comment_text, c.comment_ip, c.comment_date".get_user_table_field(", u.", "user_level").get_user_table_field(", u.", "user_name").get_user_table_field(", u.", "user_email").get_user_table_field(", u.", "user_showemail").get_user_table_field(", u.", "user_invisible").get_user_table_field(", u.", "user_joindate").get_user_table_field(", u.", "user_lastaction").get_user_table_field(", u.", "user_comments").get_user_table_field(", u.", "user_homepage").get_user_table_field(", u.", "user_icq")."
            FROM ".COMMENTS_TABLE." c
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = c.user_id)
            WHERE c.image_id = $image_id
            ORDER BY c.comment_date DESC
            LIMIT $num_items";
    $result = $site_db->query($sql);

    while ($row = $site_db->fetch_array($result)) {
      $user_name = format_rss_text($row['comment_user_name']);
      $user_email = "example@example.com";

      if (isset($row[$user_table_fields['user_name']]) && $row['user_id'] != GUEST) {
        $user_name = format_rss_text($row[$user_table_fields['user_name']]);
        if (!empty($row[$user_table_fields['user_email']]) && (!isset($row[$user_table_fields['user_showemail']]) || (isset($row[$user_table_fields['user_showemail']]) && $row[$user_table_fields['user_showemail']] == 1))) {
          $user_email = $row[$user_table_fields['user_email']];
        }
      }

      $rss_items[] = array(
        'title' => format_rss_text($row['comment_headline']),
        'link' => $site_sess->url($script_url."/details.php?".URL_IMAGE_ID."=".$image_id."#comment".$row['comment_id']),
        'pubDate' => $row['comment_date'],
        'desc' => format_rss_text($row['comment_text']),
        'category' => array(
          'name' => $rss_title,
          'domain' => $rss_link
        ),
        'author' => array(
          'name' => $user_name,
          'email' => $user_email
        ),
      );
    }
    break;

  case 'images':
  default:
    $cat_sql = "";
    if ($cat_id && isset($cat_cache[$cat_id])) {
      $rss_title .= " - " . format_rss_text($cat_cache[$cat_id]['cat_name']);
      $rss_link  = $site_sess->url($script_url."/categories.php?".URL_CAT_ID."=".$cat_id);
      $rss_desc  = format_rss_html($cat_cache[$cat_id]['cat_description']);

      $cat_sql = "AND i.cat_id = $cat_id";
    }

    $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits, c.cat_name".get_user_table_field(", u.", "user_name")."
            FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
            LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
            WHERE i.image_active = 1
              $cat_sql
              AND c.cat_id = i.cat_id
              AND i.cat_id NOT IN (".get_auth_cat_sql("auth_viewcat", "NOTIN").")
            ORDER BY i.image_date DESC, i.image_id DESC
            LIMIT $num_items";
    $result = $site_db->query($sql);

    while ($row = $site_db->fetch_array($result)) {
      $user_name = format_rss_text($lang['userlevel_guest']);
      $user_email = "example@example.com";

      if (isset($row[$user_table_fields['user_name']]) && $row['user_id'] != GUEST) {
        $user_name = format_rss_text($row[$user_table_fields['user_name']]);
        if (!empty($row[$user_table_fields['user_email']]) && (!isset($row[$user_table_fields['user_showemail']]) || (isset($row[$user_table_fields['user_showemail']]) && $row[$user_table_fields['user_showemail']] == 1))) {
          $user_email = $row[$user_table_fields['user_email']];
        }
      }

      $rss_items[] = array(
        'title' => format_rss_text($row['image_name']),
        'link' => $site_sess->url($script_url."/details.php?".URL_IMAGE_ID."=".$row['image_id']),
        'pubDate' => $row['image_date'],
        'desc' => format_rss_html($row['image_description']),
        'category' => array(
          'name' => format_rss_text($cat_cache[$row['cat_id']]['cat_name']),
          'domain' => $site_sess->url($script_url."/categories.php?".URL_CAT_ID."=".$row['cat_id'])
        ),
        'enclosure' => get_rss_enclosure($row['image_thumb_file'], "thumb", $row['cat_id']),
        'author' => array(
          'name' => $user_name,
          'email' => $user_email
        ),
        'comments' => $site_sess->url($script_url."/details.php?".URL_IMAGE_ID."=".$row['image_id']."#comments"),
      );
    }
    break;
}

$items = '';

foreach ($rss_items as $item) {
  $tpl_vars = array(
    'item_title' => $item['title'],
    'item_link' => $item['link'],
    'item_pubdate' => gmdate('D, d M Y H:i:s', $item['pubDate']) . " GMT",
    'item_description' => $item['desc'],
    'item_category' => false,
    'item_category_domain' => '',
    'item_category_name' => '',
    'item_author' => false,
    'item_author_email' => '',
    'item_author_name' => '',
    'item_enclosure' => false,
    'item_enclosure_url' => '',
    'item_enclosure_length' => '',
    'item_enclosure_type' => '',
  );

  if (@count($item['category']) > 0) {
    $tpl_vars['item_category'] = true;
    $tpl_vars['item_category_domain'] = $item['category']['domain'];
    $tpl_vars['item_category_name'] = $item['category']['name'];
  }

  if (@count($item['author']) > 0) {
    $tpl_vars['item_author'] = true;
    $tpl_vars['item_author_email'] = $item['author']['email'];
    $tpl_vars['item_author_name'] = $item['author']['name'];
  }

  if (@count($item['enclosure']) > 0) {
    $tpl_vars['item_enclosure'] = true;
    $tpl_vars['item_enclosure_url'] = $item['enclosure']['url'];
    $tpl_vars['item_enclosure_length'] = $item['enclosure']['length'];
    $tpl_vars['item_enclosure_type'] = $item['enclosure']['type'];
  }

  $site_template->register_vars($tpl_vars);
  $items .= $site_template->parse_template("rss_item");
}

$tpl_vars = array(
  'channel_title' => $rss_title,
  'channel_link' => $rss_link,
  'channel_pubdate' => gmdate('D, d M Y H:i:s') . " GMT",
  'channel_description' => $rss_desc,
  'channel_image' => false,
  'channel_image_url' => '',
  'channel_image_title' => '',
  'channel_image_link' => '',
  'channel_ttl' => $rss_ttl,
  'items' => $items
);

if (count($rss_image) > 0) {
  $tpl_vars['channel_image'] = true;
  $tpl_vars['channel_image_url'] = $rss_image['url'];
  $tpl_vars['channel_image_title'] = $rss_image['title'];
  $tpl_vars['channel_image_link'] = $rss_image['link'];
}

$site_template->register_vars($tpl_vars);

$site_template->print_template($site_template->parse_template($main_template));

$content = ob_get_contents();
ob_end_clean();

// Reset session mode
$site_sess->mode = $old_session_mode;

if ($cache_page_rss) {
  save_cache_file($cache_id, $content, true);
}

} // end if get_cache_file()

header('Content-Type: text/xml');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

echo $content;


?>
