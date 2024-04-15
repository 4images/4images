<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: update_1.0_to_1.5.php                                *
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

error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);
define('ROOT_PATH', './');

if (isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action'])) {
  $action = (isset($HTTP_GET_VARS['action'])) ? trim($HTTP_GET_VARS['action']) : trim($HTTP_POST_VARS['action']);
}
else {
  $action = "intro";
}

if (@file_exists(ROOT_PATH."config.php")) {
  @include(ROOT_PATH.'config.php');
}
if (!defined("4IMAGES_ACTIVE")) {
  header("Location: install.php");
  exit;
}
if ($action == "finish") {
  header("Location: index.php");
  exit;
}
include(ROOT_PATH.'includes/constants.php');
define('MEDIA_PATH', ROOT_PATH.MEDIA_DIR);
define('THUMB_PATH', ROOT_PATH.THUMB_DIR);

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
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
  <link rel="stylesheet" href="admin/cpstyle.css">
  <title>4images Update 1.0 to 1.5</title>
</head>
<body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF">
<table width="400" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td>
<p align="center"><span class="headline">4images Update 1.0 to 1.5</span></p><br /><br />
<?php
if ($action == "intro") {
  next_step("updatesettings", "Update Settings");
}

if ($action == "updatesettings") {
  $ok = 1;
  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD INDEX (cat_order)";
  $sql_edit[] = "ALTER TABLE ".COMMENTS_TABLE."
                 ADD INDEX (comment_date)";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 ADD INDEX (image_date)";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 ADD INDEX (image_active)";
  /*
  $sql_edit[] = "ALTER TABLE ".GROUP_ACCESS_TABLE."
                 DROP INDEX `forum_id`";
  $sql_edit[] = "ALTER TABLE ".GROUP_ACCESS_TABLE."
                 ADD INDEX (cat_id)";
  */
  $sql_edit[] = "ALTER TABLE ".SESSIONVARS_TABLE."
                 TYPE=MyISAM";
  $sql_edit[] = "ALTER TABLE ".SESSIONVARS_TABLE."
                 MODIFY sessionvars_value text";

  $sql_edit[] = "DROP TABLE IF EXISTS ".SETTINGS_TABLE;
  $sql_edit[] = "CREATE TABLE ".SETTINGS_TABLE." (
                 setting_name varchar(255) NOT NULL,
                 setting_value mediumtext NOT NULL,
                 PRIMARY KEY (setting_name)
                 ) TYPE=MyISAM";

  $result = $site_db->query("SELECT setting_name, setting_value
                             FROM ".SETTINGS_TABLE);

  while ($row = $site_db->fetch_array($result)) {
    $new_config[$row['setting_name']] = $row['setting_value'];
    if ($row['setting_name'] == "smtp_host") {
      $new_config['smtp_username'] = "";
      $new_config['smtp_password'] = "";
    }
    if ($row['setting_name'] == "max_media_size") {
      $new_config['upload_notify'] = "0";
      $new_config['upload_emails'] = "";
    }
  }

  unset($new_config['register_comments']);
  unset($new_config['register_download']);
  unset($new_config['cat_bg_color']);
  unset($new_config['imagerow_bg_color1']);
  unset($new_config['imagerow_bg_color2']);

  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  foreach ($new_config as $key => $val) {
    $sql = "INSERT INTO ".SETTINGS_TABLE."
            (setting_name, setting_value)
            VALUES
            ('$key', '".addslashes($val)."')";
    if (!$site_db->query($sql)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "Table <b>".SETTINGS_TABLE."</b> updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating table <b>".SETTINGS_TABLE."</b></span><br />\n";
  }
  next_step("updateimagestable", "Update Images Table");
}

if ($action == "updateimagestable") {
  $ok = 1;
  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 MODIFY image_active tinyint(1) NOT NULL default '1'";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 MODIFY image_allow_comments tinyint(1) NOT NULL default '1'";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 MODIFY image_media_file varchar(255) NOT NULL default ''";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 MODIFY image_thumb_file varchar(255) NOT NULL default ''";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE."
                 ADD image_download_url varchar(255) NOT NULL default '' AFTER image_thumb_file";

  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "Table <b>".IMAGES_TABLE."</b> updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating table <b>".IMAGES_TABLE."</b></span><br />\n";
  }
  next_step("updatecategoriestable", "Update Categories Table");
}

