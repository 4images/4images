<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: comments.php                                         *
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

if ($action == "") {
  $action = "modifycomments";
}

$orderbyOptions = array(
  'i.image_name' => $lang['field_image_name'],
  'c.image_id' => $lang['image'] . ' ID',
  'c.user_name' => $lang['field_username'],
  'c.comment_headline' => $lang['field_headline'],
  'c.comment_date' => $lang['field_date'],
);

function delete_comments($comment_ids) {
  global $site_db, $lang;
  if (empty($comment_ids)) {
    echo $lang['no_search_results'];
    return false;
  }
  $error_log = array();
  echo "<br />";
  $sql = "SELECT comment_id, image_id, user_id, user_name, comment_headline
          FROM ".COMMENTS_TABLE."
          WHERE comment_id IN ($comment_ids)";
  $comment_result = $site_db->query($sql);
  while ($comment_row = $site_db->fetch_array($comment_result)) {
    $sql = "DELETE FROM ".COMMENTS_TABLE."
          WHERE comment_id = ".$comment_row['comment_id'];
    $del_comment = $site_db->query($sql);

    if ($del_comment) {
      update_comment_count($comment_row['image_id'], $comment_row['user_id']);
      echo "<b>".$lang['comment_delete_success'].":</b> ".format_text($comment_row['comment_headline'], 2)." (".$lang['user'].": ".format_text($comment_row['user_name'], 2).")<br />\n";
    }
    else {
      $error_log[] = "<b>".$lang['comment_delete_error'].":</b> ".format_text($comment_row['comment_headline'], 2)." (".$lang['user'].": ".format_text($comment_row['user_name'], 2).")";
    }
    echo "<br />\n";
  }
  return $error_log;
}

show_admin_header();

if ($action == "deletecomment") {
  $deletecomments = (isset($HTTP_POST_VARS['deletecomments'])) ? $HTTP_POST_VARS['deletecomments'] : array();
  $comment_ids = "";
  if (!empty($deletecomments)) {
    foreach ($deletecomments as $val) {
      $comment_ids .= (($comment_ids != "") ? ", " : "").$val;
    }
  }
  $lang_key = (sizeof($deletecomments) > 1) ? 'comments' : 'comment';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 1);
  echo "<tr><td class=\"tablerow\">\n";
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
  $error_log = delete_comments($comment_ids);
  echo "</td></tr></table>\n";
  echo "</td></tr>\n";
  show_table_footer();
  if (!empty($error_log)) {
    show_table_header("Error Log:", 1);
    echo "<tr><td class=\"tablerow\">\n";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
    echo "<b>".$lang['error_log_desc']."</b>\n<ul>\n";
    foreach ($error_log as $val) {
      printf("<li>%s</li>\n", $val);
    }
    echo "</ul>\n</td></tr></table>\n";
    echo "</td></tr>\n";
    show_table_footer();
  }
  echo "<p>";
  show_text_link($lang['back_overview'], "comments.php?action=modifycomments");
}

if ($action == "removecomment") {
  $comment_ids = array();
  if (isset($HTTP_GET_VARS['comment_id']) || isset($HTTP_POST_VARS['comment_id'])) {
    $comment_id = (isset($HTTP_GET_VARS['comment_id'])) ? intval($HTTP_GET_VARS['comment_id']) : intval($HTTP_POST_VARS['comment_id']);
    $comment_ids[] = $comment_id;
  }
  elseif (isset($HTTP_POST_VARS['deletecomments'])) {
    $comment_ids = $HTTP_POST_VARS['deletecomments'];
  }
  else {
   $comment_ids[] = 0;
  }

  show_form_header("comments.php", "deletecomment");
  foreach ($comment_ids as $val) {
    show_hidden_input("deletecomments[]", $val);
  }
  $lang_key = (sizeof($comment_ids) > 1) ? 'comments' : 'comment';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 2);
  show_description_row($lang['delete_comment_confirm']);
  show_form_footer($lang['yes'], "", 2, $lang['no']);
}

