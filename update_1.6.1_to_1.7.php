<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: update_1.6.1_to_1.7.php                              *
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

error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);
define('ROOT_PATH', './');

if (isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action'])) {
  $action = (isset($HTTP_GET_VARS['action'])) ? trim($HTTP_GET_VARS['action']) : trim($HTTP_POST_VARS['action']);
}
else {
  $action = "intro";
}

@include(ROOT_PATH.'config.php');

if (!defined("4IMAGES_ACTIVE")) {
  header("Location: install.php");
  exit;
}
if ($action == "finish") {
  header("Location: index.php");
  exit;
}

include(ROOT_PATH.'includes/constants.php');
include(ROOT_PATH.'includes/search_utils.php');
include(ROOT_PATH.'includes/db_'.$db_servertype.'.php');
$site_db = new Db($db_host, $db_user, $db_password, $db_name);

function next_step($action, $msg) {
  global $PHP_SELF;
?>
<br />
<table width="400" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableseparator">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td class="tablerow2" align="center">
            <?php echo "Next step: <a href=\"".$PHP_SELF."?action=".$action."\"><b>".$msg."</b></a>\n"; ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />
<?php
}
?>
<html>
  <head>
  <meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
  <link rel="stylesheet" href="admin/cpstyle.css">
  <title>4images Update 1.6.1 to 1.7</title>
</head>
<body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF">
<table width="400" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td>
<p align="center"><span class="headline">4images Update 1.6.1 to 1.7</span></p><br /><br />
<?php
if ($action == "intro") {
  next_step("updatesettings", "Update Settings");
}

if ($action == "updatesettings") {
  $ok = 1;
  $sql_edit = array();
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('auto_thumbnail_resize_type', '1')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('user_edit_image', '0')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('user_delete_image', '0')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('user_edit_comments', '0')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('user_delete_comments', '0')";

  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "Settings updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating settings</b></span><br />\n";
  }
  next_step("finish", "Update complete");
}
?>
<br />
    </td>
  </tr>
</table>
<p align="center">Powered by <b>4images</b> <?php echo SCRIPT_VERSION ?><br />
  Copyright &copy; 2002 <a href="http://www.4homepages.de" target="_blank">4homepages.de</a>
</p>
</body>
</html>
