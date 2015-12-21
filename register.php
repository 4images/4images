<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: register.php                                         *
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

$main_template = 'register';

define('GET_CACHES', 1);
define('ROOT_PATH', './');
define('MAIN_SCRIPT', __FILE__);
include(ROOT_PATH.'global.php');
require(ROOT_PATH.'includes/sessions.php');
$user_access = get_permission();
include(ROOT_PATH.'includes/page_header.php');

if ($action == "") {
  $action = "signup";
}

if ($user_info['user_level'] != GUEST && $action != "activate") {
  show_error_page($lang['already_registered']);
}
$content = "";

//-----------------------------------------------------
//--- Signup ------------------------------------------
//-----------------------------------------------------
if ($action == "signup") {
  $site_template->register_vars(array(
    "lang_agreement" => $lang['agreement'],
    "lang_agreement_terms" => $lang['agreement_terms'],
    "lang_agree" => $lang['agree'],
    "lang_agree_not" => $lang['agree_not']
  ));
  $content = $site_template->parse_template("register_signup");
}

//-----------------------------------------------------
//--- Add New User ------------------------------------
//-----------------------------------------------------
if ($action == "register") {
  if (!isset($HTTP_POST_VARS['user_name'])) {
    if ($config['activation_time'] != 0) {
      $expiry = time() - 60 * 60 * 24 * $config['activation_time'];
      $sql = "DELETE FROM ".USERS_TABLE."
              WHERE (".get_user_table_field("", "user_lastaction")." < $expiry) AND ".get_user_table_field("", "user_level")." = ".USER_AWAITING;
      $site_db->query($sql);
    }
  }
  $user_name = (isset($HTTP_POST_VARS['user_name'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['user_name'])) : "";
  $user_name = preg_replace("/( ){2,}/", " ", $user_name);
  $user_password = (isset($HTTP_POST_VARS['user_password'])) ? trim($HTTP_POST_VARS['user_password']) : "";
  $user_email = (isset($HTTP_POST_VARS['user_email'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['user_email'])) : "";
  $user_showemail = (isset($HTTP_POST_VARS['user_showemail'])) ? intval($HTTP_POST_VARS['user_showemail']) : 0;
  $user_allowemails = (isset($HTTP_POST_VARS['user_allowemails'])) ? intval($HTTP_POST_VARS['user_allowemails']) : 1;
  $user_invisible = (isset($HTTP_POST_VARS['user_invisible'])) ? intval($HTTP_POST_VARS['user_invisible']) : 0;
  $user_homepage = (isset($HTTP_POST_VARS['user_homepage'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['user_homepage'])) : "";
  $user_icq = (isset($HTTP_POST_VARS['user_icq'])) ? ((intval(trim($HTTP_POST_VARS['user_icq']))) ? intval(trim($HTTP_POST_VARS['user_icq'])) : "") : "";

  $captcha = (isset($HTTP_POST_VARS['captcha'])) ? un_htmlspecialchars(trim($HTTP_POST_VARS['captcha'])) : "";

  $error = 0;
  if (isset($HTTP_POST_VARS['user_name'])) {
    if ($user_name != "") {
      $sql = "SELECT ".get_user_table_field("", "user_name")."
              FROM ".USERS_TABLE."
              WHERE ".get_user_table_field("", "user_name")." = '".strtolower($user_name)."'";
      if ($site_db->not_empty($sql)) {
        $msg .= (($msg != "") ? "<br />" : "").$lang['username_exists'];
        $error = 1;
      }
    }
    else {
      $msg .= (($msg != "") ? "<br />" : "").$field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['user_name']), $lang['field_required']);
      $error = 1;
    }

    if ($user_password == "") {
      $msg .= (($msg != "") ? "<br />" : "").$field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['password']), $lang['field_required']);
      $error = 1;
    }

    if ($user_email != "") {
      if (check_email($user_email)) {
        $sql = "SELECT ".get_user_table_field("", "user_email")."
                FROM ".USERS_TABLE."
                WHERE ".get_user_table_field("", "user_email")." = '".strtolower($user_email)."'";
        if ($site_db->not_empty($sql)) {
          $msg .= (($msg != "") ? "<br />" : "").$lang['email_exists'];
          $error = 1;
        }
      }
      else {
        $msg .= (($msg != "") ? "<br />" : "").$lang['invalid_email_format'];
        $error = 1;
      }
    }
    else {
      $msg .= (($msg != "") ? "<br />" : "").$field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $lang['email']), $lang['field_required']);
      $error = 1;
    }

    if ($captcha_enable_registration && !captcha_validate($captcha)) {
      $msg .= (($msg != "") ? "<br />" : "").$lang['captcha_required'];
      $error = 1;
    }

    if (!empty($additional_user_fields)) {
      foreach ($additional_user_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && intval($val[2]) == 1 && trim($HTTP_POST_VARS[$key]) == "") {
          $error = 1;
          $field_error = preg_replace("/".$site_template->start."field_name".$site_template->end."/siU", str_replace(":", "", $val[0]), $lang['field_required']);
          $msg .= (($msg != "") ? "<br />" : "").$field_error;
        }
      }
    }
  } // end if
  else {
    $error = 1;
  }

  if (!$error) {
    $additional_field_sql = "";
    $additional_value_sql = "";
    if (!empty($additional_user_fields)) {
      $table_fields = $site_db->get_table_fields(USERS_TABLE);
      foreach ($additional_user_fields as $key => $val) {
        if (isset($HTTP_POST_VARS[$key]) && isset($table_fields[$key])) {
          $additional_field_sql .= ", $key";
          $additional_value_sql .= ", '".un_htmlspecialchars(trim($HTTP_POST_VARS[$key]))."'";
        }
      }
    }
    $activationkey = get_random_key(USERS_TABLE, get_user_table_field("", $user_table_fields['user_activationkey']));
    $user_id = $site_db->get_next_id($user_table_fields['user_id'], USERS_TABLE);

    $current_time = time();
    $user_level = ($config['account_activation'] == 0) ? USER : USER_AWAITING;
    $user_password_hashed = salted_hash($user_password);
    $sql = "INSERT INTO ".USERS_TABLE."
            (".get_user_table_field("", "user_id").get_user_table_field(", ", "user_level").get_user_table_field(", ", "user_name").get_user_table_field(", ", "user_password").get_user_table_field(", ", "user_email").get_user_table_field(", ", "user_showemail").get_user_table_field(", ", "user_allowemails").get_user_table_field(", ", "user_invisible").get_user_table_field(", ", "user_joindate").get_user_table_field(", ", "user_activationkey").get_user_table_field(", ", "user_lastaction").get_user_table_field(", ", "user_lastvisit").get_user_table_field(", ", "user_comments").get_user_table_field(", ", "user_homepage").get_user_table_field(", ", "user_icq").$additional_field_sql.")
            VALUES
            ($user_id, $user_level, '$user_name', '$user_password_hashed', '$user_email', $user_showemail, $user_allowemails, $user_invisible, $current_time, '$activationkey', $current_time, $current_time, 0, '$user_homepage', '$user_icq'".$additional_value_sql.")";
    $result = $site_db->query($sql);

    if ($result) {
      $activation_url = $script_url."/register.php?action=activate&activationkey=".$activationkey;

      include(ROOT_PATH.'includes/email.php');
      $site_email = new Email();
      $site_email->set_to($user_email);
      $site_email->set_subject($lang['register_success_emailsubject']);
      $site_email->register_vars(array(
        "activation_url" => $activation_url,
        "user_name" => $user_name,
        "user_password" => $user_password,
        "site_name" => $config['site_name']
      ));

      switch($config['account_activation']) {
      case 2:
        $email_template = "register_activation_admin";
        $msg = $lang['register_success_admin'];
        break;
      case 1:
        if ($config['language_dir_default'] != $config['language_dir']) {
          $activation_url .= "&l=".$config['language_dir'];
        }
        $email_template = "register_activation";
        $msg = $lang['register_success'];
        break;
      case 0:
      default:
        $email_template = "register_activation_none";
        $msg = $lang['register_success_none'];
        break;
      }

      $site_email->set_body($email_template, $config['language_dir']);
      $site_email->send_email();
      if ($config['account_activation'] == 2) {
        $site_email->reset();
        $site_email->set_to($config['site_email']);
        $site_email->set_subject($lang['admin_activation_emailsubject']);
        $user_details_url = $script_url."/admin/index.php?goto=".urlencode("users.php?action=edituser&user_id=".$user_id."&activation=1");
        $site_email->register_vars("user_details_url", $user_details_url);
        $site_email->set_body("admin_activation", $config['language_dir_default']);
        $site_email->send_email();
      }
    }
    else {
      $msg = $lang['general_error'];
    }
  }

  if ($error) {
    if ($user_showemail == 1) {
      $user_showemail_yes = " checked=\"checked\"";
      $user_showemail_no = "";
    }
    else {
      $user_showemail_yes = "";
      $user_showemail_no = " checked=\"checked\"";
    }
    if ($user_allowemails == 1) {
      $user_allowemails_yes = " checked=\"checked\"";
      $user_allowemails_no = "";
    }
    else {
      $user_allowemails_yes = "";
      $user_allowemails_no = " checked=\"checked\"";
    }
    if ($user_invisible == 1) {
      $user_invisible_yes = " checked=\"checked\"";
      $user_invisible_no = "";
    }
    else {
      $user_invisible_yes = "";
      $user_invisible_no = " checked=\"checked\"";
    }
    $site_template->register_vars(array(
      "user_name" => format_text(stripslashes($user_name), 2),
      "user_email" => format_text(stripslashes($user_email), 2),
      "user_homepage" => format_text(stripslashes($user_homepage), 2),
      "user_icq" => $user_icq,
      "user_showemail_yes" => $user_showemail_yes,
      "user_showemail_no" => $user_showemail_no,
      "user_allowemails_yes" => $user_allowemails_yes,
      "user_allowemails_no" => $user_allowemails_no,
      "user_invisible_yes" => $user_invisible_yes,
      "user_invisible_no" => $user_invisible_no,
      "lang_user_name" => $lang['user_name'],
      "lang_password" => $lang['password'],
      "lang_email" => $lang['email'],
      "lang_register_msg" => $lang['register_msg'],
      "lang_submit" => $lang['submit'],
      "lang_reset" => $lang['reset'],
      "lang_email" => $lang['email'],
      "lang_show_email" => $lang['show_email'],
      "lang_allow_emails" => $lang['allow_emails'],
      "lang_invisible" => $lang['invisible'],
      "lang_optional_infos" => $lang['optional_infos'],
      "lang_homepage" => $lang['homepage'],
      "lang_icq" => $lang['icq'],
      "lang_yes" => $lang['yes'],
      "lang_no" => $lang['no'],
      "lang_captcha" => $lang['captcha'],
      "lang_captcha_desc" => $lang['captcha_desc'],
      "captcha_registration" => (bool)$captcha_enable_registration
    ));

    if (!empty($additional_user_fields)) {
      $additional_field_array = array();
      foreach ($additional_user_fields as $key => $val) {
        if ($val[1] == "radio") {
          $value = (isset($HTTP_POST_VARS[$key])) ? intval($HTTP_POST_VARS[$key]) : 1;
          if ($value == 1) {
            $additional_field_array[$key.'_yes'] = " checked=\"checked\"";
            $additional_field_array[$key.'_no'] = "";
          }
          else {
            $additional_field_array[$key.'_yes'] = "";
            $additional_field_array[$key.'_no'] = " checked=\"checked\"";
          }
        }
        else {
          $value = (isset($HTTP_POST_VARS[$key])) ? format_text(trim($HTTP_POST_VARS[$key]), 2) : "";
        }
        $additional_field_array[$key] = $value;
        $additional_field_array['lang_'.$key] = $val[0];
      }
      if (!empty($additional_field_array)) {
        $site_template->register_vars($additional_field_array);
      }
    }

    $content = $site_template->parse_template("register_form");
  }
}