if ($action == "updatecategoriestable") {
  $ok = 1;
  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD cat_order mediumint(8) unsigned NOT NULL default '1'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_viewcat tinyint(2) NOT NULL default '0'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_viewimage tinyint(2) NOT NULL default '0'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_download tinyint(2) NOT NULL default '2'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_upload tinyint(2) NOT NULL default '2'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_directupload tinyint(2) NOT NULL default '9'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_vote tinyint(2) NOT NULL default '0'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_sendpostcard tinyint(2) NOT NULL default '0'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_readcomment tinyint(2) NOT NULL default '0'";
  $sql_edit[] = "ALTER TABLE ".CATEGORIES_TABLE."
                 ADD auth_postcomment tinyint(2) NOT NULL default '2'";

  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "Table <b>".CATEGORIES_TABLE."</b> updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating table <b>".CATEGORIES_TABLE."</b></span><br />\n";
  }
  next_step("updateuserstable", "Update Users Table");
}

if ($action == "updateuserstable") {
  $ok = 1;

  $sql = "INSERT INTO ".USERS_TABLE."
          (user_id, user_level, user_name, user_password, user_email, user_showemail, user_invisible, user_joindate, user_activationkey, user_lastaction, user_location, user_lastvisit, user_comments, user_homepage, user_icq)
          VALUES
          (-1,-1,'Guest','0493984f537120be0b8d96bc9b69cdd2','',0,0,0,'',0,'',0,0,'','')";
  if (!$site_db->query($sql)) {
    $ok = 0;
  }

  $sql = "UPDATE ".COMMENTS_TABLE."
          SET user_id = ".GUEST."
          WHERE user_id = 0";
  if (!$site_db->query($sql)) {
    $ok = 0;
  }

  $sql = "UPDATE ".IMAGES_TABLE."
          SET user_id = ".GUEST."
          WHERE user_id = 0";
  if (!$site_db->query($sql)) {
    $ok = 0;
  }

  $sql = "UPDATE ".SESSIONS_TABLE."
          SET session_user_id = ".GUEST."
          WHERE session_user_id = 0";
  if (!$site_db->query($sql)) {
    $ok = 0;
  }

  if ($ok) {
    echo "Table <b>".USERS_TABLE."</b> updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating table <b>".USERS_TABLE."</b></span><br />\n";
  }
  next_step("updatelighboxestable", "Update Lightboxes Table");
}

