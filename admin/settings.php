<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: settings.php                                         *
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

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

if ( $action == "") {
  $action = "modifysettings";
}

function show_template_dir_select($setting_name, $setting_value) {
  echo "<select name=\"setting_item[".$setting_name."]\">";
  $handle = opendir(ROOT_PATH.TEMPLATE_DIR);
  while ($folder = @readdir($handle)) {
    if (@is_dir(ROOT_PATH.TEMPLATE_DIR."/".$folder) && $folder != "." && $folder != "..") {
      $folderlist[] = $folder;
    }
  }
  sort($folderlist);
  for($i = 0; $i < sizeof($folderlist); $i++) {
    echo "<option value=\"".$folderlist[$i]."\"";
    if ($setting_value == $folderlist[$i]) {
      echo " selected=\"selected\"";
    }
    echo ">".$folderlist[$i]."</option>\n";
  }
  closedir($handle);
  echo "</select>";
}

function show_cat_order_select($setting_name, $setting_value) {
  global $cat_order_optionlist;
  echo "<select name=\"setting_item[".$setting_name."]\">";
  foreach ( $cat_order_optionlist as $k => $v ) {
    echo "<option value=\"".$k."\"";
    if ($setting_value == $k) {
      echo " selected=\"selected\"";
    }
    echo ">".$v."</option>\n";  
  }
  echo "</select>\n";
}

function show_cat_sort_select($setting_name, $setting_value) {
  global $cat_sort_optionlist;
  echo "<select name=\"setting_item[".$setting_name."]\">";
  foreach ( $cat_sort_optionlist as $k => $v ) {
    echo "<option value=\"".$k."\"";
    if ($setting_value == $k) {
      echo " selected=\"selected\"";
    }
    echo ">".$v."</option>\n";  
  }
  echo "</select>\n";
}


function show_language_dir_select($setting_name, $setting_value) {
  echo "<select name=\"setting_item[".$setting_name."]\">";
  $handle = opendir(ROOT_PATH."lang");
  while ($folder = @readdir($handle)) {
    if (@is_dir(ROOT_PATH."lang/$folder") && $folder != "." && $folder != "..") {
      $folderlist[] = $folder;
    }
  }
  sort($folderlist);
  for($i = 0; $i < sizeof($folderlist); $i++) {
    echo "<option value=\"".$folderlist[$i]."\"";
    if ($setting_value == $folderlist[$i]) {
      echo " selected=\"selected\"";
    }
    echo ">".$folderlist[$i]."</option>\n";
  }
  closedir($handle);
  echo "</select>\n";
}

function show_convert_tool_select($setting_name, $setting_value) {
  global $convert_tool_optionlist;
  echo "<select name=\"setting_item[".$setting_name."]\">";
  foreach ($convert_tool_optionlist as $key => $val) {
    echo "<option value=\"$key\"";
    if ($setting_value == $key) {
      echo " selected=\"selected\"";
    }
    echo ">".$val."</option>";
  }
  echo "</select>";
}

function show_image_order_select($setting_name, $setting_value) {
  global $image_order_optionlist;
  echo "<select name=\"setting_item[".$setting_name."]\">";
  foreach ($image_order_optionlist as $key => $val) {
    echo "<option value=\"$key\"";
    if ($setting_value == $key) {
      echo " selected=\"selected\"";
    }
    echo ">".$val."</option>";
  }
  echo "</select>";
}

function show_image_sort_select($setting_name, $setting_value) {
  global $image_sort_optionlist;
  echo "<select name=\"setting_item[".$setting_name."]\">";
  foreach ($image_sort_optionlist as $key => $val) {
    echo "<option value=\"$key\"";
    if ($setting_value == $key) {
      echo " selected=\"selected\"";
    }
    echo ">".$val."</option>";
  }
  echo "</select>";
}

function show_upload_mode_options($setting_name, $setting_value) {
  global $upload_mode_optionlist;
  foreach ($upload_mode_optionlist as $key => $val) {
    echo "<input type=\"radio\" name=\"setting_item[".$setting_name."]\" value=\"$key\"";
    if ($setting_value == $key) {
      echo " checked=\"checked\"";
    }
    echo "> ".$val."<br />";
  }
}

