<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: sessions.php                                         *
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
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

//-----------------------------------------------------
//--- Start Configuration -----------------------------
//-----------------------------------------------------

define('SESSION_NAME', 'sessionid');

$user_table_fields = array(
  "user_id" => "user_id",
  "user_level" => "user_level",
  "user_name" => "user_name",
  "user_password" => "user_password",
  "user_email" => "user_email",
  "user_showemail" => "user_showemail",
  "user_allowemails" => "user_allowemails",
  "user_invisible" => "user_invisible",
  "user_joindate" => "user_joindate",
  "user_activationkey" => "user_activationkey",
  "user_lastaction" => "user_lastaction",
  "user_location" => "user_location",
  "user_lastvisit" => "user_lastvisit",
  "user_comments" => "user_comments",
  "user_homepage" => "user_homepage",
  "user_icq" => "user_icq"
);

//-----------------------------------------------------
//--- End Configuration -------------------------------
//-----------------------------------------------------

function get_user_table_field($add, $user_field) {
  global $user_table_fields;
  return (!empty($user_table_fields[$user_field])) ? $add.$user_table_fields[$user_field] : "";
}

class Session {

  var $session_id;
  var $session_key;
  var $user_ip;
  var $user_location;
  var $current_time;
  var $session_timeout;
  var $mode = "get";
  var $session_info = array();
  var $user_info = array();

  function Session() {
    global $config;
    $this->session_timeout = $config['session_timeout'] * 60;
    $this->user_ip = $this->get_user_ip();
    $this->user_location = $this->get_user_location();
    $this->current_time = time();

    if (defined('SESSION_KEY') && SESSION_KEY != '') {
        $this->session_key = SESSION_KEY;
    } else {
        $this->session_key = md5('4images' . realpath(ROOT_PATH));
    }

    // Stop adding SID to URLs
    @ini_set('session.use_trans_sid', 0);

    //@ini_set('session.cookie_lifetime', $this->session_timeout);

    session_name(urlencode(SESSION_NAME));
    @session_start();

    $this->demand_session();
  }

