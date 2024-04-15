<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: logout.php                                           *
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
define('ROOT_PATH', './');
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');

if (($user_info['user_level'] != GUEST && $user_info['user_level'] != USER_AWAITING)) {
    $site_sess->logout($user_info['user_id']);
}

if (!preg_match("/index\.php/", $url) && !preg_match("/lightbox\.php/", $url) && !preg_match("/login\.php/", $url) && !preg_match("/register\.php/", $url) && !preg_match("/member\.php/", $url)) {
  redirect($url);
}
else {
  redirect("index.php");
}
?>
