<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: home.php                                             *
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

$stats_limit = 5;

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

if ($action == "") {
  $action = "home";
}

show_admin_header();

$ip_whois_link = "http://www.ripe.net/perl/whois/?searchtext=";

if ($action == "home") {
  if (!defined('USER_INTEGRATION')) {
    printf("<span class=\"headline\">%s</span><br /><br />", $lang['headline_whosonline']);
    echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    echo "<tr class=\"tableseparator\">\n";
    echo "<td class=\"tableseparator\">".$lang['field_username']."</td>\n<td class=\"tableseparator\">".$lang['field_lastaction']."</td>\n<td class=\"tableseparator\">&nbsp;</td>\n<td class=\"tableseparator\">".$lang['field_ip']."</td>\n</tr>\n";

    $num_total_online = 0;
    $num_visible_online = 0;
    $num_invisible_online = 0;
    $num_registered_online = 0;
    $num_guests_online = 0;
    $user_online_list = "";
    $prev_user_id = "";
    $prev_ip = "";

    $sql = "SELECT ".get_user_table_field("u.", "user_id").get_user_table_field(", u.", "user_level").get_user_table_field(", u.", "user_name").get_user_table_field(", u.", "user_lastaction").get_user_table_field(", u.", "user_location").get_user_table_field(", u.", "user_invisible").", s.session_user_id, s.session_lastaction, s.session_ip
	    FROM (".USERS_TABLE." u, ".SESSIONS_TABLE." s)
	    WHERE ".get_user_table_field("u.", "user_id")." = s.session_user_id AND s.session_user_id <> ".GUEST." AND ".get_user_table_field("u.", "user_lastaction")." > ".(time() - 300)."
	    ORDER BY session_ip ASC";
    $result = $site_db->query($sql);

    while ($row = $site_db->fetch_array($result)) {
      if ($row['session_user_id'] != $prev_user_id) {
        echo "<tr class=\"".get_row_bg()."\">\n";
        $user_id = $row['session_user_id'];
        $username = format_text($row[$user_table_fields['user_name']], 2);

        $is_invisible = (isset($row[$user_table_fields['user_invisible']]) && $row[$user_table_fields['user_invisible']] == 1) ? 1 : 0;
        if ($is_invisible) { // Invisible User but show to Admin
          $invisibleuser = "*";
        }
        else {
          $invisibleuser = "";
          $num_visible_online++;
        }
        if ($row[$user_table_fields['user_level']] == ADMIN) {
          $username = sprintf("<b>%s</b>", $username);
        }
        if (empty($user_profile_link)) {
          $username = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&amp;".URL_USER_ID."=".$user_id)."\" target=\"_blank\">".$username."</a>";
        }
        echo "<td>".$username.$invisibleuser."</td>\n";
        echo "<td>".format_date($config['date_format']." ".$config['time_format'], $row[$user_table_fields['user_lastaction']])."</td>\n";

        if (preg_match("/Control Panel/i", $row[$user_table_fields['user_location']])) {
          echo "<td>Control Panel</td>";
        }
        else {
          echo "<td><a href=\"".$site_sess->url(ROOT_PATH.$row[$user_table_fields['user_location']])."\" target=\"_blank\">".$row[$user_table_fields['user_location']]."</a></td>\n";
        }
        echo "<td><a href=\"".$ip_whois_link.$row['session_ip']."\" target=\"_blank\">".$row['session_ip']."</a></td>\n";
        $num_registered_online++;
      }
      $prev_user_id = $row['session_user_id'];
    }

    $sql = "SELECT session_user_id, session_lastaction, session_ip, session_location
            FROM ".SESSIONS_TABLE."
            WHERE session_lastaction >= ".(time() - 300)." AND session_user_id = ".GUEST;
    $result = $site_db->query($sql);

    $num_guests_online = 0;
    while ($row = $site_db->fetch_array($result)) {
      if ($row['session_ip'] != $prev_ip) {
        echo "<tr class=\"".get_row_bg()."\">\n";
        echo "<td>".$lang['userlevel_guest']."</td>\n";
        echo "<td>".format_date($config['date_format']." ".$config['time_format'], $row['session_lastaction'])."</td>\n";
        if (preg_match("/Control Panel/i", $row['session_location'])) {
          echo "<td>Control Panel</td>";
        }
        else {
          echo "<td><a href=\"".$site_sess->url(ROOT_PATH.$row['session_location'])."\" target=\"_blank\">".$row['session_location']."</a></td>\n";
        }
        echo "<td>".$row['session_ip']."</td>\n";
        echo "</tr>\n";
        $num_guests_online++;
      }
      $prev_ip = $row['session_ip'];
    }

    echo "</table></td></tr></table><br />";

    $num_total_online = $num_registered_online + $num_guests_online;
    $num_invisible_online = $num_registered_online - $num_visible_online;

    $lang['online_users'] = preg_replace("/".$site_template->start."num_total".$site_template->end."/siU", $num_total_online, $lang['online_users']);
    $lang['online_users'] = preg_replace("/".$site_template->start."num_registered".$site_template->end."/siU", $num_registered_online, $lang['online_users']);
    $lang['online_users'] = preg_replace("/".$site_template->start."num_guests".$site_template->end."/siU", $num_guests_online, $lang['online_users']);
    printf ("<b>%s</b><br /><br /><br />", $lang['online_users']);
  } // End defined('USER_INTEGRATION')

  $total_images = 0;
  $total_categories = 0;
  foreach ($cat_cache as $val) {
    $total_categories++;
    if (isset($val['num_images'])) {
      $total_images += $val['num_images'];
    }
  }

  printf("<span class=\"headline\">%s</span><br /><br />", $lang['headline_stats']);

  show_table_header($lang['nav_general_main'], 4);

  //1
  echo "<tr class=\"".get_row_bg()."\">\n";
  echo "<td width=\"16%\"><b>".$lang['categories']."</b></td><td width=\"16%\">".$total_categories."</td>\n";
  $size = 0;
  echo "<td width=\"16%\"><b>".$lang['media_directory']."</b></td><td width=\"16%\">".format_file_size(get_dir_size(MEDIA_PATH))."</td>\n";
  echo "</tr>";

  //2
  echo "<tr class=\"".get_row_bg()."\">\n";

  $sql = "SELECT COUNT(*) as temp_images
          FROM ".IMAGES_TEMP_TABLE;
  $row = $site_db->query_firstrow($sql);

  $awaiting_validation = preg_replace("/".$site_template->start."num_images".$site_template->end."/siU", $row['temp_images'], $lang['images_awaiting_validation']);
  $awaiting_validation = sprintf("<a href=\"".$site_sess->url("validateimages.php?action=validateimages")."\">%s</a>", $awaiting_validation);
  echo "<td width=\"16%\"><b>".$lang['images']."</b></td><td width=\"16%\">".$total_images." / ".$awaiting_validation."</td>\n";
  $size = 0;
  echo "<td width=\"16%\"><b>".$lang['thumb_directory']."</b></td><td width=\"16%\">".format_file_size(get_dir_size(THUMB_PATH))."</td>\n";
  echo "</tr>";

  //3
  echo "<tr class=\"".get_row_bg()."\">\n";

  $sql = "SELECT COUNT(*) as users
          FROM ".USERS_TABLE."
          WHERE ".get_user_table_field("", "user_id")." <> ".GUEST;
  $row = $site_db->query_firstrow($sql);

  echo "<td width=\"16%\"><b>".$lang['users']."</b></td><td width=\"16%\">".$row['users']."</td>\n";

  echo "<td width=\"16%\"><b>".$lang['database']."</b></td><td width=\"16%\">";
  include(ROOT_PATH.'includes/db_utils.php');
  get_database_size();
  if (!empty($global_info['database_size']['total'])) {
    if (!empty($global_info['database_size']['4images'])) {
      $db_status = $lang['homestats_total']." <b>".format_file_size($global_info['database_size']['total'])."</b> / ";
      $db_status .= "4images:&nbsp;<b>".format_file_size($global_info['database_size']['4images'])."</b>";
    }
    else {
      $db_status = format_file_size(!empty($global_info['database_size']['total']));
    }
  }
  else {
    $db_status = "n/a";
  }

  echo $db_status."</td>\n";

  echo "</tr>";
  show_table_footer();

  $sql = "SELECT SUM(cat_hits) AS sum
          FROM ".CATEGORIES_TABLE;
  $row = $site_db->query_firstrow($sql);

  $sum = (isset($row['sum'])) ? $row['sum'] : 0;
  show_table_header($lang['top_cat_hits']." (".$lang['homestats_total']." ".$sum.")", 4);

  $sql = "SELECT cat_id, cat_name, cat_hits
          FROM ".CATEGORIES_TABLE."
          ORDER BY cat_hits DESC
          LIMIT $stats_limit";
  $result = $site_db->query($sql);

  $num = 1;
  while ($row = $site_db->fetch_array($result)) {
    if ($num == 1) {
      $max = $row['cat_hits'];
      if ($max == 0) {
        $max = 1;
      }
    }
    echo "<tr class=\"".get_row_bg()."\">\n";
    echo "<td>&nbsp;".$num.".</td>\n<td nowrap=\"nowrap\"><b><a href=\"".$site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$row['cat_id'])."\" target=\"_blank\">".format_text($row['cat_name'], 2)."</a></b></td>\n\n";
    $per = intval($row['cat_hits'] / $max * 100);
    echo "<td width=\"100%\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td bgcolor=\"#FFFFFF\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"".$per."%\"><tr>\n";
    echo "<td bgcolor=\"#FCDC43\"><img src=\"images/spacer.gif\" height=\"10\" width=\"2\"></td>\n";
    echo "</tr></table></td></tr></table>\n</td>";
    echo "<td align=\"center\">".$row['cat_hits']."</td></tr>\n";
    $num++;
  }
  if ($num == 1) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"4\">".$lang['no_search_results']."</td></tr>";
  }

  $sql = "SELECT SUM(image_hits) AS sum
          FROM ".IMAGES_TABLE;
  $row = $site_db->query_firstrow($sql);

  $sum = (isset($row['sum'])) ? $row['sum'] : 0;
  show_table_separator($lang['top_image_hits']." (".$lang['homestats_total']." ".$sum.")", 4);

  $sql = "SELECT image_id, image_name, image_hits
          FROM ".IMAGES_TABLE."
          ORDER BY image_hits DESC
          LIMIT $stats_limit";
  $result = $site_db->query($sql);

  $num = 1;
  while ($row = $site_db->fetch_array($result)) {
    if ($num == 1) {
      $max = $row['image_hits'];
      if ($max == 0) {
        $max = 1;
      }
    }
    echo "<tr class=\"".get_row_bg()."\">\n";
    echo "<td>&nbsp;".$num.".</td>\n<td nowrap=\"nowrap\"><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$row['image_id'])."\" target=\"_blank\">".format_text($row['image_name'], 2)."</a></b></td>\n\n";
    $per = intval($row['image_hits'] / $max * 100);
    echo "<td width=\"100%\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td bgcolor=\"#FFFFFF\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"".$per."%\"><tr>\n";
    echo "<td bgcolor=\"#FCDC43\"><img src=\"images/spacer.gif\" height=\"10\" width=\"2\"></td>\n";
    echo "</tr></table></td></tr></table>\n</td>";
    echo "<td align=\"center\">".$row['image_hits']."</td></tr>\n";
    $num++;
  }
  if ($num == 1) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"4\">".$lang['no_search_results']."</td></tr>";
  }

  show_table_separator($lang['top_image_rating'], 4);

  $sql = "SELECT image_id, image_name, image_rating
          FROM ".IMAGES_TABLE."
          ORDER BY image_rating DESC
          LIMIT $stats_limit";
  $result = $site_db->query($sql);

  $num = 1;
  while ($row = $site_db->fetch_array($result)) {
    if ($num == 1) {
      $max = $row['image_rating'];
      if ($max == 0) {
        $max = 1;
      }
    }
    echo "<tr class=\"".get_row_bg()."\">\n";
    echo "<td>&nbsp;".$num.".</td>\n<td nowrap=\"nowrap\"><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$row['image_id'])."\" target=\"_blank\">".format_text($row['image_name'], 2)."</a></b></td>\n\n";
    $per = intval($row['image_rating'] / $max * 100);
    echo "<td width=\"100%\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td bgcolor=\"#FFFFFF\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"".$per."%\"><tr>\n";
    echo "<td bgcolor=\"#FCDC43\"><img src=\"images/spacer.gif\" height=\"10\" width=\"2\"></td>\n";
    echo "</tr></table></td></tr></table>\n</td>";
    echo "<td align=\"center\">".$row['image_rating']."</td></tr>\n";
    $num++;
  }
  if ($num == 1) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"4\">".$lang['no_search_results']."</td></tr>";
  }

  $sql = "SELECT SUM(image_votes) AS sum
          FROM ".IMAGES_TABLE;
  $row = $site_db->query_firstrow($sql);

  $sum = (isset($row['sum'])) ? $row['sum'] : 0;
  show_table_separator($lang['top_image_votes']." (".$lang['homestats_total']." ".$sum.")", 4);

  $sql = "SELECT image_id, image_name, image_votes
          FROM ".IMAGES_TABLE."
          ORDER BY image_votes DESC
          LIMIT $stats_limit";
  $result = $site_db->query($sql);

  $num = 1;
  while ($row = $site_db->fetch_array($result)) {
    if ($num == 1) {
      $max = $row['image_votes'];
      if ($max == 0) {
        $max = 1;
      }
    }
    echo "<tr class=\"".get_row_bg()."\">\n";
    echo "<td>&nbsp;".$num.".</td>\n<td nowrap=\"nowrap\"><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$row['image_id'])."\" target=\"_blank\">".format_text($row['image_name'], 2)."</a></b></td>\n\n";
    $per = intval($row['image_votes'] / $max * 100);
    echo "<td width=\"100%\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td bgcolor=\"#FFFFFF\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"".$per."%\"><tr>\n";
    echo "<td bgcolor=\"#FCDC43\"><img src=\"images/spacer.gif\" height=\"10\" width=\"2\"></td>\n";
    echo "</tr></table></td></tr></table>\n</td>";
    echo "<td align=\"center\">".$row['image_votes']."</td></tr>\n";
    $num++;
  }
  if ($num == 1) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"4\">".$lang['no_search_results']."</td></tr>";
  }

  $sql = "SELECT SUM(image_downloads) AS sum
          FROM ".IMAGES_TABLE;
  $row = $site_db->query_firstrow($sql);

  $sum = (isset($row['sum'])) ? $row['sum'] : 0;
  show_table_separator($lang['top_image_downloads']." (".$lang['homestats_total']." ".$sum.")", 4);

  $sql = "SELECT image_id, image_name, image_downloads
          FROM ".IMAGES_TABLE."
          ORDER BY image_downloads DESC
          LIMIT $stats_limit";
  $result = $site_db->query($sql);

  $num = 1;
  while ($row = $site_db->fetch_array($result)) {
    if ($num == 1) {
      $max = $row['image_downloads'];
      if ($max == 0) {
        $max = 1;
      }
    }
    echo "<tr class=\"".get_row_bg()."\">\n";
    echo "<td>&nbsp;".$num.".</td>\n<td nowrap=\"nowrap\"><b><a href=\"".$site_sess->url(ROOT_PATH."details.php?".URL_IMAGE_ID."=".$row['image_id'])."\" target=\"_blank\">".format_text($row['image_name'], 2)."</a></b></td>\n\n";
    $per = intval($row['image_downloads'] / $max * 100);
    echo "<td width=\"100%\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td bgcolor=\"#FFFFFF\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"".$per."%\"><tr>\n";
    echo "<td bgcolor=\"#FCDC43\"><img src=\"images/spacer.gif\" height=\"10\" width=\"2\"></td>\n";
    echo "</tr></table></td></tr></table>\n</td>";
    echo "<td align=\"center\">".$row['image_downloads']."</td></tr>\n";
    $num++;
  }
  if ($num == 1) {
    echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"4\">".$lang['no_search_results']."</td></tr>";
  }

  show_table_footer();
}
show_admin_footer();
?>