  function set_cookie_data($name, $value, $permanent = 1) {
    $cookie_expire = ($permanent) ? $this->current_time + 60 * 60 * 24 * 365 : 0;
    $cookie_name = COOKIE_NAME.$name;
    setcookie($cookie_name, $value, $cookie_expire, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
    $HTTP_COOKIE_VARS[$cookie_name] = $value;
  }

  function read_cookie_data($name) {
    global $HTTP_COOKIE_VARS;
    $cookie_name = COOKIE_NAME.$name;
    return (isset($HTTP_COOKIE_VARS[$cookie_name])) ? $HTTP_COOKIE_VARS[$cookie_name] : false;
  }

  function get_session_id() {
    if (SID == '') {
      $this->mode = "cookie";
    }

    if (preg_match('/[^a-z0-9]+/i', session_id())) {
      @session_regenerate_id();
    }

    $this->session_id = session_id();
  }

  function demand_session() {
    $this->get_session_id();
    if (!$this->load_session_info()) {
      $this->delete_old_sessions();
      $user_id = ($this->read_cookie_data("userid")) ? intval($this->read_cookie_data("userid")) : GUEST;
      $this->start_session($user_id);
    }
    else {
      $this->user_info = $this->load_user_info($this->session_info['session_user_id']);
      $update_cutoff = ($this->user_info['user_id'] != GUEST) ? $this->current_time - $this->user_info['user_lastaction'] : $this->current_time - $this->session_info['session_lastaction'];
      if ($update_cutoff > 60) {
        $this->update_session();
        $this->delete_old_sessions();
      }
    }
  }

  function start_session($user_id = GUEST, $login_process = 0) {
    global $site_db;

    $this->user_info = $this->load_user_info($user_id);
    if ($this->user_info['user_id'] != GUEST && !$login_process) {
      if (secure_compare($this->read_cookie_data("userpass"), md5($this->user_info['user_password'])) && $this->user_info['user_level'] > USER_AWAITING) {
        $this->set_cookie_data("userpass", md5($this->user_info['user_password']));
      }
      else {
        $this->set_cookie_data("userpass", "", 0);
        $this->user_info = $this->load_user_info(GUEST);
      }
    }

    //if (!$login_process) {
      $sql = "REPLACE INTO ".SESSIONS_TABLE."
              (session_id, session_user_id, session_lastaction, session_location, session_ip)
              VALUES
              ('".addslashes($this->session_id)."', ".$this->user_info['user_id'].", $this->current_time, '$this->user_location', '$this->user_ip')";
      $site_db->query($sql);
    //}

    $this->session_info['session_user_id'] = $this->user_info['user_id'];
    $this->session_info['session_lastaction'] = $this->current_time;
    $this->session_info['session_location'] = $this->user_location;
    $this->session_info['session_ip'] = $this->user_ip;

    if ($this->user_info['user_id'] != GUEST) {
      $this->user_info['user_lastvisit'] = (!empty($this->user_info['user_lastaction'])) ? $this->user_info['user_lastaction'] : $this->current_time;
      $sql = "UPDATE ".USERS_TABLE."
              SET ".get_user_table_field("", "user_lastaction")." = $this->current_time, ".get_user_table_field("", "user_location")." = '$this->user_location', ".get_user_table_field("", "user_lastvisit")." = ".$this->user_info['user_lastvisit']."
              WHERE ".get_user_table_field("", "user_id")." = ".$this->user_info['user_id'];
      $site_db->query($sql);
    }
    $this->set_cookie_data("lastvisit", $this->user_info['user_lastvisit']);
    $this->set_cookie_data("userid", $this->user_info['user_id']);
    return true;
  }

  function login($user_name = "", $user_password = "", $auto_login = 0, $set_auto_login = 1) {
    global $site_db, $user_table_fields;

    if (empty($user_name) || empty($user_password)) {
      return false;
    }
    $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_password")."
            FROM ".USERS_TABLE."
            WHERE ".get_user_table_field("", "user_name")." = '$user_name' AND ".get_user_table_field("", "user_level")." <> ".USER_AWAITING;
    $row = $site_db->query_firstrow($sql);

    $user_id = (isset($row[$user_table_fields['user_id']])) ? $row[$user_table_fields['user_id']] : GUEST;
    if ($user_id != GUEST) {
      if (compare_passwords($user_password, $row[$user_table_fields['user_password']])) {
        $sql = "UPDATE ".SESSIONS_TABLE."
                SET session_user_id = $user_id
                WHERE session_id = '".addslashes($this->session_id)."'";
        $site_db->query($sql);
        if ($set_auto_login) {
          $this->set_cookie_data("userpass", ($auto_login) ? md5($row[$user_table_fields['user_password']]) : "");
        }
        $this->start_session($user_id, 1);
        return true;
      }
    }
    return false;
  }

  function logout($user_id) {
    global $site_db;
    $sql = "DELETE FROM ".SESSIONS_TABLE."
            WHERE session_id = '".addslashes($this->session_id)."' OR session_user_id = $user_id";
    $site_db->query($sql);
    $this->set_cookie_data("userpass", "", 0);
    $this->set_cookie_data("userid", GUEST);

    $this->session_info = array();

    return true;
  }

  function delete_old_sessions() {
    global $site_db;
    $expiry_time = $this->current_time - $this->session_timeout;
    $sql = "DELETE FROM ".SESSIONS_TABLE."
            WHERE session_lastaction < $expiry_time";
    $site_db->query($sql);

    return true;
  }

  function update_session() {
    global $site_db;

    $sql = "REPLACE INTO ".SESSIONS_TABLE."
           (session_id, session_user_id, session_lastaction, session_location, session_ip)
           VALUES
           ('".addslashes($this->session_id)."', ".$this->user_info['user_id'].", $this->current_time, '$this->user_location', '$this->user_ip')";
    $site_db->query($sql);

    $this->session_info['session_lastaction'] = $this->current_time;
    $this->session_info['session_location'] = $this->user_location;
    $this->session_info['session_ip'] = $this->user_ip;

    if ($this->user_info['user_id'] != GUEST) {
      $sql = "UPDATE ".USERS_TABLE."
              SET ".get_user_table_field("", "user_lastaction")." = $this->current_time, ".get_user_table_field("", "user_location")." = '$this->user_location'
              WHERE ".get_user_table_field("", "user_id")." = ".$this->user_info['user_id'];
      $site_db->query($sql);
    }
    return;
  }

  function return_session_info() {
    return $this->session_info;
  }

  function return_user_info() {
    return $this->user_info;
  }

  function freeze() {
    return;
  }

  function load_session_info() {
    $register_globals = strtolower(@ini_get('register_globals'));
    if ($register_globals && $register_globals != "off" && $register_globals != "false") {
      session_register($this->session_key);

      if (!isset($GLOBALS[$this->session_key])) {
        $GLOBALS[$this->session_key] = array();
      }

      $this->session_info = &$GLOBALS[$this->session_key];

    } else {
      if (isset($_SESSION)) {
        if (!isset($_SESSION[$this->session_key])) {
          $_SESSION[$this->session_key] = array();
        }

        $this->session_info = &$_SESSION[$this->session_key];

      } else {
        if (!isset($GLOBALS['HTTP_SESSION_VARS'][$this->session_key])) {
          $GLOBALS['HTTP_SESSION_VARS'][$this->session_key] = array();
        }

        $this->session_info = &$GLOBALS['HTTP_SESSION_VARS'][$this->session_key];
      }
    }

    if (!isset($this->session_info['session_ip'])) {
      $this->session_info = array();
      return false;
    }

    if ($this->mode == "get" && $this->session_info['session_ip'] != $this->user_ip) {
      if (function_exists('session_regenerate_id')) {
        @session_regenerate_id();
      }
      $this->get_session_id();
      $this->session_info = array();
      return false;
    }

    return $this->session_info;
  }

  function load_user_info($user_id = GUEST) {
    global $site_db, $user_table_fields, $additional_user_fields;

    if ($user_id != GUEST) {
      $sql = "SELECT u.*, l.*
              FROM ".USERS_TABLE." u, ".LIGHTBOXES_TABLE." l
              WHERE ".get_user_table_field("u.", "user_id")." = $user_id AND l.user_id = ".get_user_table_field("u.", "user_id");
      $user_info = $site_db->query_firstrow($sql);
      if (!$user_info) {
        $sql = "SELECT *
                FROM ".USERS_TABLE."
                WHERE ".get_user_table_field("", "user_id")." = $user_id";
        $user_info = $site_db->query_firstrow($sql);
        if ($user_info) {
          $lightbox_id = get_random_key(LIGHTBOXES_TABLE, "lightbox_id");
          $sql = "INSERT INTO ".LIGHTBOXES_TABLE."
                  (lightbox_id, user_id, lightbox_lastaction, lightbox_image_ids)
                  VALUES
                  ('$lightbox_id', ".$user_info[$user_table_fields['user_id']].", $this->current_time, '')";
          $site_db->query($sql);
          $user_info['lightbox_lastaction'] = $this->current_time;
          $user_info['lightbox_image_ids'] = "";
        }
      }
    }
    if (empty($user_info[$user_table_fields['user_id']])) {
      $user_info = array();
      $user_info['user_id'] = GUEST;
      $user_info['user_level'] = GUEST;
      $user_info['user_lastaction'] = $this->current_time;
      $user_info['user_lastvisit'] = ($this->read_cookie_data("lastvisit")) ? $this->read_cookie_data("lastvisit") : $this->current_time;
    }
    foreach ($user_table_fields as $key => $val) {
      if (isset($user_info[$val])) {
        $user_info[$key] = $user_info[$val];
      }
      elseif (!isset($user_info[$key])) {
        $user_info[$key] = "";
      }
    }
    foreach ($additional_user_fields as $key => $val)
    {
      if (!isset($user_info[$key]))
      {
        $user_info[$key] = "";
      }
    }
    return $user_info;
  }

  function set_session_var($var_name, $value) {
    $this->session_info[$var_name] = $value;
    return true;
  }

  function get_session_var($var_name) {
    if (isset($this->session_info[$var_name])) {
      return $this->session_info[$var_name];
    }

    return '';
  }

  function drop_session_var($var_name) {
    unset($this->session_info[$var_name]);
  }

  function get_user_ip() {
    global $HTTP_SERVER_VARS, $HTTP_ENV_VARS;
    $ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv("REMOTE_ADDR"));
    $ip = preg_replace("/[^\.0-9]+/", "", $ip);
    return substr($ip, 0, 50);
  }

