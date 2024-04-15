<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: update_1.5_to_1.6.php                                *
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

function next_rebuildsearchindex_step($batchstart, $batchsize) {
  global $PHP_SELF;
  $page = $PHP_SELF."?action=buildsearchindex&batchstart=".$batchstart."&batchsize=".$batchsize;
?>
<script language="javascript">
myvar = "";
timeout = 15;
function dorefresh() {
  window.status="Redirecting"+myvar;
  myvar = myvar + " .";
  timerID = setTimeout("dorefresh();", 100);
  if (timeout > 0) {
    timeout -= 1;
  }
  else {
    clearTimeout(timerID);
    window.status="";
    window.location="<?php echo $page; ?>";
  }
}
dorefresh();
</script>
<br />
<table border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="tableseparator">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td class="tablerow2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $page; ?>"><b>Click here to continue</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
  <title>4images Update 1.5 to 1.6</title>
</head>
<body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF">
<table width="400" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td>
<p align="center"><span class="headline">4images Update 1.5 to 1.6</span></p><br /><br />
<?php
if ($action == "intro") {
  next_step("updatetables", "Update Database Tables");
}

if ($action == "updatetables") {
  $ok = 1;
  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE." ADD INDEX (image_date)";
  $sql_edit[] = "ALTER TABLE ".IMAGES_TABLE." ADD INDEX (image_active)";
  $sql_edit[] = "ALTER TABLE ".USERS_TABLE." ADD user_allowemails TINYINT(1) DEFAULT '1' NOT NULL AFTER user_showemail";
  $sql_edit[] = "UPDATE ".USERS_TABLE." SET user_allowemails = 0 WHERE user_id = -1";
  $sql_edit[] = "ALTER TABLE ".USERS_TABLE." ADD INDEX (user_name)";
  $sql_edit[] = "UPDATE ".SETTINGS_TABLE." SET setting_name = 'convert_tool_path' WHERE setting_name = 'im_convert_path'";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('account_activation', '1')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('auto_thumbnail', '0')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('auto_thumbnail_dimension', '100')";
  $sql_edit[] = "INSERT INTO ".SETTINGS_TABLE." (setting_name, setting_value) VALUES ('auto_thumbnail_quality', '75')";
  $sql_edit[] = "ALTER TABLE ".GROUPS_TABLE." ADD group_type TINYINT(2) DEFAULT '1' NOT NULL";

  foreach ($sql_edit as $val) {
    if (!$site_db->query($val)) {
      $ok = 0;
    }
  }

  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".GROUP_ACCESS_TABLE." DROP INDEX forum_id";
  $sql_edit[] = "ALTER TABLE ".GROUP_ACCESS_TABLE." ADD INDEX (cat_id)";

  $result = $site_db->query("SHOW KEYS FROM ".GROUP_ACCESS_TABLE);
  $index = array();
  while ($row = $site_db->fetch_array($result)) {
    $index[$row['Key_name']] = 1;
  }

  if (isset($index['forum_id'])) {
    foreach ($sql_edit as $val) {
      if (!$site_db->query($val)) {
        $ok = 0;
      }
    }
  }

  $sql_edit = array();
  $sql_edit[] = "ALTER TABLE ".LIGHTBOXES_TABLE." DROP PRIMARY KEY";
  $sql_edit[] = "ALTER TABLE ".LIGHTBOXES_TABLE." ADD INDEX (lightbox_id)";

  $result = $site_db->query("SHOW KEYS FROM ".LIGHTBOXES_TABLE);
  $index = array();
  while ($row = $site_db->fetch_array($result)) {
    $index[$row['Key_name']] = 1;
  }

  if (isset($index['PRIMARY']) && !isset($index['lightbox_id '])) {
    foreach ($sql_edit as $val) {
      if (!$site_db->query($val)) {
        $ok = 0;
      }
    }
  }

  if ($ok) {
    echo "Tables updated succesfully!<br />\n";
  }
  else {
    echo "<span class=\"marktext\">An error occured while updating tables</b></span><br />\n";
  }
  next_step("starteditsearchindex", "Update Search Index Tables");
}

if ($action == "starteditsearchindex") {
  echo "<p>Now we have to update the search index tables. For that we have to empty the search index first. Be sure to have backups of your existing data before continuing. ";
  echo "Later on we will rebuild the search index...</p>";
  next_step("emptyindex", "Empty Search Index");
}

if ($action == "emptyindex") {
  $site_db->query("DELETE FROM ".WORDMATCH_TABLE);
  $site_db->query("DELETE FROM ".WORDLIST_TABLE);
  $site_db->query("ALTER TABLE ".WORDMATCH_TABLE." DROP INDEX word_id, ADD UNIQUE image_word_id (image_id, word_id)");
  $site_db->query("ALTER TABLE ".WORDLIST_TABLE." DROP word_common");
  echo "<p><b>Index successfully emptied</b><br />";
  echo "<b>Now we will rebuild the search index...</b></p>";
  next_step("buildsearchindex", "Rebuild Search Index");
}

if ($action == "buildsearchindex") {
  if (isset($HTTP_GET_VARS['batchstart']) || isset($HTTP_POST_VARS['batchstart'])) {
    $batchstart = (isset($HTTP_GET_VARS['batchstart'])) ? intval($HTTP_GET_VARS['batchstart']) : intval($HTTP_POST_VARS['batchstart']);
  }
  else {
    $batchstart = 0;
  }

  if (isset($HTTP_GET_VARS['batchsize']) || isset($HTTP_POST_VARS['batchsize'])) {
    $batchsize = (isset($HTTP_GET_VARS['batchsize'])) ? intval($HTTP_GET_VARS['batchsize']) : intval($HTTP_POST_VARS['batchsize']);
    if (!$batchsize) {
      $batchsize = 100;
    }
  }
  else {
    $batchsize = 100;
  }

  $sql = "SELECT MAX(image_id) as max
          FROM ".IMAGES_TABLE;
  $row = $site_db->query_firstrow($sql);
  $max = (isset($row['max'])) ? $row['max'] : 0;

  $batchend = $batchstart + $batchsize - 1;
  if ($batchend >= $max) {
    $batchend = $max;
  }

  echo "<b>Fulltext Indexing ($batchstart to $batchend):</b><p>";

  $sql = "SELECT *
          FROM ".IMAGES_TABLE."
          WHERE image_id
          BETWEEN $batchstart AND $batchend";
  $result = $site_db->query($sql);

  while ($row = $site_db->fetch_array($result)) {
    echo "Processing image <b>".$row['image_name']."</b>, ID ".$row['image_id']." ...";
    flush();
    @set_time_limit(1200);
    $search_words = array();
    foreach ($search_match_fields as $image_column => $match_column) {
      if (isset($row[$image_column])) {
        $search_words[$image_column] = $row[$image_column];
      }
    }
    add_searchwords($row['image_id'], $search_words);
    echo " <b>OK</b><br />\n";
  }
  if ($batchend < $max) {
    next_rebuildsearchindex_step($batchend + 1, $batchsize);
  }
  else {
    echo "<p><b>Search index rebuilt!</b><p>\n";
    next_step("finish", "Update complete");
  }
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
