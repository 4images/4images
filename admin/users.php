<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: users.php                                            *
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
  $action = "modifyusers";
}

function delete_users($user_ids, $delcomments = 1, $delimages = 1) {
  global $site_db, $lang, $user_table_fields;
  if (empty($user_ids)) {
    echo $lang['no_search_results'];
    return false;
  }
  $error_log = array();
  echo "<br />";

  $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_name")."
          FROM ".USERS_TABLE."
          WHERE ".get_user_table_field("", "user_id")." IN ($user_ids)";
  $user_result = $site_db->query($sql);
  $image_ids_sql = "";
  while ($user_row = $site_db->fetch_array($user_result)) {
    $user_id = $user_row[$user_table_fields['user_id']];
    $user_name = $user_row[$user_table_fields['user_name']];

    $sql = "DELETE FROM ".GROUP_MATCH_TABLE."
            WHERE user_id = $user_id";
    $site_db->query($sql);

    $sql = "SELECT group_id
            FROM ".GROUPS_TABLE."
            WHERE group_name = '".addslashes($user_name)."' AND group_type = ".GROUPTYPE_SINGLE;
    if ($groups_row = $site_db->query_firstrow($sql)) {
      $sql = "DELETE FROM ".GROUPS_TABLE."
              WHERE group_id = ".$groups_row['group_id']." AND group_type = ".GROUPTYPE_SINGLE;
      $site_db->query($sql);

      $sql = "DELETE FROM ".GROUP_ACCESS_TABLE."
              WHERE group_id = ".$groups_row['group_id'];
      $site_db->query($sql);
    }

    $sql = "DELETE FROM ".LIGHTBOXES_TABLE."
            WHERE user_id = $user_id";
    $site_db->query($sql);

    if (!defined("USER_INTEGRATION") || (defined("USER_INTEGRATION") && USER_INTEGRATION == "NONE")) {
      $sql = "DELETE FROM ".USERS_TABLE."
              WHERE ".get_user_table_field("", "user_id")." = $user_id";
      if ($site_db->query($sql)) {
        echo "<b>".$lang['user_delete_success']."</b> ".format_text($user_name, 2)." (ID: $user_id)<br />\n";
      }
      else {
        $error_log[] = "<b>".$lang['user_delete_error']."</b> ".format_text($user_name, 2)." (ID: $user_id)<br />\n";
      }
    }
    else {
      echo "<b>".$lang['user_integration_delete_msg']."</b> ".format_text($user_name, 2)." (ID: $user_id)<br />\n";
    }

    if ($delimages) {
      $sql = "SELECT image_id, cat_id, image_media_file, image_thumb_file
              FROM ".IMAGES_TABLE."
              WHERE user_id = $user_id";
      $result = $site_db->query($sql);

      while ($row = $site_db->fetch_array($result)) {
        if (file_exists(MEDIA_PATH."/".$row['cat_id']."/".$row['image_media_file'])) {
          unlink(MEDIA_PATH."/".$row['cat_id']."/".$row['image_media_file']);
        }
        if (file_exists(THUMB_PATH."/".$row['cat_id']."/".$row['image_thumb_file']) && !empty($row['image_thumb_file'])) {
          unlink(THUMB_PATH."/".$row['cat_id']."/".$row['image_thumb_file']);
        }
        $image_ids_sql .= (($image_ids_sql != "") ? ", " : "").$row['image_id'];
      }

      $sql = "DELETE FROM ".IMAGES_TABLE."
              WHERE user_id = $user_id";
      if ($site_db->query($sql)) {
        echo "&nbsp;&nbsp;".$lang['images_delete_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['images_delete_error'].": ".format_text($user_name, 2);
      }
    }
    else { //Update Images
      $sql = "UPDATE ".IMAGES_TABLE."
              SET user_id = ".GUEST."
              WHERE user_id = $user_id";
      if ($site_db->query($sql)) {
        echo "&nbsp;&nbsp;".$lang['user_images_update_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['user_images_update_error'].": ".format_text($user_name, 2);
      }
    }

    if ($delcomments) {
      $sql = "SELECT i.image_id, COUNT(c.comment_id) AS count
              FROM " . IMAGES_TABLE . " i
              LEFT JOIN " . COMMENTS_TABLE . " c ON c.image_id = i.image_id
              WHERE c.user_id = " . $user_id . "
              GROUP BY i.image_id";
      $result = $site_db->query($sql);
      while($row = $site_db->fetch_array($result))
      {
        $sql = "UPDATE " . IMAGES_TABLE . "
                SET image_comments = image_comments - " . $row['count'] ."
                WHERE image_id = " . $row['image_id'];
        $site_db->query($sql);
      }
      $sql = "DELETE FROM ".COMMENTS_TABLE."
              WHERE user_id = $user_id";
      if ($site_db->query($sql)) {
        echo "&nbsp;&nbsp;".$lang['comments_delete_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['comments_delete_error'].": ".format_text($user_name, 2);
      }
    }
    else { //Update Comments
      $sql = "UPDATE ".COMMENTS_TABLE."
              SET user_id = ".GUEST.", user_name = '$user_name'
              WHERE user_id = $user_id";
      if ($site_db->query($sql)) {
        echo "&nbsp;&nbsp;".$lang['user_comments_update_success']."<br />\n";
      }
      else {
        $error_log[] = $lang['user_comments_update_error'].": ".format_text($user_name, 2);
      }
    }
    echo "<br />\n";
  }
  include_once(ROOT_PATH.'includes/search_utils.php');
  remove_searchwords($image_ids_sql);
  return $error_log;
}

show_admin_header();

if ($action == "deleteuser") {
  $deleteusers = (isset($HTTP_POST_VARS['deleteusers'])) ? $HTTP_POST_VARS['deleteusers'] : array();
  $delcomments = intval($HTTP_POST_VARS['delcomments']);
  $delimages = intval($HTTP_POST_VARS['delimages']);
  $user_ids = "";
  if (!empty($deleteusers)) {
    foreach ($deleteusers as $val) {
      $user_ids .= (($user_ids != "") ? ", " : "").$val;
    }
  }
  $lang_key = (sizeof($deleteusers) > 1) ? 'users' : 'user';
  show_table_header($lang[$lang_key], 1);
  echo "<tr><td class=\"tablerow\">\n";
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
  $error_log = delete_users($user_ids, $delcomments, $delimages);
  echo "</td></tr></table>\n";
  echo "</td></tr>\n";
  show_table_footer();
  if ($error_log) {
    show_table_header("Error Log:", 1);
    echo "<tr><td class=\"tablerow\">\n";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
    echo "<b>".$lang['error_log_desc']."</b>\n<ul>\n";
    foreach ($error_log as $val) {
      echo "<li>".$val."</li>\n";
    }
    echo "</ul>\n</td></tr></table>\n";
    echo "</td></tr>\n";
    show_table_footer();
  }
  echo "<p>";
  show_text_link($lang['back_overview'], "users.php?action=modifyusers");
}

if ($action == "removeuser") {
  $user_ids = array();
  if (isset($HTTP_GET_VARS['user_id']) || isset($HTTP_POST_VARS['user_id'])) {
    $user_id = (isset($HTTP_GET_VARS['user_id'])) ? intval($HTTP_GET_VARS['user_id']) : intval($HTTP_POST_VARS['user_id']);
    $user_ids[] = $user_id;
  }
  elseif (isset($HTTP_POST_VARS['deleteusers'])) {
    $user_ids = $HTTP_POST_VARS['deleteusers'];
  }
  else {
   $user_ids[] = 0;
  }

  show_form_header("users.php", "deleteuser");
  foreach ($user_ids as $val) {
    show_hidden_input("deleteusers[]", $val);
  }
  $lang_key = (sizeof($user_ids) > 1) ? 'users' : 'user';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 2);
  show_description_row($lang['user_delete_confirm']);
  show_radio_row($lang['user_delete_images_confirm'], "delimages", 1);
  show_radio_row($lang['user_delete_comments_confirm'], "delcomments", 1);
  show_form_footer($lang['yes'], "", 2, $lang['no']);
}

if ($action == "updateuser") {
  $error = array();
  $user_id = (isset($HTTP_POST_VARS['user_id'])) ? intval($HTTP_POST_VARS['user_id']) : intval($HTTP_GET_VARS['user_id']);

  $user_level = intval($HTTP_POST_VARS['user_level']);
  $user_name = trim($HTTP_POST_VARS['user_name']);
  $user_email = trim($HTTP_POST_VARS['user_email']);
  $user_password = trim($HTTP_POST_VARS['user_password']);
  $user_homepage = trim($HTTP_POST_VARS['user_homepage']);
  $user_icq = (intval(trim($HTTP_POST_VARS['user_icq']))) ? intval(trim($HTTP_POST_VARS['user_icq'])) : "";
  $user_joindate = trim($HTTP_POST_VARS['user_joindate']);
  $user_lastaction = trim($HTTP_POST_VARS['user_lastaction']);
  $user_showemail = intval($HTTP_POST_VARS['user_showemail']);
  $user_allowemails = intval($HTTP_POST_VARS['user_allowemails']);
  $user_invisible = intval($HTTP_POST_VARS['user_invisible']);

  $current = get_user_info($user_id);

  $activation = intval($HTTP_POST_VARS['activation']);
  if ($current['user_level'] != USER_AWAITING) {
    $activation = 0;
  }

  if ($user_name == "") {
    $error['user_name'] = 1;
  }
  if ($user_email == "" || !check_email($user_email)) {
    $error['user_email'] = 1;
  }
  if ($user_level == GUEST) {
    $error['user_level'] = 1;
  }
  if ($user_name != "" && strtolower($current['user_name']) != strtolower(stripslashes($user_name)) && $site_db->not_empty("SELECT ".get_user_table_field("", "user_name")." FROM ".USERS_TABLE." WHERE ".get_user_table_field("", "user_name")." = '".strtolower($user_name)."'")) {
    $msg .= sprintf("%s: <b>%s</b><br />", $lang['user_name_exists'], format_text($user_name, 2));
    $error['user_name'] = 1;
  }
  if ($user_email != "" && strtolower($current['user_email']) != strtolower($user_email) && $site_db->not_empty("SELECT ".get_user_table_field("", "user_email")." FROM ".USERS_TABLE." WHERE ".get_user_table_field("", "user_email")." = '".strtolower($user_email)."'")) {
    $msg .= sprintf("%s: <b>%s</b><br />", $lang['user_email_exists'], $user_email);
    $error['user_email'] = 1;
  }

  if (!empty($additional_user_fields)) {
    foreach ($additional_user_fields as $key => $val) {
      if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
        $error[$key] = 1;
      }
    }
  }

  if (empty($error)) {
    $passinsert = ($user_password != "") ? " ".get_user_table_field("", "user_password")." = '".salted_hash($user_password)."'," : "";
    $user_joindate = ($user_joindate != "") ? "UNIX_TIMESTAMP('$user_joindate')" : time();
    $user_lastaction = ($user_lastaction != "") ? "UNIX_TIMESTAMP('$user_lastaction')" : time();

    $additional_sql = "";
    if (!empty($additional_user_fields)) {
      $table_fields = $site_db->get_table_fields(USERS_TABLE);
      foreach ($additional_user_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
          $additional_sql .= ", $key = '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
        }
      }
    }

    $sql = "UPDATE ".USERS_TABLE."
            SET ".get_user_table_field("", "user_level")." = $user_level, ".get_user_table_field("", "user_name")." = '$user_name',$passinsert ".get_user_table_field("", "user_email")." = '$user_email', ".get_user_table_field("", "user_showemail")." = $user_showemail, ".get_user_table_field("", "user_allowemails")." = $user_allowemails, ".get_user_table_field("", "user_invisible")." = $user_invisible, ".get_user_table_field("", "user_joindate")." = $user_joindate, ".get_user_table_field("", "user_lastaction")." = $user_lastaction, ".get_user_table_field("", "user_homepage")." = '$user_homepage', ".get_user_table_field("", "user_icq")." = '$user_icq'".$additional_sql."
            WHERE ".get_user_table_field("", "user_id")." = $user_id";
    $result = $site_db->query($sql);

    if ($result && $config['account_activation'] == 2 && $activation && $user_level != USER_AWAITING) {
      include(ROOT_PATH.'includes/email.php');
      $site_email = new Email();
      $site_email->set_to($user_email);
      $site_email->set_subject($lang['activation_success_emailsubject']);
      $site_email->register_vars(array(
        "user_name" => $user_name,
        "site_name" => $config['site_name']
      ));
      $site_email->set_body("activation_success", $config['language_dir']);
      $site_email->send_email();
    }

    $msg = ($result) ? $lang['user_edit_success'] : $lang['user_edit_error'];
  }
  else {
    $msg .= sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
  }
  $action = "edituser";
}

