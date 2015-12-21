<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: install.php                                          *
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
if (function_exists("set_magic_quotes_runtime"))
{
  @set_magic_quotes_runtime(0);
}

if (!function_exists("date_default_timezone_set")) {
  function date_default_timezone_set($timezone) {
    return true;
  }
}

define('ROOT_PATH', './');

function addslashes_array($array) {
  foreach ($array as $key => $val) {
    $array[$key] = (is_array($val)) ? addslashes_array($val) : addslashes($val);
  }
  return $array;
}

function get_timezone_by_offset($offset) {
    $timezones = array(
        '-12'   => 'Pacific/Kwajalein',
        '-11'   => 'Pacific/Samoa',
        '-10'   => 'Pacific/Honolulu',
        '-9.5'  => 'Pacific/Marquesas',
        '-9'    => 'America/Juneau',
        '-8'    => 'America/Los_Angeles',
        '-7'    => 'America/Denver',
        '-6'    => 'America/Mexico_City',
        '-5'    => 'America/New_York',
        '-4.5'  => 'America/Caracas',
        '-4'    => 'America/Caracas',
        '-3.5'  => 'America/St_Johns',
        '-3'    => 'America/Argentina/Buenos_Aires',
        '-2'    => 'Atlantic/South_Georgia',
        '-1'    => 'Atlantic/Azores',
        '0'     => 'Europe/London',
        '1'     => 'Europe/Berlin',
        '2'     => 'Europe/Helsinki',
        '3'     => 'Europe/Moscow',
        '3.5'   => 'Asia/Tehran',
        '4'     => 'Asia/Baku',
        '4.5'   => 'Asia/Kabul',
        '5'     => 'Asia/Karachi',
        '5.5'   => 'Asia/Calcutta',
        '5.75'  => 'Asia/Kathmandu',
        '6'     => 'Asia/Colombo',
        '6.5'   => 'Indian/Cocos',
        '7'     => 'Asia/Bangkok',
        '8'     => 'Asia/Singapore',
        '8.75'  => 'Australia/Eucla',
        '9'     => 'Asia/Tokyo',
        '9.5'   => 'Australia/Darwin',
        '10'    => 'Pacific/Guam',
        '10.5'  => 'Australia/Lord_Howe',
        '11'    => 'Asia/Magadan',
        '11.5'  => 'Pacific/Norfolk',
        '12'    => 'Asia/Kamchatka',
        '12.75' => 'Pacific/Chatham',
        '13'    => 'Pacific/Enderbury',
        '14'    => 'Pacific/Kiritimati',
    );

    if ( isset($timezones[$offset]) ) {
        return $timezones[$offset];
    }
    return $timezones['1'];
}

if (!isset($HTTP_GET_VARS)) {
  $HTTP_GET_VARS    = &$_GET;
  $HTTP_POST_VARS   = &$_POST;
  $HTTP_COOKIE_VARS = &$_COOKIE;
  $HTTP_POST_FILES  = &$_FILES;
  $HTTP_SERVER_VARS = &$_SERVER;
  $HTTP_ENV_VARS    = &$_ENV;
}

if (get_magic_quotes_gpc() == 0) {
  $HTTP_GET_VARS = addslashes_array($HTTP_GET_VARS);
  $HTTP_POST_VARS = addslashes_array($HTTP_POST_VARS);
  $HTTP_COOKIE_VARS = addslashes_array($HTTP_COOKIE_VARS);
}

if (@file_exists(ROOT_PATH."config.php")) {
  @include(ROOT_PATH.'config.php');
} else {
    date_default_timezone_set('CET');
}
if (defined("4IMAGES_ACTIVE")) {
  header("Location: index.php");
  exit;
}

if (isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action'])) {
  $action = (isset($HTTP_GET_VARS['action'])) ? stripslashes(trim($HTTP_GET_VARS['action'])) : stripslashes(trim($HTTP_POST_VARS['action']));
}
else {
  $action = "";
}

if ($action == "") {
  $action = "intro";
}

$lang_select = "";
$folderlist = array();
$handle = opendir(ROOT_PATH."lang");
while ($folder = @readdir($handle)) {
  if (@is_dir(ROOT_PATH."lang/$folder") && $folder != "." && $folder != "..") {
    $folderlist[] = $folder;
  }
}
sort($folderlist);
for($i = 0; $i < sizeof($folderlist); $i++) {
  $lang_select .= " <b><a href=\"install.php?install_lang=".$folderlist[$i]."\">".$folderlist[$i]."</a></b> \n";
}
closedir($handle);

