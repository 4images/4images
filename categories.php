<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: categories.php                                       *
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

$templates_used = 'categories,category_bit,thumbnail_bit';
$main_template = 'categories';

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();
include(ROOT_PATH.'includes/page_header.php');

if (!$cat_id || !isset($cat_cache[$cat_id]) || !check_permission("auth_viewcat", $cat_id)) {
  redirect("index.php");
}

$cache_id = create_cache_id(
  'page.categories',
  array(
    $user_info[$user_table_fields['user_id']],
    $cat_id,
    $page,
    $perpage,
    isset($user_info['lightbox_image_ids']) ? substr(md5($user_info['lightbox_image_ids']), 0, 8) : 0,
    $config['template_dir'],
    $config['language_dir']
  )
);

if (!$cache_page_categories || !$content = get_cache_file($cache_id)) {
// Always append session id if cache is enabled
if ($cache_page_categories) {
  $old_session_mode = $site_sess->mode;
  $site_sess->mode = 'get';
}

ob_start();

//-----------------------------------------------------
//--- SEO variables -----------------------------------
//-----------------------------------------------------

$site_template->register_vars(array('prepend_head_title' => $cat_cache[$cat_id]['cat_name'] . " - "));

//-----------------------------------------------------
//--- Show Categories ---------------------------------
//-----------------------------------------------------
if (!check_permission("auth_upload", $cat_id)) {
  $upload_url = "";
  $upload_button = "<img src=\"".get_gallery_image("upload_off.gif")."\" border=\"0\" alt=\"\" />";
}
else {
  $upload_url = $site_sess->url(ROOT_PATH."member.php?action=uploadform&amp;".URL_CAT_ID."=".$cat_id);
  $upload_button = "<a href=\"".$upload_url."\"><img src=\"".get_gallery_image("upload.gif")."\" border=\"0\" alt=\"\" /></a>";
}

$random_cat_image = (defined("SHOW_RANDOM_IMAGE") && SHOW_RANDOM_IMAGE == 0) ? "" : get_random_image($cat_id);
$site_template->register_vars(array(
  "categories" => get_categories($cat_id),
  "cat_name" => format_text($cat_cache[$cat_id]['cat_name'], 2),
  "cat_description" => htmlspecialchars(format_text($cat_cache[$cat_id]['cat_description'], 1, 0, 1)),
  "cat_hits" => $cat_cache[$cat_id]['cat_hits'],
  "upload_url" => $upload_url,
  "upload_button" => $upload_button,
  "random_cat_image" => $random_cat_image
));

unset($random_cat_image);

//-----------------------------------------------------
//--- Show Images -------------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "has_rss"   => true,
  "rss_title" => "RSS Feed: ".format_text($cat_cache[$cat_id]['cat_name'], 2)." (".str_replace(':', '', $lang['new_images']).")",
  "rss_url"   => $script_url."/rss.php?action=images&amp;".URL_CAT_ID."=".$cat_id
));

$num_rows_all = (isset($cat_cache[$cat_id]['num_images'])) ? $cat_cache[$cat_id]['num_images'] : 0;
$link_arg = $site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$cat_id);

include(ROOT_PATH.'includes/paging.php');
$getpaging = new Paging($page, $perpage, $num_rows_all, $link_arg);
$offset = $getpaging->get_offset();

$site_template->register_vars(array(
  "paging" => $getpaging->get_paging(),
  "paging_stats" => $getpaging->get_paging_stats()
));

$imgtable_width = ceil((intval($config['image_table_width'])) / $config['image_cells']);
if ((substr($config['image_table_width'], -1)) == "%") {
  $imgtable_width .= "%";
}

$additional_sql = "";
if (!empty($additional_image_fields)) {
  foreach ($additional_image_fields as $key => $val) {
    $additional_sql .= ", i.".$key;
  }
}

$sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits".$additional_sql.", c.cat_name".get_user_table_field(", u.", "user_name")."
        FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
        LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
        WHERE i.image_active = 1 AND i.cat_id = $cat_id AND c.cat_id = i.cat_id
        ORDER BY ".$config['image_order']." ".$config['image_sort'].", i.image_id ".$config['image_sort']."
        LIMIT $offset, $perpage";
$result = $site_db->query($sql);
$num_rows = $site_db->get_numrows($result);

if (!$num_rows)  {
  $thumbnails = "";
  $msg = $lang['no_images'];
}
else {
  $thumbnails = "<table width=\"".$config['image_table_width']."\" border=\"0\" cellpadding=\"".$config['image_table_cellpadding']."\" cellspacing=\"".$config['image_table_cellspacing']."\">\n";
  $count = 0;
  $bgcounter = 0;
  while ($image_row = $site_db->fetch_array($result)){
    if ($count == 0) {
      $row_bg_number = ($bgcounter++ % 2 == 0) ? 1 : 2;
      $thumbnails .= "<tr class=\"imagerow".$row_bg_number."\">\n";
    }
    $thumbnails .= "<td width=\"".$imgtable_width."\" valign=\"top\">\n";

    show_image($image_row);
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
    if ($leftover > 0) {
      for ($i = 0; $i < $leftover; $i++){
        $thumbnails .= "<td width=\"".$imgtable_width."\">\n&nbsp;\n</td>\n";
      }
      $thumbnails .= "</tr>\n";
    }
  }
  $thumbnails .= "</table>\n";
} //end else
$site_template->register_vars("thumbnails", $thumbnails);
unset($thumbnails);

//-----------------------------------------------------
//--- Clickstream -------------------------------------
//-----------------------------------------------------
$clickstream = "<span class=\"clickstream\"><a href=\"".$site_sess->url(ROOT_PATH."index.php")."\" class=\"clickstream\">".$lang['home']."</a>".$config['category_separator'].get_category_path($cat_id)."</span>";

//-----------------------------------------------------
//--- Print Out ---------------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "msg" => $msg,
  "clickstream" => $clickstream
));

$site_template->print_template($site_template->parse_template($main_template));

$content = ob_get_contents();
ob_end_clean();

if ($cache_page_categories) {
  // Reset session mode
  $site_sess->mode = $old_session_mode;

  save_cache_file($cache_id, $content);
}

} // end if get_cache_file()

echo $content;

//Update Category Hits
if ($user_info['user_level'] != ADMIN && $page == 1) {
  $sql = "UPDATE ".CATEGORIES_TABLE."
          SET cat_hits = cat_hits + 1
          WHERE cat_id = $cat_id";
  $site_db->query($sql);
}

include(ROOT_PATH.'includes/page_footer.php');
?>