if ($action == "edituser") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }

  $user_id = (isset($HTTP_POST_VARS['user_id'])) ? intval($HTTP_POST_VARS['user_id']) : intval($HTTP_GET_VARS['user_id']);

  $user_row = get_user_info($user_id);
  $user_row['user_joindate'] = date("Y-m-d H:i", $user_row['user_joindate']);
  $user_row['user_lastaction'] = date("Y-m-d H:i", $user_row['user_lastaction']);

  if (isset($HTTP_GET_VARS['activation']) || isset($HTTP_POST_VARS['activation'])) {
    $activation = (isset($HTTP_GET_VARS['activation'])) ? intval($HTTP_GET_VARS['activation']) : intval($HTTP_POST_VARS['activation']);
    if ($user_row['user_level'] != USER_AWAITING) {
      $activation = 0;
    }
  }
  else {
    if ($config['account_activation'] == 2 && $user_row['user_level'] == USER_AWAITING) {
        $activation = 1;
    } else {
        $activation = 0;
    }
  }

  show_form_header("users.php", "updateuser", "form", 1);
  show_hidden_input("user_id", $user_id);
  show_hidden_input("activation", $activation);
  show_table_header($lang['edit'].": ".format_text($user_row['user_name'], 2), 2);
  show_userlevel_select_row($lang['field_userlevel'], "user_level", $user_row['user_level']);
  show_input_row($lang['field_username'], "user_name", $user_row['user_name'], $textinput_size);
  show_input_row($lang['field_email'], "user_email", $user_row['user_email'], $textinput_size);
  unset($HTTP_POST_VARS['user_password']);
  show_input_row($lang['field_password_ext'], "user_password", "", $textinput_size);
  show_input_row($lang['field_homepage'], "user_homepage", $user_row['user_homepage'], $textinput_size);
  show_input_row($lang['field_icq'], "user_icq", $user_row['user_icq'], $textinput_size);
  show_radio_row($lang['field_showemail'], "user_showemail", $user_row['user_showemail']);
  show_radio_row($lang['field_allowemails'], "user_allowemails", $user_row['user_allowemails']);
  show_radio_row($lang['field_invisible'], "user_invisible", $user_row['user_invisible']);
  show_date_input_row($lang['field_joindate'].$lang['date_desc'], "user_joindate", $user_row['user_joindate'], $textinput_size);
  show_date_input_row($lang['field_lastaction'].$lang['date_desc'], "user_lastaction", $user_row['user_lastaction'], $textinput_size);
  show_additional_fields("user", $user_row, USERS_TABLE);
  show_form_footer($lang['save_changes'], $lang['reset'], 2);
}

