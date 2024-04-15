<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: lightbox.php                                         *
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

$templates_used = 'lightbox,thumbnail_bit';
$main_template = 'lightbox';

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();
include(ROOT_PATH.'includes/page_header.php');

if ($user_info['user_level'] == GUEST || $user_info['user_level'] == USER_AWAITING) {
  show_error_page($lang['lightbox_register']);
}

//-----------------------------------------------------
//--- Show Images -------------------------------------
//-----------------------------------------------------
$imgtable_width = ceil((intval($config['image_table_width'])) / $config['image_cells']);
if ((substr($config['image_table_width'], -1)) == "%") {
  $imgtable_width .= "%";
}

$download_allowed = false;

$num_rows_all = 0;
$num_rows = 0;

if (!empty($user_info['lightbox_image_ids']))  {
  $image_id_sql = str_replace(" ", ", ", trim($user_info['lightbox_image_ids']));
  $sql = "SELECT COUNT(image_id) AS images
          FROM ".IMAGES_TABLE."
          WHERE image_active = 1 AND image_id IN ($image_id_sql) AND cat_id NOT IN (".get_auth_cat_sql("auth_viewcat", "NOTIN").")";
  $result = $site_db->query_firstrow($sql);
  $num_rows_all = $result['images'];
}

$link_arg = $site_sess->url(ROOT_PATH."lightbox.php");
include(ROOT_PATH.'includes/paging.php');
$getpaging = new Paging($page, $perpage, $num_rows_all, $link_arg);
$offset = $getpaging->get_offset();
$site_template->register_vars(array(
  "paging" => $getpaging->get_paging(),
  "paging_stats" => $getpaging->get_paging_stats()
));

if ($num_rows_all) {
  $sql = "SELECT COUNT(image_id) AS images
          FROM ".IMAGES_TABLE."
          WHERE image_active = 1 AND image_id IN ($image_id_sql) AND cat_id NOT IN (".get_auth_cat_sql("auth_download", "NOTIN").")";
  $result = $site_db->query_firstrow($sql);
  $download_allowed = intval($result['images']) > 0;

  $additional_sql = "";
  if (!empty($additional_image_fields)) {
    foreach ($additional_image_fields as $key => $val) {
      $additional_sql .= ", i.".$key;
    }
  }
  $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits".$additional_sql.", c.cat_name".get_user_table_field(", u.", "user_name")."
          FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
          LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
          WHERE i.image_active = 1 AND i.image_id IN ($image_id_sql) AND c.cat_id = i.cat_id AND i.cat_id NOT IN (".get_auth_cat_sql("auth_viewcat", "NOTIN").")
          ORDER BY i.".$config['image_order']." ".$config['image_sort'].", i.image_id ".$config['image_sort']."
          LIMIT $offset, $perpage";
  $result = $site_db->query($sql);
  $num_rows = $site_db->get_numrows($result);
}

if (!$num_rows)  {
  $thumbnails = "";
  $msg .= ($msg != "") ? "<p>".$lang['lightbox_no_images'] : $lang['lightbox_no_images'];
}
else {
  set_download_token($user_info['lightbox_image_ids']);

  $thumbnails = "<table width=\"".$config['image_table_width']."\" border=\"0\" cellpadding=\"".$config['image_table_cellpadding']."\" cellspacing=\"".$config['image_table_cellspacing']."\">\n";
  $count = 0;
  $bgcounter = 0;
  while ($image_row = $site_db->fetch_array($result)) {
    if (!$download_allowed && check_permission("auth_download", $image_row['cat_id'])) {
      $download_allowed = true;
    }

    if ($count == 0) {
      $row_bg_number = ($bgcounter++ % 2 == 0) ? 1 : 2;
      $thumbnails .= "<tr class=\"imagerow".$row_bg_number."\">\n";
    }
    $thumbnails .= "<td width=\"".$imgtable_width."\" valign=\"top\">\n";

    show_image($image_row, "lightbox");
    $thumbnails .= $site_template->parse_template("thumbnail_bit");
    $thumbnails .= "\n</td>\n";

    $count++;
    if ($count == $config['image_cells']) {
      $thumbnails .= "</tr>\n";
      $count = 0;
    }
  } // end while
  if ($count > 0)  {
    $leftover = ($config['image_cells'] - $count);
    if ($leftover >= 1) {
      for ($i = 0; $i < $leftover; $i++){
        $thumbnails .= "<td width=\"".$imgtable_width."\">\n&nbsp;\n</td>\n";
      }
      $thumbnails .= "</tr>\n";
    }
  }
  $thumbnails .= "</table>\n";
} // end else

$lightbox_lastaction = format_date($config['date_format']." ".$config['time_format'], $user_info['lightbox_lastaction']);
if (empty($user_info['lightbox_lastaction'])) {
  $lightbox_lastaction = "n/a";
}

$site_template->register_vars(array(
  "thumbnails" => $thumbnails,
  "lightbox_lastaction" => $lightbox_lastaction
));
unset($thumbnails);

//-----------------------------------------------------
//--- Clickstream -------------------------------------
//-----------------------------------------------------
$clickstream = "<span class=\"clickstream\"><a href=\"".$site_sess->url(ROOT_PATH."index.php")."\" class=\"clickstream\">".$lang['home']."</a>".$config['category_separator'].$lang['lightbox']."</span>";

//-----------------------------------------------------
//--- Print Out ---------------------------------------
//-----------------------------------------------------
$download_button = "";
if (function_exists("gzcompress") && function_exists("crc32")) {
  if ($download_allowed && !empty($user_info['lightbox_image_ids'])) {
    $download_button = "<a href=\"".$site_sess->url(ROOT_PATH."download.php?action=lightbox")."\"><img src=\"".get_gallery_image("download_zip.gif")."\" border=\"0\" alt=\"\" /></a>";
  }
  else {
    $download_button = "<img src=\"".get_gallery_image("download_zip_off.gif")."\" border=\"0\" alt=\"\" />";
  }
}

$site_template->register_vars(array(
  "msg" => $msg,
  "clickstream" => $clickstream,
  "lang_lightbox" => $lang['lightbox'],
  "lang_delete_lightbox" => $lang['delete_lightbox'],
  "url_delete_lightbox" => $site_sess->url(ROOT_PATH."lightbox.php?action=clearlightbox"),
  "lang_delete_lightbox_confirm" => $lang['delete_lightbox_confirm'],
  "lang_lightbox_lastaction" => $lang['lighbox_lastaction'],
  "download_button" => $download_button
));

$site_template->print_template($site_template->parse_template($main_template));
include(ROOT_PATH.'includes/page_footer.php');
?>
