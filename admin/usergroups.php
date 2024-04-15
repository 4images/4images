<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: usergroups.php                                       *
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

$permission_select_type = "checkbox"; // choose "checkbox", "select" or "radio"

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

if ($action == "") {
  $action = "modifygroups";
}

$access_field_array = array(
  "auth_viewcat",
  "auth_viewimage",
  "auth_download",
  "auth_upload",
  "auth_directupload",
  "auth_vote",
  "auth_sendpostcard",
  "auth_readcomment",
  "auth_postcomment"
);

function show_usergroup_row($group_id, $group_name, $start_date = "", $end_date = "", $i = 0) {
  global $HTTP_POST_VARS, $lang, $usergroup_match_array, $textinput_size2;
  $i = ($i) ? "_".$i : "";
  if (isset($HTTP_POST_VARS['user_groups'.$i][$group_id]) && $HTTP_POST_VARS['user_groups'.$i][$group_id] == 1) {
    $yes_checked = " checked=\"checked\"";
    $no_checked = "";
  }
  elseif (!isset($HTTP_POST_VARS['user_groups'.$i][$group_id]) && isset($usergroup_match_array[$group_id])) {
    $yes_checked = " checked=\"checked\"";
    $no_checked = "";
  }
  else {
    $yes_checked = "";
    $no_checked = " checked=\"checked\"";
  }
  $bg_class = get_row_bg();
  echo "<tr class=\"".$bg_class."\" valign=\"top\">\n";
  echo "<td rowspan=\"2\">\n<p><b>".$group_name."</b></p></td>\n";
  echo "<td>\n";
  echo "<input type=\"radio\" name=\"user_groups".$i."[".$group_id."]\" value=\"1\"".$yes_checked."> <b>".$lang['yes']."</b>\n";
  echo "<input type=\"radio\" name=\"user_groups".$i."[".$group_id."]\" value=\"0\"".$no_checked."> <b>".$lang['no']."</b>\n";
  echo "</td></tr>\n";
  echo "<tr class=\"".$bg_class."\" valign=\"top\">\n";
  echo "<td>";
  echo "<table border=\"0\">";
  echo "<tr><td valign=\"top\"><b>".$lang['activate_date']."</b></td><td rowspan=\"2\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td valign=\"top\"><b>".$lang['expire_date']."</b></td></tr>";
  echo "<tr><td valign=\"top\">";
  if (isset($HTTP_POST_VARS['group_start_date'.$i][$group_id])) {
    $start_date = trim($HTTP_POST_VARS['group_start_date'.$i][$group_id]);
  }
  if (!check_admin_date($start_date)) {
    $start_date = date("Y-m-d H:i:s", time());
  }
  $inputname = "";
  echo "<input type=\"text\" name=\"group_start_date".$i."[".$group_id."]\" id=\"group_start_date".$i."_".$group_id."\" size=\"".$textinput_size2."\" value=\"".$start_date."\">\n";
  echo get_calendar_js("group_start_date".$i."_".$group_id, $start_date);
  echo $lang['date_desc'].$lang['date_format'];
  echo "</td><td valign=\"top\">";
  if (isset($HTTP_POST_VARS['group_end_date'.$i][$group_id])) {
    $end_date = trim($HTTP_POST_VARS['group_end_date'.$i][$group_id]);
  }
  if ((!check_admin_date($end_date) && $end_date != 0) || $end_date == "") {
    $end_date = 0;
  }
  echo "<input type=\"text\" name=\"group_end_date".$i."[".$group_id."]\" id=\"group_end_date".$i."_".$group_id."\"size=\"".$textinput_size2."\" value=\"".$end_date."\">\n";
  echo get_calendar_js("group_end_date".$i."_".$group_id, $end_date);
  echo $lang['expire_date_desc'].$lang['date_format'];
  echo "</td></tr></table>";
  echo "</td></tr>\n";
}