if ($action == "updatecomment") {
  $error = array();

  $comment_id = (isset($HTTP_POST_VARS['comment_id'])) ? intval($HTTP_POST_VARS['comment_id']) : intval($HTTP_GET_VARS['comment_id']);
  $user_name = trim($HTTP_POST_VARS['user_name']);
  $comment_headline = trim($HTTP_POST_VARS['comment_headline']);
  $comment_text = trim($HTTP_POST_VARS['comment_text']);
  $comment_ip = trim($HTTP_POST_VARS['comment_ip']);
  $comment_date = trim($HTTP_POST_VARS['comment_date']);

  if ($user_name == "") {
    $error['user_name'] = 1;
  }
  if ($comment_headline == "") {
    $error['comment_headline'] = 1;
  }
  if ($comment_text == "") {
    $error['comment_text'] = 1;
  }
  if (empty($error)) {
    if ($comment_date == "") {
      $comment_date = time();
    }
    else {
      $comment_date = "UNIX_TIMESTAMP('$comment_date')";
    }

    $sql = "UPDATE ".COMMENTS_TABLE."
            SET user_name = '$user_name', comment_headline = '$comment_headline', comment_text = '$comment_text', comment_ip = '$comment_ip', comment_date = $comment_date
            WHERE comment_id = $comment_id";
    $result = $site_db->query($sql);

    $msg = ($result) ? $lang['comment_edit_success'] : $lang['comment_edit_error'];
  }
  else {
    $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
  }
  $action = "editcomment";
}

if ($action == "editcomment") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  $comment_id = (isset($HTTP_POST_VARS['comment_id'])) ? intval($HTTP_POST_VARS['comment_id']) : intval($HTTP_GET_VARS['comment_id']);

  $sql = "SELECT *, FROM_UNIXTIME(comment_date) AS comment_date
          FROM ".COMMENTS_TABLE."
          WHERE comment_id = $comment_id";
  $comment = $site_db->query_firstrow($sql);

  show_form_header("comments.php", "updatecomment", "form", 1);
  show_hidden_input("comment_id", $comment_id);
  show_table_header($lang['nav_comments_edit'].": ".format_text($comment['comment_headline'], 2), 2);
  show_input_row($lang['field_username'], "user_name", $comment['user_name'], $textinput_size);
  show_input_row($lang['field_headline'], "comment_headline", $comment['comment_headline'], $textinput_size);
  show_textarea_row($lang['field_comment'], "comment_text", $comment['comment_text'], $textarea_size);
  show_input_row($lang['field_ip'], "comment_ip", $comment['comment_ip'], $textinput_size);
  show_date_input_row($lang['field_date'].$lang['date_format'].$lang['date_desc'], "comment_date", $comment['comment_date'], $textinput_size);
  show_form_footer($lang['save_changes'], $lang['reset'], 2);
}

if ($action == "modifycomments") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }

  show_form_header("comments.php", "findcomments", "form");
  show_table_header($lang['nav_comments_edit'], 2);
  show_input_row($lang['field_image_id_contains'], "image_id", "", $textinput_size);
  show_input_row($lang['field_image_name_contains'], "image_name", "", $textinput_size);
  show_input_row($lang['field_username_contains'], "user_name", "", $textinput_size);
  show_input_row($lang['field_headline_contains'], "comment_headline", "", $textinput_size);
  show_input_row($lang['field_comment_contains'], "comment_text", "", $textinput_size);
  show_date_input_row($lang['field_date_after'].$lang['date_format'], "dateafter", "", $textinput_size);
  show_date_input_row($lang['field_date_before'].$lang['date_format'], "datebefore", "", $textinput_size);
  show_table_separator($lang['sort_options'], 2);
  ?>
  <tr class="<?php echo get_row_bg(); ?>"><td><p><b><?php echo $lang['order_by'] ?></b></p></td><td><p>
  <select name="orderby">
  <?php foreach ($orderbyOptions as $field => $label): ?>
  <option value="<?php echo $field; ?>"><?php echo $label; ?></option>
  <?php endforeach; ?>
  </select>
  <select name="direction">
  <option selected value="ASC"><?php echo $lang['asc'] ?></option>
  <option value="DESC"><?php echo $lang['desc'] ?></option>
  </select>
  </p></td></tr>
  <?php
  show_input_row($lang['results_per_page'], "limitnumber", 50);
  show_form_footer($lang['search'], $lang['reset'], 2);
}

