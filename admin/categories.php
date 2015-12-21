<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: categories.php                                       *
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

$show_all_subcats = false;
$open_cat_link = '<img src="./images/plus.gif" border="0">';
$close_cat_link = '<img src="./images/minus.gif" border="0">';

$show_cat_order_dropdown = true;

include(ROOT_PATH.'includes/search_utils.php');

if ($action == "") {
  $action = "modifycats";
}

$access_field_array = array(
  "auth_viewcat" => AUTH_ALL,
  "auth_viewimage" => AUTH_ALL,
  "auth_download" => AUTH_USER,
  "auth_upload" => AUTH_USER,
  "auth_directupload" => AUTH_ADMIN,
  "auth_vote" => AUTH_ALL,
  "auth_sendpostcard" => AUTH_ALL,
  "auth_readcomment" => AUTH_ALL,
  "auth_postcomment" => AUTH_USER
);

$access_array = array(
  AUTH_ALL => $lang['all'],
  AUTH_USER => $lang['userlevel_registered'],
  AUTH_ACL => $lang['private'],
  AUTH_ADMIN => $lang['userlevel_admin']
);

function show_access_select($title = "", $type, $status) {
  global $access_array, $HTTP_POST_VARS;
  if (isset($HTTP_POST_VARS[$type])) {
    $status = $HTTP_POST_VARS[$type];
  }
  echo "<tr class=\"".get_row_bg()."\" valign=\"top\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n";
  echo "<td>\n<select name=\"".$type."\">\n";
  foreach ($access_array as $key => $val) {
    echo "<option value=\"".$key."\"";
    if ($status == $key) {
      echo " selected=\"selected\"";
    }
    echo ">".$val."</option>\n";
  }
  echo "</select>\n</td>\n</tr>\n";
}

function create_cat_folder($path, $mode) {
  if (@is_dir($path)) {
    @chmod($path, $mode);
    return true;
  }
  else {
    $oldumask = umask(0);
    $result = mkdir($path, $mode);
    umask($oldumask);
    if (!@is_dir($path) || !$result) {
      $result = mkdir($path, 0755);
      @chmod($path, $mode);
    }
    return $result;
  }
}

function remove_cat_folder($path) {
  $ok = 1;
  if (@is_dir($path)) {
    $handle = opendir($path);
    while ($file = @readdir($handle)) {
      if ($file != "." && $file != "..") {
        $ok = (!remove_cat_folder($path."/".$file)) ? 0 : $ok;
      }
    }
    closedir($handle);
    $ok = (!rmdir($path)) ? 0 : $ok;
  }
  else {
    $ok = (!unlink($path)) ? 0 : $ok;
  }
  return $ok;
}