  function get_user_location() {
    global $self_url;
    return (defined("IN_CP")) ? "Control Panel" : preg_replace(array("/([?|&])action=[^?|&]*/", "/([?|&])mode=[^?|&]*/", "/([?|&])phpinfo=[^?|&]*/", "/([?|&])printstats=[^?|&]*/", "/[?|&]".URL_ID."=[^?|&]*/", "/[?|&]l=[^?|&]*/", "/[&?]+$/"), array("", "", "", "", "", "", ""), addslashes($self_url));
  }

  function url($url, $amp = "&amp;") {
    global $l;
    $dummy_array = explode("#", $url);
    $url = $dummy_array[0];

    if ($this->mode == "get" && strpos($url, $this->session_id) === false) {
      $url .= strpos($url, '?') !== false ? $amp : "?";
      $url .= SESSION_NAME."=".$this->session_id;
    }

    if (!empty($l)) {
      $url .= strpos($url, '?') !== false ? $amp : "?";
      $url .= "l=".$l;
    }

    $url .= (isset($dummy_array[1])) ? "#".$dummy_array[1] : "";
    return $url;
  }
} //end of class

//-----------------------------------------------------
//--- Start Session -----------------------------------
//-----------------------------------------------------
define('COOKIE_NAME', '4images_');
define('COOKIE_PATH', '');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', '0');