if ($action == "findcomments") {

  $condition = "1=1";

  $image_name = trim($HTTP_POST_VARS['image_name']);
  if ($image_name != "") {
    $condition .= " AND INSTR(LCASE(i.image_name),'".strtolower($image_name)."')>0";
  }
  $image_id = intval($HTTP_POST_VARS['image_id']);
  if ($image_id != 0) {
    $condition .= " AND INSTR(LCASE(c.image_id),'".strtolower($image_id)."')>0";
  }
  $user_name = trim($HTTP_POST_VARS['user_name']);
  if ($user_name != "") {
    $condition .= " AND INSTR(LCASE(c.user_name),'".strtolower($user_name)."')>0";
  }
  $comment_headline = trim($HTTP_POST_VARS['comment_headline']);
  if ($comment_headline != "") {
    $condition .= " AND INSTR(LCASE(c.comment_headline),'".strtolower($comment_headline)."')>0";
  }
  $comment_text = trim($HTTP_POST_VARS['comment_text']);
  if ($comment_text != "") {
    $condition .= " AND INSTR(LCASE(c.comment_text),'".strtolower($comment_text)."')>0";
  }
  $dateafter = trim($HTTP_POST_VARS['dateafter']);
  if ($dateafter != "") {
    $condition .= " AND c.comment_date > UNIX_TIMESTAMP('$dateafter')";
  }
  $datebefore = trim($HTTP_POST_VARS['datebefore']);
  if ($datebefore != "") {
    $condition .= " AND c.comment_date < UNIX_TIMESTAMP('$datebefore')";
  }
  $orderby = trim($HTTP_POST_VARS['orderby']);
  if (!isset($orderbyOptions[$orderby])) {
    $orderby = "i.image_name";
  }
  $limitstart = (isset($HTTP_POST_VARS['limitstart'])) ? trim($HTTP_POST_VARS['limitstart']) : "";
  if ($limitstart == "") {
    $limitstart = 0;
  }
  else {
    $limitstart--;
  }
  $limitnumber = trim($HTTP_POST_VARS['limitnumber']);
  if ($limitnumber == "") {
    $limitnumber = 5000;
  }

  $direction = "ASC";
  if (isset($HTTP_GET_VARS['direction']) || isset($HTTP_POST_VARS['direction'])) {
    $requestedDirection = (isset($HTTP_GET_VARS['direction'])) ? trim($HTTP_GET_VARS['direction']) : trim($HTTP_POST_VARS['direction']);

    if ('DESC' === $requestedDirection) {
      $direction = "DESC";
    }
  }

  $sql = "SELECT COUNT(*) AS comments
          FROM (".COMMENTS_TABLE." c , ".IMAGES_TABLE." i)
          WHERE $condition AND c.image_id = i.image_id";
  $countcomments = $site_db->query_firstrow($sql);

  $limitfinish = $limitstart + $limitnumber;

  $start = 0;
  if ($countcomments['comments'] > 0) {
    $start = $limitstart + 1;
  }

  echo $lang['found']." <b>".$countcomments['comments']."</b> ".$lang['showing']." <b>$start</b>-";
  if ($limitfinish > $countcomments['comments'] == 0) {
    echo "<b>$limitfinish</b>.";
  }
  else {
    echo "<b>".$countcomments['comments']."</b>.";
  }

  show_form_header("comments.php", "removecomment", "form");
  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  if ($countcomments['comments'] > 0) {
    $sql = "SELECT c.comment_id, c.image_id, c.user_id, c.user_name, c.comment_headline, c.comment_text, c.comment_date, i.image_name
            FROM (".COMMENTS_TABLE." c, ".IMAGES_TABLE." i)
            WHERE $condition AND c.image_id = i.image_id
            ORDER BY $orderby $direction
            LIMIT $limitstart, $limitnumber";
    $result = $site_db->query($sql);
    echo "<tr class=\"tableseparator\">\n";
    echo "<td class=\"tableseparator\"><input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\" /></td>\n";
    echo "<td class=\"tableseparator\">".$lang['field_comment']."</td>\n<td class=\"tableseparator\">".$lang['field_username']."</td>\n<td class=\"tableseparator\">".$lang['image']."</td>\n<td class=\"tableseparator\">".$lang['field_date']."</td>\n<td class=\"tableseparator\">".$lang['options']."</td>\n</tr>\n";
    while ($comment_row = $site_db->fetch_array($result)) {
      echo "<tr class=\"".get_row_bg()."\">";
      echo "<td><input type=\"checkbox\" name=\"deletecomments[]\" value=\"".$comment_row['comment_id']."\" /></td>";
      $show_comment = "<b>".format_text($comment_row['comment_headline'])."</b><br />";
      if (strlen($comment_row['comment_text']) > 75) {
        $comment_row['comment_text'] = substr($comment_row['comment_text'], 0, 75)."...";
      }
      $show_comment .= format_text($comment_row['comment_text']);
      echo "<td>".$show_comment."</td>\n";
      $show_user_name = format_text($comment_row['user_name'], 2);
      if ($comment_row['user_id'] != GUEST && empty($url_show_profile)) {
        $show_user_name = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&".URL_USER_ID."=".$comment_row['user_id'])."\" target=\"_blank\">$show_user_name</a>";
      }
      echo "<td>".$show_user_name."</td>\n";
      $show_image = "<a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$comment_row['image_id'])."\" target=\"_blank\">".format_text($comment_row['image_name'], 2)."</a> (ID: ".$comment_row['image_id'].")";
      echo "<td>".$show_image."</td>\n";
      echo "<td>".format_date($config['date_format']." ".$config['time_format'], $comment_row['comment_date'])."</td>\n";
      echo "<td><p>";
      show_text_link($lang['edit'], "comments.php?action=editcomment&comment_id=".$comment_row['comment_id']);
      show_text_link($lang['delete'], "comments.php?action=removecomment&comment_id=".$comment_row['comment_id']);
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
  echo "<form action=\"".$site_sess->url("comments.php")."\" name=\"form2\" method=\"post\">\n";

  if ($limitnumber != 5000 && $limitfinish < $countcomments['comments']) {
    show_hidden_input("action", "findcomments");
    show_hidden_input("image_id", $image_id);
    show_hidden_input("image_name", $image_name, 1);
    show_hidden_input("user_name", $user_name, 1);
    show_hidden_input("comment_headline", $comment_headline, 1);
    show_hidden_input("comment_text", $comment_text, 1);
    show_hidden_input("dateafter", $dateafter);
    show_hidden_input("datebefore", $datebefore);

    show_hidden_input("orderby", $orderby, 1);
    show_hidden_input("direction", $direction, 1);
    show_hidden_input("limitstart", $limitstart + $limitnumber + 1);
    show_hidden_input("limitnumber", $limitnumber);

    echo "<input type=\"submit\" value=\"   ".$lang['search_next_page']."   \" class=\"button\">\n";
  }
  echo "<input type=\"button\" value=\"   ".$lang['back']."   \" onclick=\"history.go(-1)\" class=\"button\">\n";
  echo "</form>";
  echo "</div>";
}
show_admin_footer();
?>