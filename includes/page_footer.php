<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: page_footer.php                                      *
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

if (defined("PRINT_STATS") && PRINT_STATS == 1) {
  $starttime = explode(" ", $start_time);
  $endtime = explode(" ", microtime());
  $total_time = $endtime[0] - $starttime[0] + $endtime[1] - $starttime[1];
  $sql_time = $site_db->query_time;
  $php_time = $total_time - $sql_time;
  $gzip_text = ($config['gz_compress'] == 1) ? "GZIP compression enabled" : "GZIP compression disabled";
  $gzip_text .= ($config['gz_compress'] == 1 && !extension_loaded("zlib")) ? "*" : "";
  printf("<p align=\"center\"><font size=\"-2\">Page generated in %f seconds with ".$site_db->query_count." queries, spending %f seconds doing MySQL queries and %f doing PHP things. $gzip_text</font></p>", $total_time, $sql_time, $php_time);
}

if (defined("PRINT_QUERIES") && PRINT_QUERIES == 1) {
  echo implode('<br><br>', $site_db->query_array);
}

$site_db->close();
$site_sess->freeze();

if ($do_gzip_compress) {
  if (preg_match("/gzip/i", $HTTP_SERVER_VARS["HTTP_ACCEPT_ENCODING"])) {
    $encoding = "gzip";
  }
  elseif (preg_match("/x-gzip/i", $HTTP_SERVER_VARS["HTTP_ACCEPT_ENCODING"])) {
    $encoding = "x-gzip";
  }

  $gzip_contents = ob_get_contents();
  ob_end_clean();

  if (defined("PRINT_STATS") && PRINT_STATS == 1) {
    $s = sprintf ("\n<!-- Use Encoding:         %s -->", $encoding);
    $s .= sprintf("\n<!-- Not compress length:  %s -->", strlen($gzip_contents));
    $s .= sprintf("\n<!-- Compressed length:    %s -->", strlen(gzcompress($gzip_contents, $config['gz_compress_level'])));
    $gzip_contents .= $s;
  }

  $gzip_size = strlen($gzip_contents);
  $gzip_crc = crc32($gzip_contents);

  $gzip_contents = gzcompress($gzip_contents, $config['gz_compress_level']);
  $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

  header("Content-Encoding: $encoding");
  echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
  echo $gzip_contents;
  echo pack("V", $gzip_crc);
  echo pack("V", $gzip_size);
}

exit;
?>
