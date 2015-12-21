<?php // PLUGIN_TITLE: Files Check
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: files_check.php                                      *
 *        Copyright: (C) 2002-2015 4homepages.de                          *
 *            Email: jan@4homepages.de                                    *
 *              Web: http://www.4homepages.de                             *
 *    Scriptversion: 1.7.13                                               *
 *    File Version: 1.2 (by V@no)                                         *
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

define('ROOT_PATH', "./../../");
require(ROOT_PATH.'admin/admin_global.php');
include(ROOT_PATH.'includes/search_utils.php');

if ($action == "") {
  $action = "intro";
}
$ids = array();
if (isset($HTTP_GET_VARS['thumbs']) || isset($HTTP_POST_VARS['thumbs'])) {
  $thumbs = (isset($HTTP_GET_VARS['thumbs'])) ? intval($HTTP_GET_VARS['thumbs']) : intval($HTTP_POST_VARS['thumbs']);
}
else {
  $thumbs = 0;
}
function delete_images($image_ids, $delfromserver = 1) {
  global $site_db, $lang, $ids;
  if (empty($image_ids)) {
    echo $lang['no_search_results'];
    return false;
  }
  $error_log = array();
  echo "<br />";
  $sql = "SELECT image_id, cat_id, user_id, image_name, image_media_file, image_thumb_file
          FROM ".IMAGES_TABLE."
          WHERE image_id IN ($image_ids)";
  $image_result = $site_db->query($sql);
  while ($image_row = $site_db->fetch_array($image_result)) {
    $sql = "DELETE FROM ".IMAGES_TABLE."
            WHERE image_id = ".$image_row['image_id'];
    if ($site_db->query($sql)) {
      echo "<b>".$lang['image_delete_success']."</b> ".$image_row['image_name']." (ID: ".$image_row['image_id'].")<br />\n";
      $ids[] = $image_row['image_id'];
    }
    else {
      $error_log[] = "<b>".$lang['image_delete_error']."</b> ".$image_row['image_name']." (ID: ".$image_row['image_id'].")<br />";
    }

    if ($delfromserver) {
      if (!is_remote($image_row['image_media_file']) && !is_local_file($image_row['image_media_file'])) {
        if (@unlink(MEDIA_PATH."/".$image_row['cat_id']."/".$image_row['image_media_file'])) {
          echo "&nbsp;&nbsp;".$lang['file_delete_success']." (".$image_row['image_media_file'].")<br />\n";
        }
        else {
          $error_log[] = "<b>".$lang['file_delete_error']." (".$image_row['image_media_file'].")<br />";
        }
      }
      if (!empty($image_row['image_thumb_file']) && !is_remote($image_row['image_thumb_file']) && !is_local_file($image_row['image_thumb_file'])) {
        if (@unlink(THUMB_PATH."/".$image_row['cat_id']."/".$image_row['image_thumb_file'])) {
          echo "&nbsp;&nbsp;".$lang['thumb_delete_success']." (".$image_row['image_thumb_file'].")<br />\n";
        }
        else {
          $error_log[] = "<b>".$lang['thumb_delete_error']." (".$image_row['image_thumb_file'].")<br />\n";
        }
      }
			if (@unlink(MEDIA_PATH."/".$image_row['cat_id']."/big/".$image_row['image_media_file'])) {
          echo "&nbsp;&nbsp;".$lang['file_delete_success']." (big/".$image_row['image_media_file'].")<br />\n";
       }else {
          echo "&nbsp;&nbsp;No Original Found <br />\n";
       }
    }

    if (!empty($user_table_fields['user_comments'])) {
      $sql = "SELECT user_id
              FROM ".COMMENTS_TABLE."
              WHERE image_id = ".$image_row['image_id']." AND user_id <> ".GUEST;
      $result = $site_db->query($sql);

      while ($row = $site_db->fetch_array($result)) {
        $sql = "UPDATE ".USERS_TABLE."
                SET ".get_user_table_field("", "user_comments")." = ".get_user_table_field("", "user_comments")." - 1
                WHERE ".get_user_table_field("", "user_id")." = ".$row['user_id'];
        $site_db->query($sql);
      }
    }

    $sql = "DELETE FROM ".COMMENTS_TABLE."
            WHERE image_id = ".$image_row['image_id'];
    if ($site_db->query($sql)) {
      echo $lang['comments_delete_success']."<br />\n";
    }
    else {
      $error_log[] = "<b>".$lang['comments_delete_success']."</b> ".$image_row['image_name'].", (ID: ".$image_row['image_id'].")<br />\n";
    }
    echo "<br />\n";
  }
  remove_searchwords($image_ids);
  return $error_log;
}
function ok($ok) {
	if ($ok == 0) {
		return "<b>OK</b>";
	}elseif ($ok == 1) {
		return "<b><font color=red>error</font></b>";
	}elseif ($ok == 2) {
		return "<b>Skiped</b>";
	}else{
		return "<b>missing</b>";
	}
}
function where($where){
	if ($where == 0){
		return "green";
	}elseif ($where == 1){
		return "orange";
	}
	return "black";
}
function next_step($imchkstart, $imchksize, $autoredirect, $cat, $local, $subcat, $thumbs) {
  global $site_sess;
  $page = $site_sess->url("files_check.php?action=checkimages&imchkstart=".$imchkstart."&imchksize=".$imchksize."&autoredirect=".$autoredirect."&local=".$local."&cat=".$cat."&subcat=".$subcat."&thumbs=".$thumbs, "&");
if ($autoredirect) {
?>
<script language="javascript">
myvar = "";
timeout = 15;
function dorefresh() {
  window.status="Redirecting"+myvar;
  myvar = myvar + " .";
  timerID = setTimeout("dorefresh();", 100);
  if (timeout > 0) {
    timeout -= 1;
  }
  else {
    clearTimeout(timerID);
    window.status="";
    window.location="<?php echo $page; ?>";
  }
}
dorefresh();
</script>
<?php
}
?>
<br />
<table border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="tableseparator">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td class="tablerow2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo str_replace("&", "&amp;", $page); ?>"><b>Click here to continue</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />
<?php
}
function final_step($imchksize, $autoredirect = 0, $thumbs = 1) {
  global $site_sess;
  $page = $site_sess->url("files_check.php?action=final&imchksize=".$imchksize."&autoredirect=".$autoredirect."&thumbs=".$thumbs, "&");
if ($autoredirect) {
?>
<script language="javascript">
myvar = "";
timeout = 15;
function dorefresh() {
  window.status="Redirecting"+myvar;
  myvar = myvar + " .";
  timerID = setTimeout("dorefresh();", 100);
  if (timeout > 0) {
    timeout -= 1;
  }
  else {
    clearTimeout(timerID);
    window.status="";
    window.location="<?php echo $page; ?>";
  }
}
dorefresh();
</script>
<?php
}
?>
<br />
<table border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="tableseparator">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td class="tablerow2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo str_replace("&", "&amp;", $page); ?>"><b>Click here to see report</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />
<?php
}
show_admin_header();


