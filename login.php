<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: login.php                                            *
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

$main_template = 0;

$nozip = 1;
define('ROOT_PATH', './');
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');

$error = 0;
if ($user_info['user_level'] != GUEST || empty($HTTP_POST_VARS['user_name']) || empty($HTTP_POST_VARS['user_password'])) {
  if (!preg_match("/index\.php/", $url) && !preg_match("/login\.php/", $url) && !preg_match("/register\.php/", $url) && !preg_match("/member\.php/", $url)) {
    redirect($url);
  }
  else {
    redirect("index.php");
  }
}
else {
  $user_name = trim($HTTP_POST_VARS['user_name']);
  $user_password = trim($HTTP_POST_VARS['user_password']);
  $auto_login = (isset($HTTP_POST_VARS['auto_login']) && $HTTP_POST_VARS['auto_login'] == 1) ? 1 : 0;

  if ($site_sess->login($user_name, $user_password, $auto_login)) {
    if (!preg_match("/index\.php/", $url) && !preg_match("/login\.php/", $url) && !preg_match("/register\.php/", $url) && !preg_match("/member\.php/", $url)) {
      redirect($url);
    }
    else {
      redirect("index.php");
    }
  }
  else {
    $error = $lang['invalid_login'];
  }
}
if ($error) {
  $main_template = "error";
  include(ROOT_PATH.'includes/page_header.php');
  show_error_page($error);
}
?>
