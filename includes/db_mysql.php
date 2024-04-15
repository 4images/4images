<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: db_mysql.php                                         *
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

class Db {
  var $no_error = 0;
  var $connection;
  var $query_id = 0;
  var $query_count = 0;
  var $query_time = 0;
  var $query_array = array();
  var $table_fields = array();

  function __construct($db_host, $db_user, $db_password = "", $db_name = "", $db_pconnect = 0) {
    $connect_handle = ($db_pconnect) ? "mysql_pconnect" : "mysql_connect";
    if (!$this->connection = @$connect_handle($db_host, $db_user, $db_password)) {
      $this->error("Could not connect to the database server (".safe_htmlspecialchars($db_host).", ".safe_htmlspecialchars($db_user).").", 1);
    }
    if ($db_name != "") {
      if (!@mysql_select_db($db_name)) {
        @mysql_close($this->connection);
        $this->error("Could not select database (".safe_htmlspecialchars($db_name).").", 1);
      }
    }
    mysql_set_charset('utf8', $this->connection);
    return $this->connection;
  }

  function escape($value) {
    return mysql_real_escape_string($value, $this->connection);
  }

  function close() {
    if ($this->connection) {
      if ($this->query_id) {
        @mysql_free_result($this->query_id);
      }
      return @mysql_close($this->connection);
    }
    else {
      return false;
    }
  }

  function query($query = "") {
    unset($this->query_id);
    if ($query != "") {
      if ((defined("PRINT_QUERIES") && PRINT_QUERIES == 1) || (defined("PRINT_STATS") && PRINT_STATS == 1)) {
        $startsqltime = explode(" ", microtime());
      }
      if (!$this->query_id = @mysql_query($query, $this->connection)) {
        $this->error("<b>Bad SQL Query</b>: ".safe_htmlspecialchars($query)."<br /><b>".safe_htmlspecialchars(mysql_error())."</b>");
      }
      if ((defined("PRINT_QUERIES") && PRINT_QUERIES == 1) || (defined("PRINT_STATS") && PRINT_STATS == 1)) {
        $endsqltime = explode(" ", microtime());
        $totalsqltime = round($endsqltime[0]-$startsqltime[0]+$endsqltime[1]-$startsqltime[1],3);
        $this->query_time += $totalsqltime;
        $this->query_count++;
      }
      if (defined("PRINT_QUERIES") && PRINT_QUERIES == 1) {
        $query_stats = htmlentities($query);
        $query_stats .= "<br><b>Querytime:</b> ".$totalsqltime;
        $this->query_array[] = $query_stats;
      }
      return $this->query_id;
    }
  }

  function fetch_array($query_id = -1, $assoc = 0) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    if ($this->query_id) {
      return ($assoc) ? mysql_fetch_assoc($this->query_id) : mysql_fetch_array($this->query_id);
    }
  }

  function free_result($query_id = -1) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    return @mysql_free_result($this->query_id);
  }

  function query_firstrow($query = "") {
    if ($query != "") {
      $this->query($query);
    }
    $result = $this->fetch_array($this->query_id);
    $this->free_result();
    return $result;
  }

  function get_numrows($query_id = -1) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    return mysql_num_rows($this->query_id);
  }

  function get_insert_id() {
    return ($this->connection) ? @mysql_insert_id($this->connection) : 0;
  }

  function get_next_id($column = "", $table = "") {
    if (!empty($column) && !empty($table)) {
      $sql = "SELECT MAX($column) AS max_id
              FROM $table";
      $row = $this->query_firstrow($sql);
      return (($row['max_id'] + 1) > 0) ? $row['max_id'] + 1 : 1;
    }
    else {
      return NULL;
    }
  }

  function get_numfields($query_id = -1) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    return @mysql_num_fields($this->query_id);
  }

  function get_fieldname($query_id = -1, $offset) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    return @mysql_field_name($this->query_id, $offset);
  }

  function get_fieldtype($query_id = -1, $offset) {
    if ($query_id != -1) {
      $this->query_id = $query_id;
    }
    return @mysql_field_type($this->query_id, $offset);
  }

  function affected_rows() {
    return ($this->connection) ? @mysql_affected_rows($this->connection) : 0;
  }

  function is_empty($query = "") {
    if ($query != "") {
      $this->query($query);
    }
    return (!mysql_num_rows($this->query_id)) ? 1 : 0;
  }

  function not_empty($query = "") {
    if ($query != "") {
      $this->query($query);
    }
    return (!mysql_num_rows($this->query_id)) ? 0 : 1;
  }

  function get_table_fields($table) {
    if (!empty($this->table_fields[$table])) {
      return $this->table_fields[$table];
    }
    $this->table_fields[$table] = array();
    $result = $this->query("SHOW FIELDS FROM $table");
    while ($row = $this->fetch_array($result)) {
      $this->table_fields[$table][$row['Field']] = $row['Type'];
    }
    return $this->table_fields[$table];
  }

  function error($errmsg, $halt = 0) {
    if (!$this->no_error) {
      global $user_info;
      if (!defined("4IMAGES_ACTIVE") || (isset($user_info['user_level']) && $user_info['user_level'] == ADMIN)){
        echo "<br /><font color='#FF0000'><b>DB Error</b></font>: ".$errmsg."<br />";
      } else {
        echo "<br /><font color='#FF0000'><b>An unexpected error occured. Please try again later.</b></font><br />";
      }
      if ($halt) {
        exit;
      }
    }
  }
} // end of class
?>