if ($action == "activate") {
  if ($config['activation_time'] != 0) {
    $expiry = time() - 60 * 60 * 24 * $config['activation_time'];
    $sql = "DELETE FROM ".USERS_TABLE."
            WHERE (".get_user_table_field("", "user_lastaction")." < $expiry) AND ".get_user_table_field("", "user_level")." = ".USER_AWAITING;
    $site_db->query($sql);
  }
  if (!isset($HTTP_GET_VARS['activationkey'])){
    $msg = $lang['missing_activationkey'];
  }
  else {
    if ($config['account_activation'] == 2 && $user_info['user_level'] != ADMIN) {
      show_error_page($lang['no_permission']);
      exit;
    }
    $activationkey = trim($HTTP_GET_VARS['activationkey']);
    $sql = "SELECT ".get_user_table_field("", "user_name").get_user_table_field(", ", "user_email").get_user_table_field(", ", "user_activationkey")."
            FROM ".USERS_TABLE."
            WHERE ".get_user_table_field("", "user_activationkey")." = '$activationkey'";
    $row = $site_db->query_firstrow($sql);
    if (!$row) {
      $msg = $lang['invalid_activationkey'];
    }
    else {
      $sql = "UPDATE ".USERS_TABLE."
              SET ".get_user_table_field("", "user_level")." = ".USER."
              WHERE ".get_user_table_field("", "user_activationkey")." = '$activationkey'";
      $site_db->query($sql);
      $msg = $lang['activation_success'];

      if ($config['account_activation'] == 2) {
        include(ROOT_PATH.'includes/email.php');
        $site_email = new Email();
        $site_email->set_to($row[$user_table_fields['user_email']]);
        $site_email->set_subject($lang['activation_success_emailsubject']);
        $site_email->register_vars(array(
          "user_name" => $row[$user_table_fields['user_name']],
          "site_name" => $config['site_name']
        ));
        $site_email->set_body("activation_success", $config['language_dir']);
        $site_email->send_email();
      }
    }
  }
}

//-----------------------------------------------------
//--- Clickstream -------------------------------------
//-----------------------------------------------------
$clickstream = "<span class=\"clickstream\"><a href=\"".$site_sess->url(ROOT_PATH."index.php")."\" class=\"clickstream\">".$lang['home']."</a>".$config['category_separator'].$lang['register']."</span>";

//-----------------------------------------------------
//--- Print Out ---------------------------------------
//-----------------------------------------------------
$site_template->register_vars(array(
  "content" => $content,
  "msg" => $msg,
  "clickstream" => $clickstream,
  "lang_register" => $lang['register']
));
$site_template->print_template($site_template->parse_template($main_template));
include(ROOT_PATH.'includes/page_footer.php');
?>
