<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: download.php                                         *
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

$main_template = 0;

$nozip = 1;
define('GET_CACHES', 1);
define('ROOT_PATH', './');
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();

if (!function_exists('file_get_contents')) {
  function file_get_contents($filename, $incpath = false, $resource_context = null) {
    if (false === $fh = fopen($filename, 'rb', $incpath)) {
      user_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
      return false;
    }

    clearstatcache();
    if ($fsize = @filesize($filename)) {
      $data = fread($fh, $fsize);
    } else {
      $data = '';
      while (!feof($fh)) {
        $data .= fread($fh, 8192);
      }
    }

    fclose($fh);
    return $data;
  }
}

function fix_file_path($file_path) {
  if (!is_remote_file($file_path) && !file_exists($file_path)) {
    $file_path = preg_replace("/\/{2,}/", "/", get_document_root()."/".$file_path);
  }
  return $file_path;
}

function send_file($file_name, $file_path) {
  @session_write_close();

  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  if (get_user_os() == "MAC") {
    header("Content-Type: application/x-unknown\n");
    header("Content-Disposition: attachment; filename=\"".$file_name."\"\n");
  }
  elseif (get_browser_info() == "MSIE") {
    $disposition = (!preg_match("/\.zip$/i", $file_name)) ? 'attachment' : 'inline';
    header("Content-Disposition: $disposition; filename=\"".$file_name."\"\n");
    header("Content-Type: application/x-ms-download\n");
  }
  else {
    header("Content-Disposition: attachment; filename=\"".$file_name."\"\n");
    header("Content-Type: application/octet-stream\n");
  }

  $file_path = fix_file_path($file_path);

  if (!is_remote_file($file_path) && ($filesize = filesize($file_path)) > 0 && !@ini_get('zlib.output_compression') && !@ini_get('output_handler')) {
    header("Content-Length: ".$filesize."\n\n");
  }

  @readfile($file_path);
}

$file = array();
$file_path = null;
$file_name = null;