function remove_subcategories($cid = 0, $depth = 1) {
  global $site_db, $error_log, $lang, $category_cache;

  if (!isset($category_cache[$cid])) {
    return false;
  }
  foreach ($category_cache[$cid] as $key => $cats) {
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>\n";
    if ($depth > 1) {
      echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth - 1)."\n";
    }
    echo "</td><td>\n";

    $sql = "DELETE FROM ".GROUP_ACCESS_TABLE."
            WHERE cat_id = ".$cats['cat_id'];
    $site_db->query($sql);

    $sql = "DELETE FROM ".CATEGORIES_TABLE."
            WHERE cat_id = ".$cats['cat_id'];

    if ($site_db->query($sql)) {
      echo $lang['cat_delete_success']." <b>".format_text($cats['cat_name'], 2)."</b> (ID: ".$cats['cat_id'].")<br />\n";
    }
    else {
      $error_log[] = $lang['cat_delete_error']." <b>".format_text($cats['cat_name'], 2)."</b> (ID: ".$cats['cat_id'].")";
    }

    $sql = "SELECT image_id
            FROM ".IMAGES_TABLE."
            WHERE cat_id = ".$cats['cat_id'];
    $img_result = $site_db->query($sql);

    $image_ids_sql = 0;
    while ($image_row = $site_db->fetch_array($img_result)) {
      $image_ids_sql .= (($image_ids_sql != "") ? ", " : "").$image_row['image_id'];
    }

    $sql = "DELETE FROM ".IMAGES_TABLE."
            WHERE image_id IN ($image_ids_sql)";
    if ($site_db->query($sql)) {
      echo "-&raquo ".$lang['image_delete_success']."<br />\n";
    }
    else {
      $error_log[] = $lang['image_delete_error']." (".format_text($cats['cat_name'], 2).", ID: ".$cats['cat_id'].")";
    }

    if (!empty($cats['cat_id'])) {
      if (remove_cat_folder(MEDIA_PATH."/".$cats['cat_id'])) {
        echo "-&raquo ".$lang['file_delete_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['file_delete_error']." (".format_text($cats['cat_name'], 2).", ID: ".$cats['cat_id'].")";
      }
      if (remove_cat_folder(THUMB_PATH."/".$cats['cat_id'])) {
        echo "-&raquo ".$lang['thumb_delete_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['thumb_delete_error']." (".format_text($cats['cat_name'], 2).", ID: ".$cats['cat_id'].")";
      }
    }

    $sql = "DELETE FROM ".COMMENTS_TABLE."
            WHERE image_id IN ($image_ids_sql)";
    if ($site_db->query($sql)) {
      echo "-&raquo ".$lang['comments_delete_success']."<br />\n";
    }
    else {
      $error_log[] = $lang['comments_delete_error']." (".format_text($cats['cat_name'], 2).", ID: ".$cats['cat_id'].")";
    }

    remove_searchwords($image_ids_sql);

    echo "<br /></td></tr></table>\n";
    remove_subcategories($cats['cat_id'], $depth + 1);
  }
  unset($category_cache[$cid]);
  return true;
}

function show_category_rows($cid = 0, $depth = 1) {
  global $site_db, $site_sess, $lang, $category_cache, $cat_parent_cache;

  if (!isset($category_cache[$cid])) {
    return false;
  }
  foreach ($category_cache[$cid] as $key => $cats) {
    $class = "tablerow2";
    if ($cats['cat_parent_id'] == 0) {
      $class = "tablerow";
    }

    echo "<tr class=\"$class\">\n";

    if (!$GLOBALS['show_all_subcats']) {
      if (!empty($cat_parent_cache[$cats['cat_id']])) {

        $href = $site_sess->url("categories.php?action=modifycats");
        if (isset($GLOBALS['map'][$cats['cat_id']]) || $GLOBALS['open_all']) {
          $char = $GLOBALS['close_cat_link'];
          $href .= "&closecat=".$cats['cat_id'];
        } else {
          $char = $GLOBALS['open_cat_link'];
          $href .= "&opencat=".$cats['cat_id'];
        }
        $char = '<a href="'.$href.'">'.$char.'</a>';
      } else {
          $char = '&nbsp;';
      }
      echo "<td align=\"center\">".$char."</td>";
    }

    echo "<td>\n";

    if ($depth > 1) {
      echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth - 1)."<img src=\"images/folder_path.gif\" alt=\"\">\n";
    }
    echo "<img src=\"".ROOT_PATH."admin/images/folder.gif\" alt=\"\"><b><a href=\"".$site_sess->url(ROOT_PATH."categories.php?cat_id=".$cats['cat_id'])."\" target=\"_blank\">".format_text($cats['cat_name'], 2)."</a>\n</b> (ID: ".$cats['cat_id'].")&nbsp;&nbsp;&nbsp;&nbsp;";
    show_text_link($lang['edit'], "categories.php?action=editcat&cat_id=".$cats['cat_id']);
    show_text_link($lang['delete'], "categories.php?action=removecat&cat_id=".$cats['cat_id']);
    show_text_link($lang['add_subcategory'], "categories.php?action=addcat&cat_parent_id=".$cats['cat_id']);
    echo "\n</td>\n<td align=\"center\">";
    echo "<a href=\"".$site_sess->url("categories.php?action=ordercat&move=up&cat_id=".$cats['cat_id'])."\"><img src=\"images/arrow_up.gif\" border=\"0\"></a>\n";
    echo "<a href=\"".$site_sess->url("categories.php?action=ordercat&move=down&cat_id=".$cats['cat_id'])."\"><img src=\"images/arrow_down.gif\" border=\"0\"></a>\n";
    echo "</td>\n</tr>\n";
    show_category_rows($cats['cat_id'], $depth + 1);
  }
  unset($category_cache[$cid]);
}

