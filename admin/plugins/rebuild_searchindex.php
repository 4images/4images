<?php // PLUGIN_TITLE: Rebuild Search Index
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: rebuild_searchindex.php                              *
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
define('ROOT_PATH', "./../../");
require(ROOT_PATH.'admin/admin_global.php');
include(ROOT_PATH.'includes/search_utils.php');

if ($config['language_dir'] == 'deutsch') {
  $lang_rebuild_title         = 'Suchindex neu erstellen';
  $lang_rebuild_batchsize     = 'Anzahl Bilder pro Durchgang';
  $lang_rebuild_batchstart    = 'Start-ID';
  $lang_rebuild_category      = 'Kategorie';
  $lang_rebuild_autoredirect  = 'Automatisch auf n&auml;chste Seite weiterleiten';
  $lang_rebuild_image         = 'Verarbeite Bild <b>%s</b>, ID %d ...';
  $lang_rebuild_continue      = 'Klicke hier um fortzufahren';
  $lang_rebuild_status        = 'Volltext-Indizierung zwischen ID %d und %d:';
  $lang_rebuild_success       = 'Suchindex neu erstellt!';
  $lang_rebuild_back          = 'Zur&uuml;ck';
  $lang_rebuild_empty         = 'Vor dem Neuerstellen des Suchindex, solltest Du den Index leeren.';
  $lang_rebuild_empty_click   = 'Klicke hier um den Index zu leeren!';
  $lang_rebuild_empty_confirm = 'Bist Du sicher, dass Du den Suchindex leeren willst?';
  $lang_rebuild_empty_success = 'Suchindex erfolgreich geleert';
} else {
  $lang_rebuild_title         = 'Rebuild search index';
  $lang_rebuild_batchsize     = 'Number of images to do per cycle';
  $lang_rebuild_batchstart    = 'Image ID to start at';
  $lang_rebuild_category      = 'Category';
  $lang_rebuild_autoredirect  = 'Include automatic JavaScript redirect to next page';
  $lang_rebuild_image         = 'Processing image <b>%s</b>, ID %d ...';
  $lang_rebuild_continue      = 'Click here to continue';
  $lang_rebuild_status        = 'Fulltext Indexing between ID %d and %d:';
  $lang_rebuild_success       = 'Search index rebuilt!';
  $lang_rebuild_back          = 'Back';
  $lang_rebuild_empty         = 'If you are reindexing, you may want to empty the indexes.';
  $lang_rebuild_empty_click   = 'Click Here to do so!';
  $lang_rebuild_empty_confirm = 'Are you sure you wish to empty the search index?';
  $lang_rebuild_empty_success = 'Index successfully emptied';
}

if ($action == "") {
  $action = "intro";
}

if (!defined('MIN_SEARCH_KEYWORD_LENGTH')) {
  define('MIN_SEARCH_KEYWORD_LENGTH', 3);
}
if (!defined('MAX_SEARCH_KEYWORD_LENGTH')) {
  define('MAX_SEARCH_KEYWORD_LENGTH', 25);
}

if (isset($HTTP_GET_VARS['batchstart']) || isset($HTTP_POST_VARS['batchstart'])) {
  $batchstart = (isset($HTTP_GET_VARS['batchstart'])) ? intval($HTTP_GET_VARS['batchstart']) : intval($HTTP_POST_VARS['batchstart']);
  $site_sess->set_session_var("rsibatchstart", $batchstart);
}
else {
  $batchstart = $site_sess->get_session_var("rsibatchstart");
  $batchstart = $batchstart !== "" ? intval($batchstart) : 0;
}

if (isset($HTTP_GET_VARS['batchsize']) || isset($HTTP_POST_VARS['batchsize'])) {
  $batchsize = (isset($HTTP_GET_VARS['batchsize'])) ? intval($HTTP_GET_VARS['batchsize']) : intval($HTTP_POST_VARS['batchsize']);
  if (!$batchsize) {
    $batchsize = 25;
  }
  if (isset($HTTP_POST_VARS['batchsize'])) {
    $site_sess->set_session_var("rsibatchsize", $batchsize);
  }
}
else {
  $batchsize = intval($site_sess->get_session_var("rsibatchsize"));
  if (!$batchsize) {
    $batchsize = 50;
  }
}