if ($action == "deleteimage") {
  $selectimages = (isset($HTTP_POST_VARS['selectimages'])) ? $HTTP_POST_VARS['selectimages'] : array();
  $delfromserver = (isset($HTTP_POST_VARS['delfromserver'])) ? intval($HTTP_POST_VARS['delfromserver']) : 1;
  $image_ids = "";
  if (!empty($selectimages)) {
    foreach ($selectimages as $val) {
      $image_ids .= (($image_ids != "") ? ", " : "").$val;
    }
  }
  $lang_key = (count($selectimages) > 1) ? 'images' : 'image';
  show_table_header($lang['delete'].": ".$lang[$lang_key], 1);
  echo "<tr><td class=\"tablerow\">\n";
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
  $error_log = delete_images($image_ids, $delfromserver);
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
  $action = "final";
}

if ($action == "removeimage") {
  $image_ids = array();
  if ((isset($HTTP_GET_VARS['image_id']) && intval($HTTP_GET_VARS['image_id'])) || (isset($HTTP_POST_VARS['image_id']) && intval($HTTP_POST_VARS['image_id']))) {
    $image_id = (isset($HTTP_GET_VARS['image_id'])) ? intval($HTTP_GET_VARS['image_id']) : intval($HTTP_POST_VARS['image_id']);
    $image_ids[] = $image_id;
  }
  elseif (isset($HTTP_POST_VARS['selectimages'])) {
    $image_ids = $HTTP_POST_VARS['selectimages'];
  }
  else {
   $image_ids[] = 0;
  }

  if ($image_ids[0] != 0) {
    show_form_header("files_check.php", "deleteimage");
    foreach ($image_ids as $val) {
      show_hidden_input("selectimages[]", $val);
    }
    $lang_key = (count($image_ids) > 1) ? 'images' : 'image';
    show_table_header($lang['delete'].": ".$lang[$lang_key], 2);
    show_description_row($lang['delete_image_confirm']);
    show_radio_row($lang['delete_image_files_confirm'], "delfromserver", 1);
    show_form_footer($lang['yes'], "", 2, $lang['no']);
  }else{
    show_table_header("Error Log:", 1);
    echo "<tr><td class=\"tablerow\">\n";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td>&nbsp;</td><td>\n";
    echo "<b>".$lang['error_log_desc']."</b>\n<ul>\n";
    printf("<li>%s</li>\n", $lang['no_search_results']);
    echo "</ul>\n</td></tr></table>\n";
    echo "</td></tr>\n";
    show_table_footer();
    $action = "final";
  }
}