function update_cat_order($parent_id = 0) {
  global $site_db;
  $sql = "SELECT cat_id
          FROM ".CATEGORIES_TABLE."
          WHERE cat_parent_id = $parent_id
          ORDER BY cat_order ASC";
  $result = $site_db->query($sql);
  $i = 10;
  while ($row = $site_db->fetch_array($result)) {
    $sql = "UPDATE ".CATEGORIES_TABLE."
            SET cat_order = $i
            WHERE cat_id = ".$row['cat_id'];
    $site_db->query($sql);
    $i += 10;
  }
}

function get_cat_order_dropdown($parent_id = 0, $cat_order = 0) {
  global $cat_cache, $cat_parent_cache, $lang, $HTTP_POST_VARS;

  $category_list = "
<script language=\"JavaScript\" type=\"text/JavaScript\">
<!--
var group = new Array();
";
foreach ($cat_parent_cache as $key => $val) {
  $i = 0;
  $category_list .= "group[".$key."] = new Array();\n";
  $category_list .= "group[".$key."][".$i++."] = new Option(\"".$lang['at_beginning']."\", \"5\");\n";
  $category_list .= "group[".$key."][".$i++."] = new Option(\"".$lang['at_end']."\", \"0\");\n";

  foreach ($val as $val2) {
    $category_list .= "group[".$key."][".$i++."] = new Option(\"".$lang['after']." ".format_text($cat_cache[$val2]['cat_name'], 2)."\", \"".($cat_cache[$val2]['cat_order'] + 5)."\");\n";
  }
  $category_list .= "\n";
}
$category_list .= "
function update_order_select(x) {
  for (i = document.cat_form.cat_order.length - 1; i > 0; i--) {
    document.cat_form.cat_order.options[i] = null;
  }

  if (!group[x]) {
    return;
  }

  for (i = 0; i < group[x].length; i++) {
    document.cat_form.cat_order.options[i] = new Option(group[x][i].text, group[x][i].value);
  }
  document.cat_form.cat_order.options[0].selected = true;
}
//-->
</script>
";

  $category_list .= "\n<select name=\"cat_order\" class=\"categoryselect\">\n";
  $category_list .= "<option value=\"0\"";
  if (isset($HTTP_POST_VARS['cat_order']) && $HTTP_POST_VARS['cat_order'] == 0) {
    $category_list .= " selected";
  }
  $category_list .= ">".$lang['at_end']."</option>\n";
  $category_list .= "<option value=\"5\"";
  if (isset($HTTP_POST_VARS['cat_order']) && $HTTP_POST_VARS['cat_order'] == 5) {
    $category_list .= " selected";
  }
  elseif (!isset($HTTP_POST_VARS['cat_order']) && $cat_order == 10) {
    $category_list .= " selected";
  }
  $category_list .= ">".$lang['at_beginning']."</option>\n";
  if (isset($cat_parent_cache[$parent_id])) {
    foreach ($cat_parent_cache[$parent_id] as $key => $val) {
      $category_list .= "<option value=\"".($cat_cache[$val]['cat_order'] + 5)."\"";
      if (isset($HTTP_POST_VARS['cat_order']) && $HTTP_POST_VARS['cat_order'] == ($cat_cache[$val]['cat_order'] + 5)) {
        $category_list .= " selected";
      }
      elseif (!isset($HTTP_POST_VARS['cat_order']) && $cat_cache[$val]['cat_order'] == $cat_order - 10) {
        $category_list .= " selected";
      }
      $category_list .= ">".$lang['after']." ".format_text($cat_cache[$val]['cat_name'], 2)."</option>\n";
    }
  }
  $category_list .= "</select>
  <script language=\"JavaScript\" type=\"text/JavaScript\">
  //update_order_select(".$parent_id.");
  </script>
  ";

  return $category_list;
}

