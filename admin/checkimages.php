<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: checkimages.php                                      *
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
if (isset($_GET['showthumb']) && !empty($_GET['showthumb']))
{
	$no_zip = 1;
}
require('admin_global.php');

if (!defined("ICON_EXT"))
	define("ICON_EXT", "gif");

$showthumb = isset($HTTP_GET_VARS['showthumb']) ? stripslashes(trim(urldecode($HTTP_GET_VARS['showthumb']))) : false;

//--------| Default Settings |-------------

$quality_default = (isset($config['auto_image_quality']) && $config['auto_image_quality']) ? $config['auto_image_quality'] : 85; //image quality
$quality_thumbs_default = $config['auto_thumbnail_quality']; //thumbnail quality
$dimension_default = $config['max_image_width']; //image dimension
$dimension_thumbs_default = $config['auto_thumbnail_dimension']; //thumbnail demension
$max_dimension_default = 600; //displayed in preview image dimension (not image resizing)
$num_newimages_default = 30; //images per circle
$big_default = 0; //save original image (0 or 1)
$big_folder_default = "big"; //name of the "big" folder where original image will be copied to, if its bigger then size set in the settings (http://www.4homepages.de/forum/index.php?topic=3236.0)
$big_annotate = 0; //add annotation to the "original" images in "big" folder? (0 or 1)
$backup_orig = ""; //leave empty if u dont want backup files when it needed resize or add watermark (to preserve IPTC and EXIF headers) NO TRAILING SLASH!
//$backup_orig = ROOT_PATH . "data/backup"; //example
$translit = 1; //use translit for cyrillic filenames
$thumbpreview = 1; //create temporary thumbnails for preview if no original was found. (only for none-detailed view) (0 or 1)
$thumbpreview_quality = 70; //quality for temporary thumbnails
$thumbpreview_dimensions = 50; //max dimensions for temporary thumbnails
$thumbpreview_tempdir = ROOT_PATH . 'data/temp'; //directory for temporary files. Must be writable (CHMOD 777).
$rescan = 1; //rescan for new images after first portion of images added (0 or 1)
$annotate_default = (isset($config['annotation_use'])) ? $config['annotation_use'] : 0; //add annotation to the images (require "Annotation MOD" by SLL)
$iptc_date_default = 0; //auto insert date from IPTC (0 or 1)
$iptc_keywords_default = 0; //auto insert keyword from IPTC (0 or 1)
$iptc_description_default = 0; //auto insert captions from IPTC (0 or 1)
$iptc_name_default = 0; //auto insert name from IPTC (0 or 1)
$detailed_default = 0; //detailed view (0 or 1)
$auto_resize_default = 1; //resize images (0 or 1)
$auto_thumbs_default = 1; //create thumbs (0 or 1)
$subcats_default = 0; //include subcategories (0 or 1)

//----------| End Settings |---------------

if ($showthumb)
{
	$result = true;
	if (!@is_dir($thumbpreview_tempdir))
	{
		$oldumask = umask(0);
		$result = @mkdir($thumbpreview_tempdir);
		umask($oldumask);
		if (!@is_dir($thumbpreview_tempdir) || !$result)
		{
			$result = @mkdir($thumbpreview_tempdir, 0755);
		}
	}

	require(ROOT_PATH.'includes/image_utils.php');
	$convert_options = init_convert_options();
	$image = MEDIA_PATH . "/" . (($cat_id) ? $cat_id ."/" : "") . $showthumb;
	$ext = get_file_extension($showthumb);
	$delete = false;
	if (!file_exists($image))
	{
		$thumb = ICON_PATH . "/404." . ICON_EXT;
	}
	elseif ($result && !$convert_options['convert_error'] && $img = @getimagesize($image))
	{
		if ($img[2] >= 0 && $img[2] < 4)
		{
			$thumb = $image;
			$file = $thumbpreview_tempdir . "/" . MD5(time().microtime()) . ".". $ext;
			if (create_thumbnail($thumb, $file, $thumbpreview_quality, $thumbpreview_dimensions, 1))
			{
				$delete = true;
				$thumb = $file;
			}
		}
		else
		{
			$thumb = ICON_PATH."/" . $ext . "." . ICON_EXT;
		}
	}
	else
	{
		$thumb = ICON_PATH."/" . $ext . "." . ICON_EXT;
	}
	header("Content-Type: image/" . get_file_extension($thumb));
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // turn off caching
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	@readfile($thumb);
	if ($delete)
	{
		@unlink($file);
	}
	exit;
}

include(ROOT_PATH.'includes/search_utils.php');

// BEGIN FUNCTIONS

function copyFile($src, $dest, $name, $cat_id = 0)
{
	if ($cat_id)
		$dest .= "/".$cat_id;

	$result = true;
	if (!@is_dir($dest))
	{
		$oldumask = umask(0);
		$result = _mkdir($dest, 0755);
		umask($oldumask);
	}
	if ($result)
	{
		return copy($src, $dest."/".$name);
	}
	return false;
}

function get_subcats($cat_id = 0, $cid = 0) {
	global $sub_cat_cache, $cat_cache, $cats;

	if (!isset($sub_cat_cache[$cid])) {
		return "";
	}
	foreach ($sub_cat_cache[$cid] as $key => $category_id) {
		$cats[] = $category_id;
		get_subcats($cat_id, $category_id);
	}
	unset($sub_cat_cache[$cid]);
}

function get_category_dropdown_check($cat_id = 0)
{
	global $lang, $drop_down_cat_cache, $cat_parent_cache;
		$category = "\n<select name=\"cat_id_p\" class=\"categoryselect\">\n";
		$category .= "<option value=\"0\">".$lang['all_categories']."</option>\n";
		$category .= "<option value=\"0\">-------------------------------</option>\n";

	$drop_down_cat_cache = array();
	$drop_down_cat_cache = $cat_parent_cache;
	$category .= get_category_dropdown_bits($cat_id);
	$category .= "</select>\n";
	return $category;
}

if (!function_exists("trim_value"))
{
  function trim_value(&$value)
  {
    $value = trim($value);
  }
}

function _rename($dir, $file_src, $file_dest, $force = false)
{
  $oldwd = getcwd();
  chdir(realpath($dir));
  if (!file_exists($file_src))
    return false;
  $copy = "";
  $file_name = get_file_name($file_dest);
  $file_ext = get_file_extension($file_dest);
  if (!$force && strtolower($file_src) == $file_dest && substr(PHP_OS, 0, 3) != "WIN")
  {
  	$n = 2;
  	while (file_exists($file_name.$copy.".".$file_ext))
  	{
  		$copy = "_".$n;
  		$n++;
  	}
  }
  $file = $file_name.$copy.".".$file_ext;
  $ok = rename($file_src, $file);
  chdir($oldwd);
  return $ok ? $file : false;
}