if ($action == "modifyusers") {
  show_form_header("users.php", "findusers", "form");
  show_table_header($lang['nav_users_edit'], 2);
  show_userlevel_select_row($lang['field_userlevel']);
  show_input_row($lang['field_username_contains'], "user_name", "", $textinput_size);
  show_input_row($lang['field_email_contains'], "user_email", "", $textinput_size);
  show_date_input_row($lang['field_joindate_after'].$lang['date_format'], "dateafter", "", $textinput_size);
  show_date_input_row($lang['field_joindate_before'].$lang['date_format'], "datebefore", "", $textinput_size);
  show_date_input_row($lang['field_lastaction_after'].$lang['date_format'], "lastactionafter", "", $textinput_size);
  show_date_input_row($lang['field_lastaction_before'].$lang['date_format'], "lastactionbefore", "", $textinput_size);
  show_table_separator($lang['sort_options'], 2);
  ?>
  <tr class="<?php echo get_row_bg(); ?>"><td><p><b><?php echo $lang['order_by']; ?></b></p></td><td><p>
  <select name="orderby">
  <option value="<?php echo get_user_table_field("", "user_name"); ?>" selected><?php echo $lang['field_username']; ?></option>
  <option value="<?php echo get_user_table_field("", "user_email"); ?>"><?php echo $lang['field_email']; ?></option>
  <option value="<?php echo get_user_table_field("", "user_joindate"); ?>"><?php echo $lang['field_joindate']; ?></option>
  <option value="<?php echo get_user_table_field("", "user_lastaction"); ?>"><?php echo $lang['field_lastaction']; ?></option>
  </select>
  <select name="direction">
  <option selected value="ASC"><?php echo $lang['asc']; ?></option>
  <option value="DESC"><?php echo $lang['desc']; ?></option>
  </select>
  </p></td></tr>
  <?php
  show_input_row($lang['results_per_page'], "limitnumber", 50);
  show_form_footer($lang['search'], $lang['reset'], 2);
}