function get_subcategories_id($cat_id = 0) {
  global $subcat_ids, $cat_parent_cache;

  if (!isset($cat_parent_cache[$cat_id])) {
    return false;
  }

  foreach ($cat_parent_cache[$cat_id] as $key => $val) {
    $subcat_ids[] = $val;
    get_subcategories_id($val);
  }

  return $subcat_ids;
}

function forward_to_modify($msg) {
  global $site_sess;
  $site_sess->set_session_var('msg', $msg);
  redirect("categories.php?action=modifycats");
}

if ($action == "ordercat") {
  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : intval($HTTP_GET_VARS['cat_id']);
  $move = (isset($HTTP_POST_VARS['move'])) ? trim($HTTP_POST_VARS['move']) : trim($HTTP_GET_VARS['move']);

  $number = ($move == "up") ? -15 : 15;

  $sql = "UPDATE ".CATEGORIES_TABLE."
          SET cat_order = cat_order + $number
          WHERE cat_id = $cat_id";
  $site_db->query($sql);

  update_cat_order($cat_cache[$cat_id]['cat_parent_id']);
  //$action = "modifycats";
  forward_to_modify($msg);
}

if ($action == "deletecat") {
  $error_log = array();
  show_admin_header();
  show_table_header($lang['main_category'], 1);
  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : intval($HTTP_GET_VARS['cat_id']);

  $sql = "SELECT cat_id, cat_name, cat_parent_id
          FROM ".CATEGORIES_TABLE."
          ORDER BY cat_order, cat_name ASC";
  $result = $site_db->query($sql);
  $category_cache = array();
  while ($row = $site_db->fetch_array($result)) {
    $category_cache[$row['cat_parent_id']][$row['cat_id']] = $row;
  }

  echo "<tr><td class=\"tablerow\">\n";
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=0><tr><td>&nbsp;</td><td>\n";

  $sql = "DELETE FROM ".GROUP_ACCESS_TABLE."
          WHERE cat_id = ".$cat_id;
  $site_db->query($sql);

  $sql = "DELETE FROM ".CATEGORIES_TABLE."
          WHERE cat_id = ".$cat_id;

  if ($site_db->query($sql)) {
    echo $lang['cat_delete_success']." <b>".format_text($cat_cache[$cat_id]['cat_name'], 2)."</b> (ID: ".$cat_id.")<br />\n";
  }
  else {
    $error_log[] = $lang['cat_delete_error']." <b>".format_text($cat_cache[$cat_id]['cat_name'], 2)."</b> (ID: ".$cat_id.")";
  }

  $sql = "SELECT image_id
          FROM ".IMAGES_TABLE."
          WHERE cat_id = ".$cat_id;
  $img_result = $site_db->query($sql);

  $image_ids_sql = 0;
  while ($image_row = $site_db->fetch_array($img_result)) {
    $image_ids_sql .= (($image_ids_sql != "") ? ", " : "").$image_row['image_id'];
  }

  $sql = "DELETE FROM ".IMAGES_TABLE."
          WHERE image_id IN ($image_ids_sql)";
  if ($site_db->query($sql)) {
    echo "-&raquo ".$lang['image_delete_success']."<br />\n";
  }
  else {
    $error_log[] = $lang['image_delete_error']." (".format_text($cat_cache[$cat_id]['cat_name'], 2).", ID: ".$cat_id.")";
  }

  if (!empty($cat_id)) {
    if (remove_cat_folder(MEDIA_PATH."/".$cat_id)) {
      echo "-&raquo ".$lang['file_delete_success']."<br />\n";
    }
    else {
      $error_log[] = $lang['file_delete_error']." (".format_text($cat_cache[$cat_id]['cat_name'], 2).", ID: ".$cat_id.")";
    }
    if (remove_cat_folder(THUMB_PATH."/".$cat_id)) {
      echo "-&raquo ".$lang['thumb_delete_success']."<br />\n";
    }
    else {
      $error_log[] = $lang['thumb_delete_error']." (".format_text($cat_cache[$cat_id]['cat_name'], 2).", ID: ".$cat_id.")";
    }
  }

  $sql = "DELETE FROM ".COMMENTS_TABLE."
          WHERE image_id IN ($image_ids_sql)";
  if ($site_db->query($sql)) {
    echo "-&raquo ".$lang['comments_delete_success']."<br />\n";
  }
  else {
    $error_log[] = $lang['comments_delete_error']." (".format_text($cat_cache[$cat_id]['cat_name'], 2).", ID: ".$cat_id.")";
  }

  remove_searchwords($image_ids_sql);

  echo "<br /></td></tr></table>\n";
  echo "</td></tr>\n";
  show_table_separator($lang['sub_categories'], 1);
  echo "<tr><td class=\"tablerow\">\n";
  if (!remove_subcategories($cat_id)) {
    echo $lang['no_subcategories'];
  }
  echo "</td></tr>\n";
  show_table_footer();
  if (!empty($error_log)) {
    show_table_header("Error Log:", 1);
    echo "<tr><td class=\"tablerow\">\n";
    echo "<table border=\"0\" cellpadding=\"\"2 cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
    echo "<b>".$lang['error_log_desc']."</b>\n<ul>\n";
    foreach ($error_log as $key => $val) {
      printf ("<li>%s</li>\n", $val);
    }
    echo "</ul>\n</td></tr></table>\n";
    echo "</td></tr>\n";
    show_table_footer();
  }
  echo "<p>";
  show_text_link($lang['back_overview'],"categories.php?action=modifycats");
}

