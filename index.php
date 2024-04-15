<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: index.php                                            *
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

$templates_used = 'home,category_bit,whos_online,thumbnail_bit';
$main_template = 'home';

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
define('GET_USER_ONLINE', 1);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();

if (isset($HTTP_GET_VARS['template']) || isset($HTTP_POST_VARS['template'])) {
  $template = (isset($HTTP_GET_VARS['template'])) ? get_basefile(stripslashes($HTTP_GET_VARS['template'])) : get_basefile(stripslashes($HTTP_POST_VARS['template']));
  if (!file_exists(TEMPLATE_PATH."/".$template.".".$site_template->template_extension)) {
    $template = "";
  }
  else {
    $main_template = $template;
  }
}
else {
  $template = "";
}
include(ROOT_PATH.'includes/page_header.php');

if (!empty($template)) {
  $clickstream = "<a href=\"".$site_sess->url(ROOT_PATH."index.php")."\">".$lang['home']."</a>".$config['category_separator'].str_replace("_", " ", ucfirst($template));
  $site_template->register_vars("clickstream", $clickstream);
  $site_template->print_template($site_template->parse_template($main_template));
  include(ROOT_PATH.'includes/page_footer.php');
}

$cache_id = create_cache_id(
  'page.index',
  array(
    $user_info[$user_table_fields['user_id']],
    isset($user_info['lightbox_image_ids']) ? substr(md5($user_info['lightbox_image_ids']), 0, 8) : 0,
    $config['template_dir'],
    $config['language_dir']
  )
);

if (!$cache_page_index || !$content = get_cache_file($cache_id)) {
// Always append session id if cache is enabled
if ($cache_page_index) {
  $old_session_mode = $site_sess->mode;
  $site_sess->mode = 'get';
}

ob_start();

//-----------------------------------------------------
//--- Show Categories ---------------------------------
//-----------------------------------------------------
$categories = get_categories(0);
if (!$categories)  {
  $categories = $lang['no_categories'];
}
$site_template->register_vars("categories", $categories);
unset($categories);

//-----------------------------------------------------
//--- Show New Images ---------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "has_rss"   => true,
  "rss_title" => "RSS Feed: ".format_text($config['site_name'], 2)." (".str_replace(':', '', $lang['new_images']).")",
  "rss_url"   => $script_url."/rss.php?action=images"
));

$imgtable_width = ceil(intval($config['image_table_width']) / $config['image_cells']);
if ((substr($config['image_table_width'], -1)) == "%") {
  $imgtable_width .= "%";
}

$additional_sql = "";
if (!empty($additional_image_fields)) {
  foreach ($additional_image_fields as $key => $val) {
    $additional_sql .= ", i.".$key;
  }
}

$num_new_images = $config['image_cells'];
$sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_description, i.image_keywords, i.image_date, i.image_active, i.image_media_file, i.image_thumb_file, i.image_download_url, i.image_allow_comments, i.image_comments, i.image_downloads, i.image_votes, i.image_rating, i.image_hits".$additional_sql.", c.cat_name".get_user_table_field(", u.", "user_name")."
        FROM (".IMAGES_TABLE." i,  ".CATEGORIES_TABLE." c)
        LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = i.user_id)
        WHERE i.image_active = 1 AND c.cat_id = i.cat_id AND i.cat_id NOT IN (".get_auth_cat_sql("auth_viewcat", "NOTIN").")
        ORDER BY i.image_date DESC
        LIMIT $num_new_images";
$result = $site_db->query($sql);
$num_rows = $site_db->get_numrows($result);

if (!$num_rows)  {
  $new_images = "<table width=\"".$config['image_table_width']."\" border=\"0\" cellpadding=\"".$config['image_table_cellpadding']."\" cellspacing=\"".$config['image_table_cellspacing']."\"><tr class=\"imagerow1\"><td>";
  $new_images .= $lang['no_new_images'];
  $new_images .= "</td></tr></table>";
}
else  {
  $new_images = "<table width=\"".$config['image_table_width']."\" border=\"0\" cellpadding=\"".$config['image_table_cellpadding']."\" cellspacing=\"".$config['image_table_cellspacing']."\">";
  $count = 0;
  $bgcounter = 0;
  while ($image_row = $site_db->fetch_array($result)){
    if ($count == 0) {
      $row_bg_number = ($bgcounter++ % 2 == 0) ? 1 : 2;
      $new_images .= "<tr class=\"imagerow".$row_bg_number."\">\n";
    }
    $new_images .= "<td width=\"".$imgtable_width."\" valign=\"top\">\n";

    show_image($image_row);
    $new_images .= $site_template->parse_template("thumbnail_bit");
    $new_images .= "\n</td>\n";
    $count++;
    if ($count == $config['image_cells']) {
      $new_images .= "</tr>\n";
      $count = 0;
    }
  } // end while

  if ($count > 0)  {
    $leftover = ($config['image_cells'] - $count);
    if ($leftover >= 1) {
      for ($f = 0; $f < $leftover; $f++) {
        $new_images .= "<td width=\"".$imgtable_width."\">\n&nbsp;\n</td>\n";
      }
      $new_images .= "</tr>\n";
    }
  }
  $new_images .= "</table>\n";
} // end else

$site_template->register_vars("new_images", $new_images);
unset($new_images);

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

if ($cache_page_index) {
  // Reset session mode
  $site_sess->mode = $old_session_mode;

  save_cache_file($cache_id, $content);
}

} // end if get_cache_file()

echo $content;

include(ROOT_PATH.'includes/page_footer.php');
?>