if ($action == "findusers") {
  $site_sess->set_session_var('back_url', $self_url);

  $condition = "1=1";

  $user_level = intval($HTTP_POST_VARS['user_level']);
  if ($user_level != GUEST) {
    $condition .= " AND ".get_user_table_field("", "user_level")." = $user_level";
  }
  $user_name = trim($HTTP_POST_VARS['user_name']);
  if ($user_name != "") {
    $condition .= " AND INSTR(LCASE(".get_user_table_field("", "user_name")."),'".strtolower($user_name)."')>0";
  }
  $user_email = trim($HTTP_POST_VARS['user_email']);
  if ($user_email != "") {
    $condition .= " AND INSTR(LCASE(".get_user_table_field("", "user_email")."),'".strtolower($user_email)."')>0";
  }
  $dateafter = trim($HTTP_POST_VARS['dateafter']);
  if ($dateafter != "") {
    $condition .= " AND ".get_user_table_field("", "user_joindate")." > UNIX_TIMESTAMP('$dateafter')";
  }
  $datebefore = trim($HTTP_POST_VARS['datebefore']);
  if ($datebefore != "") {
    $condition .= " AND ".get_user_table_field("", "user_joindate")." < UNIX_TIMESTAMP('$datebefore')";
  }
  $lastactionafter = trim($HTTP_POST_VARS['lastactionafter']);
  if ($lastactionafter != "") {
    $condition .= " AND ".get_user_table_field("", "user_lastaction")." > UNIX_TIMESTAMP('$lastactionafter')";
  }
  $lastactionbefore = trim($HTTP_POST_VARS['lastactionbefore']);
  if ($lastactionbefore != "") {
    $condition .= " AND ".get_user_table_field("", "user_lastaction")." < UNIX_TIMESTAMP('$lastactionbefore')";
  }
  $orderby = trim($HTTP_POST_VARS['orderby']);
  if ($orderby == "") {
    $orderby = get_user_table_field("", "user_name");
  }
  $limitstart = (isset($HTTP_POST_VARS['limitstart'])) ? trim($HTTP_POST_VARS['limitstart']) : "";
  if ($limitstart == "") {
    $limitstart = 0;
  }
  else {
    $limitstart--;
  }
  $limitnumber = (isset($HTTP_POST_VARS['limitnumber'])) ? trim($HTTP_POST_VARS['limitnumber']) : "";
  if ($limitnumber == "") {
    $limitnumber = 5000;
  }

  if (isset($HTTP_GET_VARS['direction']) || isset($HTTP_POST_VARS['direction'])) {
    $direction = (isset($HTTP_GET_VARS['direction'])) ? trim($HTTP_GET_VARS['direction']) : trim($HTTP_POST_VARS['direction']);
  }
  else {
    $direction = "ASC";
  }

  $sql = "SELECT COUNT(*) AS users
          FROM ".USERS_TABLE."
          WHERE $condition AND ".get_user_table_field("", "user_id")." <> ".GUEST;
  $countusers = $site_db->query_firstrow($sql);

  $limitfinish = $limitstart + $limitnumber;

  $start = 0;
  if ($countusers['users'] > 0) {
    $start = $limitstart + 1;
  }

  echo $lang['found']." <b>".$countusers['users']."</b>. ".$lang['showing']." <b>$start</b>-";
  if ($limitfinish > $countusers['users'] == 0) {
    echo "<b>".$limitfinish."</b>.";
  }
  else {
    echo "<b>".$countusers['users']."</b>.";
  }
  show_form_header("users.php", "removeuser", "form");
  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  if ($countusers['users'] > 0) {
    $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_name").get_user_table_field(", ", "user_email").get_user_table_field(", ", "user_joindate").get_user_table_field(", ", "user_lastaction")."
            FROM ".USERS_TABLE."
            WHERE $condition AND ".get_user_table_field("", "user_id")." <> ".GUEST."
            ORDER BY $orderby $direction
            LIMIT $limitstart, $limitnumber";
    $result = $site_db->query($sql);
    echo "<tr class=\"tableseparator\">\n";
    echo "<td class=\"tableseparator\"><input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\" /></td>\n";
    echo "<td class=\"tableseparator\">".$lang['field_username']."</td>\n<td class=\"tableseparator\">".$lang['field_email']."</td>\n<td class=\"tableseparator\">".$lang['field_joindate']."</td>\n<td class=\"tableseparator\">".$lang['field_lastaction']."</td>\n<td class=\"tableseparator\">".$lang['options']."</td>\n</tr>\n";
    while ($user_row = $site_db->fetch_array($result)) {
      echo "<tr class=\"".get_row_bg()."\">";
      echo "<td><input type=\"checkbox\" name=\"deleteusers[]\" value=\"".$user_row[$user_table_fields['user_id']]."\" /></td>";
      $show_user_name = format_text($user_row[$user_table_fields['user_name']], 2);
      if ($user_row[$user_table_fields['user_id']] != GUEST && empty($url_show_profile)) {
        $show_user_name = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&".URL_USER_ID."=".$user_row[$user_table_fields['user_id']])."\" target=\"_blank\">$show_user_name</a>";
      }
      echo "<td><b>".$show_user_name."</b></td>\n";
      echo "<td>".$user_row[$user_table_fields['user_email']]."</td>\n";
      echo "<td>".format_date($config['date_format']." ".$config['time_format'], $user_row[$user_table_fields['user_joindate']])."</td>\n";
      echo "<td>".format_date($config['date_format']." ".$config['time_format'], $user_row[$user_table_fields['user_lastaction']])."</td>\n";
      echo "<td><p>";
      if (!defined('USER_INTEGRATION') || (defined('USER_INTEGRATION') && USER_INTEGRATION == "none")) {
        show_text_link($lang['edit'],"users.php?action=edituser&user_id=".$user_row[$user_table_fields['user_id']]);
      }
      show_text_link($lang['delete'],"users.php?action=removeuser&user_id=".$user_row[$user_table_fields['user_id']]);
      echo "&nbsp;&nbsp;";
      show_text_link($lang['permissions'], "usergroups.php?action=editpermissions&user_id=".$user_row[$user_table_fields['user_id']]);
      show_text_link($lang['nav_usergroups'], "usergroups.php?action=edituser&user_id=".$user_row[$user_table_fields['user_id']]);
      echo "</p></td>\n";
      echo "</tr>\n";
    }

    echo "<tr class=\"tablefooter\">\n<td colspan=\"6\" align=\"left\">\n&nbsp;";
    echo "<input type=\"submit\" value=\"  ".$lang['delete']."   \" class=\"button\">\n";
    echo "&nbsp;\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n</form>\n";
  }
  else {
    show_description_row($lang['no_search_results'], 6);
    show_form_footer("", "");
  }

  echo "<div align=\"right\">";
  echo "<form action=\"".$site_sess->url("users.php")."\" name=\"form2\" method=\"post\">\n";

  //if ($limitnumber != 5000 && $limitfinish < $countusers['users']) {
    show_hidden_input("action", "findusers");
    show_hidden_input("user_level", $user_level);
    show_hidden_input("user_name", $user_name, 1);
    show_hidden_input("user_email", $user_email, 1);
    show_hidden_input("dateafter", $dateafter);
    show_hidden_input("datebefore", $datebefore);
    show_hidden_input("lastactionafter", $lastactionafter);
    show_hidden_input("lastactionbefore", $lastactionbefore);
    show_hidden_input("orderby", $orderby, 1);
    show_hidden_input("direction", $direction, 1);
    show_hidden_input("limitstart", $limitstart + $limitnumber + 1);
    show_hidden_input("limitnumber", $limitnumber);

  if ($limitstart > 1) {
    echo "<input type=\"button\" value=\"   ".$lang['back']."   \" onclick=\"limitstart.value=limitstart.value-limitnumber.value*2;submit();\" class=\"button\">\n";
  }

  if ($limitnumber != 5000 && $limitfinish < $countusers['users']) {
    echo "<input type=\"submit\" value=\"   ".$lang['search_next_page']."   \" class=\"button\">\n";
  }
  // echo "<input type=\"button\" value=\"   ".$lang['back']."   \" onclick=\"history.go(-1)\" class=\"button\">\n";
  echo "</form>";
  echo "</div>";
}