function show_category_rows($cid = 0, $depth = 1) {
  global $site_db, $site_sess, $lang, $cat_parent_cache, $cat_cache, $access_field_array, $cat_access_array, $permission_select_type;

  if (!isset($cat_parent_cache[$cid])) {
    return false;
  }
  foreach ($cat_parent_cache[$cid] as $key => $category_id) {
      $class = "tablerow2";
      if ($category_id == 0) {
        $class = "tablerow";
      }
      echo "<tr><td class=\"$class\" nowrap=\"nowrap\">\n";
      if ($depth > 1) {
        echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth - 1)."<img src=\"images/folder_path.gif\" alt=\"\">\n";
      }
      $category_name = $cat_cache[$category_id]['cat_name'];
      echo "<img src=\"images/folder.gif\" alt=\"\"><b><a href=\"".$site_sess->url("categories.php?action=editcat&cat_id=".$category_id)."\">".format_text($category_name, 2)."</a></b></td>";
      foreach ($access_field_array as $val) {
        echo "<td class=\"".$class."\" align=\"center\">";
        if ($cat_cache[$category_id][$val] == AUTH_ACL) {
          if ($permission_select_type == "radio") {
            if (isset($cat_access_array[$category_id]) && $cat_access_array[$category_id][$val] == 1) {
              echo "<input type=\"radio\" name=\"auth[".$category_id."][".$val."]\" value=\"1\" checked=\"checked\">".$lang['yes']." \n<input type=\"radio\" name=\"auth[".$category_id."][".$val."]\" value=\"0\">".$lang['no']."\n";
            }
            else {
              echo "<input type=\"radio\" name=\"auth[".$category_id."][".$val."]\" value=\"1\">".$lang['yes']." \n<input type=\"radio\" name=\"auth[".$category_id."][".$val."]\" value=\"0\" checked=\"checked\">".$lang['no']."\n";
            }
          }
          elseif ($permission_select_type == "checkbox") {
            $checked = "";
            if (isset($cat_access_array[$category_id]) && $cat_access_array[$category_id][$val] == 1) {
              $checked = " checked=\"checked\"\n";
            }
            echo "<input type=\"checkbox\" name=\"auth[".$category_id."][".$val."]\" value=\"1\"$checked>\n";

          }
          else {
            echo "<select name=\"auth[".$category_id."][".$val."]\">\n";
            if (isset($cat_access_array[$category_id]) && $cat_access_array[$category_id][$val] == 1) {
              echo "<option value=\"1\" selected=\"selected\">".$lang['yes']."</option>\n<option value=\"0\">".$lang['no']."</option>\n";
            }
            else {
              echo "<option value=\"1\">".$lang['yes']."</option>\n<option value=\"0\" selected=\"selected\">".$lang['no']."</option>\n";
            }
            echo "</select>\n";
          }
        }
        else {
          echo "&nbsp;";
        }
        echo "</td>\n";
      }
      echo "</tr>";
    show_category_rows($category_id, $depth + 1);
  }
  unset($cat_parent_cache[$cid]);
}