if ($action == "removecat") {
  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : intval($HTTP_GET_VARS['cat_id']);

  show_admin_header();
  show_form_header("categories.php", "deletecat");
  show_hidden_input("cat_id", $cat_id);
  show_table_header($lang['delete'].": ".format_text($cat_cache[$cat_id]['cat_name'], 2), 2);
  show_description_row($lang['delete_cat_confirm']);
  show_form_footer($lang['yes'], "", 2, $lang['no']);
}

if ($action == "savecat") {
  $error = array();
  $cat_name = un_htmlspecialchars(trim($HTTP_POST_VARS['cat_name']));
  $cat_description = un_htmlspecialchars(trim($HTTP_POST_VARS['cat_description']));
  $cat_parent_id = intval($HTTP_POST_VARS['cat_parent_id']);
  $cat_order = (isset($HTTP_POST_VARS['cat_order'])) ? intval($HTTP_POST_VARS['cat_order']) : 0;

  $auth_viewcat = intval($HTTP_POST_VARS['auth_viewcat']);
  $auth_viewimage = intval($HTTP_POST_VARS['auth_viewimage']);
  $auth_download = intval($HTTP_POST_VARS['auth_download']);
  $auth_upload = intval($HTTP_POST_VARS['auth_upload']);
  $auth_directupload = intval($HTTP_POST_VARS['auth_directupload']);
  $auth_vote = intval($HTTP_POST_VARS['auth_vote']);
  $auth_sendpostcard = intval($HTTP_POST_VARS['auth_sendpostcard']);
  $auth_readcomment = intval($HTTP_POST_VARS['auth_readcomment']);
  $auth_postcomment = intval($HTTP_POST_VARS['auth_postcomment']);

  if ($cat_name == "") {
    $error['cat_name'] = 1;
  }

  if (empty($error)) {
    if (!$cat_order) {
      $sql = "SELECT cat_order
              FROM ".CATEGORIES_TABLE."
              WHERE cat_parent_id = $cat_parent_id
              ORDER BY cat_order DESC
              LIMIT 1";
      $catorder = $site_db->query_firstrow($sql);
      $cat_order = $catorder['cat_order'] + 10;
      $do_update_cat_order = 0;
    }
    else {
      $do_update_cat_order = 1;
    }

    $sql = "INSERT INTO ".CATEGORIES_TABLE."
            (cat_name, cat_description, cat_parent_id, cat_order, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment)
            VALUES
            ('$cat_name', '$cat_description', $cat_parent_id, $cat_order, $auth_viewcat, $auth_viewimage, $auth_download, $auth_upload, $auth_directupload, $auth_vote, $auth_sendpostcard, $auth_readcomment, $auth_postcomment)";
    $result = $site_db->query($sql);
    $cat_id = $site_db->get_insert_id();

    if ($result && $cat_id) {
      if ($do_update_cat_order) {
        update_cat_order($cat_parent_id);
      }
      $msg = $lang['cat_add_success'];
      create_cat_folder(MEDIA_PATH."/".$cat_id, CHMOD_DIRS);
      create_cat_folder(THUMB_PATH."/".$cat_id, CHMOD_DIRS);
    }
    else {
      $msg = $lang['cat_add_error'];
    }
    //$action = "modifycats";
    forward_to_modify($msg);
  }
  else {
    $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "addcat";
  }
}

