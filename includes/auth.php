<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: auth.php                                             *
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
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

function get_auth_subcat_ids($cid = 0, $cat_id = 0, $cat_parent_cache) {
  global $cat_subcat_ids;

  if (!isset($cat_parent_cache[$cid])) {
    return false;
  }
  foreach ($cat_parent_cache[$cid] as $key => $val) {
    $cat_subcat_ids[$cat_id][] = $val;
    get_subcat_ids($val, $cat_id, $cat_parent_cache);
  }
  return $cat_subcat_ids;
}

function check_permission($type, $cat_id = 0) {
  global $cat_cache, $user_info, $user_access;

  if (!isset($cat_cache[$cat_id][$type])) {
    return false;
  }
  if ($cat_cache[$cat_id][$type] == AUTH_ALL || $user_info['user_level'] == ADMIN) {
    return true;
  }
  elseif ($cat_cache[$cat_id][$type] == AUTH_USER && ($user_info['user_level'] != GUEST && $user_info['user_level'] != USER_AWAITING)) {
    return true;
  }
  elseif ($cat_cache[$cat_id][$type] == AUTH_ADMIN && $user_info['user_level'] != ADMIN) {
    return false;
  }
  elseif ($cat_cache[$cat_id][$type] == AUTH_ACL && ($user_info['user_level'] != GUEST && $user_info['user_level'] != USER_AWAITING)) {
    if (isset($user_access[$cat_id])) {
      for ($i = 0; $i < sizeof($user_access[$cat_id]); $i++) {
        if (isset($user_access[$cat_id][$i][$type]) &&  $user_access[$cat_id][$i][$type] == 1) {
          return true;
        }
      }
    }
  }
  return false;
}

function get_permission() {
  global $site_db, $cat_cache, $cat_parent_cache, $user_info, $subcat_ids;

  foreach ($cat_cache as $key => $val) {
    if ($val['auth_viewcat'] != AUTH_ALL) {
      $cat_subcat_ids = get_auth_subcat_ids($key, $key, $cat_parent_cache);
      if (isset($cat_subcat_ids[$key])) {
        foreach ($cat_subcat_ids[$key] as $key2 => $val2) {
          if ($cat_cache[$val2]['auth_viewcat'] < $cat_cache[$key]['auth_viewcat']) {
            $cat_cache[$val2]['auth_viewcat'] = $cat_cache[$key]['auth_viewcat'];
          }
        }
      }
    }
  }

  $user_access = array();
  if ($user_info['user_id'] != 0 && $user_info['user_level'] != GUEST && $user_info['user_level'] != USER_AWAITING) {
    $current_time = time();
    /*
    $sql = "DELETE FROM ".GROUP_MATCH_TABLE."
            WHERE groupmatch_enddate <= $current_time AND groupmatch_enddate <> 0";
    $site_db->query($sql);
    */
    $sql = "SELECT a.cat_id, a.auth_viewcat, a.auth_viewimage, a.auth_download, a.auth_upload, a.auth_directupload, a.auth_vote, a.auth_sendpostcard, a.auth_readcomment, a.auth_postcomment
            FROM (".GROUP_ACCESS_TABLE." a, ".GROUP_MATCH_TABLE." m)
            WHERE m.user_id = ".$user_info['user_id']."
            AND a.group_id = m.group_id
            AND m.groupmatch_startdate <= $current_time
            AND (groupmatch_enddate > $current_time OR groupmatch_enddate = 0)";
    $result = $site_db->query($sql);
    while ($row = $site_db->fetch_array($result)) {
      $user_access[$row['cat_id']][] = $row;
    }
  }
  return $user_access;
}

// cat_id's for "auth_viewcat" is already defined in page_header.php
function get_auth_cat_sql($type, $mode = "IN") {
  global $auth_cat_sql, $cat_cache;
  if (!empty($auth_cat_sql[$type][$mode])) {
    return $auth_cat_sql[$type][$mode];
  }
  $auth_cat_sql[$type]['IN'] = 0;
  $auth_cat_sql[$type]['NOTIN'] = 0;
  foreach ($cat_cache as $key => $val) {
    if (!check_permission($type, $key)) {
      $auth_cat_sql[$type]['NOTIN'] .= ", ".$key;
    }
    else {
      $auth_cat_sql[$type]['IN'] .= ", ".$key;
    }
  }
  return $auth_cat_sql[$type][$mode];
}
?>