function update_single_usergroup($user_id) {
  global $site_db, $user_table_fields;
  $sql = "SELECT ".get_user_table_field("", "user_name")."
          FROM ".USERS_TABLE."
          WHERE ".get_user_table_field("", "user_id")." = $user_id";
  $row = $site_db->query_firstrow($sql);
  if (!$row) {
    return false;
  }
  else {
    $group_name = $row[$user_table_fields['user_name']];
    unset($row);
  }
  $sql = "SELECT group_id
          FROM ".GROUPS_TABLE."
          WHERE group_name = '".addslashes($group_name)."' AND group_type = ".GROUPTYPE_SINGLE;
  $row = $site_db->query_firstrow($sql);
  if (!$row) {
    $sql = "INSERT INTO ".GROUPS_TABLE."
            (group_name, group_type)
            VALUES
            ('".addslashes($group_name)."', ".GROUPTYPE_SINGLE.")";
    $site_db->query($sql);
    $group_id = $site_db->get_insert_id();
  }
  else {
    $group_id = $row['group_id'];
  }
  $sql = "SELECT group_id
          FROM ".GROUP_MATCH_TABLE."
          WHERE group_id = $group_id AND user_id = $user_id";
  if ($site_db->is_empty($sql)) {
    $sql = "INSERT INTO ".GROUP_MATCH_TABLE."
            (group_id, user_id, groupmatch_startdate, groupmatch_enddate)
            VALUES
            ($group_id, $user_id, 0, 0)";
    $site_db->query($sql);
  }
  return array("group_id" => $group_id, "group_name" => $group_name);
}
show_admin_header('
    <script language="JavaScript">
    <!--
    function CheckAllCats(el, type) {
      for (var i=0;i<document.form.elements.length;i++) {
        var e = document.form.elements[i];
        if (e.name != el && e.name.indexOf("["+type+"]") > 0) {
          e.checked = el.checked;
        }
      }
    }
    // -->
    </script>
');

if ($action == "updateuser") {
  $user_groups = (isset($HTTP_POST_VARS['user_groups'])) ? $HTTP_POST_VARS['user_groups'] : "";
  $user_id = intval($HTTP_POST_VARS['user_id']);

  if (!empty($user_groups)) {
    $group_delete_sql = "";
    foreach ($user_groups as $key => $val) {
      $group_delete_sql .= (($group_delete_sql != "") ? ", " : "").$key;
    }
    if (!empty($group_delete_sql)) {
      $sql = "DELETE FROM ".GROUP_MATCH_TABLE."
              WHERE user_id = $user_id AND group_id IN ($group_delete_sql)";
      $site_db->query($sql);
    }

    foreach ($user_groups as $key => $val) {
      if ($val == 1) {
        $start_date = trim($HTTP_POST_VARS['group_start_date'][$key]);
        $start_date = ($start_date != "" && check_admin_date($start_date)) ? "UNIX_TIMESTAMP('$start_date')" : time();

        $end_date = trim($HTTP_POST_VARS['group_end_date'][$key]);
        $end_date = ($end_date != "" && check_admin_date($end_date)) ? "UNIX_TIMESTAMP('$end_date')" : 0;

        $sql = "INSERT INTO ".GROUP_MATCH_TABLE."
                (group_id, user_id, groupmatch_startdate, groupmatch_enddate)
                VALUES
                ($key, $user_id, $start_date, $end_date)";
        $site_db->query($sql);
      }
    }
  }
  $msg = $lang['user_edit_success'];
  $action = "edituser";
}

if ($action == "edituser") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  $user_id = (isset($HTTP_POST_VARS['user_id'])) ? intval($HTTP_POST_VARS['user_id']) : intval($HTTP_GET_VARS['user_id']);
  $user_row = get_user_info($user_id);

  $sql = "SELECT group_id, group_name
          FROM ".GROUPS_TABLE."
          WHERE group_type = ".GROUPTYPE_GROUP;
  $result = $site_db->query($sql);

  $usergroup_array = array();
  while ($row = $site_db->fetch_array($result)) {
    $usergroup_array[$row['group_id']] = $row['group_name'];
  }

  $sql = "SELECT group_id, groupmatch_startdate, groupmatch_enddate
          FROM ".GROUP_MATCH_TABLE."
          WHERE user_id = $user_id";
  $result = $site_db->query($sql);

  $usergroup_match_array = array();
  while ($row = $site_db->fetch_array($result)) {
    $usergroup_match_array[$row['group_id']] = $row;
  }

  show_form_header("usergroups.php", "updateuser", "form", 1);
  show_hidden_input("user_id", $user_id);
  show_table_header($lang['member_of_usergroup'], 2);
  if (empty($usergroup_array)) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"2\">".$lang['no_search_results']."</td></tr>";
  }
  else {
    foreach ($usergroup_array as $key => $val) {
      $start_date = (isset($usergroup_match_array[$key]['groupmatch_startdate'])) ? date("Y-m-d H:i:s", $usergroup_match_array[$key]['groupmatch_startdate']) : date("Y-m-d H:i:s", time());
      if (isset($usergroup_match_array[$key]['groupmatch_enddate'])) {
        $end_date = ($usergroup_match_array[$key]['groupmatch_enddate'] != 0) ? date("Y-m-d H:i:s", $usergroup_match_array[$key]['groupmatch_enddate']) : $usergroup_match_array[$key]['groupmatch_enddate'];
      }
      else {
        $end_date = 0;
      }
      show_usergroup_row($key, $val, $start_date, $end_date);
    }
  }
  show_form_footer($lang['save_changes'], $lang['reset'], 2);
}

if ($action == "updatepermissions") {
  $group_id = (isset($HTTP_POST_VARS['group_id'])) ? intval($HTTP_POST_VARS['group_id']) : intval($HTTP_GET_VARS['group_id']);
  $auth = (isset($HTTP_POST_VARS['auth'])) ? $HTTP_POST_VARS['auth'] : array();

  $sql = "DELETE FROM ".GROUP_ACCESS_TABLE."
          WHERE group_id = $group_id";
  $site_db->query($sql);

  foreach ($auth as $key => $val) {
    $auth_viewcat = (isset($auth[$key]['auth_viewcat']) && $auth[$key]['auth_viewcat'] == 1) ? 1 : 0;
    $auth_viewimage = (isset($auth[$key]['auth_viewimage']) && $auth[$key]['auth_viewimage'] == 1) ? 1 : 0;
    $auth_download = (isset($auth[$key]['auth_download']) && $auth[$key]['auth_download'] == 1) ? 1 : 0;
    $auth_upload = (isset($auth[$key]['auth_upload']) && $auth[$key]['auth_upload'] == 1) ? 1 : 0;
    $auth_directupload = (isset($auth[$key]['auth_directupload']) && $auth[$key]['auth_directupload'] == 1) ? 1 : 0;
    $auth_vote = (isset($auth[$key]['auth_vote']) && $auth[$key]['auth_vote'] == 1) ? 1 : 0;
    $auth_sendpostcard = (isset($auth[$key]['auth_sendpostcard']) && $auth[$key]['auth_sendpostcard'] == 1) ? 1 : 0;
    $auth_readcomment = (isset($auth[$key]['auth_readcomment']) && $auth[$key]['auth_readcomment'] == 1) ? 1 : 0;
    $auth_postcomment = (isset($auth[$key]['auth_postcomment']) && $auth[$key]['auth_postcomment'] == 1) ? 1 : 0;

    if ($auth_viewcat || $auth_viewimage || $auth_download || $auth_upload || $auth_directupload || $auth_vote || $auth_sendpostcard || $auth_readcomment || $auth_postcomment) {
      $sql = "INSERT INTO ".GROUP_ACCESS_TABLE."
              (group_id, cat_id, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment)
              VALUES
              ($group_id, $key, $auth_viewcat, $auth_viewimage, $auth_download, $auth_upload, $auth_directupload, $auth_vote, $auth_sendpostcard, $auth_readcomment, $auth_postcomment)";
      $site_db->query($sql);
    }
  }
  $msg = $lang['permissions_edit_success'];
  $action = "editpermissions";
}

