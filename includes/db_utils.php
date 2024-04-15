<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: db_utils.php                                         *
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

function split_sql_dump($sql)
{
    $sql = preg_replace("/\r/s", "\n", $sql);
    $sql = preg_replace("/[\n]{2,}/s", "\n", $sql);
    $lines = explode("\n", $sql);
    $queries = array();
    $in_query = 0;
    $i = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$in_query) {
            if (preg_match("/^CREATE/i", $line)) {
                $in_query = 1;
                $queries[$i] = $line;
            } elseif (!empty($line) && $line[0] != "#") {
                $queries[$i] = preg_replace("/;$/", "", $line);
                $i++;
            }
        } elseif ($in_query) {
            if (preg_match("/^[\)]/", $line)) {
                $in_query = 0;
                $queries[$i] .= preg_replace("/;$/", "", $line);
                $i++;
            } elseif (!empty($line) && $line[0] != "#") {
                $queries[$i] .= $line;
            }
        }
    }
    return $queries;
}

function get_table_def_mysql($table, $crlf)
{
    global $site_db, $tables_info;
    $dump  = "DROP TABLE IF EXISTS $table;$crlf";
    if (get_mysql_version() >= 32321) {
        $site_db->query('SET SQL_QUOTE_SHOW_CREATE = 0');
        if ($row = $site_db->query_firstrow("SHOW CREATE TABLE $table")) {
            $dump .= str_replace("\n", $crlf, $row[1]);
        }
        $site_db->free_result();
        echo $dump.";".$crlf;
        return true;
    }
    $dump .= "CREATE TABLE $table (".$crlf;
    $result = $site_db->query("SHOW FIELDS FROM $table");
    while ($row = $site_db->fetch_array($result)) {
        $dump .= "   ".$row['Field']." ".((isset($row['Type'])) ? $row['Type'] : $row['Engine']);
        if ($row['Null'] != "YES") {
            $dump .= " NOT NULL";
        }
        if (isset($row['Default']) && $row['Default'] != "") {
            $dump .= " DEFAULT '".$row['Default']."'";
        }
        if ($row['Extra'] != "") {
            $dump .= " ".$row['Extra'];
        }
        $dump .= ",".$crlf;
    }
    $dump = preg_replace("/,".$crlf."$/", "", $dump);
    $site_db->free_result();

    $result = $site_db->query("SHOW KEYS FROM $table");
    $index = array();
    while ($row = $site_db->fetch_array($result)) {
        if ($row['Key_name'] != "PRIMARY" && $row['Non_unique'] == 0) {
            $row['Key_name'] = "UNIQUE|".$row['Key_name'];
        }
        if (isset($row['Comment']) && $row['Comment'] == "FULLTEXT") {
            $row['Key_name'] = "FULLTEXT|".$row['Key_name'];
        }
        if (!isset($index[$row['Key_name']])) {
            $index[$row['Key_name']] = $row['Column_name'];
        } else {
            $index[$row['Key_name']] .= ", ".$row['Column_name'];
        }
    }
    $site_db->free_result();

    if (!empty($index)) {
        foreach ($index as $key => $val) {
            preg_match("/(PRIMARY|UNIQUE|FULLTEXT)?[\|]?(.*)/i", $key, $regs);
            $dump .= ",".$crlf."   ".(!empty($regs[1]) ? $regs[1]." " : "")."KEY".(!empty($regs[2]) ? " ".$regs[2] : "")." (".$val.")";
        }
    }
    $dump .= $crlf.")".((isset($tables_info[$table])) ? " TYPE=".$tables_info[$table] : "").";";
    echo $dump.$crlf;
    return true;
}


function get_table_content_mysql($table, $crlf)
{
    global $site_db;
    $result = $site_db->query("SELECT * FROM $table");
    if ($result && $site_db->get_numrows($result)) {
        echo $crlf."#".$crlf."# Table Data for ".$table.$crlf."#".$crlf;

        $column_list = "";
        $num_fields = $site_db->get_numfields($result);
        for ($i = 0; $i < $num_fields; $i++) {
            $column_list .= (($column_list != "") ? ", " : "").$site_db->get_fieldname($i, $result);
        }
    }

    while ($row = $site_db->fetch_array($result)) {
        $dump = "INSERT INTO ".$table." (".$column_list.") VALUES (";
        for ($i = 0; $i < $num_fields; $i++) {
            $dump .= ($i > 0) ? ", " : "";
            if (!isset($row[$i])) {
                $dump .= "NULL";
            } elseif ($row[$i] == "0" || $row[$i] != "") {
                $type = $site_db->get_fieldtype($i, $result);
                if ($type == 1 || $type == 2 || $type == 9 || $type == 3 || $type == 8) {
                    $dump .= $row[$i];
                } else {
                    $search_array = array('\\', '\'', "\x00", "\x0a", "\x0d", "\x1a");
                    $replace_array = array('\\\\', '\\\'', '\0', '\n', '\r', '\Z');
                    if (get_php_version() >= 40005) {
                        $row[$i] = str_replace($search_array, $replace_array, $row[$i]);
                    } else {
                        for ($i = 0; $i < sizeof($search_array); $i++) {
                            $row[$i] = str_replace($search_array[$i], $replace_array[$i], $row[$i]);
                        }
                    }
                    $dump .= "'".$row[$i]."'";
                }
            } else {
                $dump .= "''";
            }
        }
        $dump .= ');';
        echo $dump.$crlf;
    }
    echo $crlf;
    return true;
}

function get_database_size()
{
    global $global_info, $site_db, $db_name, $table_prefix;

    if (!empty($global_info['database_size'])) {
        return $global_info['database_size'];
    }

    $database_size_total = 0;
    $database_size_4images = 0;
    if (get_mysql_version() >= 32303) {
        $db = (get_mysql_version() >= 32306)  ? "`$db_name`" : $db_name;
        if ($result = $site_db->query("SHOW TABLE STATUS FROM $db")) {
            while ($row = $site_db->fetch_array($result)) {
                if (preg_match('/^(MyISAM|ISAM|HEAP|InnoDB)$/i', ((isset($row['Type'])) ? $row['Type'] : $row['Engine']))) {
                    if ($table_prefix != "") {
                        if (preg_match("/^".$table_prefix."/", $row['Name'])) {
                            $database_size_4images += $row['Data_length'] + $row['Index_length'];
                        }
                        $database_size_total += $row['Data_length'] + $row['Index_length'];
                    } else {
                        $database_size_total += $row['Data_length'] + $row['Index_length'];
                    }
                }
            }
        }
    }
    $global_info['database_size'] = array(
    "total" => $database_size_total,
    "4images" => $database_size_4images
  );
    return $global_info['database_size'];
}
