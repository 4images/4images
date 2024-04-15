<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: update_RC-1_to_RC-2.php                              *
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

$sql_add = array();
$sql_edit = array();
//$sql[] = "DROP TABLE IF EXISTS ".$table_prefix."wordlist";
$sql_add[$table_prefix.'wordlist'] = "CREATE TABLE ".$table_prefix."wordlist (
                                      word_text varchar(50) binary NOT NULL default '',
                                      word_id mediumint(8) unsigned NOT NULL auto_increment,
                                      word_common tinyint(1) unsigned NOT NULL default '0',
                                      PRIMARY KEY  (word_text),
                                      KEY word_id (word_id)
                                      ) TYPE=MyISAM";
//$sql[] = "DROP TABLE IF EXISTS ".$table_prefix."wordmatch";
$sql_add[$table_prefix.'wordmatch'] = "CREATE TABLE ".$table_prefix."wordmatch (
                                       image_id mediumint(8) unsigned NOT NULL default '0',
                                       word_id mediumint(8) unsigned NOT NULL default '0',
                                       name_match tinyint(1) NOT NULL default '0',
                                       desc_match tinyint(1) NOT NULL default '0',
                                       keys_match tinyint(1) NOT NULL default '0',
                                       KEY word_id (word_id)
                                       ) TYPE=MyISAM";
$sql_edit[] = "ALTER TABLE ".SESSION_VARS_TABLE."
               TYPE=MyISAM";
$sql_edit[] = "ALTER TABLE ".SESSION_VARS_TABLE."
               MODIFY sessionvars_value text";

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
  <title>4images Update RC-1 to RC-2</title>
</head>
<body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF">
<table width="400" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td>
<p align="center"><span class="headline">4images Update RC-1 to RC-2</span></p><br /><br />
<?php

if ($action == "intro") {
  next_step("createcategories", "Create category folders");
}

if ($action == "createcategories") {
  $result = $site_db->query("SELECT cat_id, cat_name
                             FROM ".CATEGORIES_TABLE."
                             ORDER BY cat_name ASC");
  if (!is_writable(MEDIA_PATH)) {
    umask(0111);
    @chmod(MEDIA_PATH, 0777);
  }
  if (!is_writable(THUMB_PATH)) {
    umask(0111);
    @chmod(THUMB_PATH, 0777);
  }
  while ($row = $site_db->fetch_array($result)){
    echo "Create folder for ".$row['cat_name'].": .......... \n";
    flush();
    $oldumask = umask(0);
    echo (@mkdir(MEDIA_PATH."/".$row['cat_id'], 0777) && @mkdir(THUMB_PATH."/".$row['cat_id'], 0777)) ? "<b>OK</b><br />\n" : "<b><span class=\"marktext\">Failed</span></b><br />\n";
    umask($oldumask);
  }

  next_step("copyimages", "Copy Images");
}

if ($action == "copyimages") {
  $sql = "SELECT MAX(image_id) as max
          FROM ".IMAGES_TABLE;
  $result = $site_db->query_firstrow($sql);

  $max = $result['max'];
  $batchsize = 25; // Process this many posts per loop
  $batchcount = 0;
  $counter = 0;

  while ($counter < $max) {
    $batchstart = $counter + 1;
    $batchend = $counter + $batchsize;
    if ($batchend > $max) {
      $batchend = $max;
    }
    $batchcount++;

    echo "Copy images to category folders ($batchstart to $batchend): ";
    flush();

    $sql = "SELECT cat_id, image_media_file, image_thumb_file
            FROM ".IMAGES_TABLE."
            WHERE image_id
            BETWEEN $batchstart AND $batchend";
    $result = $site_db->query($sql);
    $ok = 0;
    if ($row = $site_db->fetch_array($result)) {
      do {
        if (!empty($row['image_media_file'])) {
          $ok = (copy(MEDIA_PATH."/".$row['image_media_file'], MEDIA_PATH."/".$row['cat_id']."/".$row['image_media_file'])) ? 1 : 0;
          if ($ok) {
            unlink(MEDIA_PATH."/".$row['image_media_file']);
            chmod(MEDIA_PATH."/".$row['cat_id']."/".$row['image_media_file'], 0777);
          }
        }

        if (!empty($row['image_thumb_file'])) {
          $ok = (copy(THUMB_PATH."/".$row['image_thumb_file'], THUMB_PATH."/".$row['cat_id']."/".$row['image_thumb_file'])) ? 1 : 0;
          if ($ok) {
            unlink(THUMB_PATH."/".$row['image_thumb_file']);
            chmod(THUMB_PATH."/".$row['cat_id']."/".$row['image_thumb_file'], 0777);
          }
        }

        echo ".";
        flush();
      }
      while ($row = $site_db->fetch_array($result) );
    }
    $site_db->free_result($result);
    $counter += $batchsize;
    echo ($ok) ? " <b>OK</b><br />\n" : "<b><span class=\"marktext\">Failed</span></b><br />\n";
  }
  next_step("addtables", "Add new Tables to database");
}

if ($action == "addtables") {
  foreach ($sql_add as $key => $val) {
    if ($site_db->query($val)) {
      echo "Table <b>$key</b> created!<br />\n";
    }
    else {
      echo "<span class=\"marktext\">An error occured while creating table <b>$key</b></span><br />\n";
    }
  }
  $ok = 1;
  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }
  echo ($ok) ? "Tables changed successfully!<br />\n" : "<span class=\"marktext\">An error occured while editing tables<br />\n";
  next_step("addsearchindex", "Add search Index");
}

if ($action == "addsearchindex") {
  $sql = "SELECT MAX(image_id) as max
          FROM ".IMAGES_TABLE;
  $result = $site_db->query_firstrow($sql);

  $max = $result['max'];
  $batchsize = 50; // Process this many posts per loop
  $batchcount = 0;
  $counter = 0;

  while ($counter < $max) {
    $batchstart = $counter + 1;
    $batchend = $counter + $batchsize;
    if ($batchend > $max) {
      $batchend = $max;
    }
    $batchcount++;

    echo "Fulltext Indexing ($batchstart to $batchend): ";
    flush();

    $sql = "SELECT *
            FROM ".IMAGES_TABLE."
            WHERE image_id
            BETWEEN $batchstart AND $batchend";
    $result = $site_db->query($sql);

    if ($row = $site_db->fetch_array($result)) {
      do {
		$search_words = array();
        foreach ($search_match_fields as $image_column => $match_column) {
          if (isset($row[$image_column])) {
            $search_words[$image_column] = $row[$image_column];
          }
        }
        add_searchwords($row['image_id'], $search_words);
        echo ".";
        flush();
      }
      while ($row = $site_db->fetch_array($result));
    }
    $site_db->free_result($result);
    $counter += $batchsize;
    print " <b>OK</b><br />\n";
  }
  next_step("finish", "Update complete");
}
?>
<br />
    </td>
  </tr>
</table>
<p align="center">Powered by <b>4images</b> <?php echo SCRIPT_VERSION ?><br />
  Copyright &copy; 2002-2023 <a href="http://www.4homepages.de" target="_blank">4homepages.de</a>
</p>
</body>
</html>
