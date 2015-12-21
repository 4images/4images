<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: email.php                                            *
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

$nozip = 1;
define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

if ($action == "") {
  $action = "emailusers";
}

show_admin_header();

if ($action == "sendemails") {
  $error = array();
  $subject = stripslashes(trim($HTTP_POST_VARS['subject']));
  $message = stripslashes(trim($HTTP_POST_VARS['message']));

  if ($subject == "") {
    $error['subject'] = 1;
  }
  if ($message == "") {
    $error['message'] = 1;
  }
  if (!isset($HTTP_POST_VARS['emails']) || empty($HTTP_POST_VARS['emails'])) {
    $error['emails'] = 1;
  }
  if (empty($error)) {
    @set_time_limit(1200);
    include(ROOT_PATH.'includes/email.php');
    $site_email = new Email();
    $site_email->set_to($config['site_email']);
    $site_email->set_subject($subject);
    $site_email->register_vars(array(
      "message" => $message,
      "site_email" => $config['site_email'],
      "site_name" => $config['site_name']
    ));
    $site_email->set_body("admin_email", $config['language_dir']);
    $emails = $HTTP_POST_VARS['emails'];
    $site_email->set_bcc($emails);
    echo ($site_email->send_email()) ? $lang['send_emails_success'] : $lang['send_emails_error'];
    echo "<p>";
    show_text_link($lang['back'], "javascript:history.back(1)");
  }
  else {
    $msg = sprintf("<span class=\"marktext\">%s</span>", $lang['lostfield_error']);
    $action = "emailusers";
  }
}

if ($action == "emailusers") {
  if ($msg != "") {
    printf("<b>%s</b>\n", $msg);
  }
  show_form_header("email.php", "sendemails");
  show_table_header($lang['send_emails'], 2);
  show_input_row($lang['send_emails_subject'], "subject", "", 45);
  show_textarea_row($lang['send_emails_message'], "message", "", 60, 20);

  $select = "<select name=\"emails[]\" size=\"15\" multiple=\"multiple\">\n";

  $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_level").get_user_table_field(", ", "user_name").get_user_table_field(", ", "user_email")."
          FROM ".USERS_TABLE."
          WHERE ".get_user_table_field("", "user_id")." <> ".GUEST." AND ".get_user_table_field("", "user_allowemails")." = 1
          ORDER BY ".get_user_table_field("", "user_level")." DESC";
  $result = $site_db->query($sql);

  $level = 1000;
  while ($row = $site_db->fetch_array($result)) {
    $user_level = $row[$user_table_fields['user_level']];
    if ($level != $user_level && $user_level == ADMIN) {
      $select .= "<option value=\"0\">__________________________</option>\n";
      $select .= "<option value=\"0\" class=\"dropdownmarker\">".$lang['userlevel_admin']."</option>\n";
    }
    elseif ($level != $user_level && $user_level == USER) {
      $select .= "<option value=\"0\">__________________________</option>\n";
      $select .= "<option value=\"0\" class=\"dropdownmarker\">".$lang['userlevel_registered']."</option>\n";
    }
    elseif ($level != $user_level && $user_level == USER_AWAITING) {
      $select .= "<option value=\"0\">__________________________</option>\n";
      $select .= "<option value=\"0\" class=\"dropdownmarker\">".$lang['userlevel_registered_awaiting']."</option>\n";
    }
    $user_email = $row[$user_table_fields['user_email']];
    $user_name = $row[$user_table_fields['user_name']];
    $selected = (isset($HTTP_POST_VARS['emails']) && !in_array($user_email, $HTTP_POST_VARS['emails'])) ? "" : " selected=\"selected\"";
    $select .= "<option value=\"".$user_email."\"".$selected.">&raquo; ".format_text($user_name, 2)." (".$user_email.")</option>\n";
    $level = $user_level;
  }
  $select .= "</select>\n";

  $title = $lang['select_email_user'];
  if (isset($error['emails'])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  show_custom_row($title, $select);
  show_form_footer($lang['send_emails'], "", 2);
}

show_admin_footer();
?>