// END FUNCTIONS

if ($action == "")
{
	$action = "checkimages";
}
$status = 0;


show_admin_header();

if ($action == "savenewimages")
{
	@include(ROOT_PATH."includes/db_field_definitions.php");

	$date = time();
	$error = array();
	$num_newimages = $HTTP_POST_VARS['num_newimages'];
	$detailed = $HTTP_POST_VARS['detailed'];
	$auto_resize = (isset($HTTP_POST_VARS['auto_resize']) && $HTTP_POST_VARS['auto_resize'] == 1) ? 1 : 0;
	$auto_thumbs = (isset($HTTP_POST_VARS['auto_thumbs']) && $HTTP_POST_VARS['auto_thumbs'] == 1) ? 1 : 0;
	$dimension = (isset($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : $dimension_default;
	$resize_type = (isset($HTTP_POST_VARS['resize_type'])) ? intval($HTTP_POST_VARS['resize_type']) : 1;
	$quality = (isset($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) <= 100) ? intval($HTTP_POST_VARS['quality']) : $quality_default;
	$dimension_thumbs = (isset($HTTP_POST_VARS['dimension_thumbs'])) ? intval($HTTP_POST_VARS['dimension_thumbs']) : $dimension_thumbs_default;
	$resize_type_thumbs = (isset($HTTP_POST_VARS['resize_type_thumbs'])) ? intval($HTTP_POST_VARS['resize_type_thumbs']) : 1;
	$quality_thumbs = (isset($HTTP_POST_VARS['quality_thumbs']) && intval($HTTP_POST_VARS['quality_thumbs']) && intval($HTTP_POST_VARS['quality_thumbs']) <= 100) ? intval($HTTP_POST_VARS['quality_thumbs']) : $quality_thumbs_default;
	$big = (isset($HTTP_POST_VARS['big']) && $HTTP_POST_VARS['big'] == 1) ? 1 : $big_default;
	$annotate = (isset($HTTP_POST_VARS['annotate']) && $HTTP_POST_VARS['annotate'] == 1) ? 1 : $annotate_default;
	$big_folder = (isset($HTTP_POST_VARS['big_folder'])) ? trim($HTTP_POST_VARS['big_folder']) : $big_folder_default;
	for ($i = 1; $i <= $num_newimages; $i++)
	{
		$addimage = (isset($HTTP_POST_VARS['addimage_'.$i]) && $HTTP_POST_VARS['addimage_'.$i] == 1) ? 1 : 0;
		if ($addimage)
		{
			$image_name = trim($HTTP_POST_VARS['image_name_'.$i]);
			$cat_id = intval($HTTP_POST_VARS['cat_id_'.$i]);
			$image_download_url = (isset($HTTP_POST_VARS['image_download_url_'.$i])) ? trim($HTTP_POST_VARS['image_download_url_'.$i]) : "";

			if ($image_name == "")
			{
				$error['image_name_'.$i] = 1;
			}
			if ($cat_id == 0)
			{
				$error['cat_id_'.$i] = 1;
			}
			if ($image_download_url != "" && !is_remote($image_download_url) && !is_local_file($image_download_url))
			{
				$error['image_download_url_'.$i] = 1;
			}

			if (!empty($additional_image_fields))
			{
				foreach ($additional_image_fields as $key => $val)
				{
					if (isset($HTTP_POST_VARS[$key.'_'.$i]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key.'_'.$i]) == "")
					{
						$error[$key.'_'.$i] = 1;
					}
				}
			}
		}
	}
	if (empty($error))
	{
		require(ROOT_PATH.'includes/image_utils.php');
		$no_resize = 0;
		$convert_options = init_convert_options();
		if ($convert_options['convert_error'])
		{
			$no_resize = 1;
		}
		show_table_header($lang['nav_images_check']." log", 1);
		echo "<tr>\n<td class=\"tablerow\">\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
		$n = 0;
		for ($i = 1; $i <= $num_newimages; $i++)
		{
			$log = array();
			$backup = false;
			$addimage = (isset($HTTP_POST_VARS['addimage_'.$i]) && $HTTP_POST_VARS['addimage_'.$i] == 1) ? 1 : 0;
			$result = false;
			$error_minor = false;
			$error_major = false;
			if ($addimage)
			{
				$image_media_file = stripslashes(trim($HTTP_POST_VARS['image_media_file_'.$i]));
				$image_thumb_file = stripslashes(trim($HTTP_POST_VARS['image_thumb_file_'.$i]));
				$image_name = trim($HTTP_POST_VARS['image_name_'.$i]);

				$cat_id = intval($HTTP_POST_VARS['cat_id_'.$i]);
				$old_cat_id = intval($HTTP_POST_VARS['old_cat_id_'.$i]);

				$user_id = (isset($HTTP_POST_VARS['user_id_'.$i]) && intval($HTTP_POST_VARS['user_id_'.$i]) != 0) ? intval($HTTP_POST_VARS['user_id_'.$i]) : "";
				$user_id = ($user_id) ? $user_id : ((isset($HTTP_POST_VARS['user_id']) && intval($HTTP_POST_VARS['user_id']) != 0) ? intval($HTTP_POST_VARS['user_id']) : $user_info['user_id']);

				$image_description = (isset($HTTP_POST_VARS['image_description_'.$i])) ? trim($HTTP_POST_VARS['image_description_'.$i]) : "";
				$image_date = (isset($HTTP_POST_VARS['image_date_'.$i])) ? ((trim($HTTP_POST_VARS['image_date_'.$i] != "")) ? "UNIX_TIMESTAMP('".trim($HTTP_POST_VARS['image_date_'.$i])."')" : time()) : time();
				$image_download_url = (isset($HTTP_POST_VARS['image_download_url_'.$i])) ? trim($HTTP_POST_VARS['image_download_url_'.$i]) : "";

				if (isset($HTTP_POST_VARS['image_keywords_'.$i]))
				{
					$image_keywords = trim($HTTP_POST_VARS['image_keywords_'.$i]);
					$image_keywords = preg_replace("/[\n\r]/is", ",", $image_keywords);
					$image_keywords_arr = explode(',', $image_keywords);
					array_walk($image_keywords_arr, 'trim_value');
					$image_keywords = implode(',', array_unique(array_filter($image_keywords_arr)));
				}
				else
				{
					$image_keywords = "";
				}
				$image_active = intval($HTTP_POST_VARS['image_active_'.$i]);
				$image_allow_comments = intval($HTTP_POST_VARS['image_allow_comments_'.$i]);

				$additional_field_sql = "";
				$additional_value_sql = "";
				if (!empty($additional_image_fields))
				{
					$table_fields = $site_db->get_table_fields(IMAGES_TABLE);
					foreach ($additional_image_fields as $key => $val)
					{
						if (isset($HTTP_POST_VARS[$key.'_'.$i]) && isset($table_fields[$key]))
						{
							$additional_field_sql .= ", $key";
							$additional_value_sql .= ", '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key.'_'.$i]))."'";
						}
					}
				}
				$file = MEDIA_PATH.(($old_cat_id != 0) ? "/".$old_cat_id : "")."/".$image_media_file;
                $big_dir = MEDIA_PATH."/".$old_cat_id."/".$big_folder;
				$big_file = "";
				$log[] = str_replace("{file}", str_replace(ROOT_PATH, "", $file), $lang['cni_working']);
				if (file_exists($file))
				{
					$image_media_file_backup = $image_media_file;
					if ($cat_id != $old_cat_id)
					{
						$image_media_file = copy_media($image_media_file, $old_cat_id, $cat_id);
						if ($image_media_file && file_exists(MEDIA_PATH."/".$cat_id."/".$image_media_file))
						{
							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id, $lang['cni_copy_success']);
						}
						else
						{
							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id, $lang['cni_copy_error']);
							$error_major = 1;
						}
						if ($image_thumb_file = copy_thumbnail($image_media_file_backup, $image_thumb_file, $old_cat_id, $cat_id))
						{
                            if (file_exists(THUMB_PATH."/".$cat_id."/".$image_thumb_file))
      						{
      							$log[] = str_replace("{name}", THUMB_DIR."/".$cat_id, $lang['cni_copy_success']);
      						}
      						else
      						{
      							$log[] = str_replace("{name}", THUMB_DIR."/".$cat_id, $lang['cni_copy_error']);
      						}
      					}
      					if (!$error_major && $big)
      					{
    						  if (file_exists($big_dir."/".$image_media_file_backup))
    						  {
    						    if ($big_file = copy_file($big_dir, MEDIA_PATH."/".$cat_id."/".$big_folder, $image_media_file_backup, $image_media_file, 1))
    						    {
    							    $log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$big_folder, $lang['cni_copy_success']);
    							  }
        						else
        						{
        							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$big_folder, $lang['cni_copy_error']);
        						}
    						    
    						  }
      					}
					}
					else
					{
					  if ($big && file_exists($big_dir."/".$image_media_file_backup))
					  {
   					  $big_file = $image_media_file_backup;
   					}
					  if (($image_media_file = filterFileName($image_media_file_backup)) != $image_media_file_backup)
					  {
						  if (($file_name = _rename(MEDIA_PATH."/".$cat_id, $image_media_file_backup, $image_media_file)) && file_exists(MEDIA_PATH."/".$cat_id."/".$file_name))
					    {
  							$log[] = str_replace("{from}", $image_media_file_backup, str_replace("{to}", $file_name, $lang['cni_file_rename_success']));
  							$image_media_file = $file_name;
  						}
  						else
  						{
  							$log[] = str_replace("{from}", $image_media_file_backup, str_replace("{to}", $image_media_file, $lang['cni_file_rename_error']));
  							$image_media_file = $image_media_file_backup;
  							$error_minor = 1;
  						}
  						if (!$error_minor)
  						{
  						  if ($image_media_file != $image_media_file_backup && file_exists(THUMB_PATH."/".$cat_id."/".$image_media_file_backup))
    						{
    						  if (($file_name = _rename(THUMB_PATH."/".$cat_id, $image_media_file_backup, $image_media_file, 1)) && file_exists(THUMB_PATH."/".$cat_id."/".$file_name))
    					    {
      							$log[] = str_replace("{from}", $image_media_file_backup, str_replace("{to}", $file_name, $lang['cni_thumbnail_rename_success']));
      							$image_thumb_file = $file_name;
      						}
      						else
      						{
      							$log[] = str_replace("{from}", $image_media_file_backup, str_replace("{to}", $image_media_file, $lang['cni_thumbnail_rename_error']));
      							$image_thumb_file = "";
  //    							$image_thumb_file = $image_media_file_backup;
      						}
      					}
  						  if ($big && $big_file)
  						  {
  						    if ($image_media_file != $big_file)
    						  {
      						  if (($file_name = _rename($big_dir, $big_file, $image_media_file, 1)) && file_exists($big_dir."/".$file_name))
      					    {
        							$big_file = $file_name;
        							$log[] = str_replace("{from}", $big_folder."/".$image_media_file_backup, str_replace("{to}", $big_folder."/".$big_file, $lang['cni_file_rename_success']));
        						}
        						else
        						{
        							$log[] = str_replace("{from}", $big_folder."/".$image_media_file_backup, str_replace("{to}", $big_folder."/".$image_media_file, $lang['cni_file_rename_error']));
        						}
        					}
      					}
      				}
					  }
					}
					$file = MEDIA_PATH."/".$cat_id."/".$image_media_file;
          $image_info = false;
				  $do_resize = false;
				  $do_annotate = false;
				  $do_backup = false;
				  $do_thumb = false;
					if (!$error_major)
					{
						if (!$no_resize && ($image_info = getimagesize($file)) && $image_info[2] > 0 && $image_info[2] < 4)
						{
						  $do_thumb = true;
						  if ($auto_resize)
						  {
  							if ($resize_type == 1 && ($image_info[0] > $dimension || $image_info[1] > $dimension))
  							{
  								$do_resize = true;
  							}
  							elseif ($resize_type == 2 && $image_info[0] > $dimension)
  							{
  								$do_resize = true;
  							}
  							elseif ($resize_type == 3 && $image_info[1] > $dimension)
  							{
  								$do_resize = true;
  							}
  						}
							if ($annotate)
							{
  							@include_once(ROOT_PATH.'includes/annotate.php');
  							if (function_exists("annotate_image"))
  							{
  							  $do_annotate = true;
  							}
  						}
						}
  					if ($big && $do_resize && !$big_file)
  					{
  					  if ($big_file = copy_file(MEDIA_PATH."/".$cat_id, MEDIA_PATH."/".$cat_id."/".$big_folder, $image_media_file, $image_media_file, 1, 1, 0))
  					  {
  					    $log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$big_folder, $lang['cni_copy_success']);
  					  }
  						else
  						{
  							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$big_folder, $lang['cni_copy_error']);
  						}
  					}
  					if ($backup_orig && !$backup && ($do_resize || $do_annotate))
  					{
  					  if ($big_file)
  					  {
  					    $src_dir = MEDIA_PATH."/".$cat_id."/".$big_folder;
  					  }
  					  else
  					  {
  					    $src_dir = MEDIA_PATH."/".$cat_id;
  					  }
  					  if ($backup_file = copy_file($src_dir, $backup_orig."/".$cat_id, $image_media_file, $image_media_file, 1, 1, 0))
  					  {
  							$log[] = str_replace("{name}", str_replace(ROOT_PATH, "", $backup_orig) . "/".$cat_id . "/" . $image_media_file, $lang['cni_backup_success']);
  						}
  						else
  						{
  							$log[] = str_replace("{name}", str_replace(ROOT_PATH, "", $backup_orig) . "/" . $cat_id . "/" . $image_media_file, $lang['cni_backup_error']);
  						}
  					}
  					$file_thumb = THUMB_PATH."/".$cat_id."/".$image_media_file;
  					if ($do_thumb && $auto_thumbs && $image_thumb_file == "" && !file_exists($file_thumb))
  					{
  						$ok = 0;
  						if ($resize_type_thumbs == 1 && ($image_info[0] > $dimension_thumbs || $image_info[1] > $dimension_thumbs))
  						{
  							$ok = 1;
  						}
  						elseif ($resize_type_thumbs == 2 && $image_info[0] > $dimension_thumbs)
  						{
  							$ok = 1;
  						}
  						elseif ($resize_type_thumbs == 3 && $image_info[1] > $dimension_thumbs)
  						{
  							$ok = 1;
  						}
  						if ($ok)
  						{
  							if (create_thumbnail($file, $file_thumb, $quality_thumbs, $dimension_thumbs, $resize_type_thumbs))
  							{
  								$log[] = $lang['cni_thumbnail_success'];
  								$image_thumb_file = $image_media_file;
  							}
  							else
  							{
  								$log[] = $lang['cni_thumbnail_error'];
  								$image_thumb_file = "";
  							}
  						}
  					}
  					if ($do_resize)
  					{
  						if (resize_image($file, $quality, $dimension, $resize_type))
  						{
  							$log[] = $lang['cni_resized_success'];
  						}
  						else
  						{
  							$log[] = $lang['cni_resized_error'];
  						}
  					}
  					if ($do_annotate)
  					{
  						if (annotate_image($file))
  						{
  							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$image_media_file, $lang['cni_annotation_success']);
  						}
  						else
  						{
  							$log[] = str_replace("{name}", MEDIA_DIR."/".$cat_id."/".$image_media_file, $lang['cni_annotation_error']);
  						}
  						if ($big_annotate)
  						{
  							if (annotate_image(MEDIA_PATH . "/" . $cat_id . "/" . $big_folder . "/" . $image_media_file))
  							{
  								$log[] = str_replace("{name}", MEDIA_DIR . "/" . $cat_id . "/" . $big_folder . "/" . $image_media_file, $lang['cni_annotation_success']);
  							}
  							else
  							{
  								$log[] = str_replace("{name}", MEDIA_DIR . "/" . $cat_id . "/" . $big_folder . "/" . $image_media_file, $lang['cni_annotation_error']);
  							}
  						}
  					}
  					if (!$error_major)
  					{
  						$sql = "INSERT INTO ".IMAGES_TABLE."
  										(cat_id, user_id, image_name, image_description, image_keywords, image_date, image_active, image_media_file, image_thumb_file, image_download_url, image_allow_comments".$additional_field_sql.")
  										VALUES
  										($cat_id, $user_id, '$image_name', '$image_description', '$image_keywords', $image_date, $image_active, '".addslashes($image_media_file)."', '".addslashes($image_thumb_file)."', '$image_download_url', $image_allow_comments".$additional_value_sql.")";
  						$result = $site_db->query($sql);
  						$image_id = $site_db->get_insert_id();
  					}
  				}
				}
				else
				{
					$log[] = $lang['file_not_found'];
				}

				if ($result)
				{
					$search_words = array();
					foreach ($search_match_fields as $image_column => $match_column)
					{
						if (isset($HTTP_POST_VARS[$image_column.'_'.$i]))
						{
							$search_words[$image_column] = stripslashes($HTTP_POST_VARS[$image_column.'_'.$i]);
						}
					}
					add_searchwords($image_id, $search_words);
					$log[] = $lang['image_add_success'].": <b><a target=\"_blank\" href=\"".$site_sess->url(ROOT_PATH."details.php?image_id=".$image_id)."\">".format_text($image_name)."</a></b>";
//ensure that rescaned images wont get same info as already added ones.
					$n++;
				}
				else
				{
					$log[] = $lang['cni_error'];
				}
				$log[] = "";
				unset($HTTP_POST_VARS['image_name_'.$i]);
				unset($HTTP_POST_VARS['image_media_file_'.$i]);
				unset($HTTP_POST_VARS['image_thumb_file_'.$i]);
				unset($HTTP_POST_VARS['image_name_'.$i]);
				unset($HTTP_POST_VARS['cat_id_'.$i]);
				unset($HTTP_POST_VARS['old_cat_id_'.$i]);
				unset($HTTP_POST_VARS['user_id_'.$i]);
				unset($HTTP_POST_VARS['image_description_'.$i]);
				unset($HTTP_POST_VARS['image_date_'.$i]);
				unset($HTTP_POST_VARS['image_download_url_'.$i]);
				unset($HTTP_POST_VARS['image_keywords_'.$i]);
				unset($HTTP_POST_VARS['image_active_'.$i]);
				unset($HTTP_POST_VARS['image_allow_comments_'.$i]);
				unset($error['image_name_'.$i]);
				unset($error['image_media_file_'.$i]);
				unset($error['image_thumb_file_'.$i]);
				unset($error['image_name_'.$i]);
				unset($error['cat_id_'.$i]);
				unset($error['old_cat_id_'.$i]);
				unset($error['user_id_'.$i]);
				unset($error['image_description_'.$i]);
				unset($error['image_date_'.$i]);
				unset($error['image_download_url_'.$i]);
				unset($error['image_keywords_'.$i]);
				unset($error['image_active_'.$i]);
				unset($error['image_allow_comments_'.$i]);
				if (!empty($additional_image_fields))
				{
					foreach ($additional_image_fields as $key => $val)
					{
						unset($HTTP_POST_VARS[$key.'_'.$i]);
						unset($error[$key.'_'.$i]);
					}
				}
			}
			if (count($log))
			{
				foreach ($log as $val)
				{
					echo $val."<br />";
				}
			}
		}
		if (!$n)
		{
			echo $lang['no_newimages_added'];
		}
		echo "</td>\n</tr>\n</table>\n";
		echo "</td>\n</tr>\n";
		show_table_footer();
		$status = 1;
		$action = "checkimages";
	}
	else
	{
		$msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
		$action = "checkimages";
	}
	if (!$rescan)
	{
		unset($HTTP_POST_VARS['action']);
	}
}