if (isset($HTTP_GET_VARS['install_lang']) || isset($HTTP_POST_VARS['install_lang'])) {
  $install_lang = (isset($HTTP_GET_VARS['install_lang'])) ? trim($HTTP_GET_VARS['install_lang']) : trim($HTTP_POST_VARS['install_lang']);
}

if (empty($install_lang) || !in_array($install_lang, $folderlist)) {
  $install_lang = "deutsch";
}

$lang = array();
include(ROOT_PATH.'lang/'.$install_lang.'/install.php');

$db_servertype   = (isset($HTTP_POST_VARS['db_servertype'])) ? trim($HTTP_POST_VARS['db_servertype']) : "mysql";
$db_host         = (isset($HTTP_POST_VARS['db_host'])) ? trim($HTTP_POST_VARS['db_host']) : "";
$db_name         = (isset($HTTP_POST_VARS['db_name'])) ? trim($HTTP_POST_VARS['db_name']) : "";
$db_user         = (isset($HTTP_POST_VARS['db_user'])) ? trim($HTTP_POST_VARS['db_user']) : "";
$db_password     = (isset($HTTP_POST_VARS['db_password'])) ? trim($HTTP_POST_VARS['db_password']) : "";
$table_prefix    = (isset($HTTP_POST_VARS['table_prefix'])) ? trim($HTTP_POST_VARS['table_prefix']) : "4images_";

$admin_user      = (isset($HTTP_POST_VARS['admin_user'])) ? trim($HTTP_POST_VARS['admin_user']) : "";
$admin_password  = (isset($HTTP_POST_VARS['admin_password'])) ? trim($HTTP_POST_VARS['admin_password']) : "";
$admin_password2 = (isset($HTTP_POST_VARS['admin_password2'])) ? trim($HTTP_POST_VARS['admin_password2']) : "";
$selected_timezone = (isset($HTTP_POST_VARS['timezone_select'])) ? trim($HTTP_POST_VARS['timezone_select']) : '1';
$selected_timezone = get_timezone_by_offset($selected_timezone);

include(ROOT_PATH.'includes/constants.php');

