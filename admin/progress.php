<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: progress.php                                         *
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

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

?>
<html>
<head>
<title><?php echo $lang['upload_progress']; ?><</title>
<link rel="stylesheet" href="./cpstyle.css">
<script language="javascript1.2">
<!--
var start_pad = 2;
var end_pad = 2;
var sel = 0;
var mod = 3;
var timer;

var image_on = new Image();
image_on.src = 'images/arrow.gif';
var image_off = new Image();
image_off.src = 'images/spacer.gif';

function animate() {
  for (var i = start_pad; i < document.images.length - end_pad; i++) {
    if (i % mod == sel) {
      document.images[i].src = image_on.src;
    }
    else {
      document.images[i].src = image_off.src;
    }
  }
  sel++;
  if (sel == mod) {
    sel = 0;
  }
  start_animation();
}

function start_animation() {
  timer=window.setTimeout("animate();",250);
}

function stop_animation() {
  window.clearTimeout(timer);
}

// -->
</script>
</head>

<body onload="start_animation()">
<center>
<span class="title"><?php echo $lang['upload_progress']; ?></span>
<p>
<?php echo $lang['upload_progress_desc']; ?>
<p>
<table border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td> <img src="images/folder_big.gif"> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/spacer.gif" width="8" height="11" /> </td>
  <td> <img src="images/folder_big.gif" /> </td>
 </tr>
</table>
</center>
</body>