if ($action == "checkimages")
{
	$max_dimension = (isset($HTTP_POST_VARS['max_dimension'])) ? $HTTP_POST_VARS['max_dimension'] : $max_dimension_default;
	$cat_id = (isset($HTTP_POST_VARS['cat_id_p'])) ? intval($HTTP_POST_VARS['cat_id_p']) : 0;
	$num_newimages = (isset($HTTP_POST_VARS['num_newimages'])) ? intval($HTTP_POST_VARS['num_newimages']) : $num_newimages_default;
	$detailed = (isset($HTTP_POST_VARS['detailed'])) ? intval($HTTP_POST_VARS['detailed']) : $detailed_default;
	$detailed_checked = ($detailed == 1) ? " checked=\"checked\"" : "";
	$auto_resize = (isset($HTTP_POST_VARS['auto_resize'])) ? intval($HTTP_POST_VARS['auto_resize']) : $auto_resize_default;
	$auto_resize_checked = ($auto_resize == 1) ? " checked=\"checked\"" : "";
	$auto_thumbs = (isset($HTTP_POST_VARS['auto_thumbs'])) ? intval($HTTP_POST_VARS['auto_thumbs']) : $auto_thumbs_default;
	$auto_thumbs_checked = ($auto_thumbs == 1) ? " checked=\"checked\"" : "";
	$iptc_date = (isset($HTTP_POST_VARS['iptc_date'])) ? intval($HTTP_POST_VARS['iptc_date']) : $iptc_date_default;
	$iptc_description = (isset($HTTP_POST_VARS['iptc_description'])) ? intval($HTTP_POST_VARS['iptc_description']) : $iptc_description_default;
	$iptc_keywords = (isset($HTTP_POST_VARS['iptc_keywords'])) ? intval($HTTP_POST_VARS['iptc_keywords']) : $iptc_keywords_default;
	$iptc_name = (isset($HTTP_POST_VARS['iptc_name'])) ? intval($HTTP_POST_VARS['iptc_name']) : $iptc_name_default;
	$dimension = (isset($HTTP_POST_VARS['dimension'])) ? intval($HTTP_POST_VARS['dimension']) : $dimension_default;
	$user_id = (isset($HTTP_POST_VARS['user_id'])) ? intval($HTTP_POST_VARS['user_id']) : $user_info['user_id'];
	$resize_type = (isset($HTTP_POST_VARS['resize_type'])) ? intval($HTTP_POST_VARS['resize_type']) : 1;
	$quality = (isset($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) && intval($HTTP_POST_VARS['quality']) <= 100) ? intval($HTTP_POST_VARS['quality']) : $quality_default;
	$dimension_thumbs = (isset($HTTP_POST_VARS['dimension_thumbs'])) ? intval($HTTP_POST_VARS['dimension_thumbs']) : $dimension_thumbs_default;
	$resize_type_thumbs = (isset($HTTP_POST_VARS['resize_type_thumbs'])) ? intval($HTTP_POST_VARS['resize_type_thumbs']) : 1;
	$quality_thumbs = (isset($HTTP_POST_VARS['quality_thumbs']) && intval($HTTP_POST_VARS['quality_thumbs']) && intval($HTTP_POST_VARS['quality_thumbs']) <= 100) ? intval($HTTP_POST_VARS['quality_thumbs']) : $quality_thumbs_default;
	$big = (isset($HTTP_POST_VARS['big'])) ? $HTTP_POST_VARS['big'] : $big_default;
	$annotate = (isset($HTTP_POST_VARS['annotate'])) ? $HTTP_POST_VARS['annotate'] : $annotate_default;
	$big_folder = (isset($HTTP_POST_VARS['big_folder'])) ? trim($HTTP_POST_VARS['big_folder']) : $big_folder_default;
	$subcats = (isset($HTTP_POST_VARS['subcats'])) ? trim($HTTP_POST_VARS['subcats']) : $subcats_default;
	$subcats_checked = ($subcats == 1) ? " checked=\"checked\"" : "";
	if ($num_newimages == "" || !$num_newimages)
	{
		$num_newimages = 30;
	}


	show_form_header("checkimages.php", "checkimages");
	show_table_header($lang['nav_images_check'], 2);
	show_input_row($lang['num_newimages_desc'], "num_newimages", $num_newimages);
	show_input_row($lang['cni_max_dim'], "max_dimension", $max_dimension);
//  show_table_separator("IPTC info", 2);
	show_radio_row($lang['cni_iptc_name'], "iptc_name", $iptc_name);
	show_radio_row($lang['cni_iptc_description'], "iptc_description", $iptc_description);
	show_radio_row($lang['cni_iptc_keywords'], "iptc_keywords", $iptc_keywords);
	show_radio_row($lang['cni_iptc_date'], "iptc_date", $iptc_date);
	show_radio_row($lang['detailed_version'], "detailed", $detailed);
	$desc = get_category_dropdown_check($cat_id);
	$desc .= "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"subcats\" value=\"1\"".$subcats_checked.">".$lang['cni_check_subcat'];
	show_custom_row($desc, "<input type=\"submit\" value=\"".$lang['nav_images_check']."\" class=\"button\">");
	show_table_footer();
	echo "</form>";
}