if (isset($HTTP_POST_VARS['cat_id'])) {
  $site_sess->set_session_var("rsicatid", $cat_id);
}

if (isset($HTTP_GET_VARS['autoredirect']) || isset($HTTP_POST_VARS['autoredirect'])) {
  $autoredirect = (isset($HTTP_GET_VARS['autoredirect'])) ? intval($HTTP_GET_VARS['autoredirect']) : intval($HTTP_POST_VARS['autoredirect']);
  if (isset($HTTP_POST_VARS['autoredirect'])) {
    $site_sess->set_session_var("rsiautoredirect", $autoredirect);
  }
}
else {
  $autoredirect = $site_sess->get_session_var("rsiautoredirect");
  $autoredirect = $autoredirect !== "" ? intval($autoredirect) : 1;
}

if (isset($HTTP_GET_VARS['subcat']) || isset($HTTP_POST_VARS['subcat'])) {
  $subcat = (isset($HTTP_GET_VARS['subcat'])) ? intval($HTTP_GET_VARS['subcat']) : intval($HTTP_POST_VARS['subcat']);
  if (isset($HTTP_POST_VARS['subcat'])) {
    $site_sess->set_session_var("rsisubcat", $subcat);
  }
}
else {
  $subcat = $site_sess->get_session_var("rsisubcat");
  $subcat = $subcat !== "" ? intval($subcat) : 1;
}

function next_step($batchstart, $batchsize, $autoredirect = 0)
{
  global $site_sess, $cat_id, $subcat, $lang_rebuild_continue;
  $page = $site_sess->url("rebuild_searchindex.php?action=buildsearchindex&batchstart=".$batchstart."&batchsize=".$batchsize."&autoredirect=".$autoredirect."&cat_id=".$cat_id."&subcat=".$subcat);
?>
<br />
<table border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="tableseparator">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td class="tablerow2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $page; ?>" onclick="dorefresh(-1, this.href); return false;"><b><?php echo $lang_rebuild_continue; ?></b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />
<script type="text/javascript">
  var timerID;
  var timeoutNum = 10;
  var div = document.createElement("div");
  document.body.appendChild(div);
  function dorefresh(timeout, page)
  {
    var timeout = timeout || -1;
    if (timeout > 0)
    {
      var m = "Redirecting" + new Array(timeoutNum - timeout).join(" .");
      window.status = m;
      div.innerHTML = "<br />" + m;
      timeout--;
      timerID = setTimeout('dorefresh(' + timeout + ', "' + page + '");', 100);
    }
    else
    {
      clearTimeout(timerID);
      window.status=div.innerHTML="";
      window.location.replace(page);
    }
  }
<?php
  if ($autoredirect) {
?>
  dorefresh(timeoutNum, "<?php echo $page; ?>");
<?php
  }
?>
</script>
<?php
}

show_admin_header();

if ($action == "emptyindex") {
  $sql = "SELECT VERSION()";
  $version = $site_db->query_firstrow($sql);
  if (version_compare($version[0], "3.23.32", ">")) {
    $site_db->query("TRUNCATE TABLE ".WORDMATCH_TABLE);
    $site_db->query("TRUNCATE TABLE ".WORDLIST_TABLE);
  }
  else {
    $site_db->query("DELETE FROM ".WORDMATCH_TABLE);
    $site_db->query("ALTER TABLE ".WORDMATCH_TABLE." AUTO_INCREMENT=1");
    $site_db->query("DELETE FROM ".WORDLIST_TABLE);
    $site_db->query("ALTER TABLE ".WORDLIST_TABLE." AUTO_INCREMENT=1");
  }
  $site_sess->set_session_var("rsimsg", "<p><b>" . $lang_rebuild_empty_success . "</b></p>");
  redirect("rebuild_searchindex.php");
}

