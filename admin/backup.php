<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: backup.php                                           *
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

$nozip = 1;
define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');
include(ROOT_PATH.'includes/db_utils.php');

$default_tables = array(
  CATEGORIES_TABLE,
  COMMENTS_TABLE,
  GROUP_ACCESS_TABLE,
  GROUP_MATCH_TABLE,
  GROUPS_TABLE,
  IMAGES_TABLE,
  IMAGES_TEMP_TABLE,
  LIGHTBOXES_TABLE,
  POSTCARDS_TABLE,
  SESSIONS_TABLE,
  SESSIONVARS_TABLE,
  SETTINGS_TABLE,
  USERS_TABLE,
  WORDLIST_TABLE,
  WORDMATCH_TABLE
);

if ($action == "") {
    $action = "modifybackups";
}

if (isset($HTTP_GET_VARS['file']) || isset($HTTP_POST_VARS['file'])) {
    $file = (isset($HTTP_GET_VARS['file'])) ? get_basefile(trim($HTTP_GET_VARS['file'])) : get_basefile(trim($HTTP_POST_VARS['file']));
    if (0 !== stripos(realpath(ROOT_PATH.DATABASE_DIR."/".$file), realpath(ROOT_PATH.DATABASE_DIR))) {
        $file = "";
    };
} else {
    $file = "";
}

if ($action == "downloadbackup") {
    $size = @filesize(ROOT_PATH.DATABASE_DIR."/".$file);
    header("Content-type: application/x-unknown");
    header("Content-length: $size\n");
    header("Content-Disposition: attachment; filename=$file\n");
    readfile(ROOT_PATH.DATABASE_DIR."/".$file);
    exit;
}

if ($action == "showbackup") {
    ob_start();
    @ob_implicit_flush(0);
    if (preg_match("/\.gz$/i", $file)) {
        readgzfile(ROOT_PATH.DATABASE_DIR."/".$file);
    } else {
        readfile(ROOT_PATH.DATABASE_DIR."/".$file);
    }
    $contents = ob_get_contents();
    ob_end_clean();
    echo "<pre>".format_text($contents)."</pre>";
    exit;
}

if ($action == "deletebackup") {
    if (@unlink(ROOT_PATH.DATABASE_DIR."/".$file)) {
        $msg = "<p><b>".$lang['backup_delete_success']."</b></p>";
    } else {
        $msg = "<p><b>".$lang['backup_delete_error']."</b></p>";
    }
    $action = "modifybackups";
}

if ($action == "restorebackup") {
    ob_start();
    @ob_implicit_flush(0);
    if (preg_match("/\.gz$/i", $file)) {
        readgzfile(ROOT_PATH.DATABASE_DIR."/".$file);
    } else {
        readfile(ROOT_PATH.DATABASE_DIR."/".$file);
    }
    $contents = ob_get_contents();
    ob_end_clean();

    $error_log = array();
    if (!empty($contents)) {
        $split_file = split_sql_dump($contents);
        foreach ($split_file as $sql) {
            $sql = trim($sql);
            if (!empty($sql) and $sql[0] != "#") {
                @set_time_limit(1200);
                if (!$site_db->query($sql)) {
                    $error_log[] = $sql;
                }
            }
        }
    }

    if (!empty($error_log)) {
        $msg = "<p><b>".$lang['backup_restore_error']."</b></p>";
        $msg .= "<ol>";
        foreach ($error_log as $val) {
            $msg .= sprintf("<li>%s</li>", $val);
        }
        $msg .= "</ol>";
    } else {
        $msg .= sprintf("<p><b>%s</b></p>", $lang['backup_restore_success']);
    }

    $action = "modifybackups";
}

if ($action == "makebackup") {
    $db_tables = $HTTP_POST_VARS['db_tables'];
    $crlf = (get_user_os() == "WIN") ? "\r\n" : ((get_user_os() == "MAC") ? "\r" : "\n");

    $tables_info = array();
    $db = (get_mysql_version() >= 32306)  ? "`$db_name`" : $db_name;
    $result = $site_db->query("SHOW TABLE STATUS FROM $db");
    if ($result) {
        while ($row = $site_db->fetch_array($result)) {
            $tables_info[$row['Name']] = ((isset($row['Type'])) ? $row['Type'] : $row['Engine']);
        }
        $site_db->free_result($result);
    }

    ob_start();
    @ob_implicit_flush(0);

    echo "#----------------------------------------------------------".$crlf;
    echo "# Database Backup for ".$config['site_name'].$crlf;
    echo "# ".date("Y-m-d H:i").$crlf;
    echo "#----------------------------------------------------------".$crlf;
    foreach ($db_tables as $table) {
        if (!isset($tables_info[$table])) {
            continue;
        }
        @set_time_limit(1200);
        echo $crlf."#".$crlf."# Structure for Table ".$table.$crlf."#".$crlf;
        get_table_def_mysql($table, $crlf);
        get_table_content_mysql($table, $crlf);
    }

    $contents = ob_get_contents();
    ob_end_clean();

    @umask(0111);
    if ($config['gz_compress'] == 1 && extension_loaded("zlib")) {
        $file_name = "backup".date("YmdHi").".sql.gz";
        $fp = gzopen(ROOT_PATH.DATABASE_DIR."/".$file_name, "w9");
        $ok = gzwrite($fp, $contents);
        gzclose($fp);
    } else {
        $file_name = "backup".date("YmdHi").".sql";
        $fp = fopen(ROOT_PATH.DATABASE_DIR."/".$file_name, "w");
        $ok = fwrite($fp, $contents);
        fclose($fp);
    }

    $msg = ($ok) ? sprintf("<p><b>%s</b></p>", $lang['make_backup_success']) : sprintf("<p><b>%s</b></p>", $lang['make_backup_error']);
    $action = "modifybackups";
}