if (isset($HTTP_POST_VARS['action']) && $action == "checkimages")
{
	if (isset($HTTP_POST_VARS['detailed']) && $HTTP_POST_VARS['detailed'] == 1)
	{
		$detailed = 1;
		$colspan = 2;
	}
	else
	{
		$detailed = 0;
		$colspan = 7;
	}
	$cat_id = (isset($HTTP_POST_VARS['cat_id_p'])) ? intval($HTTP_POST_VARS['cat_id_p']) : 0;
	$cat_id_p = $cat_id;
	$image_list_sql = array();
	$cats = array($cat_id);
	if ($cat_id)
	{
		if ($subcats)
		{
			$sub_cat_cache = $cat_parent_cache;
			get_subcats($cat_id, $cat_id);
		}

		$image_list_all = array();
		foreach ($cats as $key)
		{
			$cat_path = "/".$key;
			if ($handle = opendir(MEDIA_PATH.$cat_path))
			{
				while ($file = @readdir($handle))
				{
					if ($file != "." && $file != "..")
					{
						if (check_media_type($file))
						{
							$image_list_all[$key][] = $file;
						}
					}
				}
				closedir($handle);
			}
		}
		$cat_sql = implode(", ", $cats);
		foreach ($image_list_all as $key => $val)
		{
			sort($image_list_all[$key]);
		}
	}
	else
	{
		$image_list_all = array();
		$cat_image = array();
		if ($handle = opendir(MEDIA_PATH))
		{
			while ($file = @readdir($handle))
			{
				if ($file != "." && $file != "..")
				{
					if (check_media_type($file))
					{
						$image_list_all[0][] = $file;
					}
				}
			}
			closedir($handle);
		}
		foreach ($image_list_all as $key => $val)
		{
			sort($image_list_all[$key]);
		}
		$sql = "SELECT cat_id
						FROM ".CATEGORIES_TABLE;
		$result = $site_db->query($sql);
		while ($row = $site_db->fetch_array($result))
		{
			$cat_id = $cats[] = $row['cat_id'];
			$cat_path = ($cat_id == 0) ? "" : "/".$cat_id;
			if ($handle = opendir(MEDIA_PATH.$cat_path))
			{
				while ($file = @readdir($handle))
				{
					if ($file != "." && $file != "..")
					{
						if (check_media_type($file))
						{
							$image_list_all[$row['cat_id']][] = $file;
						}
					}
				}
				closedir($handle);
			}
		}
		foreach ($image_list_all as $key => $val)
		{
			sort($image_list_all[$key]);
		}
		$cat_sql = implode(", ", $cats);
	}
	$sql = "SELECT image_media_file, cat_id
					FROM ".IMAGES_TABLE."
					WHERE cat_id IN ($cat_sql)
					ORDER BY cat_id";
	$result = $site_db->query($sql);

	while ($row = $site_db->fetch_array($result))
	{
		$image_list_sql[$row['cat_id']][] = $row['image_media_file'];
	}
	foreach ($image_list_sql as $key => $val)
	{
		sort($image_list_sql[$key]);
	}

	$image_list = array();
	$image_counter = 0;
	foreach ($image_list_all as $key => $val)
	{
		for ($i = 0; $i < count($image_list_all[$key]); $i++)
		{
			if ($image_counter == $num_newimages)
			{
				break;
			}
            if (!array_key_exists($key, $image_list_sql) || !@in_array($image_list_all[$key][$i], $image_list_sql[$key]))
			{
				$image_list[$key][] = $image_list_all[$key][$i];
				$image_counter++;
			}
		}
	}
	foreach ($image_list as $key => $val)
	{
		sort($image_list[$key]);
	}
	$num_all_newimages = $image_counter;

	if ($msg != "")
	{
		printf("<b>%s</b>\n", $msg);
	}

	show_form_header("checkimages.php", "savenewimages", "form");

	show_table_header($lang['cni_auto_resizer']."&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"auto_resize\" value=\"1\"".(($auto_resize) ? " checked" : "").">".$lang['on']."&nbsp;<input type=\"radio\" name=\"auto_resize\" value=\"\"".(($auto_resize) ? "" : " checked" ).">".$lang['off'], 2);
	show_input_row($lang['resize_dimension_desc'], "dimension", $dimension);

	$resize = "\n<select name=\"resize_type\">\n";
	foreach ($auto_thumbnail_resize_type_optionlist as $key => $val)
	{
		$resize .= "<option value=\"$key\"";
		if ($resize_type == $key)
		{
			$resize .= " selected=\"selected\"";
		}
		$resize .= ">$val</option>\n";
	}
	$resize .= "</select>\n";
	show_custom_row($lang['resize_proportions_desc'], $resize);

	show_input_row($lang['resize_quality_desc'], "quality", $quality);
	show_radio_row($lang['cni_save_orig'], "big", $big);
	show_input_row($lang['cni_big_folder'], "big_folder", $big_folder);
	show_radio_row($lang['cni_add_ann'], "annotate", $annotate);
	show_table_separator($lang['cni_auto_thumbnailer']."&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"auto_thumbs\" value=\"1\"".(($auto_thumbs) ? " checked" : "").">".$lang['on']."&nbsp;<input type=\"radio\" name=\"auto_thumbs\" value=\"\"".(($auto_thumbs) ? "" : " checked" ).">".$lang['off'], 2);
	show_input_row($lang['convert_thumbnail_dimension'], "dimension_thumbs", $dimension_thumbs);

	$resize = "\n<select name=\"resize_type_thumbs\">\n";
	foreach ($auto_thumbnail_resize_type_optionlist as $key => $val)
	{
		$resize .= "<option value=\"$key\"";
		if ($resize_type_thumbs == $key)
		{
			$resize .= " selected=\"selected\"";
		}
		$resize .= ">$val</option>\n";
	}
	$resize .= "</select>\n";
	show_custom_row($lang['resize_proportions_desc'], $resize);

	show_input_row($lang['convert_thumbnail_quality'], "quality_thumbs", $quality_thumbs);
	if (!$detailed)
	{
		show_table_separator($lang['user'], 2);
		show_user_select_row($lang['add_as_user'], $user_id);
	}
	show_table_footer();

	show_hidden_input("cat_id_p", $cat_id_p);
	show_hidden_input("mode", 1);
	show_table_header(preg_replace("/".$site_template->start."num_all_newimages".$site_template->end."/siU", $num_all_newimages, $lang['checkimages_note']), $colspan);

	if ($num_all_newimages)
	{
		show_description_row("<input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\"".(($status) ? "" : " checked=\"checked\"")." /> <b>".$lang['check_all']."</b>", $colspan);
	}
	else
	{
		show_description_row($lang['no_newimages'], $colspan);
	}
	$i = 0;
	foreach ($image_list as $key => $val)
	{
		$cat_id = $key;
		$cat_path = ($cat_id == 0) ? "" : "/".$cat_id;
		$cat_name = @$cat_cache[$cat_id]['cat_name'];
		$count = count($val);
		foreach ($val as $file)
		{
			 $i++;
		 //Check Image
			$file_type = get_file_extension($file);
			$iptc_auto_now = (($iptc_name || $iptc_date || $iptc_description || $iptc_keywords) && $imageinfo = @getimagesize(MEDIA_PATH.$cat_path."/".$file, $info)) ? 1 : 0;
			$image_name = get_file_name($file);
			$date = date("Y-m-d H:i:s", time());
			$caption = "";
			$keywords = "";

			$thumb_file = 0;
			if (file_exists(THUMB_PATH.$cat_path."/".$image_name.".jpg"))
			{
				$thumb_file = $image_name.".jpg";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".JPG"))
			{
				$thumb_file = $image_name.".JPG";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".jpeg"))
			{
				$thumb_file = $image_name.".jpeg";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".JPEG"))
			{
				$thumb_file = $image_name.".JPEG";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".gif"))
			{
				$thumb_file = $image_name.".gif";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".GIF"))
			{
				$thumb_file = $image_name.".GIF";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".png"))
			{
				$thumb_file = $image_name.".png";
			}
			elseif (file_exists(THUMB_PATH.$cat_path."/".$image_name.".PNG"))
			{
				$thumb_file = $image_name.".PNG";
			}
			$image_name = str_replace("_", " ", $image_name);
			$image_name = str_replace("%20", " ", $image_name);

			$checked = (isset($HTTP_POST_VARS['image_name_'.$i]) && (!isset($HTTP_POST_VARS['addimage_'.$i]) || $HTTP_POST_VARS['addimage_'.$i] != 1) || $status) ? "" : " checked=\"checked\"";

			if ($detailed)
			{
				show_table_separator("<input type=\"checkbox\" name=\"addimage_".$i."\" value=\"1\"".$checked." /> ".$file, 2);
				show_custom_row($lang['cni_foundin']." ", ($cat_name == "" && !$key) ? "[".$lang['cni_root_folder']."]" : $cat_name." (ID:$key)");
				if ($file_type == "gif" || $file_type == "jpg"  || $file_type == "png")
				{
					$file_src = MEDIA_PATH.$cat_path."/".$file;
					$img_info = @getimagesize($file_src);
					$width = $max_dimension;
					$height = $max_dimension;
					if ($img_info[0] > 0 && $img_info[1] > 0)
					{
						if ($img_info[0] > $width || $img_info[1] > $height)
						{
							$ratio = $img_info[0] / $img_info[1];
							if ($ratio > 1)
							{
								$width = $max_dimension;
								$height = round(($max_dimension / $img_info[0]) * $img_info[1]);
							}
							else
							{
								$width = round(($max_dimension / $img_info[1]) * $img_info[0]);
								$height = $max_dimension;
							}
						}
						else
						{
							$width = $img_info[0];
							$height = $img_info[1];
						}
					}
					show_image_row($lang['image']."<br /><span class=\"smalltext\">(".$img_info[0]."x".$img_info[1].")</span>", $file_src, 1, "", $height, $width);
				}
				else
				{
					show_image_row($lang['image'], ICON_PATH."/".$file_type."." . ICON_EXT, 1);
				}
				show_hidden_input("image_media_file_".$i, $file);

				if ($thumb_file)
				{
					$thumb_src = THUMB_PATH.$cat_path."/".$thumb_file;
					$img_info = @getimagesize($thumb_src);
					$width = 48;
					$height = 48;
					$dim = $width;
					if ($img_info[0] > 0 && $img_info[1] > 0)
					{
						if ($img_info[0] > $width || $img_info[1] > $height)
						{
							$ratio = $img_info[0] / $img_info[1];
							if ($ratio > 1)
							{
								$width = $dim;
								$height = round(($dim / $img_info[0]) * $img_info[1]);
							}
							else
							{
								$width = round(($dim / $img_info[1]) * $img_info[0]);
								$height = $dim;
							}
						}
						else
						{
							$width = $img_info[0];
							$height = $img_info[1];
						}
					}
					show_image_row($lang['thumb'], $thumb_src, 1, "", $height, $width);
					show_hidden_input("image_thumb_file_".$i, $thumb_file);
				}
				else
				{
					show_custom_row($lang['thumb'], $lang['no_thumb_newimages_ext']);
					show_hidden_input("image_thumb_file_".$i, "");
				}
				show_input_row($lang['field_download_url'].$lang['download_url_desc'], "image_download_url_".$i, "", $textinput_size);


				$image_name = (isset($error['image_name_'.$i]) && isset($HTTP_POST_VARS['image_name_'.$i])) ? $HTTP_POST_VARS['image_name_'.$i] : str_replace("_"," ", $image_name);
				$iptc = "";
				if (isset($info['APP13']))
				{
					$iptc = iptcparse($info['APP13']);
				}
				if (is_array($iptc) && $iptc_name)
				{
					if (isset($iptc['2#005'][0]))
					{
						$image_name = $iptc['2#005'][0];
					}
				}
				$title = $lang['field_image_name'].((isset($file_src)) ? get_iptc_insert_link($file_src, "object_name", "image_name_".$i, 0) : "");
				show_input_row($title, "image_name_".$i, stripslashes($image_name), $textinput_size);

				$title = $lang['field_description_ext'].((isset($file_src)) ? get_iptc_insert_link($file_src, "caption", "image_description_".$i) : "");
				if (is_array($iptc) && $iptc_description)
				{
					$caption = (isset($iptc['2#120'][0])) ? $iptc['2#120'][0] : "";
// Uncomment lines below, to add date into captions
/*
					if (isset($iptc['2#055'][0]))
					{
						$caption .= (($caption != "") ? "\n" : "").$iptc['2#055'][0];
						$caption = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $caption);
					}
*/
				}
				show_textarea_row($title, "image_description_".$i, $caption, $textarea_size);

				$title = $lang['field_keywords_ext'].((isset($file_src)) ? get_iptc_insert_link($file_src, "keyword", "image_keywords_".$i) : "");
				if (is_array($iptc) && $iptc_keywords)
				{
					if (isset($iptc['2#025']))
					{
    					$_iptc_keywords = array();

						foreach ($iptc['2#025'] as $val)
						{
							$_iptc_keywords[] = $val;
						}
    					$keywords = trim(implode(',', $_iptc_keywords));
    					$keywords = preg_replace("/[\n\r]/is", ",", $keywords);
    					$keywords_arr = explode(',', $keywords);
    					array_walk($keywords_arr, 'trim_value');
    					$keywords = implode(',', array_unique(array_filter($keywords_arr)));						
					}
				}
				show_textarea_row($title, "image_keywords_".$i, $keywords, $textarea_size);
				if (isset($error['cat_id_'.$i]))
				{
					$title = sprintf("<span class=\"marktext\">%s *</span>", $lang['field_category']);
				}
				else
				{
					$title = $lang['field_category'];
				}
				echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td>".get_category_dropdown($cat_id, 0, 3, $i)."</td>\n</tr>\n";

				show_user_select_row($lang['user'], $user_id, $i);

				if (is_array($iptc) && $iptc_date)
				{
					{
						$date = (isset($iptc['2#055'][0])) ? preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $iptc['2#055'][0]) : $date;
					}
				}
				$title = $lang['field_date'].$lang['date_desc'].$lang['date_format'].((isset($file_src)) ? get_iptc_insert_link($file_src, "date_created", "image_date_".$i, 0) : "");
				show_input_row($title, "image_date_".$i, $date, $textinput_size);

				show_hidden_input("old_cat_id_".$i, $cat_id);
				show_radio_row($lang['field_free'], "image_active_".$i, 1);
				show_radio_row($lang['field_allow_comments'], "image_allow_comments_".$i, 1);
				show_additional_fields("image", array(), IMAGES_TABLE, $i);
			}
			else
			{
				echo "<tr class=".get_row_bg().">\n";
				echo "<td><input type=\"checkbox\" name=\"addimage_".$i."\" value=\"1\"$checked></td>\n";
				$link = "<a href=\"".MEDIA_PATH.$cat_path."/".$file."\" target=\"_blank\">".$file."</a>";
				show_hidden_input("image_media_file_".$i, $file);
				if ($thumb_file)
				{
					$file_src = THUMB_PATH.$cat_path."/".$thumb_file;
					$img_info = @getimagesize($file_src);
					$width = 48;
					$height = 48;
					$dim = $width;
					if ($img_info[0] > 0 && $img_info[1] > 0)
					{
						if ($img_info[0] > $width || $img_info[1] > $height)
						{
							$ratio = $img_info[0] / $img_info[1];
							if ($ratio > 1)
							{
								$width = $dim;
								$height = floor(($dim / $img_info[0]) * $img_info[1]);
							}
							else
							{
								$width = floor(($dim / $img_info[1]) * $img_info[0]);
								$height = $dim;
							}
						}
						else
						{
							$width = $img_info[0];
							$height = $img_info[1];
						}
					}
					$thumb_status = $lang['thumb_newimages_exists'];
					$thumb_status .= "<br><img src=\"".$file_src."\" width=\"".$width."\" height=\"".$height."\">";
					show_hidden_input("image_thumb_file_".$i, $thumb_file);
				}
				else
				{
					$thumb_status = $lang['no_thumb_newimages'];
					if ($thumbpreview)
					{
						$thumb_status .= "<br><img src=\"checkimages.php?showthumb=".$file."&cat_id=".$cat_id."\">";
					}
					show_hidden_input("image_thumb_file_".$i, "");
				}
				echo "<td><b>".$link."</b><br />-&raquo; ".$thumb_status."</td>\n";
				echo "<td>" . $lang['cni_foundin'] . ": ".(($cat_name == "" && !$key) ? "[root folder]" : "$cat_name (ID:$key)")."</td>\n";
				if (isset($error['image_name_'.$i]))
				{
					$field_image_name = sprintf("<span class=\"marktext\">%s</span>", $lang['field_image_name']);
					$image_name = $HTTP_POST_VARS['image_name_'.$i];
				}
				else
				{
					$field_image_name = $lang['field_image_name'];//sprintf("%s", $lang['field_image_name']);
					$image_name = (isset($HTTP_POST_VARS['image_name_'.$i])) ? $HTTP_POST_VARS['image_name_'.$i] : str_replace("_"," ", $image_name);
					if ($iptc_auto_now)
					{
						if (isset($info['APP13']))
						{
							$iptc = iptcparse($info['APP13']);
							if (is_array($iptc))
							{
								if ($iptc_name && isset($iptc['2#005'][0]))
								{
									$image_name =  $iptc['2#005'][0];
								}
								$caption = ($iptc_description && isset($iptc['2#120'][0])) ? $iptc['2#120'][0] : "";
								$_iptc_keywords = array();
								if ($iptc_keywords && isset($iptc['2#025']))
								{
									foreach ($iptc['2#025'] as $val)
									{
										$_iptc_keywords[] = $val;
									}
                					$keywords = trim(implode(',', $_iptc_keywords));
                					$keywords = preg_replace("/[\n\r]/is", ",", $keywords);
                					$keywords_arr = explode(',', $keywords);
                					array_walk($keywords_arr, 'trim_value');
                					$keywords = implode(',', array_unique(array_filter($keywords_arr)));
								}
								if ($iptc_date)
								{
									$date = (isset($iptc['2#055'][0])) ? preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $iptc['2#055'][0]) : $date;
								}
							}
						}
					}
				}
				echo "<td>".$field_image_name.":<br /><input type=\"text\" name=\"image_name_".$i."\" value=\"".stripslashes($image_name)."\">\n";
				$field_category = $lang['field_category'];
				if (isset($error['cat_id_'.$i]))
				{
					$field_category = sprintf("<span class=\"marktext\">%s</span>", $lang['field_category']);
				}
				$cat_id_selected = (isset($HTTP_POST_VARS['cat_id_'.$i])) ? intval($HTTP_POST_VARS['cat_id_'.$i]) : $cat_id;
				echo "<td>".$field_category.":<br />".get_category_dropdown($cat_id_selected, 0, 3, $i)."</td>\n";
				show_hidden_input("old_cat_id_".$i, $cat_id);
				show_hidden_input("image_description_".$i, htmlspecialchars($caption));
				show_hidden_input("image_keywords_".$i, htmlspecialchars($keywords));
				show_hidden_input("image_date_".$i, htmlspecialchars($date));

				echo "<td>".$lang['field_free'].":<br />";
				if (isset($HTTP_POST_VARS['image_active_'.$i]) && $HTTP_POST_VARS['image_active_'.$i] == 0)
				{
					$c1 = "";
					$c2 = " checked=\"checked\"";
				}
				else
				{
					$c1 = " checked=\"checked\"";
					$c2 = "";
				}
				echo "<input type=\"radio\" name=\"image_active_".$i."\" value=\"1\"".$c1."> ".$lang['yes']."&nbsp;&nbsp;&nbsp;\n";
				echo "<input type=\"radio\" name=\"image_active_".$i."\" value=\"0\"".$c2."> ".$lang['no']." ";
				echo "</td>\n";

				echo "<td>".$lang['field_allow_comments'].":<br />";
				if (isset($HTTP_POST_VARS['image_allow_comments_'.$i]) && $HTTP_POST_VARS['image_allow_comments_'.$i] == 0)
				{
					$c1 = "";
					$c2 = " checked=\"checked\"";
				}
				else
				{
					$c1 = " checked=\"checked\"";
					$c2 = "";
				}
				echo "<input type=\"radio\" name=\"image_allow_comments_".$i."\" value=\"1\"".$c1."> ".$lang['yes']."&nbsp;&nbsp;&nbsp;\n";
				echo "<input type=\"radio\" name=\"image_allow_comments_".$i."\" value=\"0\"".$c2."> ".$lang['no']." ";
				echo "</td>\n";
				echo "</tr>\n";
//        show_hidden_input("user_id_".$i, $user_id);
			}
		}
	}
	if ($num_all_newimages)
	{
		show_hidden_input("max_dimension", $max_dimension);
		show_hidden_input("num_newimages", $num_newimages);
		show_hidden_input("detailed", $detailed);
		show_hidden_input("subcats", $subcats);
		show_hidden_input("mode", 1);
		show_form_footer($lang['add'], $lang['reset'], $colspan);
	}
	else
	{
		show_table_footer();
	}
}
show_admin_footer();
?>