if ($action == "lightbox") {
  if (empty($user_info['lightbox_image_ids']) || !function_exists("gzcompress") || !function_exists("crc32")) {
    redirect("lightbox.php");
  }

  if (!check_download_token($user_info['lightbox_image_ids'])) {
    redirect("lightbox.php");
  }

  $image_id_sql = str_replace(" ", ", ", trim($user_info['lightbox_image_ids']));
  $image_ids = array();
  $sql = "SELECT image_id, cat_id, image_media_file, image_download_url
          FROM ".IMAGES_TABLE."
          WHERE image_active = 1 AND image_id IN ($image_id_sql) AND cat_id NOT IN (".get_auth_cat_sql("auth_viewimage", "NOTIN").", ".get_auth_cat_sql("auth_viewcat", "NOTIN").", ".get_auth_cat_sql("auth_download", "NOTIN").")";
  $result = $site_db->query($sql);

  if ($result) {
    include(ROOT_PATH."includes/zip.php");
    $zipfile = new zipfile();
    $file_added = 0;
    while ($image_row = $site_db->fetch_array($result)) {
      $file_path = null;
      $file_name = null;
      if (!empty($image_row['image_download_url'])) {
        if (is_remote_file($image_row['image_download_url']) || is_local_file($image_row['image_download_url'])) {
          $file_path = $image_row['image_download_url'];
          $file_name = basename($image_row['image_download_url']);
        }
      }
      elseif (is_remote($image_row['image_media_file'])) {
        $file_path = $image_row['image_media_file'];
        $file_name = get_basefile($image_row['image_media_file']);
      }
      else {
        $file_path = MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file'];
        $file_name = $image_row['image_media_file'];
      }

      if (!empty($file_path)) {
        @set_time_limit(120);
        $file_path = fix_file_path($file_path);
        if (!$file_data = @file_get_contents($file_path)) {
          continue;
        }
        $zipfile->add_file($file_data, $file_name);
        $file_added = 1;
        unset($file_data);
        $image_ids[] = $image_row['image_id'];
      }
    }

    if ($file_added) {
      if ($user_info['user_level'] != ADMIN) {
        $sql = "UPDATE ".IMAGES_TABLE."
                SET image_downloads = image_downloads + 1
                WHERE image_id IN (".trim(implode(", ", $image_ids)).")";
        $site_db->query($sql);
      }

      $zipfile->send(time().".zip");
      exit;
    }
    else {
      redirect("lightbox.php?empty=1");
    }
  }
}
elseif ($image_id) {
  if (isset($HTTP_GET_VARS['size']) || isset($HTTP_POST_VARS['size'])) {
    $size = (isset($HTTP_GET_VARS['size'])) ? intval($HTTP_GET_VARS['size']) : intval($HTTP_POST_VARS['size']);
  }
  else {
    $size = 0;
  }

  $sql = "SELECT image_id, cat_id, user_id, image_media_file, image_download_url, image_downloads
          FROM ".IMAGES_TABLE."
          WHERE image_id = $image_id AND image_active = 1";
  $image_row = $site_db->query_firstrow($sql);

  if (!$image_row || !check_permission("auth_viewcat", $image_row['cat_id']) || !check_permission("auth_viewimage", $image_row['cat_id'])) {
    redirect($url);
  }
  else {
    if (!check_permission("auth_download", $image_row['cat_id'])) {
      redirect($url);
    }

    if (!check_download_token($image_row['image_id'])) {
      echo "Hotlinking is not allowed";
	  exit;
	  redirect("index.php");
    }
  }

  $remote_url = 0;
  if (!empty($image_row['image_download_url'])) {
    if (is_remote_file($image_row['image_download_url']) || is_local_file($image_row['image_download_url'])) {
      preg_match("/(.+)\.(.+)/", basename($image_row['image_download_url']), $regs);
      $file_name = $regs[1];
      $file_extension = $regs[2];

      $file['file_name'] = $file_name.(($size) ? "_".$size : "").".".$file_extension;
      $file['file_path'] = dirname($image_row['image_download_url'])."/".$file['file_name'];
    }
    else {
      $file['file_path'] = $image_row['image_download_url'];
      $remote_url = 1;
    }
  }
  elseif (is_remote_file($image_row['image_media_file'])) {
    preg_match("/(.+)\.(.+)/", get_basefile($image_row['image_media_file']), $regs);
    $file_name = $regs[1];
    $file_extension = $regs[2];

    $file['file_name'] = $file_name.(($size) ? "_".$size : "").".".$file_extension;
    $file['file_path'] = dirname($image_row['image_media_file'])."/".$file['file_name'];
  }
  else {
    preg_match("/(.+)\.(.+)/", get_basefile($image_row['image_media_file']), $regs);
    $file_name = $regs[1];
    $file_extension = $regs[2];

    $file['file_name'] = $file_name.(($size) ? "_".$size : "").".".$file_extension;
    $file['file_path'] = (is_local_file($image_row['image_media_file'])) ? dirname($image_row['image_media_file'])."/".$file['file_name'] : MEDIA_PATH."/".$image_row['cat_id']."/".$file['file_name'];
  }

  if ($user_info['user_level'] != ADMIN) {
    $sql = "UPDATE ".IMAGES_TABLE."
            SET image_downloads = image_downloads + 1
            WHERE image_id = $image_id";
    $site_db->query($sql);
  }

  if (!empty($file['file_path'])) {
    @set_time_limit(120);
    if ($remote_url) {
      redirect($file['file_path']);
    }

    if ($action == "zip" && !preg_match("/\.zip$/i", $file['file_name']) && function_exists("gzcompress") && function_exists("crc32")) {
      include(ROOT_PATH."includes/zip.php");
      $zipfile = new zipfile();
      $zipfile->add_file(file_get_contents($file['file_path']), $file['file_name']);

      $zipfile->send(get_file_name($file['file_name']).".zip");
    } else {
        send_file($file['file_name'], $file['file_path']);
    }
    exit;
  }
  else {
    echo $lang['download_error']."\n<!-- EMPTY FILE PATH //-->";
    exit;
  }
}
else {
  echo $lang['download_error']."\n<!-- NO ACTION SPECIFIED //-->";
  exit;
}

exit;
?>