if ($action == "addcat") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }

  show_admin_header();
  show_form_header("categories.php", "savecat", "cat_form");
  show_table_header($lang['nav_categories_add'], 2);
  show_input_row($lang['field_category_name'], "cat_name", "", $textinput_size);
  show_textarea_row($lang['field_description_ext'], "cat_description", "", $textarea_size);

  $cat_parent_id = (isset($HTTP_GET_VARS['cat_parent_id'])) ? intval($HTTP_GET_VARS['cat_parent_id']) : 0;
  $category = "\n<select name=\"cat_parent_id\" class=\"categoryselect\" onChange=\"update_order_select(this.options[this.selectedIndex].value)\">\n";
  $category .= "<option value=\"0\">".$lang['main_category']."</option>\n";
  $category .= "<option value=\"0\">--------------</option>\n";
  $drop_down_cat_cache = array();
  $drop_down_cat_cache = $cat_parent_cache;
  $category .= get_category_dropdown_bits($cat_parent_id);
  $category .= "</select>\n";
  show_custom_row($lang['field_parent'], $category);

  if ($show_cat_order_dropdown) {
    show_custom_row($lang['cat_order'], get_cat_order_dropdown($cat_parent_id));
  }

  $permission_headline = $lang['permissions'];
  if ($cat_parent_id != 0) {
    $new_access_field_array = array();

    $sql = "SELECT cat_name, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment
            FROM ".CATEGORIES_TABLE."
            WHERE cat_id = $cat_parent_id";
    $row = $site_db->query_firstrow($sql);

    foreach ($access_field_array as $key => $val) {
      $new_access_field_array[$key] = $row[$key];
    }
    unset($access_field_array);
    $access_field_array = $new_access_field_array;
    $permission_headline .= "<span class=\"smalltext\"><br>".$lang['permissions_inherited']." (".format_text($row['cat_name'], 2).")</span>";
  }

  show_table_separator($permission_headline, 2);
  foreach ($access_field_array as $key => $val) {
    show_access_select($lang[$key], $key, $val);
  }

  show_form_footer($lang['add'], $lang['reset'], 2);
}

