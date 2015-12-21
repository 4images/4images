<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: image_utils.php                                      *
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
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

function init_convert_options() {
  global $config, $lang;

  $convert_options = array(
    "convert_error" => 0,
    "convert_tool" => $config['convert_tool'],
    "convert_path" => preg_replace("/\/$/", "", $config['convert_tool_path'])
  );
  switch($config['convert_tool']) {
  case "im":
    $exec = check_executable("convert");
    $convert_options['convert_path'] = preg_replace("/\/?(".check_executable("mogrify")."|$exec)+$/i", '', $convert_options['convert_path']);
    $convert_options['convert_path'] = $convert_options['convert_path'] . '/' . $exec;
    if (!@is_executable($convert_options['convert_path'])) {
      $convert_options['convert_error'] = "<b class=\"marktext\">".$lang['im_error']."</b><br />\n".$lang['check_module_settings'];
    }
    break;
  case "gd":
    $convert_options['convert_gd2'] = false;

    if (defined('CONVERT_IS_GD2')) {
      $convert_options['convert_gd2'] = CONVERT_IS_GD2 == 0 ? false : true;
    } elseif (function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled")) {
      $convert_options['convert_gd2'] = true;
    }

    if (!function_exists("imagetypes")) {
      $convert_options['convert_error'] = (defined("IN_CP")) ? "<b class=\"marktext\">".$lang['gd_error']."</b><br />\n".$lang['check_module_settings'] : 1;
    }
    break;
  case "netpbm":
    if (!@is_executable($convert_options['convert_path']."/".check_executable("pnmscale"))) {
      $convert_options['convert_error'] = (defined("IN_CP")) ? "<b class=\"marktext\">".$lang['netpbm_error']."</b><br />\n".$lang['check_module_settings'] : 1;
    }
    break;
  default:
    $convert_options['convert_error'] = (defined("IN_CP")) ? "<b class=\"marktext\">".$lang['no_convert_module']."</b><br />\n".$lang['check_module_settings'] : 1;
  }
  return $convert_options;
}

function resize_image_gd($src, $dest, $quality, $width, $height, $image_info) {
  global $convert_options;

  $types = array(1 => "gif", 2 => "jpeg", 3 => "png");
  if ($convert_options['convert_gd2']) {
    $thumb = imagecreatetruecolor($width, $height);
  }
  else {
    $thumb = imagecreate($width, $height);
  }
  $image_create_handle = "imagecreatefrom".$types[$image_info[2]];
  if ($image = $image_create_handle($src)) {
    if ($convert_options['convert_gd2']) {
      imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
    }
    else {
      imagecopyresized($thumb, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
    }

    if ($image_info[2] == 3) {
      $quality = 9;
    }

    $image_handle = "image".$types[$image_info[2]];
    $image_handle($thumb, $dest, $quality);
    imagedestroy($image);
    imagedestroy($thumb);
  }
  return (file_exists($dest)) ? 1 : 0;
}

function resize_image_im($src, $dest, $quality, $width, $height, $image_info) {
  global $convert_options;

  $command = $convert_options['convert_path']." -quality ".$quality." -antialias -geometry $width"."x"."$height -profile '*' -unsharp 0.5x1 \"$src\"  \"$dest\"";
  system($command);
  return (file_exists($dest)) ? 1 : 0;
}

function resize_image_netpbm($src, $dest, $quality, $width, $height, $image_info) {
  global $convert_options;

  $convert_path = $convert_options['convert_path'];
  $types = array(1 => "gif", 2 => "jpeg", 3 => "png");
  $target = ($width > $height) ? $width : $height;
  $command = $convert_path."/".check_executable($types[$image_info[2]]."topnm")." ".$src." | ".$convert_path."/".check_executable("pnmscale")." --quiet -xysize ".$target." ".$target." | ";
  if ($image_info[2] == 1) {
    $command .= $convert_path."/".check_executable("ppmquant")." 256 | " . $convert_path."/".check_executable("ppmtogif")." > ".$dest;
  }
  elseif ($image_info[2] == 3) {
    $command .= $convert_path."/".check_executable("pnmtopng")." > ".$dest;
  }
  else {
    $jpeg_exec = (file_exists($convert_path."/".check_executable("pnmtojpeg"))) ? check_executable("pnmtojpeg") : check_executable("ppmtojpeg");
    $command .= $convert_path."/".$jpeg_exec." --quality=".$quality." > ".$dest;
  }
  system($command);
  return (file_exists($dest)) ? 1 : 0;
}

function get_width_height($dimension, $width, $height, $resize_type = 1, $max_height = false) {
  $max_width = $dimension;
  $max_height = ($max_height === false) ? $max_width : $max_height;
  if ($resize_type == 2)
  {
    $new_width = $max_width;
    $new_height = floor(($max_width/$width) * $height);
  }
  elseif ($resize_type == 3)
  {
    $new_width = floor(($max_height/$height) * $width);
    $new_height = $max_height;
  }
  else
  {
    $new_width = $width;
    $new_height = $height;
    if ($width > $max_width || $height > $max_height)
    {
      $scale = min($max_width/$width, $max_height/$height);
      $new_width = floor($scale*$width);
      $new_height = floor($scale*$height);
    }
  }
  return array("width" => $new_width, "height" => $new_height);
}

function create_thumbnail($src, $dest, $quality, $dimension, $resize_type) {
  global $convert_options;

  if (file_exists($dest)) {
    @unlink($dest);
  }
  $image_info = (defined("IN_CP")) ? getimagesize($src) : @getimagesize($src);
  if (!$image_info) {
    return false;
  }
  $width_height = get_width_height($dimension, $image_info[0], $image_info[1], $resize_type);
  $resize_handle = "resize_image_".$convert_options['convert_tool'];
  if ($resize_handle($src, $dest, $quality, $width_height['width'], $width_height['height'], $image_info)) {
    @chmod($dest, CHMOD_FILES);
    return true;
  }
  else {
    return false;
  }
}

function resize_image($file, $quality, $dimension, $resize_type = 1, $height = false) {
  global $convert_options;
  $image_info = (defined("IN_CP")) ? getimagesize($file) : @getimagesize($file);
  if (!$image_info) {
    return false;
  }
  $file_bak = $file.".bak";
  if (!rename($file, $file_bak)) {
    return false;
  }
  $width_height = get_width_height($dimension, $image_info[0], $image_info[1], $resize_type, $height);
  $resize_handle = "resize_image_".$convert_options['convert_tool'];
  if ($resize_handle($file_bak, $file, $quality, $width_height['width'], $width_height['height'], $image_info)) {
    @chmod($file, CHMOD_FILES);
    @unlink($file_bak);
    return true;
  }
  else {
    rename($file_bak, $file);
    return false;
  }
}
?>