if ($action == "updatelighboxestable") {
  $ok = 1;

  $sql = "ALTER TABLE ".LIGHTBOXES_TABLE."
          MODIFY lightbox_id varchar(32) NOT NULL default ''";
  if (!$site_db->query($sql)) {
    $ok = 0;
  }

  $result = $site_db->query("SELECT lightbox_id
                             FROM ".LIGHTBOXES_TABLE);
  while ($row = $site_db->fetch_array($result)) {
    $lid = md5(uniqid(microtime()));
    $sql = "UPDATE ".LIGHTBOXES_TABLE."
            SET lightbox_id = '$lid'
            WHERE lightbox_id = '".$row['lightbox_id']."'";
    if (!$site_db->query($sql)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "Table <b>".LIGHTBOXES_TABLE."</b> updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating table <b>".LIGHTBOXES_TABLE."</b></span><br />\n";
  }
  next_step("addnewtables", "Add New Tables");
}

if ($action == "addnewtables") {
  $ok = 1;
  $sql_add = array();

  //$sql_add[] = "DROP TABLE IF EXISTS ".$table_prefix."groupaccess";
  $sql_add[] = "CREATE TABLE ".$table_prefix."groupaccess (
                group_id mediumint(8) NOT NULL default '0',
                cat_id mediumint(8) unsigned NOT NULL default '0',
                auth_viewcat tinyint(1) NOT NULL default '0',
                auth_viewimage tinyint(1) NOT NULL default '0',
                auth_download tinyint(1) NOT NULL default '0',
                auth_upload tinyint(1) NOT NULL default '0',
                auth_directupload tinyint(1) NOT NULL default '0',
                auth_vote tinyint(1) NOT NULL default '0',
                auth_sendpostcard tinyint(1) NOT NULL default '0',
                auth_readcomment tinyint(1) NOT NULL default '0',
                auth_postcomment tinyint(1) NOT NULL default '0',
                KEY group_id (group_id),
                KEY forum_id (cat_id)
                ) TYPE=MyISAM";

  //$sql_add[] = "DROP TABLE IF EXISTS ".$table_prefix."groupmatch";
  $sql_add[] = "CREATE TABLE ".$table_prefix."groupmatch (
                group_id mediumint(8) NOT NULL default '0',
                user_id mediumint(8) NOT NULL default '0',
                groupmatch_startdate int(11) unsigned NOT NULL default '0',
                groupmatch_enddate int(11) unsigned NOT NULL default '0',
                KEY group_id (group_id),
                KEY user_id (user_id)
                ) TYPE=MyISAM";

  //$sql_add[] = "DROP TABLE IF EXISTS ".$table_prefix."groups";
  $sql_add[] = "CREATE TABLE ".$table_prefix."groups (
                group_id mediumint(8) NOT NULL auto_increment,
                group_name varchar(100) NOT NULL default '',
                PRIMARY KEY  (group_id)
                ) TYPE=MyISAM";

  //$sql_add[] = "DROP TABLE IF EXISTS ".$table_prefix."images_temp";
  $sql_add[] = "CREATE TABLE ".$table_prefix."images_temp (
                image_id mediumint(8) NOT NULL auto_increment,
                cat_id mediumint(8) NOT NULL default '0',
                user_id mediumint(8) NOT NULL default '0',
                image_name varchar(255) NOT NULL default '',
                image_description text NOT NULL,
                image_keywords text NOT NULL,
                image_date int(11) unsigned NOT NULL default '0',
                image_media_file varchar(255) NOT NULL default '',
                image_thumb_file varchar(255) NOT NULL default '',
                image_download_url varchar(255) NOT NULL default '',
                PRIMARY KEY  (image_id),
                KEY cat_id (cat_id),
                KEY user_id (user_id)
                ) TYPE=MyISAM";

  //$sql_add[] = "DROP TABLE IF EXISTS ".$table_prefix."postcards";
  $sql_add[] = "CREATE TABLE ".$table_prefix."postcards (
                postcard_id varchar(32) NOT NULL default '',
                image_id mediumint(8) NOT NULL default '0',
                postcard_date int(11) unsigned NOT NULL default '0',
                postcard_bg_color varchar(100) NOT NULL default '',
                postcard_border_color varchar(100) NOT NULL default '',
                postcard_font_color varchar(100) NOT NULL default '',
                postcard_font_face varchar(100) NOT NULL default '',
                postcard_sender_name varchar(255) NOT NULL default '',
                postcard_sender_email varchar(255) NOT NULL default '',
                postcard_recipient_name varchar(255) NOT NULL default '',
                postcard_recipient_email varchar(255) NOT NULL default '',
                postcard_headline varchar(255) NOT NULL default '',
                postcard_message text NOT NULL,
                PRIMARY KEY  (postcard_id)
                ) TYPE=MyISAM";
  foreach ($sql_add as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  if ($ok) {
    echo "New Tables added succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while adding new tables></span><br />\n";
  }
  next_step("finish", "Update complete");
}
?>
<br />
    </td>
  </tr>
</table>
<p align="center">Powered by <b>4images</b> <?php echo SCRIPT_VERSION ?>
  Copyright &copy; 2002-2023 <a href="http://www.4homepages.de" target="_blank">4homepages.de</a>
</p>
</body>
</html>