if ($action == "downloadconfig") {
  header("Content-Type: text/x-delimtext; name=\"config.php\"");
  header("Content-disposition: attachment; filename=config.php");
  $config_file = stripslashes(trim($HTTP_POST_VARS['config_file']));
  echo $config_file;
  exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type">
  <link rel="stylesheet" href="admin/cpstyle.css">
  <title>4images Installer</title>
</head>
<body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF">
<form action="install.php" name="form" method="post">
  <table cellpadding="0" cellspacing="0" border="0" width="500" align="center">
    <tr>
      <td class="tableborder"><img src="admin/images/logo_installer.gif" width="500" height="45"><br />
<?php
if ($action == "startinstall") {
  $error = array();
  if ($db_servertype == "") {
    $error['db_servertype'] = 1;
  }
  if ($db_host == "") {
    $error['db_host'] = 1;
  }
  if ($db_name == "") {
    $error['db_name'] = 1;
  }
  if ($db_user == "") {
    $error['db_user'] = 1;
  }
  /*
  if ($db_password == "")[
    $error['db_password'] = 1;
  }
  */

  if ($admin_user == "") {
    $error['admin_user'] = 1;
  }
  if ($admin_password != $admin_password2 || $admin_password == "" || $admin_password2 == "") {
    $error['admin_password'] = 1;
    $error['admin_password2'] = 1;
  }

  if (!empty($error)) {
    foreach ($error as $key => $val) {
      $lang[$key] = sprintf("<span class=\"marktext\">%s *</span>", $lang[$key]);
    }
    $action = "intro";
  }
  else {
?>
        <table cellpadding="3" cellspacing="1" border="0" width="100%">
          <tr class="tablerow2">
            <td>
<?php
    $error_log = array();
    $error_msg = "";
    include(ROOT_PATH.'includes/db_'.strtolower($db_servertype).'.php');
    $site_db = new Db($db_host, $db_user, $db_password, $db_name);
    if (!$site_db->connection) {
      $error_log[] = "No connection to database!";
    }
    //$site_db->no_error = 1;

    include(ROOT_PATH.'includes/db_utils.php');

    $db_file = ROOT_PATH.DATABASE_DIR."/default/".strtolower($db_servertype)."_default.sql";
    $cont = @fread(@fopen($db_file, 'r'), @filesize($db_file));
    if (empty($cont)) {
      $error_log[] = "Could not load: ".$db_file;
    }
    if (empty($error_log)) {
      $cont = preg_replace('/4images_/', $table_prefix, $cont);
      $pieces = split_sql_dump($cont);
      for ($i = 0; $i < sizeof($pieces); $i++) {
        $sql = trim($pieces[$i]);
        if (!empty($sql) and $sql[0] != "#") {
          if (!$site_db->query($sql)) {
            $error_log[] = $sql;
          }
        }
      }

      include(ROOT_PATH.'includes/security_utils.php');

      $admin_pass_hashed = salted_hash($admin_password);
      $current_time = time();
      $sql = "UPDATE ".$table_prefix."users
              SET user_name = '$admin_user', user_password = '$admin_pass_hashed', user_joindate = $current_time, user_lastaction = $current_time, user_lastvisit = $current_time
              WHERE user_name = 'admin'";
      if (!$site_db->query($sql)) {
        $error_log[] = $sql;
      }

      $sql = "UPDATE ".$table_prefix."settings
              SET setting_value = '$install_lang'
              WHERE setting_name = 'language_dir'";
      if (!$site_db->query($sql)) {
        $error_log[] = $sql;
      }
    }

    if (empty($error_log)) {
      $config_file = '<?php'."\n";
      $config_file .= '/**************************************************************************'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' *    4images - A Web Based Image Gallery Management System               *'."\n";
      $config_file .= ' *    ----------------------------------------------------------------    *'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' *             File: config.php                                           *'."\n";
      $config_file .= ' *        Copyright: (C) 2002-2015 4homepages.de                          *'."\n";
      $config_file .= ' *            Email: jan@4homepages.de                                    *'."\n";
      $config_file .= ' *              Web: http://www.4homepages.de                             *'."\n";
      $config_file .= ' *    Scriptversion: 1.7.13                                                *'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' *    Never released without support from: Nicky (http://www.nicky.net)   *'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' **************************************************************************'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' *    Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-       *'."\n";
      $config_file .= ' *    bedingungen (Lizenz.txt) für weitere Informationen.                 *'."\n";
      $config_file .= ' *    ---------------------------------------------------------------     *'."\n";
      $config_file .= ' *    This script is NOT freeware! Please read the Copyright Notice       *'."\n";
      $config_file .= ' *    (Licence.txt) for further information.                              *'."\n";
      $config_file .= ' *                                                                        *'."\n";
      $config_file .= ' *************************************************************************/'."\n\n";
      $config_file .= 'date_default_timezone_set("' . $selected_timezone . '");'."\n\n";
      $config_file .= '$db_servertype = \''.$db_servertype.'\';'."\n";
      $config_file .= '$db_host = \''.$db_host.'\';'."\n";
      $config_file .= '$db_name = \''.$db_name.'\';'."\n";
      $config_file .= '$db_user = \''.$db_user.'\';'."\n";
      $config_file .= '$db_password = \''.$db_password.'\';'."\n\n";
      $config_file .= '$table_prefix = \''.$table_prefix . '\';'."\n\n";
      $config_file .= 'define("4IMAGES_ACTIVE", 1);'."\n\n";
      $config_file .= '?'.'>';

      @umask(0111);
      $fp = @fopen('./config.php', 'w');
      $ok = @fwrite($fp, $config_file);
      if (!$ok) {
        $cant_write_config = 1;
      }
      @fclose($fp);
      $msg = "<br /><blockquote><b>".$lang['install_success']."</b>";
      if (!isset($cant_write_config)) {
        $msg .= "<br /><br />".$lang['install_success_login'];
      }
      else {
        $msg .= "<br /><br />".$lang['config_download_desc'];
        $msg .= '<input type="hidden" name="config_file" value=\''.$config_file.'\' />';
        $msg .= '<input type="hidden" name="action" value="downloadconfig" />';
      }
      $msg .= "</blockquote>";
    }
    else {
      $msg = $lang['database_error'];
      $error_msg .= "<ol>";
      foreach ($error_log as $val) {
        $error_msg .= sprintf("<li>%s</li>", $val);
      }
      $error_msg .= "</ol>";
    }
?>
              <p class="rowtitle"><?php echo $msg.$error_msg ?></p>
            </td>
          </tr>
<?php
    if (isset($cant_write_config)) {
?>
          <tr class="tablefooter">
            <td align="center"><input type="submit" value="   <?php echo $lang['config_download'] ?>   " class="button" name="submit"></td>
          </tr>
<?php
    }
?>
        </table>
<?php
  }
}
if ($action == "intro") {
  $db_servertype_select = "<select name=\"db_servertype\">";
  $db_types = array("mysql");
  $handle = opendir(ROOT_PATH."includes");
  while ($file = @readdir($handle)) {
    if (preg_match("/db_(.*)\.php/", $file, $regs)) {
      if ($regs[1] != "mysql" && file_exists(ROOT_PATH."data/database/default/".$regs[1]."_default.sql")) {
        $db_types[] = $regs[1];
      }
    }
  }
  foreach ($db_types as $db_type) {
    $db_servertype_select .= "<option value=\"".$db_type."\"".(($db_servertype == $db_type) ? " selected=\"selected\"" : "").">".$db_type."</option>";
  }
	$db_servertype_select .= "</select>";
  if (!empty($error)) {
    $lang['start_install_desc'] = $lang['start_install_desc'].sprintf("<br /><br /><span class=\"marktext\">%s *</span>", $lang['lostfield_error']);
  }
?>
        <table cellpadding="3" cellspacing="1" border="0" width="100%">
          <tr class="tablerow">
            <td colspan="2" align="right"><?php echo $lang_select; ?></td>
          </tr>
          <tr class="tablerow2">
            <td colspan="2"><br /><blockquote><?php echo $lang['start_install_desc'] ?></blockquote></td>
          </tr>
          <tr class="tableheader">
            <td colspan="2"><b><span class="tableheader"><?php echo $lang['db'] ?></span></b></td>
          </tr>
          <tr class="tablerow">
            <td>
              <p class="rowtitle"><?php echo $lang['db_servertype'] ?></p>
            </td>
            <td>
              <p><?php echo $db_servertype_select; ?></p>
            </td>
          </tr>
          <tr class="tablerow2">
            <td>
              <p class="rowtitle"><?php echo $lang['db_host'] ?></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="db_host" value="<?php echo stripslashes($db_host); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow">
            <td>
              <p class="rowtitle"><?php echo $lang['db_name'] ?></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="db_name" value="<?php echo stripslashes($db_name); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow2">
            <td>
              <p class="rowtitle"><?php echo $lang['db_user'] ?></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="db_user" value="<?php echo stripslashes($db_user); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow">
            <td>
              <p class="rowtitle"><?php echo $lang['db_password'] ?></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="db_password" value="<?php echo stripslashes($db_password); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow2">
            <td>
              <p class="rowtitle"><?php echo $lang['table_prefix'] ?></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="table_prefix" value="<?php echo stripslashes($table_prefix); ?>">
              </p>
            </td>
          </tr>
          <tr class="tableseparator">
            <td colspan="2"><b><span class="tableseparator"><?php echo $lang['admin'] ?></span></b></td>
          </tr>
          <tr class="tablerow">
            <td>
              <p><b><?php echo $lang['timezone_select'] ?></b></p>
            </td>
            <td>
              <p>
                <select name="timezone_select" style="width: 188px;">
                    <option value="-12">Baker Island Time (UTC-12)</option>
                    <option value="-11">Niue Time, Samoa Standard Time (UTC-11)</option>
                    <option value="-10">Hawaii-Aleutian Standard Time, Cook Island Time (UTC-10)</option>
                    <option value="-9.5">Marquesas Islands Time (UTC-9:30)</option>
                    <option value="-9">Alaska Standard Time, Gambier Island Time (UTC-9)</option>
                    <option value="-8">Pacific Standard Time (UTC-8)</option>
                    <option value="-7">Mountain Standard Time (UTC-7)</option>
                    <option value="-6">Central Standard Time (UTC-6)</option>
                    <option value="-5">Eastern Standard Time (UTC-5)</option>
                    <option value="-4.5">Venezuelan Standard Time (UTC-4:30)</option>
                    <option value="-4">Atlantic Standard Time (UTC-4)</option>
                    <option value="-3.5">Newfoundland Standard Time (UTC-3:30)</option>
                    <option value="-3">Amazon Standard Time, Central Greenland Time (UTC-3)</option>
                    <option value="-2">Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time (UTC-2)</option>
                    <option value="-1">Azores Standard Time, Cape Verde Time, Eastern Greenland Time (UTC-1)</option>
                    <option value="0">Western European Time, Greenwich Mean Time (UTC)</option>
                    <option value="1" selected="selected">Central European Time, West African Time (UTC+1)</option>
                    <option value="2">Eastern European Time, Central African Time (UTC+2)</option>
                    <option value="3">Moscow Standard Time, Eastern African Time (UTC+3)</option>
                    <option value="3.5">Iran Standard Time (UTC+3:30)</option>
                    <option value="4">Gulf Standard Time, Samara Standard Time (UTC+4)</option>
                    <option value="4.5">Afghanistan Time (UTC+4:30)</option>
                    <option value="5">Pakistan Standard Time, Yekaterinburg Standard Time (UTC+5)</option>
                    <option value="5.5">Indian Standard Time, Sri Lanka Time (UTC+5:30)</option>
                    <option value="5.75">Nepal Time (UTC+5:45)</option>
                    <option value="6">Bangladesh Time, Bhutan Time, Novosibirsk Standard Time (UTC+6)</option>
                    <option value="6.5">Cocos Islands Time, Myanmar Time (UTC+6:30)</option>
                    <option value="7">Indochina Time, Krasnoyarsk Standard Time (UTC+7)</option>
                    <option value="8">Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time (UTC+8)</option>
                    <option value="8.75">Southeastern Western Australia Standard Time (UTC+8:45)</option>
                    <option value="9">Japan Standard Time, Korea Standard Time, Chita Standard Time (UTC+9)</option>
                    <option value="9.5"> Australian Central Standard Time (UTC+9:30)</option>
                    <option value="10">Australian Eastern Standard Time, Vladivostok Standard Time (UTC+10)</option>
                    <option value="10.5">Lord Howe Standard Time (UTC+10:30)</option>
                    <option value="11">Solomon Island Time, Magadan Standard Time (UTC+11)</option>
                    <option value="11.5">Norfolk Island Time (UTC+11:30)</option>
                    <option value="12">New Zealand Time, Fiji Time, Kamchatka Standard Time (UTC+12)</option>
                    <option value="12.75">Chatham Islands Time (UTC+12:45)</option>
                    <option value="13">Tonga Time, Phoenix Islands Time (UTC+13)</option>
                    <option value="14">Line Island Time (UTC+14)</option>
                </select>
              </p>
            </td>
          </tr>
          <tr class="tablerow2">
            <td>
              <p><b><?php echo $lang['admin_user'] ?></b></p>
            </td>
            <td>
              <p>
                <input type="text" size="30" name="admin_user" value="<?php echo stripslashes($admin_user); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow">
            <td>
              <p class="rowtitle"><b><?php echo $lang['admin_password'] ?></b></p>
            </td>
            <td>
              <p>
                <input type="password" size="30" name="admin_password" value="<?php echo stripslashes($admin_password); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablerow2">
            <td>
              <p class="rowtitle"><b><?php echo $lang['admin_password2'] ?></b></p>
            </td>
            <td>
              <p>
                <input type="password" size="30" name="admin_password2" value="<?php echo stripslashes($admin_password2); ?>">
              </p>
            </td>
          </tr>
          <tr class="tablefooter">
            <td colspan="2" align="center">
              <input type="hidden" name="action" value="startinstall">
              <input type="hidden" name="install_lang" value="<?php echo $install_lang; ?>">
              <input type="submit" value="   <?php echo $lang['start_install'] ?>   " class="button" name="submit">
            </td>
          </tr>
        </table>
<?php
}
?>
      </td>
    </tr>
  </table>
</form>
<p align="center"> Powered by <b>4images</b> <?php echo SCRIPT_VERSION ?><br />
  Copyright &copy; 2002-<?php echo date('Y'); ?> <a href="http://www.4homepages.de" target="_blank">4homepages.de</a>
</p>
</body>
</html>