if ($action == "saveusers") {
  $error = array();
  $num_newusers = $HTTP_POST_VARS['num_newusers'];
  for ($i = 1; $i <= $num_newusers; $i++) {
    $user_level = intval($HTTP_POST_VARS['user_level_'.$i]);
    $user_name = trim($HTTP_POST_VARS['user_name_'.$i]);
    $user_email = trim($HTTP_POST_VARS['user_email_'.$i]);
    $user_password = trim($HTTP_POST_VARS['user_password_'.$i]);

    if ($user_name == "") {
      $error['user_name_'.$i] = 1;
    }
    if ($user_password == "") {
      $error['user_password_'.$i] = 1;
    }
    if ($user_email == "" || !check_email($user_email)) {
      $error['user_email_'.$i] = 1;
    }
    if ($user_level == GUEST) {
      $error['user_level_'.$i] = 1;
    }

    if ($user_name != "") {
      $sql = "SELECT ".get_user_table_field("", "user_name")."
              FROM ".USERS_TABLE."
              WHERE ".get_user_table_field("", "user_name")." = '".strtolower($user_name)."'";
      if ($site_db->not_empty($sql)) {
        $msg .= sprintf("%s: <b>%s (%s %s)</b><br />", $lang['user_name_exists'], format_text($user_name, 2), $lang['user'], $i);
        $error['user_name_'.$i] = 1;
      }
    }

    if ($user_email != "") {
      $sql = "SELECT ".get_user_table_field("", "user_email")."
              FROM ".USERS_TABLE."
              WHERE ".get_user_table_field("", "user_email")." = '".strtolower($user_email)."'";
      if ($site_db->not_empty($sql)) {
        $msg .= sprintf("%s: <b>%s (%s %s)</b><br />", $lang['user_email_exists'], $user_email, $lang['user'], $i);
        $error['user_email_'.$i] = 1;
      }
    }

    if (!empty($additional_user_fields)) {
      foreach ($additional_user_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key.'_'.$i]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key.'_'.$i]) == "") {
          $error[$key.'_'.$i] = 1;
        }
      }
    }
  }
  if (empty($error)) {
    for ($i = 1; $i <= $num_newusers; $i++) {
      $log = array();
      $user_level = trim($HTTP_POST_VARS['user_level_'.$i]);
      $user_name = trim($HTTP_POST_VARS['user_name_'.$i]);
      $user_email = trim($HTTP_POST_VARS['user_email_'.$i]);
      $user_password = trim($HTTP_POST_VARS['user_password_'.$i]);
      $user_homepage = trim($HTTP_POST_VARS['user_homepage_'.$i]);
      $user_icq = intval(trim($HTTP_POST_VARS['user_icq_'.$i]));
      if (!$user_icq) {
      	$user_icq = "";
      }
      $user_showemail = intval($HTTP_POST_VARS['user_showemail_'.$i]);
      $user_allowemails = intval($HTTP_POST_VARS['user_allowemails_'.$i]);
      $user_invisible = intval($HTTP_POST_VARS['user_invisible_'.$i]);

      $activationkey = get_random_key(USERS_TABLE, get_user_table_field("", "user_activationkey"));
      $user_id = $site_db->get_next_id(get_user_table_field("", "user_id"), USERS_TABLE);

      $additional_field_sql = "";
      $additional_value_sql = "";
      if (!empty($additional_user_fields)) {
        $table_fields = $site_db->get_table_fields(USERS_TABLE);
        foreach ($additional_user_fields as $key => $val) {
          if (isset($HTTP_POST_VARS[$key.'_'.$i]) && isset($table_fields[$key])) {
            $additional_field_sql .= ", $key";
            $additional_value_sql .= ", '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key.'_'.$i]))."'";
          }
        }
      }

      $current_time = time();
      $user_password_hashed = salted_hash($user_password);
      //(user_id, user_level, user_name, user_password, user_email, user_showemail, user_allowemails, user_invisible, user_joindate, user_activationkey, user_lastaction, user_lastvisit, user_homepage, user_icq".$additional_field_sql.")
      $sql = "INSERT INTO ".USERS_TABLE."
              (".get_user_table_field("", "user_id").get_user_table_field(", ", "user_level").get_user_table_field(", ", "user_name").get_user_table_field(", ", "user_password").get_user_table_field(", ", "user_email").get_user_table_field(", ", "user_showemail").get_user_table_field(", ", "user_allowemails").get_user_table_field(", ", "user_invisible").get_user_table_field(", ", "user_joindate").get_user_table_field(", ", "user_activationkey").get_user_table_field(", ", "user_lastaction").get_user_table_field(", ", "user_lastvisit").get_user_table_field(", ", "user_comments").get_user_table_field(", ", "user_homepage").get_user_table_field(", ", "user_icq").$additional_field_sql.")
              VALUES
              ($user_id, $user_level, '$user_name', '$user_password_hashed', '$user_email', $user_showemail, $user_allowemails, $user_invisible, $current_time, '$activationkey', $current_time, $current_time, 0, '$user_homepage', '$user_icq'".$additional_value_sql.")";
      $result = $site_db->query($sql);
      if (empty($user_id)) {
        $user_id = $site_db->get_insert_id();
      }

      if ($result) {
        $sql = "INSERT INTO ".GROUPS_TABLE."
                (group_name, group_type)
                VALUES
                ('$user_name', ".GROUPTYPE_SINGLE.")";
        $site_db->query($sql);
        $group_id = $site_db->get_insert_id();
        $sql = "INSERT INTO ".GROUP_MATCH_TABLE."
                (group_id, user_id, groupmatch_startdate, groupmatch_enddate)
                VALUES
                ($group_id, $user_id, 0, 0)";
        $site_db->query($sql);
        $log[] = $lang['user_add_success']." : <b>".format_text($user_name, 2)."</b>";
      }
      else {
        $log[] = $lang['user_add_error'].": <b>".format_text($user_name, 2)."</b>";
      }
      show_table_header($lang['user']." ".$i, 1);
      echo "<tr><td class=\"tablerow\">\n";
      echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
      foreach ($log as $val) {
        echo $val."<br />";
      }
      echo "</td></tr></table>\n";
      echo "</td></tr>\n";
      show_table_footer();
      echo "<br />";
    }
  }
  else {
    $msg .= sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "addusers";
  }
}

