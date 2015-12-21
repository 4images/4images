<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: templates.php                                        *
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

if ($action == "") {
  $action = "modifytemplates";
}

if (isset($HTTP_GET_VARS['template_folder']) || isset($HTTP_POST_VARS['template_folder'])) {
  $template_folder = (isset($HTTP_GET_VARS['template_folder'])) ? trim($HTTP_GET_VARS['template_folder']) : trim($HTTP_POST_VARS['template_folder']);
  $template_folder = str_replace('.', '', $template_folder);
}
else {
  $template_folder = "";
}

if (isset($HTTP_GET_VARS['template_file_name']) || isset($HTTP_POST_VARS['template_file_name'])) {
  $template_file_name = (isset($HTTP_GET_VARS['template_file_name'])) ? trim($HTTP_GET_VARS['template_file_name']) : trim($HTTP_POST_VARS['template_file_name']);
  $template_file_name = (strpos($template_file_name, 'media/') !== false ? 'media/' : '') . basename($template_file_name);
}
else {
  $template_file_name = "";
}

function show_template_select_row($title, $template_folder = "") {
  global $template_file_name, $lang;
  if ($template_folder == "") {
    $template_folder = "default";
  }
  echo "<tr class=\"".get_row_bg()."\">\n<td width=\"30%\"><p class=\"rowtitle\">".$title."</p></td>\n<td width=\"70%\">\n";
  $file_list = array();
  $handle = @opendir(ROOT_PATH.TEMPLATE_DIR."/".$template_folder);
  while ($file = @readdir($handle)) {
    if (@is_file(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/".$file) && $file != "." && $file != "..") {
      $file_list[] = $file;
    }
  }
  @closedir($handle);
  if (isset($file_list) && is_array($file_list)) sort($file_list);
  $handle = @opendir(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/media");
  while ($file = @readdir($handle)) {
    if (@is_file(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/media/".$file) && $file != "." && $file != "..") {
      $file_list[] = "media/".$file;
    }
  }
  @closedir($handle);
  if (empty($file_list) || !is_array($file_list)) {
    echo $lang['no_template'];
    return false;
  }
  else {
    echo "<table border=\"0\">\n<tr><form method=\"post\" action=\"./templates.php\">\n<td>\n";
    echo "<input type=\"hidden\" name=\"action\" value=\"loadtemplate\">\n";
    echo "<input type=\"hidden\" name=\"template_folder\" value=\"$template_folder\">\n";
    echo "<select name=template_file_name>\n";
    for($i = 0; $i < sizeof($file_list); $i++) {
      echo "<option value=\"".$file_list[$i]."\"";
      if ($template_file_name == $file_list[$i]) {
        echo " selected=\"selected\"";
      }
      echo ">".$file_list[$i]."</option>\n";
    }
    echo "</select>\n</td>\n<td>\n<input type=\"submit\" value=\"".$lang['edit_templates']."\" class=\"button\">\n";
    echo "</td>\n</form>\n</tr>\n</table>\n";
  }
  echo "</td>\n</tr>\n";
}

function show_theme_select_row($title) {
  global $template_file_name, $template_folder, $themes_found, $lang;
  echo "<tr class=\"".get_row_bg()."\" width=\"30%\">\n<td><p class=\"rowtitle\">$title</p></td>\n<td width=\"70%\">\n";
  $folder_list = array();
  $handle = @opendir(ROOT_PATH.TEMPLATE_DIR);
  while ($folder = @readdir($handle)){
    if (@is_dir(ROOT_PATH.TEMPLATE_DIR."/".$folder) && $folder != "." && $folder != "..") {
      $folder_list[] = $folder;
    }
  }
  $themes_found = 1;
  if (empty($folder_list)) {
    echo $lang['no_themes'];
    $themes_found = 0;
  }
  else {
    sort($folder_list);
    echo "<table border=\"0\">\n<tr><form method=\"post\" action=\"./templates.php\">\n<td>\n";
    echo "<select name=\"template_folder\">\n";
    for($i = 0; $i < sizeof($folder_list); $i++) {
      echo "<option value=\"".$folder_list[$i]."\"";
      if ($template_folder == $folder_list[$i]) {
        echo " selected=\"selected\"";
      }
      echo ">".$folder_list[$i]."</option>\n";
    }
    echo "</select>\n</td>\n<td>\n<input type=\"submit\" value=\"".$lang['load_theme']."\" class=\"button\">\n";
    echo "</td>\n</form>\n</tr>\n</table>\n";
  }
  echo "</td>\n</tr>\n";
}

show_admin_header();

if ($action == "loadtemplate") {
  $content = implode("", file(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/".$template_file_name));
  $action = "modifytemplates";
}

if ($action == "savetemplate") {
  if (isset($HTTP_POST_VARS['content'])) {
    $content = trim($HTTP_POST_VARS['content']);
  }
  else {
    $content = "";
  }

	if ($template_file_name != "" && $content != "" && file_exists(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/".$template_file_name)) {
    $content = un_htmlspecialchars($content);
    $content = stripslashes($content);
    $fp = @fopen(ROOT_PATH.TEMPLATE_DIR."/".$template_folder."/".$template_file_name, "w+");
    if (@fwrite($fp, $content)) {
      $msg = $lang['template_edit_success'];
    }
    else {
      $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['template_edit_error']);
    }
  }
  $action = "modifytemplates";
}

if ($action == "modifytemplates") {
  if ($msg != "") {
    printf("<p><b>%s</b></p>\n", $msg);
  }
  show_table_header($lang['edit_templates'], 2);
  show_theme_select_row($lang['choose_theme']);

  if ($themes_found) {
    show_template_select_row($lang['choose_template'], $template_folder);
  }
  show_table_footer();

  if (!isset($content)) {
    $content = "";
  }
  show_form_header("templates.php", "savetemplate");
  show_table_header($lang['edit_template'].": ".$template_file_name, 1);
  echo "<tr class=\"tablerow\"><td>";
  ?>
  <style>
  .template_textarea {
    width: 100%;
  }
  </style>
  <?php
  echo "<textarea name=\"content\" cols=\"60\" rows=\"30\" wrap=\"off\" class=\"template_textarea\">";
  echo htmlspecialchars($content);
  echo "</textarea>";
  show_hidden_input("template_file_name", $template_file_name);
  show_hidden_input("template_folder", $template_folder);
  show_form_footer($lang['save_changes'], "", 1);
}

show_admin_footer();
?>