function show_auto_thumbnail_resize_type_options($setting_name, $setting_value) {
  global $auto_thumbnail_resize_type_optionlist;
  foreach ($auto_thumbnail_resize_type_optionlist as $key => $val) {
    echo "<input type=\"radio\" name=\"setting_item[".$setting_name."]\" value=\"$key\"";
    if ($setting_value == $key) {
      echo " checked=\"checked\"";
    }
    echo "> ".$val."<br />";
  }
}

function show_account_activation_options($setting_name, $setting_value) {
  global $account_activation_optionlist;
  foreach ($account_activation_optionlist as $key => $val) {
    echo "<input type=\"radio\" name=\"setting_item[".$setting_name."]\" value=\"$key\"";
    if ($setting_value == $key) {
      echo " checked=\"checked\"";
    }
    echo "> ".$val."<br />";
  }
}

function show_setting_row($setting_name, $value_option = "", $htmlspecialchars = 0) {
  global $config, $setting;
	$config_value = $config[$setting_name];
  $config_value = ($htmlspecialchars) ? htmlspecialchars($config[$setting_name]) : $config[$setting_name];
  $setting[$setting_name] = replace_url($setting[$setting_name]);
  if ($value_option == "") {
    show_input_row($setting[$setting_name], "setting_item[".$setting_name."]", $config_value);
  }
  elseif ($value_option == "textarea") {
    show_textarea_row($setting[$setting_name], "setting_item[".$setting_name."]", $config_value, "", 6);
  }
  elseif ($value_option == "radio") {
    show_radio_row($setting[$setting_name], "setting_item[".$setting_name."]", $config_value);
  }
  else {
    echo "<tr class=\"".get_row_bg()."\">\n<td valign=\"top\"><p class=\"rowtitle\">".$setting[$setting_name]."</p></td>\n";
    echo "<td><p>";
    $value_option($setting_name, $config_value);
    echo "</p></td>\n</tr>\n";
  }
}

// end of functions

show_admin_header();

if ($action == "updatesettings") {
  $setting_item = $HTTP_POST_VARS['setting_item'];
  foreach ($setting_item as $key => $val) {
    $val = trim($val);
    
    $sql = "SELECT * FROM ".SETTINGS_TABLE." WHERE setting_name = '$key'";
    $res = $site_db->get_numrows($site_db->query($sql));

    if ( !$res > 0 ) {
        $sql = "INSERT INTO ".SETTINGS_TABLE." (setting_value, setting_name)
                VALUES ('$val', '$key');";
    } else {
        $sql = "UPDATE ".SETTINGS_TABLE."
                SET setting_value = '$val'
                WHERE setting_name = '$key'";    
    }
    
    $res = $site_db->query($sql);
  }

  if ($HTTP_POST_VARS['setting_item']['language_dir'] != $config['language_dir']) {
    include(ROOT_PATH.'lang/'.$HTTP_POST_VARS['setting_item']['language_dir'].'/admin.php');
?>
    <script language="javascript">
    <!--
    top.head.location = '<?php echo $site_sess->url(ROOT_PATH."admin/index.php?action=head"); ?>';
    top.nav.location = '<?php echo $site_sess->url(ROOT_PATH."admin/index.php?action=nav"); ?>';
    top.main.location = '<?php echo $site_sess->url(ROOT_PATH."admin/settings.php?action=modifysettings&settings_msg=".urlencode($lang['save_settings_success'])); ?>';
    // -->
    </script>
<?php
    printf("<b>%s</b><p>", $lang['save_settings_success']);
    show_admin_footer();
  }
  else {
    $msg = sprintf("<b>%s</b><p>", $lang['save_settings_success']);
    $action = "modifysettings";
  }
}