if ($action == "editpermissions") {
  if (isset($HTTP_GET_VARS['group_id']) || isset($HTTP_POST_VARS['group_id'])) {
    $group_id = (isset($HTTP_GET_VARS['group_id'])) ? intval($HTTP_GET_VARS['group_id']) : intval($HTTP_POST_VARS['group_id']);
  }
  else {
    $group_id = 0;
  }

  if (isset($HTTP_GET_VARS['user_id']) || isset($HTTP_POST_VARS['user_id'])) {
    $user_id = (isset($HTTP_GET_VARS['user_id'])) ? intval($HTTP_GET_VARS['user_id']) : intval($HTTP_POST_VARS['user_id']);
  }
  else {
    $user_id = 0;
  }

  if (!$group_id && $user_id) {
    $sql = "SELECT g.group_id, g.group_name
            FROM (".GROUPS_TABLE." g, ".GROUP_MATCH_TABLE." gm)
            WHERE gm.user_id = $user_id AND g.group_id = gm.group_id AND g.group_type = ".GROUPTYPE_SINGLE;
    $row = $site_db->query_firstrow($sql);
    if (!$row) {
      $row = update_single_usergroup($user_id);
    }
    $group_id = $row['group_id'];
    $group_name = $row['group_name'];
  }
  elseif ($group_id) {
    $sql = "SELECT group_name
            FROM ".GROUPS_TABLE."
            WHERE group_id = $group_id";
    $row = $site_db->query_firstrow($sql);
    $group_name = $row['group_name'];
  }
  else {
    $group_name = "INVALID USER GROUP";
  }

  if ($msg != "") {
    printf("<b>%s</b>\n<p>", $msg);
  }

  show_form_header("usergroups.php", "updatepermissions", "form");
  show_hidden_input("group_id", $group_id);

  $cols = sizeof($access_field_array) + 1;
  $col_width = ceil((intval(100)) / $cols);

  show_table_header($lang['permissions'].": ".format_text($group_name, 2), $cols);
  show_description_row("&nbsp;", $cols);
  echo "<tr class=\"tableseparator\">\n";
  echo "<td class=\"tableseparator\">".$lang['field_category_name']."</td>\n";
  foreach ($access_field_array as $val) {
    echo "<td class=\"tableseparator\" width=\"".$col_width."%\" align=\"center\">".($permission_select_type == "checkbox" ? "<input name=\"allbox[".$val."]\" type=\"checkbox\" onClick=\"CheckAllCats(this, '".$val."');\" />":"").$lang[$val]."</td>\n";
  }

  if (sizeof($cat_cache) == 0) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"".$cols."\">".$lang['no_categories']."</td></tr>";
  }
  else {
    $sql = "SELECT *
            FROM ".GROUP_ACCESS_TABLE."
            WHERE group_id = $group_id";
    $result = $site_db->query($sql);

    $cat_access_array = array();
    while ($row = $site_db->fetch_array($result)) {
      $cat_access_array[$row['cat_id']] = $row;
    }
    show_category_rows();
  }
  show_form_footer($lang['save_changes'], $lang['reset'], $cols, $lang['back']);
}