if ($action == "updatecat") {
  $error = array();
  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : intval($HTTP_GET_VARS['cat_id']);

  $cat_parent_id = intval($HTTP_POST_VARS['cat_parent_id']);
  $cat_name = un_htmlspecialchars(trim($HTTP_POST_VARS['cat_name']));
  $cat_description = strip_tags(un_htmlspecialchars(trim($HTTP_POST_VARS['cat_description'])), '<div><p><a><strong><bold><i><em><u><h1><h2><h3><h4><h5><h6><span>');
  $cat_hits = intval(trim($HTTP_POST_VARS['cat_hits']));
  $cat_order = (isset($HTTP_POST_VARS['cat_order'])) ? intval($HTTP_POST_VARS['cat_order']) : 0;

  $auth_viewcat = intval($HTTP_POST_VARS['auth_viewcat']);
  $auth_viewimage = intval($HTTP_POST_VARS['auth_viewimage']);
  $auth_download = intval($HTTP_POST_VARS['auth_download']);
  $auth_upload = intval($HTTP_POST_VARS['auth_upload']);
  $auth_directupload = intval($HTTP_POST_VARS['auth_directupload']);
  $auth_vote = intval($HTTP_POST_VARS['auth_vote']);
  $auth_sendpostcard = intval($HTTP_POST_VARS['auth_sendpostcard']);
  $auth_readcomment = intval($HTTP_POST_VARS['auth_readcomment']);
  $auth_postcomment = intval($HTTP_POST_VARS['auth_postcomment']);

  $subcat_ids = array();
  get_subcategories_id($cat_id);

  if ($cat_parent_id == $cat_id || in_array($cat_parent_id, $subcat_ids)) {
    $msg .= sprintf("<span class=\"marktext\">%s</span><br />", $lang['parent_cat_error']);
    $error['cat_parent_id'] = 1;
  }
  if ($cat_name == "") {
    $error['cat_name'] = 1;
  }

  if (empty($error)) {
    if (!$cat_order) {
      $sql = "SELECT cat_order
              FROM ".CATEGORIES_TABLE."
              WHERE cat_parent_id = $cat_parent_id
              ORDER BY cat_order DESC
              LIMIT 1";
      $catorder = $site_db->query_firstrow($sql);
      $cat_order = $catorder['cat_order'] + 10;
      $do_update_cat_order = 0;
    }
    else {
      $do_update_cat_order = 1;
    }

    $sql = "UPDATE ".CATEGORIES_TABLE."
            SET cat_name = '$cat_name', cat_description = '$cat_description', cat_parent_id = $cat_parent_id, cat_order = $cat_order, cat_hits = $cat_hits, auth_viewcat = $auth_viewcat, auth_viewimage = $auth_viewimage, auth_download = $auth_download, auth_upload = $auth_upload, auth_directupload = $auth_directupload, auth_vote = $auth_vote, auth_sendpostcard = $auth_sendpostcard, auth_readcomment = $auth_readcomment, auth_postcomment = $auth_postcomment
            WHERE cat_id = $cat_id";
    $result = $site_db->query($sql);

    if ($result && $do_update_cat_order) {
      update_cat_order($cat_parent_id);
    }

    $msg = ($result) ? $lang['cat_edit_success'] : $lang['cat_edit_error'];
    //$action = "modifycats";
    forward_to_modify($msg);
  }
  else {
    $msg .= sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "editcat";
  }
}

if ($action == "editcat") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  $cat_id = (isset($HTTP_POST_VARS['cat_id'])) ? intval($HTTP_POST_VARS['cat_id']) : intval($HTTP_GET_VARS['cat_id']);

  $sql = "SELECT cat_name, cat_description, cat_parent_id, cat_hits, cat_order, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment
          FROM ".CATEGORIES_TABLE."
          WHERE cat_id = $cat_id";
  $cat_row = $site_db->query_firstrow($sql);

  show_admin_header();
  show_form_header("categories.php", "updatecat", "cat_form");
  show_hidden_input("cat_id", $cat_id);
  show_table_header($lang['nav_categories_edit'], 2);
  show_input_row($lang['field_category_name'], "cat_name", $cat_row['cat_name'], $textinput_size);
  show_textarea_row($lang['field_description_ext'], "cat_description", $cat_row['cat_description'], $textarea_size);

  $cat_parent_id = $cat_row['cat_parent_id'];
  $category = "\n<select name=\"cat_parent_id\" class=\"categoryselect\" onChange=\"update_order_select(this.options[this.selectedIndex].value)\">\n";
  $category .= "<option value=\"0\">".$lang['main_category']."</option>\n";
  $category .= "<option value=\"0\">--------------</option>\n";
  $drop_down_cat_cache = array();
  $drop_down_cat_cache = $cat_parent_cache;
  $category .= get_category_dropdown_bits($cat_parent_id);
  $category .= "</select>\n";
  show_custom_row($lang['field_parent'], $category);

  if ($show_cat_order_dropdown) {
    show_custom_row($lang['cat_order'], get_cat_order_dropdown($cat_parent_id, $cat_row['cat_order']));
  }

  show_input_row($lang['field_hits'], "cat_hits", $cat_row['cat_hits'], 5);

  show_table_separator($lang['permissions'], 2);
  foreach ($access_field_array as $key => $val) {
    show_access_select($lang[$key], $key, $cat_row[$key]);
  }

  show_form_footer($lang['save_changes'], $lang['reset'], 2, $lang['back']);
}