if ($action == "intro") {

	$category = "\n<select name=\"cat\" class=\"categoryselect\">\n";
	$category .= "<option value=\"0\">".$lang['all_categories']."</option>\n";
	$category .= "<option value=\"0\">-------------------------------</option>\n";
	$drop_down_cat_cache = array();
	$drop_down_cat_cache = $cat_parent_cache;
	$category .= get_category_dropdown_bits($cat_id);
	$category .= "</select>\n";
  show_form_header("files_check.php", "checkimages");
  show_table_header("Files Check", 2);
  show_custom_row("Category", $category);
  show_radio_row("Include sub-categories", "subcat", 1);
  show_input_row("Number of images to do per cycle", "imchksize", 50, 5);
  show_radio_row("Check Remote Files", "local", 1);
  show_radio_row("Check Thumbnails", "thumbs", 1);
  show_radio_row("Include automatic JavaScript redirect to next page", "autoredirect", 1);
  show_form_footer($lang['submit'], $lang['reset'], 2);
}

if ($action == "checkimages") {
  if (isset($HTTP_GET_VARS['cat']) || isset($HTTP_POST_VARS['cat'])) {
    $cat = (isset($HTTP_GET_VARS['cat'])) ? intval($HTTP_GET_VARS['cat']) : intval($HTTP_POST_VARS['cat']);
  }
  else {
    $cat = 0;
  }
  if (isset($HTTP_GET_VARS['subcat']) || isset($HTTP_POST_VARS['subcat'])) {
    $subcat = (isset($HTTP_GET_VARS['subcat'])) ? intval($HTTP_GET_VARS['subcat']) : intval($HTTP_POST_VARS['subcat']);
  }
  else {
    $subcat = 0;
  }

if ($cat) {
  $cats = array($cat);
  if ($subcat)
  {
    $subcat_ids = array();
    get_subcat_ids($cat, $cat, $cat_parent_cache);
    if (isset($subcat_ids[$cat]))
    {
      $cats = array_merge($cats, $subcat_ids[$cat]);
    }
  }
	$condition = "WHERE cat_id IN (".implode(",", $cats).")";
}else{
	$condition = "";
}
  if (isset($HTTP_GET_VARS['imchksize']) || isset($HTTP_POST_VARS['imchksize'])) {
    $imchksize = (isset($HTTP_GET_VARS['imchksize'])) ? intval($HTTP_GET_VARS['imchksize']) : intval($HTTP_POST_VARS['imchksize']);
    if (!$imchksize) {
      $imchksize = 25;
    }
  }
  else {
    $imchksize = 50;
  }

  if (isset($HTTP_GET_VARS['autoredirect']) || isset($HTTP_POST_VARS['autoredirect'])) {
    $autoredirect = (isset($HTTP_GET_VARS['autoredirect'])) ? intval($HTTP_GET_VARS['autoredirect']) : intval($HTTP_POST_VARS['autoredirect']);
  }
  else {
    $autoredirect = 0;
  }
  if (isset($HTTP_GET_VARS['local']) || isset($HTTP_POST_VARS['local'])) {
    $local = (isset($HTTP_GET_VARS['local'])) ? intval($HTTP_GET_VARS['local']) : intval($HTTP_POST_VARS['local']);
  }
  else {
    $local = 0;
  }
  if (isset($HTTP_GET_VARS['imchkstart']) || isset($HTTP_POST_VARS['imchkstart'])) {
    $imchkstart = (isset($HTTP_GET_VARS['imchkstart'])) ? intval($HTTP_GET_VARS['imchkstart']) : intval($HTTP_POST_VARS['imchkstart']);
  }
  else {
    $imchkstart = 0;
  }
if (!$imchkstart) {
  $site_sess->set_session_var("imchklog", "");
}
  $sql = "SELECT COUNT(image_id) as max
          FROM ".IMAGES_TABLE."
          ".$condition;
  $row = $site_db->query_firstrow($sql);
  $max = (isset($row['max'])) ? $row['max'] : 0;

  $imchkend = $imchkstart + $imchksize - 1;
  if ($imchkend + 1 >= $max) {
    $imchkend = $max;
  }

  $sql = "SELECT image_id, image_name, cat_id, image_media_file, image_thumb_file
          FROM ".IMAGES_TABLE."
          $condition
          ORDER BY cat_id ASC, image_name ASC
          LIMIT $imchkstart, $imchksize";
  $result = $site_db->query($sql);
	$i = $imchkstart+1;
	$log = "";
	echo "Total files to check: $max<br><br>";
	echo "<table><tr><td bgcolor=\"green\" width=\"10\" height=\"10\">&nbsp;</td><td width=\"150\"> - Local files</td>";
	echo "<td bgcolor=\"red\" width=\"10\" height=\"10\">&nbsp;</td><td> - Critical errors</td></tr>";
	echo "<td bgcolor=\"orange\" width=\"10\" height=\"10\">&nbsp;</td><td> - Remote files</td>";
	echo "<td bgcolor=\"black\" width=\"10\" height=\"10\">&nbsp;</td><td> - Warnings</td></tr></table><br>";
	echo "<table class=\"tableheader\" cellspacing=\"1\" cellpadding=\"0\"><tr><td>";
	echo "<table bgcolor=\"white\" cellspacing=\"1\" cellpadding=\"3\"><tr align=\"center\"><td class=\"tableheader\">&nbsp;</td><td class=\"tableheader\">Image name</td><td class=\"tableheader\">ID</td><td class=\"tableheader\">Image file</td><td class=\"tableheader\">Ext.</td>".($thumbs ? "<td class=\"tableheader\">Thumb file</td><td class=\"tableheader\">Ext.</td>" : "")."</tr>\n";
	$ok_t = null;
	$where_t = null;
  while ($row = $site_db->fetch_array($result)) {
    @set_time_limit(90);
	  if (is_remote($row['image_media_file'])) {
 		  $where = 1;
			if ($local){
				if (remote_file_exists($row['image_media_file'], 1)) {
					$ok = 0;
				}else{
					$ok = 1;
				}
			}else{
				$ok = 2;
			}
    }else{
	  	$where = 0;
	  	$file_name = MEDIA_PATH."/".$row['cat_id']."/".$row['image_media_file'];
			if (file_exists($file_name)) {
				$ok = 0;
			}else{
				$ok = 1;
			}
		}
		if ($thumbs)
		{
  		if ($row['image_thumb_file']){
  		  if (is_remote($row['image_thumb_file'])) {
  	 		  $where_t = 1;
  				if ($local){
  					if (remote_file_exists($row['image_thumb_file'], 1)) {
  						$ok_t = 0;
  					}else{
  						$ok_t = 1;
  					}
  				}else{
  					$ok_t = 2;
  				}
  	    }else{
  		  	$where_t = 0;
  		  	$file_name = THUMB_PATH."/".$row['cat_id']."/".$row['image_thumb_file'];
  				if (file_exists($file_name)) {
  					$ok_t = 0;
  				}else{
  					$ok_t = 1;
  				}
  			}
  		}else{
  			$ok_t = 3;
  			$where_t = 2;
  		}
    }
		if ($ok || ($ok_t !== null && $ok_t)) {
			$log .= $row['image_id'].",".$ok.",".$ok_t.",".$where.",".$where_t.";";
		}
		$ok_show = ok($ok);
		$ok_t_show = ok($ok_t);
		$where = where($where);
		$where_t = where($where_t);
    echo "<tr align=\"center\" class=\"".get_row_bg()."\"><td>$i</td><td align=\"left\"><b>".$row['image_name']."</b></td><td>".$row['image_id']."</td>";
    echo "<td><font color=$where>$ok_show</font></td><td>".substr(strrchr($row['image_media_file'],"."), 1)."</td>".($thumbs ? "<td><font color=$where_t>$ok_t_show</font></td><td>".substr(strrchr($row['image_thumb_file'],"."), 1)."</td>" : "")."</tr>\n";


 	$i++;
  }
  echo "</table></td></tr></table>";
	$log = $site_sess->get_session_var("imchklog").$log;
  $site_sess->set_session_var("imchklog", $log);

  if ($imchkend < $max) {
    next_step($imchkend + 1, $imchksize, $autoredirect, $cat, $local, $subcat, $thumbs);
  }
  else {
		if ($log) {
    	final_step($imchksize, $autoredirect, $thumbs);
  	}else{
    	echo "<p><b>Files Check Complete!<br /><br /> No errors found.</b><p>\n";
    	show_text_link("Back", "files_check.php");
  	}
  }
}
if ($action == "final") {
	$log = $site_sess->get_session_var("imchklog");
	$log = trim($log, ";");
	$log_array = explode(";",$log);
  $log = array();
  $i = 0;
	foreach ($log_array as $key) {
		$error = explode(",", $key);
	  if ($error[0] && !in_array($error[0], $ids)) {
	    $log[] = $key;
	    $i++;
	  }
	}
  $site_sess->set_session_var("imchklog", implode(";", $log));

  if ($i) {
    show_form_header("files_check.php", "removeimage", "form");
  	echo "Found <font color=red><b>".count($log)."</b></font> errors<br><br>";
  	echo "<table><tr><td bgcolor=\"green\" width=\"10\" height=\"10\">&nbsp;</td><td width=\"150\"> - Local files</td>";
  	echo "<td bgcolor=\"red\" width=\"10\" height=\"10\">&nbsp;</td><td> - Critical errors</td></tr>";
  	echo "<td bgcolor=\"orange\" width=\"10\" height=\"10\">&nbsp;</td><td> - Remote files</td>";
  	echo "<td bgcolor=\"black\" width=\"10\" height=\"10\">&nbsp;</td><td> - Warnings</td></tr></table><br>";
  	echo "<table class=\"tableheader\" cellspacing=\"1\" cellpadding=\"0\"><tr><td>";
  	echo "<table bgcolor=\"white\" cellspacing=\"1\" cellpadding=\"3\"><tr align=\"center\"><td class=\"tableheader\">&nbsp;</td>\n";
    echo "<td class=\"tableseparator\"><input name=\"allbox\" type=\"checkbox\" onClick=\"CheckAll();\" /></td>\n";
  	echo "<td class=\"tableheader\">Image name</td><td class=\"tableheader\">ID</td>";
  	echo "<td class=\"tableheader\">Category</td><td class=\"tableheader\">User Name</td><td class=\"tableheader\">Date</td><td class=\"tableheader\">Image file</td><td class=\"tableheader\">Ext.</td>".($thumbs ? "<td class=\"tableheader\">Thumb file</td><td class=\"tableheader\">Ext.</td>" : "")."<td class=\"tableheader\">Action</td></tr>\n";
  	$i = 1;
  	foreach ($log as $key) {
  		$error = explode(",", $key);
      $sql = "SELECT i.image_id, i.cat_id, i.user_id, i.image_name, i.image_media_file, i.image_thumb_file, i.image_date".get_user_table_field(", u.", "user_name")."
              FROM ".IMAGES_TABLE." i, ".USERS_TABLE." u
              WHERE image_id = ".$error[0]." AND ".get_user_table_field("u.", "user_id")." = i.user_id
              LIMIT 1";
  		$image_row = $site_db->query_firstrow($sql);
  		$ok_show = ok($error[1]);
  		$ok_t_show = ok($error[2]);
  		$where = where($error[3]);
  		$where_t = where($error[4]);
  		echo "<tr align=\"center\" class=\"".get_row_bg()."\"><td>$i</td>";
      echo "<td><input type=\"checkbox\" name=\"selectimages[]\" value=\"".$image_row['image_id']."\" /></td>";
  		echo "<td align=\"left\">";
  		$thumb = "<img src=\"".((empty($image_row['image_thumb_file'])) ? ICON_PATH."/".get_file_extension($image_row['image_media_file']).".gif" : (((is_remote($image_row['image_thumb_file'])) ? ((remote_file_exists($image_row['image_thumb_file'], 1)) ? $image_row['image_thumb_file'] : ICON_PATH."/404.gif") : ((file_exists(ROOT_PATH.THUMB_DIR."/".$image_row['cat_id']."/".$image_row['image_thumb_file'])) ? ROOT_PATH.THUMB_DIR."/".$image_row['cat_id']."/".$image_row['image_thumb_file'] : ICON_PATH."/404.gif"))))."\" width=\"40\" height=\"40\" border=\"1\" alt=\"\" /><b>&nbsp;&nbsp;".$image_row['image_name']."</b>";
  		echo "<a href=\"../images.php?action=editimage&image_id=".$image_row['image_id']."\" target=\"4images_editimage\">".$thumb."</a>";
  		echo "</td><td>".$image_row['image_id']."</td>";
      echo "<td><a href=\"".$site_sess->url(ROOT_PATH."categories.php?".URL_CAT_ID."=".$image_row['cat_id'])."\" target=\"_blank\">".htmlspecialchars($cat_cache[$image_row['cat_id']]['cat_name'])."&nbsp;&nbsp;[ID: <b>".$image_row['cat_id']."</b>]</a></td>\n";
      $show_user_name = htmlspecialchars($image_row[$user_table_fields['user_name']]);
      if ($image_row['user_id'] != GUEST && empty($url_show_profile)) {
        $show_user_name = "<a href=\"".$site_sess->url(ROOT_PATH."member.php?action=showprofile&".URL_USER_ID."=".$image_row['user_id'])."\" target=\"_blank\">$show_user_name</a>";
      }
      echo "<td>".$show_user_name."</a></td>\n";
      echo "<td>".format_date($config['date_format'], $image_row['image_date'])."</td>\n";
  		echo "<td><font color=$where>$ok_show</font></td><td>".substr(strrchr($image_row['image_media_file'],"."), 1)."</td>".($thumbs ? "<td><font color=$where_t>$ok_t_show</font></td><td>".substr(strrchr($image_row['image_thumb_file'],"."), 1)."</td>" : "")."<td>&nbsp;&nbsp;";
      show_text_link("View", "../../details.php?".URL_IMAGE_ID."=".$image_row['image_id'], 1);
      show_text_link($lang['edit'],"../images.php?action=editimage&image_id=".$image_row['image_id'], 1);
      show_text_link($lang['delete'],"files_check.php?action=removeimage&image_id=".$image_row['image_id']);
  		echo "</td></tr>\n";
  	$i++;
  	}
    echo "<tr class=\"tablefooter\">\n<td colspan=\"12\" align=\"left\" class=\"tableseparator\">\n&nbsp;";
    echo "<input type=\"submit\" value=\"  ".$lang['delete']."   \" class=\"button\">\n&nbsp;&nbsp;&nbsp;";
    echo "</tr></table></td></tr></table><br>";
   	show_text_link("Back", "files_check.php");
  }else{
  	echo "<p><b>Files Check Complete!<br /><br /> No more errors found.</b><p>\n";
  	show_text_link("Back", "files_check.php");
  }
}

show_admin_footer();
?>