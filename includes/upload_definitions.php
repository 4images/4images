<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: upload_definitions.php                               *
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

$mime_type_match['jpg'] = array("image/jpg", "image/jpeg", "image/pjpeg");
$mime_type_match['jpeg'] = array("image/jpg", "image/jpeg", "image/pjpeg");

$mime_type_match['gif'] = array("image/gif");

$mime_type_match['png'] = array("image/png", "image/x-png");

$mime_type_match['tif'] = array("image/tiff", "application/octet-stream");
$mime_type_match['tiff'] = array("image/tiff", "application/octet-stream");

$mime_type_match['bmp'] = array("image/bmp", "image/x-ms-bmp");

$mime_type_match['aif'] = array("audio/x-aiff");

$mime_type_match['aiff'] = array("audio/x-aiff");

$mime_type_match['au'] = array("audio/basic");

$mime_type_match['snd'] = array("audio/basic");

$mime_type_match['mid'] = array("audio/x-midi", "audio/mid", "audio/midi");

$mime_type_match['mp3'] = array("audio/mpeg", "audio/x-mpeg", "audio/mp3", "audio/mpg");

$mime_type_match['ra'] = array("audio/x-pn-realaudio");

$mime_type_match['ram'] = array("audio/x-pn-realaudio");

$mime_type_match['rm'] = array("audio/vnd.rn-realmedia", "application/vnd.rn-realmedia", "video/vnd.rn-realvideo", "application/vnd");

$mime_type_match['rpm'] = array("audio/x-pn-realaudio-plugin");

$mime_type_match['wav'] = array("audio/x-wav");

$mime_type_match['avi'] = array("video/x-msvideo", "video/avi");

$mime_type_match['mpg'] = array("video/mpeg");
$mime_type_match['mpeg'] = array("video/mpeg");
$mime_type_match['mpe'] = array("video/mpeg");

$mime_type_match['mov'] = array("video/quicktime");
$mime_type_match['qt'] = array("video/quicktime");

$mime_type_match['swf'] = array("application/x-shockwave-flash");

$mime_type_match['psd'] = array("application/octet-stream");
$mime_type_match['fla'] = array("application/octet-stream");

$mime_type_match['gz'] = array("application/gzip", "application/x-gzip-compressed");
$mime_type_match['rar'] = array("application/x-rar-compressed");
$mime_type_match['tar'] = array("application/x-tar");
$mime_type_match['gtar'] = array("application/x-gtar");
$mime_type_match['zip'] = array("application/zip", "application/x-zip-compressed");
$mime_type_match['sit'] = array("application/x-stuffit");

$mime_type_match['pdf'] = array("application/pdf", "application/x-pdf");

$mime_type_match['ai'] = array("application/postscript");
$mime_type_match['eps'] = array("application/postscript");
$mime_type_match['ps'] = array("application/postscript");

$mime_type_match['txt'] = array("text/plain", "text/richtext", "text/rtf", "text/html");
$mime_type_match['rtf'] = array("text/plain", "text/richtext", "text/rtf");
$mime_type_match['rtx'] = array("text/plain", "text/richtext", "text/rtf");

$mime_type_match['doc'] = array("application/msword");
$mime_type_match['xls'] = array("application/vnd", "application/x-msexcel");
$mime_type_match['ppt'] = array("application/vnd");

$mime_type_match['csv'] = array("text/comma-separated-values");
$mime_type_match['js'] = array("text/javascript");
$mime_type_match['css'] = array("text/css");
?>