if ($action == "deletegroup") {
  $group_id = intval($HTTP_POST_VARS['group_id']);

  $sql = "DELETE FROM ".GROUPS_TABLE."
          WHERE  group_id = $group_id";
  $result = $site_db->query($sql);

  $sql = "DELETE FROM ".GROUP_ACCESS_TABLE."
          WHERE  group_id = $group_id";
  $result2 = $site_db->query($sql);

  $sql = "DELETE FROM ".GROUP_MATCH_TABLE."
          WHERE  group_id = $group_id";
  $result3 = $site_db->query($sql);

  $msg = ($result && $result2 && $result3) ? $lang['usergroup_delete_success'] : $lang['usergroup_delete_error'];
  $action = "modifygroups";
}

if ($action == "removegroup") {
  $group_id = (isset($HTTP_POST_VARS['group_id'])) ? intval($HTTP_POST_VARS['group_id']) : intval($HTTP_GET_VARS['group_id']);

  $sql = "SELECT group_id, group_name
          FROM ".GROUPS_TABLE."
          WHERE group_id = $group_id";
  $result = $site_db->query_firstrow($sql);

  show_form_header("usergroups.php", "deletegroup");
  show_hidden_input("group_id", $group_id);
  show_table_header($lang['delete'].": ".format_text($result['group_name'], 2), 2);
  show_description_row($lang['delete_group_confirm']);
  show_form_footer($lang['yes'], "", 2, $lang['no']);
}

if ($action == "addgroup") {
  $group_name = trim($HTTP_POST_VARS['group_name']);
  if ($group_name != "") {
    $sql = "INSERT INTO ".GROUPS_TABLE."
            (group_name, group_type)
            VALUES
            ('$group_name', ".GROUPTYPE_GROUP.")";
    $result = $site_db->query($sql);

    $msg = ($result) ? $lang['usergroup_add_success'] : $lang['usergroup_add_error'];
  }
  $action = "modifygroups";
}

if ($action == "updategroup") {
  $error = array();
  $group_id = (isset($HTTP_POST_VARS['group_id'])) ? intval($HTTP_POST_VARS['group_id']) : intval($HTTP_GET_VARS['group_id']);
  $group_name = trim($HTTP_POST_VARS['group_name']);

  if ($group_name == "") {
    $error['group_name'] = 1;
  }
  if (empty($error)) {
    $sql = "UPDATE ".GROUPS_TABLE."
            SET group_name = '$group_name'
            WHERE group_id = $group_id";
    $result = $site_db->query($sql);

    $msg = ($result) ? $lang['usergroup_edit_success'] : $lang['usergroup_edit_error'];
    $action = "modifygroups";
  }
  else {
    $msg .= sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "editgroup";
  }
}

if ($action == "editgroup") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  $group_id = (isset($HTTP_POST_VARS['group_id'])) ? intval($HTTP_POST_VARS['group_id']) : intval($HTTP_GET_VARS['group_id']);

  $sql = "SELECT group_id, group_name
          FROM ".GROUPS_TABLE."
          WHERE group_id = $group_id";
  $result = $site_db->query_firstrow($sql);

  show_form_header("usergroups.php", "updategroup");
  show_hidden_input("group_id", $group_id);
  show_table_header($lang['nav_usergroups'], 2);
  show_input_row($lang['field_usergroup_name'], "group_name", $result['group_name'], $textinput_size);
  show_form_footer($lang['save_changes'], $lang['reset'], 2, $lang['back']);
}

if ($action == "modifygroups") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  show_form_header("usergroups.php", "addgroup", "form");
  show_table_header($lang['nav_usergroups'], 2);

  $sql = "SELECT group_id, group_name
          FROM ".GROUPS_TABLE."
          WHERE group_type = ".GROUPTYPE_GROUP."
          ORDER BY group_name ASC";
  $result = $site_db->query($sql);

  $found = 0;
  while ($row = $site_db->fetch_array($result)) {
    echo "<tr class=\"".get_row_bg()."\"><td><p><b>".$row['group_name']."</b></p></td><td><p>";
    show_text_link($lang['edit'], "usergroups.php?action=editgroup&group_id=".$row['group_id']);
    show_text_link($lang['delete'], "usergroups.php?action=removegroup&group_id=".$row['group_id']);
    show_text_link("<b>".$lang['permissions']."</b>", "usergroups.php?action=editpermissions&group_id=".$row['group_id']);
    echo "</p></td></tr>";
    $found = 1;
  }
  if (!$found) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"2\">".$lang['no_search_results']."</td></tr>";
  }
  show_table_separator($lang['add_usergroup'], 2);
  show_custom_row("<input type=\"text\" name=\"group_name\" value=\"\" size=\"".$textinput_size."\">", "<input type=\"submit\" value=\"".$lang['add_usergroup']."\" class=\"button\">");
  show_table_footer();
}

show_admin_footer();
?>