if ($action == "intro") {
  $msg = $site_sess->get_session_var("rsimsg");
  if ($msg) {
    echo $msg;
    $site_sess->drop_session_var("rsimsg");
  }
?>
  <script language="JavaScript">
  <!--
  function ConfirmEmptySearchindex()
  {
    if (confirm('<?php echo $lang_rebuild_empty_confirm; ?>')) {
      window.location.replace("<?php echo $site_sess->url("rebuild_searchindex.php?action=emptyindex"); ?>");
    }
  }
  //-->
  </script>
<?php
  if (!$cat_id) {
    $cat_id = intval($site_sess->get_session_var("rsicatid"));
  }
  show_form_header("rebuild_searchindex.php", "buildsearchindex");
  show_table_header($lang_rebuild_title, 2);
  show_input_row($lang_rebuild_batchsize, "batchsize", $batchsize);
  show_input_row($lang_rebuild_batchstart, "batchstart", $batchstart);
  show_cat_select_row($lang_rebuild_category, $cat_id, 2);
  show_radio_row("Include subcategories", "subcat", $subcat);
  show_radio_row($lang_rebuild_autoredirect, "autoredirect", $autoredirect);
  show_form_footer($lang['submit'], $lang['reset'], 2);
  echo "<p align=\"center\"><b>" . $lang_rebuild_empty;
  echo " <a href=\"javascript:ConfirmEmptySearchindex()\">" . $lang_rebuild_empty_click . "</a></b></p>";
}

if ($action == "buildsearchindex") {
  $sql = "SELECT MAX(image_id) as max
          FROM ".IMAGES_TABLE;
  $row = $site_db->query_firstrow($sql);
  $max = (isset($row['max'])) ? $row['max'] : 0;
  $cat_filter = "1=1";
  $order = " ORDER BY image_id ASC LIMIT $batchsize";
  $filter = "image_id >= " . $batchstart . $order;
  if ($cat_id) {
    $cat_ids = array($cat_id);
    if ($subcat) {
      $subcat_ids = array();
      get_subcat_ids($cat_id, $cat_id, $cat_parent_cache);
      if (isset($subcat_ids[$cat_id])) {
        $cat_ids = array_merge($cat_ids, array_values($subcat_ids[$cat_id]));
      }
    }
    $cat_filter = "cat_id IN (" . implode(",", $cat_ids) . ")";
    $filter = $cat_filter . " AND " . $filter;
  }
  $batchend = $batchstart + $batchsize - 1;
  if ($batchend >= $max) {
    $batchend = $max;
  }

  echo "<b>" . sprintf($lang_rebuild_status, $batchstart, $batchend) . "</b><p>";

  $sql = "SELECT *
          FROM ".IMAGES_TABLE."
          WHERE $filter";
  $result = $site_db->query($sql);
  $num_rows = $site_db->get_numrows($result);
  while ($row = $site_db->fetch_array($result)) {
    if ($cat_id) {
      $batchend = $row['image_id'];
    }
    printf($lang_rebuild_image, $row['image_name'], $row['image_id']);
    flush();
    @set_time_limit(90);
    $search_words = array();
    foreach ($search_match_fields as $image_column => $match_column) {
      if (isset($row[$image_column])) {
        $search_words[$image_column] = $row[$image_column];
      }
    }
    remove_searchwords($row['image_id']);
    add_searchwords($row['image_id'], $search_words);
    echo " <b>OK</b><br />\n";
    flush();
  }
  if ($num_rows) {
    $sql = "SELECT cat_id
            FROM ".IMAGES_TABLE."
            WHERE $cat_filter AND image_id >= " . ($batchend + 1) . $order;
    $num_rows = $site_db->query_firstrow($sql);
  }
  if ($num_rows) {
    next_step($batchend + 1, $batchsize, $autoredirect);
    show_text_link($lang_rebuild_back, "rebuild_searchindex.php");
  }
  else {
    echo "<p><b>" . $lang_rebuild_success . "</b><p>\n";
    show_text_link($lang_rebuild_back, "rebuild_searchindex.php");
    $site_sess->set_session_var("rsibatchstart", 0);

  }
}

show_admin_footer();
?>