show_admin_header();

if ($action == "modifybackups") {
    ?>
  <script language="JavaScript">
  <!--
  function RestoreBackup(what) {
    if (confirm('<?php echo $lang['backup_restore_confirm']; ?> ' + what)) {
      window.location = "<?php echo $site_sess->url("backup.php?action=restorebackup"); ?>&file=" + what;
    }
  }
  function DeleteBackup(what) {
    if (confirm('<?php echo $lang['backup_delete_confirm']; ?> ' + what)) {
      window.location = "<?php echo $site_sess->url("backup.php?action=deletebackup"); ?>&file=" + what;
    }
  }
  //-->
  </script>
  <?php
  if ($db_servertype != "mysqli") {
      echo "<span class=\"marktext\"><b>Note:</b> You are not using MySQL. Maybe the backup function won't work!</span>";
  }
    if ($msg != "") {
        printf("<b>%s</b>\n", $msg);
    }

    $db_status = "";
    get_database_size();
    if (!empty($global_info['database_size']['total'])) {
        $db_status .= "<p><b>".$lang['database'].":</b><br>".$lang['homestats_total']." <b>".format_file_size($global_info['database_size']['total'])."</b>";
        if (!empty($global_info['database_size']['4images'])) {
            $db_status .= " / 4images: <b>".format_file_size($global_info['database_size']['4images'])."</b></p>";
        }
    }

    show_form_header("backup.php", "makebackup");
    show_table_header($lang['do_backup'], 2);
    $table_select = "<select name=\"db_tables[]\" size=\"10\" multiple>\n";

    $result = $site_db->query("SHOW tables");
    while ($row = $site_db->fetch_array($result)) {
        $table_select .= "<option value=\"".$row[0]."\"";
        if (in_array($row[0], $default_tables) && preg_match("/^".$table_prefix."/i", $row[0])) {
            $table_select .= " selected";
        }
        $table_select .= ">".$row[0]."</option>\n";
    }
    $table_select .= "</select>\n";
    show_custom_row($lang['do_backup_desc'].$db_status, $table_select);
    show_form_footer($lang['do_backup'], $lang['reset']);

    show_table_header($lang['list_backups'], 4);
    $handle = opendir(ROOT_PATH.DATABASE_DIR);
    $filelist = array();
    while ($file = @readdir($handle)) {
        if (is_file(ROOT_PATH.DATABASE_DIR."/".$file) && $file != "." && $file != ".." && preg_match("/\.sql/i", $file)) {
            $filelist[] = $file;
        }
    }
    closedir($handle);
    if (!empty($filelist)) {
        rsort($filelist);
        foreach ($filelist as $key => $file) {
            echo "<tr class=\"".get_row_bg()."\" width=\"30%\">\n<td><p class=\"rowtitle\">$file</p></td>\n";
            $file_time = format_date($config['date_format']." ".$config['time_format'], filemtime(ROOT_PATH.DATABASE_DIR."/".$file));
            echo "<td>".$file_time."</td>";
            $file_size = format_file_size(@filesize(ROOT_PATH.DATABASE_DIR."/".$file));
            echo "<td>".$file_size."</td>";
            echo "<td>";
            echo "<a href=\"javascript:RestoreBackup('".$file."')\">[".$lang['restore_backup']."]</a>&nbsp;&nbsp;";
            echo "<a href=\"javascript:DeleteBackup('".$file."')\">[".$lang['delete_backup']."]</a>&nbsp;&nbsp;";
            show_text_link($lang['download_backup'], "backup.php?action=downloadbackup&file=$file");
            show_text_link($lang['show_backup'], "backup.php?action=showbackup&file=$file");
            echo "</td></tr>";
        }
    } else {
        show_description_row($lang['no_backups'], 4);
    }
    show_table_footer();
}

show_admin_footer();
?>