if ($action == "modifysettings") {
  if (isset($HTTP_GET_VARS['settings_msg'])) {
    printf("<b>%s</b><p>", trim($HTTP_GET_VARS['settings_msg']));
  }
  elseif (!empty($msg)) {
    echo $msg;
  }

  $sql = "SELECT setting_name, setting_value
          FROM ".SETTINGS_TABLE;
  $result = $site_db->query($sql);
  $config = array();
  while ($row = $site_db->fetch_array($result)) {
    $config[$row['setting_name']] = $row['setting_value'];
  }

  show_form_header("settings.php", "updatesettings");
  show_table_header($lang['nav_general_settings'], 2);
  echo "<tr class=\"tablerow\"><td colspan=\"2\">";
  echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\"><tr valign=\"top\">\n";
  $numgroups = sizeof($setting_group);
  $percolumn = ceil($numgroups / 2);
  $settingcounter = 0;
  foreach ($setting_group as $key => $val) {
    $settingcounter++;
    echo "<td>";
    show_text_link($val,"#setting_group_".$key);
    echo "<br /></td>\n";
    if ($settingcounter == 4) {
      echo "</tr><tr>";
      $settingcounter = 0;
    }
  }

  echo "</tr></table>\n";
  echo "</td></tr>";
  show_table_separator($setting_group[1], 2, "setting_group_1");
  show_setting_row("site_name", "", 1);
  show_setting_row("site_email");
  show_setting_row("use_smtp", "radio");
  show_setting_row("smtp_host");
  show_setting_row("smtp_username");
  show_setting_row("smtp_password");
  show_setting_row("template_dir", "show_template_dir_select");
  show_setting_row("language_dir", "show_language_dir_select");
  show_setting_row("date_format");
  show_setting_row("time_format");
  show_setting_row("convert_tool", "show_convert_tool_select");
  show_setting_row("convert_tool_path");
  show_setting_row("gz_compress", "radio");
  show_setting_row("gz_compress_level");

  show_table_separator($setting_group[2], 2, "setting_group_2");
  show_setting_row("cat_order", "show_cat_order_select");
  show_setting_row("cat_sort", "show_cat_sort_select");
  show_setting_row("cat_cells");
  show_setting_row("cat_table_width");
  show_setting_row("cat_table_cellspacing");
  show_setting_row("cat_table_cellpadding");
  show_setting_row("num_subcats");

  show_table_separator($setting_group[3], 2, "setting_group_3");
  show_setting_row("image_order", "show_image_order_select");
  show_setting_row("image_sort", "show_image_sort_select");
  show_setting_row("new_cutoff");
  show_setting_row("image_border");
  show_setting_row("image_cells");
  show_setting_row("default_image_rows");
  show_setting_row("custom_row_steps");
  show_setting_row("image_table_width");
  show_setting_row("image_table_cellspacing");
  show_setting_row("image_table_cellpadding");

  show_table_separator($setting_group[4], 2, "setting_group_4");
  show_setting_row("upload_mode", "show_upload_mode_options");
  show_setting_row("allowed_mediatypes");
  show_setting_row("max_thumb_width");
  show_setting_row("max_thumb_height");
  show_setting_row("max_thumb_size");
  show_setting_row("max_image_width");
  show_setting_row("max_image_height");
  show_setting_row("max_media_size");
  show_setting_row("upload_notify", "radio");
  show_setting_row("upload_emails");
  show_setting_row("auto_thumbnail", "radio");
  show_setting_row("auto_thumbnail_dimension");
  show_setting_row("auto_thumbnail_resize_type", "show_auto_thumbnail_resize_type_options");
  show_setting_row("auto_thumbnail_quality");

  show_table_separator($setting_group[5], 2, "setting_group_5");
  show_setting_row("badword_list", "textarea");
  show_setting_row("badword_replace_char");
  show_setting_row("wordwrap_comments");
  show_setting_row("html_comments", "radio");
  show_setting_row("bb_comments", "radio");
  show_setting_row("bb_img_comments", "radio");

  show_table_separator($setting_group[6], 2, "setting_group_6");
  show_setting_row("category_separator", "", 1);
  show_setting_row("paging_range");

  show_table_separator($setting_group[7], 2, "setting_group_7");
  show_setting_row("user_edit_image", "radio");
  show_setting_row("user_delete_image", "radio");
  show_setting_row("user_edit_comments", "radio");
  show_setting_row("user_delete_comments", "radio");
  show_setting_row("account_activation", "show_account_activation_options");
  show_setting_row("activation_time");
  show_setting_row("session_timeout");
  show_setting_row("display_whosonline", "radio");
  show_setting_row("highlight_admin", "radio");

  show_form_footer($lang['save_changes'], "", 2);
}

show_admin_footer();
?>