if ($action == "addusers") {
  if (isset($HTTP_GET_VARS['num_newusers']) || isset($HTTP_POST_VARS['num_newusers'])) {
    $num_newusers = (isset($HTTP_GET_VARS['num_newusers'])) ? intval($HTTP_GET_VARS['num_newusers']) : intval($HTTP_POST_VARS['num_newusers']);
  }
  else {
    $num_newusers = 1;
  }

  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }

  show_form_header("users.php", "saveusers", "form", 1);
  show_table_header($lang['nav_users_add'], 2);
  show_num_select_row("&nbsp;", "num_newusers", $lang['num_newusers_desc']);

  for ($i = 1; $i <= $num_newusers; $i++) {
    show_table_separator($lang['user']." ".$i, 2);
    show_userlevel_select_row($lang['field_userlevel'], "user_level_".$i);
    show_input_row($lang['field_username'], "user_name_".$i, "", $textinput_size);
    show_input_row($lang['field_email'], "user_email_".$i, "", $textinput_size);
    show_input_row($lang['field_password'], "user_password_".$i, "", $textinput_size);
    show_input_row($lang['field_homepage'], "user_homepage_".$i, "", $textinput_size);
    show_input_row($lang['field_icq'], "user_icq_".$i, "", $textinput_size);
    show_radio_row($lang['field_showemail'], "user_showemail_".$i, 0);
    show_radio_row($lang['field_allowemails'], "user_allowemails_".$i, 1);
    show_radio_row($lang['field_invisible'], "user_invisible_".$i, 0);
    show_additional_fields("user", array(), USERS_TABLE, $i);
  }
  show_hidden_input("num_newusers", $num_newusers);
  show_form_footer($lang['add'], $lang['reset'], 2);
}

show_admin_footer();
?>