if ($action == "modifycats") {
  $site_sess->set_session_var('back_url', $self_url);

    show_admin_header();
    if ($msg != "") {
    printf("<b>%s</b>\n<p>", $msg);
  } else {
    $msg = $site_sess->get_session_var('msg');
    if ($msg != "") {
      printf("<b>%s</b>\n<p>", $msg);
    }
    $site_sess->drop_session_var('msg');
  }

  if ($ser_map = $site_sess->get_session_var('map')) {
    $GLOBALS['map'] = unserialize($ser_map);
  } else {
    $GLOBALS['map'] = array();
  }

  if (isset($HTTP_GET_VARS['opencat'])) {
    $opencat = intval($HTTP_GET_VARS['opencat']);
    $GLOBALS['map'][$opencat] = $opencat;
  }

  if (isset($HTTP_GET_VARS['closecat'])) {
    $closecat = intval($HTTP_GET_VARS['closecat']);
    unset($GLOBALS['map'][$closecat]);
  }

  $open_all = false;

  if (isset($HTTP_GET_VARS['openall'])) {
    $open_all = true;
  }

  if (isset($HTTP_GET_VARS['closeall'])) {
    $open_all = false;
    $GLOBALS['map'] = array();
  }

  $sql = "SELECT cat_id, cat_name, cat_description, cat_parent_id, cat_hits, cat_order, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment
          FROM ".CATEGORIES_TABLE."
          WHERE cat_parent_id = 0
          ORDER BY cat_order, cat_name ASC";
  $result = $site_db->query($sql);

  $category_cache = array();
  while ($row = $site_db->fetch_array($result)) {
    $category_cache[$row['cat_parent_id']][$row['cat_id']] = $row;
  }

  if ($show_all_subcats || $open_all || (!empty($GLOBALS['map']) && is_array($GLOBALS['map']))) {
    $where_sql = "";
    if (!$show_all_subcats && !$open_all) {
        $where_sql = "WHERE cat_parent_id IN (".implode(", ", $GLOBALS['map']).")";
    }
    $sql = "SELECT cat_id, cat_name, cat_description, cat_parent_id, cat_hits, cat_order, auth_viewcat, auth_viewimage, auth_download, auth_upload, auth_directupload, auth_vote, auth_sendpostcard, auth_readcomment, auth_postcomment
            FROM ".CATEGORIES_TABLE."
            $where_sql
            ORDER BY cat_order, cat_name ASC";

    $result = $site_db->query($sql);

    while ($row = $site_db->fetch_array($result)) {
      $category_cache[$row['cat_parent_id']][$row['cat_id']] = $row;
      if ($open_all) {
        $GLOBALS['map'][$row['cat_id']] = $row['cat_id'];
      }
    }
  }

  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  echo "<tr class=\"tableseparator\">\n";
  if (!$GLOBALS['show_all_subcats']) {
    echo "<td class=\"tableseparator\" width=\"30\">";
    $href = $site_sess->url("categories.php?action=modifycats&openall=1");
    echo '<a href="'.$href.'">'.$GLOBALS['open_cat_link'].'</a>';
    $href = $site_sess->url("categories.php?action=modifycats&closeall=1");
    echo '<a href="'.$href.'">'.$GLOBALS['close_cat_link'].'</a>';
    echo "</td>";
  }
  echo "<td class=\"tableseparator\">".$lang['nav_categories_edit']."</td>\n<td class=\"tableseparator\" align=\"center\">".$lang['cat_order']."</td>\n</tr>\n";
  if (sizeof($category_cache) == 0) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"3\">".$lang['no_categories']."</td></tr>";
  }
  else {
    show_category_rows();
  }
  show_table_footer();

  $site_sess->set_session_var('map', serialize($GLOBALS['map']));
}
show_admin_footer();
?>