$site_sess = new Session();

// Get Userinfo
$session_info = $site_sess->return_session_info();
$user_info = $site_sess->return_user_info();

//-----------------------------------------------------
//--- Get User Caches ---------------------------------
//-----------------------------------------------------
$num_total_online = 0;
$num_visible_online = 0;
$num_invisible_online = 0;
$num_registered_online = 0;
$num_guests_online = 0;
$user_online_list = "";
$prev_user_ids = array();
$prev_session_ips = array();

if (defined("GET_USER_ONLINE") && ($config['display_whosonline'] == 1 || $user_info['user_level'] == ADMIN)) {
  $time_out = time() - 300;
  $sql = "SELECT s.session_user_id, s.session_lastaction, s.session_ip".get_user_table_field(", u.", "user_id").get_user_table_field(", u.", "user_level").get_user_table_field(", u.", "user_name").get_user_table_field(", u.", "user_invisible")."
      FROM ".SESSIONS_TABLE." s
      LEFT JOIN ".USERS_TABLE." u ON (".get_user_table_field("u.", "user_id")." = s.session_user_id)
      WHERE s.session_lastaction >= $time_out
      ORDER BY ".get_user_table_field("u.", "user_id")." ASC, s.session_ip ASC";
  $result = $site_db->query($sql);
  while ($row = $site_db->fetch_array($result)) {
    if ($row['session_user_id'] != GUEST && (isset($row[$user_table_fields['user_id']]) && $row[$user_table_fields['user_id']] != GUEST)) {
      if (!isset($prev_user_ids[$row['session_user_id']])) {
        $is_invisible = (isset($row[$user_table_fields['user_invisible']]) && $row[$user_table_fields['user_invisible']] == 1) ? 1 : 0;
        $invisibleuser = ($is_invisible) ? "*" : "";
        $username = (isset($row[$user_table_fields['user_level']]) && $row[$user_table_fields['user_level']] == ADMIN && $config['highlight_admin'] == 1) ? sprintf("<b>%s</b>", $row[$user_table_fields['user_name']]) : $row[$user_table_fields['user_name']];
        if (!$is_invisible || $user_info['user_level'] == ADMIN) {
          $user_online_list .= ($user_online_list != "") ? ", " : "";
          $user_profile_link = (!empty($url_show_profile)) ? preg_replace("/{user_id}/", $row['session_user_id'], $url_show_profile) : ROOT_PATH."member.php?action=showprofile&amp;".URL_USER_ID."=".$row['session_user_id'];
          $user_online_list .= "<a href=\"".$site_sess->url($user_profile_link)."\">".str_replace(array("{", "}"), array("&#123;", "&#125;"), $username)."</a>".$invisibleuser;
        }
        (!$is_invisible) ? $num_visible_online++ : $num_invisible_online++;
        $num_registered_online++;
      }
      $prev_user_ids[$row['session_user_id']] = 1;
    }
    else {
      if (!isset($prev_session_ips[$row['session_ip']])) {
        $num_guests_online++;
      }
    }
    $prev_session_ips[$row['session_ip']] = 1;
  }
  $num_total_online = $num_registered_online + $num_guests_online;
  //$num_invisible_online = $num_registered_online - $num_visible_online;

  $site_template->register_vars(array(
    "num_total_online" => $num_total_online,
    "num_invisible_online" => $num_invisible_online,
    "num_registered_online" => $num_registered_online,
    "num_guests_online" => $num_guests_online,
    "user_online_list" => $user_online_list,
    "lang_user_online" => str_replace('{num_total_online}', $num_total_online, $lang['user_online']),
    "lang_user_online_detail" => str_replace(array('{num_registered_online}','{num_invisible_online}','{num_guests_online}'), array($num_registered_online,$num_invisible_online,$num_guests_online), $lang['user_online_detail']),
  ));
  $whos_online = $site_template->parse_template("whos_online");
  $site_template->register_vars("whos_online", $whos_online);
  unset($whos_online);
  unset($prev_user_ids);
  unset($prev_session_ips);
}
?>
