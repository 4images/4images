<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: admin_functions.php                                  *
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

function get_iptc_insert_link($file, $iptc_tag, $input, $add_text = 1) {
  global $lang;
  if ($imageinfo = @getimagesize($file, $info)) {
    if (isset($info['APP13'])) {
      $iptc = iptcparse($info['APP13']);

      if (is_array($iptc)) {
        $separator = "";
        switch ($iptc_tag) {
        case "caption":
          if (isset($iptc['2#120'][0])) {
            $value = trim($iptc['2#120'][0]);
          }
          break;

        case "date_created":
          if (isset($iptc['2#055'][0])) {
            $value = $iptc['2#055'][0];
            $value = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $value);
          }
          break;

        case "keyword":
          $value = "";
          if (isset($iptc['2#025'])) {
            foreach ($iptc['2#025'] as $val) {
              $value .= (($value != "" ) ? "," : "").trim($val);
            }
          }
          $separator = ",";
          break;

        case "object_name":
          if (isset($iptc['2#005'][0])) {
            $value = trim($iptc['2#005'][0]);
          }
          break;

        default:
          $value = "";
          break;
        }
        if (!empty($value)) {
          $html = "\n<input type=\"hidden\" name=\"hidden_".$input."\" value=\"".$value." \">\n";
          $html .= "<script language=\"javascript\">\n<!--\n";
          $html .= "this.document.writeln('<br /><br /><input type=\"button\" value=\"IPTC ".str_replace(":", "", $lang['iptc_'.$iptc_tag])." &raquo;\" onClick=\"this.form.".$input.".value=".(($add_text) ? "this.form.".$input.".value + ".($separator ? "(this.form.".$input.".value.replace(/^\s+/, \'\').replace(/\s+$/, \'\') == \'\' ? \'\' : \'".$separator."\')+" : "") : "")."this.form.hidden_".$input.".value\">');";
          $html .= "\n//-->\n</script>\n";
          return $html;
        }
      }
    }
  }
}

function copy_media($image_media_file, $from_cat = 0, $to_cat = 0) {
  global $config;

  if (is_remote($image_media_file)) {
    return $image_media_file;
  }

  $image_src = ($from_cat != -1) ? MEDIA_PATH.(($from_cat != 0) ? "/".$from_cat : "") : MEDIA_TEMP_PATH;
  $image_dest = ($to_cat != -1) ? MEDIA_PATH.(($to_cat != 0) ? "/".$to_cat : "") : MEDIA_TEMP_PATH;
  return copy_file($image_src, $image_dest, $image_media_file, $image_media_file, $config['upload_mode']);
}


function copy_thumbnail($image_media_file, $image_thumb_file, $from_cat = 0, $to_cat = 0) {
  if (is_remote($image_thumb_file)) {
    return $image_thumb_file;
  }

  $thumb_src = ($from_cat != -1) ? THUMB_PATH.(($from_cat != 0) ? "/".$from_cat : "") : THUMB_TEMP_PATH;
  $thumb_dest = ($to_cat != -1) ? THUMB_PATH.(($to_cat != 0) ? "/".$to_cat : "") : THUMB_TEMP_PATH;

  if ($image_thumb_file != "" && file_exists($thumb_src."/".$image_thumb_file)) {
    $thumb_extension = get_file_extension($image_thumb_file);
    $new_thumb = get_file_name($image_media_file).".".$thumb_extension;
    if ($new_thumb = copy_file($thumb_src, $thumb_dest, $image_thumb_file, $new_thumb, 1))
    {
      $image_thumb_file = $new_thumb;
    }
  }
  return $image_thumb_file;
}

function copy_file($image_src, $image_dest, $image_media_file, $dest_file_name, $type, $filter = 1, $move = 1)
{
  $image_src_file = $image_src."/".$image_media_file;
  $dest_file_name = ($filter) ? filterFileName($dest_file_name) : $dest_file_name;
  $ok = 0;
  if (!file_exists($image_dest) || !is_dir($image_dest))
  {
		$oldumask = umask(0);
		$result = _mkdir($image_dest);
        @chmod($image_dest, CHMOD_DIRS);
		umask($oldumask);
  }
  switch ($type) {
  case 1: // overwrite mode
    if (file_exists($image_src."/".$image_media_file)) {
      if (file_exists($image_dest."/".$dest_file_name)) {
        unlink($image_dest."/".$dest_file_name);
      }
      $ok = copy($image_src."/".$image_media_file, $image_dest."/".$dest_file_name);
    }
    break;

  case 2: // create new with incremental extention
     if (file_exists($image_src."/".$image_media_file)) {
       $file_extension = get_file_extension($dest_file_name);
       $file_name = get_file_name($dest_file_name);

       $n = 2;
       $copy = "";
       while (file_exists($image_dest."/".$file_name.$copy.".".$file_extension)) {
         $copy = "_".$n;
         $n++;
       }
       $new_file = $file_name.$copy.".".$file_extension;
       $ok = copy($image_src."/".$image_media_file, $image_dest."/".$new_file);
       $dest_file_name = $new_file;
     }
     break;

   case 3: // do nothing if exists, highest protection
   default:
     if (file_exists($image_src."/".$image_media_file)) {
       if (file_exists($image_dest."/".$dest_file_name)) {
         $ok = 0;
       }
       else {
         $ok = copy($image_src."/".$image_media_file, $image_dest."/".$dest_file_name);
       }
     }
     break;
  }

  if ($ok) {
    if ($move)
    {
      @unlink($image_src_file);
    }
    @chmod($image_dest."/".$dest_file_name, CHMOD_FILES);
    return $dest_file_name;
  }
  else {
    return false;
  }
}

function show_admin_header($headinsert = "") {
  global $newlangfile, $config, $old_language_dir, $self_url, $site_sess, $lang;

  header ("Cache-Control: no-store, no-cache, must-revalidate");
  header ("Cache-Control: pre-check=0, post-check=0, max-age=0", false);
  header ("Pragma: no-cache");
  header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

  $onload = "";
  if ($newlangfile && strstr($self_url, "settings.php") === false) {
    $browser_language = getenv('HTTP_ACCEPT_LANGUAGE');
    if (strstr($browser_language, "de") !== false) {
      $alert_msg = "Ihr in der Konfiguration angegebenes Language-Pack \\'\\'$old_language_dir\\'\\' wurde nicht gefunden. Bitte ändern Sie Ihre Spracheinstellungen unter \\'\\'Konfiguration -> Einstellungen -> Allgemeine Einstellungen\\'\\'.\\n Es wurde folgendes Pack gefunden und verwendet: \\'\\'$config[language_dir]\\'\\'.";
    }
    elseif (preg_match("/s(b|r)/", $browser_language)) {
      $alert_msg = "Vas Language-Pack \\'\\'$old_language_dir\\'\\' u konfiguraciji nemoze da bude nadjen. Molimo Vas da promenite ispod \\'\\'Konfiguracija -> Promena -> Generalne Promene\\'\\'.\\n Trenutno je sledeci jezik pronadjen i bice koriscen: \\'\\'$config[language_dir]\\'\\'.\\n\\nTranslation sponsored by: Nicky (http://www.nicky.net)";
    }
    else {
      $alert_msg = "Your configured language-pack \\'\\'$old_language_dir\\'\\' was not found. Please modify your language settings under \\'\\'Configuration -> Settings -> General Settings\\'\\'. The following language-pack was found and used: \\'\\'$config[language_dir]\\'\\'";
    }
    $onload = " onload=\"javascript:alert('$alert_msg')\"";
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html dir="<?php echo $lang['direction']; ?>">
  <head>
    <title>4images - Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['charset']; ?>">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>admin/cpstyle.css">
    <?php
    echo $headinsert;
    ?>
    <script language="JavaScript">
    <!--
    var statusWin, toppos, leftpos;
    toppos = (screen.height - 401)/2;
    leftpos = (screen.width - 401)/2;
    function showProgress() {
      statusWin = window.open('<?php echo $site_sess->url("progress.php"); ?>','Status','height=150,width=350,top='+toppos+',left='+leftpos+',location=no,scrollbars=no,menubars=no,toolbars=no,resizable=yes');
      statusWin.focus();
    }

    function hideProgress() {
      if (statusWin != null) {
        if (!statusWin.closed) {
          statusWin.close();
        }
      }
    }
    function CheckAll() {
      for (var i=0;i<document.form.elements.length;i++) {
        var e = document.form.elements[i];
        if ((e.name != 'allbox') && (e.type=='checkbox')) {
          e.checked = document.form.allbox.checked;
        }
      }
    }

    function CheckCheckAll() {
      var TotalBoxes = 0;
      var TotalOn = 0;
      for (var i=0;i<document.form.elements.length;i++) {
        var e = document.form.elements[i];
        if ((e.name != 'allbox') && (e.type=='checkbox')) {
          TotalBoxes++;
          if (e.checked) {
            TotalOn++;
          }
        }
      }
      if (TotalBoxes==TotalOn) {
        document.form.allbox.checked=true;
      }
      else {
        document.form.allbox.checked=false;
      }
    }
    // -->
    </script>
    <script type="text/javascript" language="javascript" src="<?php echo ROOT_PATH; ?>admin/browserSniffer.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo ROOT_PATH; ?>admin/calendar.js"></script>
  </head>
  <body leftmargin="20" topmargin="20" marginwidth="20" marginheight="20" bgcolor="#FFFFFF" text="#0F5475" link="#0F5475" vlink="#0F5475" alink="#0F5475"<?php echo $onload; ?>>
<?php
}

function show_admin_footer() {
  global $site_db, $start_time, $alluserinfo, $do_gzip_compress, $nozip, $config;
?>
  </body>
</html>
<?php
  $site_db->close();
  if ($do_gzip_compress) {
    if (preg_match("/gzip/i", $HTTP_SERVER_VARS["HTTP_ACCEPT_ENCODING"])) {
      $encoding = "gzip";
    }
    elseif (preg_match("/x-gzip/i", $HTTP_SERVER_VARS["HTTP_ACCEPT_ENCODING"])) {
      $encoding = "x-gzip";
    }

    $gzip_contents = ob_get_contents();
    ob_end_clean();

    if (defined("PRINT_STATS") && PRINT_STATS == 1){
      $s = sprintf ("<!-- Use Encoding:         %s -->\n", $encoding);
      $s .= sprintf("<!-- Not compress length:  %s -->\n", strlen($gzip_contents));
      $s .= sprintf("<!-- Compressed length:    %s -->\n", strlen(gzcompress($gzip_contents, $config['gz_compress_level'])));
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
}

function get_calendar_js($id, $value)
{
    $cleanname = preg_replace("/[^-\._a-zA-Z0-9]/", "", $id);
?>
<script language="JavaScript" type="text/javascript">
var root_path = '<?php echo ROOT_PATH; ?>';
<!--
function calendarSetDate_<?php echo $cleanname; ?>(day, month, year)
{
    var split = document.getElementById('<?php echo $id; ?>').value.split(' ');
    var currTime = split[1] ? split[1] : '';

    if (day < 10) {
        day = '0' + day;
    }

    if (month < 10) {
        month = '0' + month;
    }

    var value = year + '-' + month + '-' + day;

    if (currTime) {
         value += ' ' + currTime;
    }

    document.getElementById('<?php echo $id; ?>').value = value;
}

var calendar_<?php echo $cleanname; ?> = new calendar('calendar_<?php echo $cleanname; ?>', 'calendarSetDate_<?php echo $cleanname; ?>');
<?php
if ($value) {
?>
calendar_<?php echo $cleanname; ?>.setCurrentMonth(<?php echo intval(date('m', strtotime($value))); ?>);
calendar_<?php echo $cleanname; ?>.setCurrentYear(<?php echo date('Y', strtotime($value)); ?>);
<?php
}
?>
calendar_<?php echo $cleanname; ?>.writeHTML();
//-->
</script>
<?php

}

function get_row_bg() {
  global $bgcounter;
  return ($bgcounter++ % 2 == 0) ? "tablerow" : "tablerow2";
}

function show_table_header($title, $colspan = 2, $anchor = "") {
  global $bgcounter;
  echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n<tr>\n<td class=\"tableborder\">\n<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
  echo "<tr class=\"tableheader\">\n<td colspan=\"$colspan\"><a name=\"".$anchor."\"><b><span class=\"tableheader\">";
  echo $title;
  echo "</span></b></a>\n</td>\n</tr>\n";
  $bgcounter = 0;
}

function show_table_separator($title, $colspan = 2, $anchor = "") {
  global $bgcounter;
  echo "<tr class=\"tableseparator\">\n<td colspan=\"$colspan\"><a name=\"".$anchor."\"><b><span class=\"tableseparator\">".$title."</span></b></a></td>\n</tr>\n";
  $bgcounter = 0;
}

function show_table_footer() {
  echo "</table>\n</td>\n</tr>\n</table><br />\n";
}

function show_form_header($phpscript, $action = "", $name = "formular", $uploadform = 0) {
  global $site_sess;

  if ($uploadform) {
    $upload = " ENCTYPE=\"multipart/form-data\"";
  }
  else {
    $upload = "";
  }
  echo "<form action=\"".$site_sess->url(safe_htmlspecialchars(strip_tags($phpscript)))."\"".$upload." name=\"".$name."\" method=\"post\">\n";
  if ($action != "") {
    echo "<input type=\"hidden\" name=\"action\" value=\"".$action."\">\n";
  }
}

function show_form_footer($submitname = "Submit", $resetname = "Reset", $colspan = 2, $goback = "", $javascript = "") {
	echo "<tr class=\"tablefooter\">\n<td colspan=\"".$colspan."\" align=\"center\">\n&nbsp;";
  if ($submitname != "") {
    echo "<input type=\"submit\" value=\"   ".$submitname."   \" class=\"button\"";
    if ($javascript != "") {
      echo " ".$javascript;
    }
    echo ">\n";
  }
  if ($resetname != "") {
    echo "<input type=\"reset\" value=\"   ".$resetname."   \" class=\"button\">\n";
  }
  if ($goback != "") {
		echo "<input type=\"submit\" name=\"goback\" value=\"   ".$goback."   \" class=\"button\">\n";
  }
  echo "&nbsp;\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n</form>\n";
}

function show_custom_row($title, $value) {
  echo "<tr class=\"".get_row_bg()."\" valign=\"top\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td><p>".$value."</p></td>\n</tr>\n";
}

function show_num_select_row($title, $option, $desc = "") {
  global $site_sess, $PHP_SELF, $action, $$option;
  echo "<tr class=\"".get_row_bg()."\">\n<td><p>".$title."</p></td>\n";
  echo "<td align=\"right\"><p>".$desc;
  $url = addslashes(str_replace('"', '&quot;', $PHP_SELF));
  $url .= preg_match("/\?/", $url) ? "&amp;" : "?";
  $url .= "action=".$action;
  $url = $site_sess->url($url);
  echo "<select name=\"num\" onchange=\"window.location=('".$url."&";
  echo $option."='+this.options[this.selectedIndex].value)\">\n";
  for ($i = 1; $i < 11; $i++) {
    echo "<option value=\"$i\"";
    if ($i == ${$option}) {
      echo " selected";
    }
    echo ">".$i."</option>\n";
  }
  echo "</select></p></td>\n</tr>\n";
}

function show_upload_row($title, $name, $extra = "", $value = "") {
  global $error, $HTTP_POST_VARS, $textinput_size;
  if (isset($error[$name]) || isset($error['remote_'.$name])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS['remote_'.$name])/* && $value == ""*/) {
    $value = stripslashes($HTTP_POST_VARS['remote_'.$name]);
  }

  echo "<tr class=\"".get_row_bg()."\" valign='top'>\n<td><p class=\"rowtitle\">$title</p></td>\n";
  echo "<td><p>";
  echo "<b>Upload:</b><br><input type=\"file\" name=\"".$name."\"><br>";
  echo "<b>URL:</b><br><input type=\"text\" name=\"remote_".$name."\" value=\"".$value."\" size=\"".$textinput_size."\">";
  echo $extra."</p></td>\n</tr>\n";
}

function show_image_row($title, $src, $border = 0, $delete_box = "", $height = 0, $width = 0) {
  global $HTTP_POST_VARS, $lang;
  $dimension = "";
  if ($height) {
    $dimension .= " height=\"".$height."\"";
  }
  if ($width) {
    $dimension .= " width=\"".$width."\"";
  }
  echo "<tr class=\"".get_row_bg()."\" valign='top'>\n<td><p class=\"rowtitle\">".$title."</p></td>\n";
  echo "<td><img src=\"".$src."\"".$dimension." border=\"".$border."\" alt=\"\">";
  if ($delete_box != "") {
    $checked = '';
    if (isset($HTTP_POST_VARS[$delete_box]) && $HTTP_POST_VARS[$delete_box] == 1) {
      $checked = ' checked="checked"';
    }
    echo "<input type=\"checkbox\" name=\"".$delete_box."\" value=\"1\"".$checked."> ".$lang['delete'];
  }
  echo "</td>\n</tr>\n";
}

function show_description_row($text, $colspan = 2) {
  echo "<tr class=\"".get_row_bg()."\">\n<td colspan=\"".$colspan."\">".$text."</td>\n</tr>\n";
}

function show_radio_row($title, $name, $value = 1) {
  global $HTTP_POST_VARS, $lang;
  if (isset($HTTP_POST_VARS[$name])) {
    $value = $HTTP_POST_VARS[$name];
  }
  echo "<tr class=\"".get_row_bg()."\">\n";
  echo "<td><p class=\"rowtitle\">".$title."</p></td>\n<td><p>";
  echo "<input type=\"radio\" name=\"$name\" value=\"1\"";
  if ($value == 1) {
    echo " checked=\"checked\"";
  }
  echo "> ".$lang['yes']."&nbsp;&nbsp;&nbsp;\n";
  echo "<input type=\"radio\" name=\"".$name."\" value=\"0\"";
  if ($value != 1) {
    echo " checked=\"checked\"";
  }
  echo "> ".$lang['no']." ";
  echo "</p></td>\n</tr>";
}

function show_input_row($title, $name, $value = "", $size = "") {
  global $error, $HTTP_POST_VARS, $textinput_size;
  $size = (empty($size)) ? $textinput_size : $size;
  if (isset($error[$name])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS[$name])/* && $value == ""*/) {
    $value = stripslashes($HTTP_POST_VARS[$name]);
  }
  echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td><p><input type=\"text\" size=\"".$size."\" name=\"".$name."\" value=\"".format_text($value, 2)."\"></p></td>\n</tr>\n";
}

function show_date_input_row($title, $name, $value = "", $size = "") {
  global $error, $HTTP_POST_VARS, $textinput_size;
  $size = (empty($size)) ? $textinput_size : $size;
  if (isset($error[$name])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS[$name])/* && $value == ""*/) {
    $value = stripslashes($HTTP_POST_VARS[$name]);
  }

  echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td><input type=\"text\" size=\"".$size."\" name=\"".$name."\" id=\"".$name."\" value=\"".format_text($value, 2)."\"> ";
  echo get_calendar_js($name, $value);
  echo "</td>\n</tr>\n";
}

function show_textarea_row($title, $name, $value = "", $cols = "", $rows = 10) {
  global $error, $HTTP_POST_VARS, $textarea_size;
  $cols = (empty($cols)) ? $textarea_size : $cols;
  if (isset($error[$name])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS[$name])/* && $value == ""*/) {
    $value = stripslashes($HTTP_POST_VARS[$name]);
  }
  echo "<tr class=\"".get_row_bg()."\" valign=\"top\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td><p><textarea name=\"".$name."\" rows=\"".$rows."\" cols=\"".$cols."\">".format_text($value, 2)."</textarea></p></td>\n</tr>\n";
}

function show_user_select_row($title, $user_id, $i = 0) {
  global $error, $lang, $HTTP_POST_VARS, $site_db, $user_table_fields, $user_select_row_cache;

  if (empty($user_select_row_cache)) {
    $sql = "SELECT ".get_user_table_field("", "user_id").get_user_table_field(", ", "user_name")."
            FROM ".USERS_TABLE."
            WHERE ".get_user_table_field("", "user_id")." <> ".GUEST."
            ORDER BY ".get_user_table_field("", "user_name")." ASC";
    $result = $site_db->query($sql);
    $user_select_row_cache = array();
    while ($row = $site_db->fetch_array($result)) {
      $user_select_row_cache[$row[$user_table_fields['user_id']]] = $row[$user_table_fields['user_name']];
    }
  }

  if (isset($error['user_id_'.$i]) || isset($error['user_id'])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS['user_id_'.$i])) {
    $user_id = $HTTP_POST_VARS['user_id_'.$i];
  }
  elseif (isset($HTTP_POST_VARS['user_id'])) {
    $user_id = $HTTP_POST_VARS['user_id'];
  }
  $i = ($i) ? "_".$i : "";
  echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n";
  echo "<td>\n";
  echo "<select name=\"user_id".$i."\" class=\"categoryselect\">\n";
  echo "<option value=\"".GUEST."\">".$lang['userlevel_guest']."</option>\n";
  echo "<option value=\"".GUEST."\">-------------------------------</option>\n";
  foreach ($user_select_row_cache as $key => $val) {
    echo "<option value=\"".$key."\"";
    if ($key == $user_id) {
      echo " selected=\"selected\"";
    }
    echo ">".format_text($val, 2)."</option>\n";
  }
  echo "</select>\n";
  echo "</td>\n</tr>\n";
}

function show_cat_select_row($title, $cat_id, $admin = 0, $i = 0) {
  global $error, $HTTP_POST_VARS;
  if (isset($error['cat_id_'.$i]) || isset($error['cat_id']) || isset($error['cat_parent_id'])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS['cat_parent_id'])) {
    $cat_id = $HTTP_POST_VARS['cat_parent_id'];
  }
  elseif (isset($HTTP_POST_VARS['cat_id_'.$i])) {
    $cat_id = $HTTP_POST_VARS['cat_id_'.$i];
  }
  elseif (isset($HTTP_POST_VARS['cat_id'])) {
    $cat_id = $HTTP_POST_VARS['cat_id'];
  }
  echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td>".get_category_dropdown($cat_id, 0, $admin, $i)."</td>\n</tr>\n";
}

function show_userlevel_select_row($title, $name = "user_level", $userlevel = "") {
  global $lang, $error, $HTTP_POST_VARS;
  if (isset($error[$name])) {
    $title = sprintf("<span class=\"marktext\">%s *</span>", $title);
  }
  if (isset($HTTP_POST_VARS[$name])/* && $userlevel == ""*/) {
    $userlevel = stripslashes($HTTP_POST_VARS[$name]);
  }
  echo "<tr class=\"".get_row_bg()."\">\n<td><p class=\"rowtitle\">".$title."</p></td>\n<td>\n";
  echo "<select name=".$name.">\n";
  echo "<option value=\"".GUEST."\"";
  if ($userlevel == GUEST || $userlevel == "") {
    echo " selected=\"selected\"";
  }
  echo ">--</option>\n";
  echo "<option value=\"".ADMIN."\"";
  if ($userlevel == ADMIN && $userlevel != "") {
    echo " selected=\"selected\"";
  }
  echo ">".$lang['userlevel_admin']."</option>\n";
  echo "<option value=\"".USER."\"";
  if ($userlevel == USER && $userlevel != "") {
    echo " selected=\"selected\"";
  }
  echo ">".$lang['userlevel_registered']."</option>\n";
  echo "<option value=\"".USER_AWAITING."\"";
  if ($userlevel == USER_AWAITING && $userlevel != "") {
    echo " selected=\"selected\"";
  }
  echo ">".$lang['userlevel_registered_awaiting']."</option>\n";
  echo "</select>\n</td>\n</tr>\n";
}

function show_hidden_input($name, $value = "", $htmlise = 1) {
  if ($htmlise) {
    $value = format_text($value, 2);
  }
  echo "<input type=\"hidden\" name=\"$name\" value=\"".$value."\">\n";
}

function show_text_link($text, $url, $newwin = 0) {
  global $site_sess;
  $target = ($newwin) ? " target=\"_blank\"" : "";
  echo "<a href=\"".$site_sess->url(safe_htmlspecialchars(strip_tags($url)))."\"".$target.">[".$text."]</a>&nbsp;&nbsp;";
}

function get_navrow_bg() {
  global $navbgcounter;
  return ($navbgcounter++ % 2 == 0) ? "#E5E5E5" : "#F5F5F5";
}

function show_nav_option($title, $url, $extra = "")  {
  global $site_sess;
  $bgcolor = get_navrow_bg();
  echo "<tr><td bgcolor=\"$bgcolor\" valign=top onmouseover=\"this.style.backgroundColor='#FFE673';this.style.cursor='hand';\" onclick=\"parent.frames['main'].location='".$site_sess->url($url)."'\" onmouseout=\"this.style.backgroundColor='".$bgcolor."'\">\n";
  echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\"><tr><td>\n";
  echo "<a href=\"".$site_sess->url(safe_htmlspecialchars(strip_tags($url)))."\" class=\"navlink\">".$title."</a> $extra\n";
  echo "</td></tr></table>\n";
  echo "</td></tr>\n";
  echo "<tr><td bgcolor=\"#FFFFFF\"><img src=\"".ROOT_PATH."admin/images/spacer.gif\"></td></tr>\n";
}

function show_nav_header($title)  {
  global $navbgcounter;
  echo "<tr><td class=navheader>\n";
  echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\"><tr><td class=\"navheader\">\n";
  echo $title;
  echo "</td></tr></table>\n";
  echo "</td></tr>\n";
  echo "<tr><td bgcolor=\"#FFFFFF\"><img src=\"".ROOT_PATH."admin/images/spacer.gif\"></td></tr>\n";
  $navbgcounter = 0;
}

function check_admin_date($date) {
  return (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})+(( )+[0-9]{2}:[0-9]{2}:([0-9]{2}))?$/', stripslashes(trim($date)))) ? 1 : 0;
}

function get_dir_size($dir) {
  $size = 0;
  $dir = (!preg_match("#/$#", $dir)) ? $dir."/" : $dir;
  $handle = @opendir($dir);
  while ($file = @readdir($handle)) {
    if (preg_match("/^\.{1,2}$/",$file)) {
      continue;
    }
    $size += (is_dir($dir.$file)) ? get_dir_size($dir.$file."/") : filesize($dir.$file);
  }
  @closedir($handle);
  return $size;
}

function show_additional_fields($type = "image", $image_row = array(), $table = IMAGES_TEMP_TABLE, $i = 0) {
  global $site_db, $lang;

  $field_type_array = "additional_".$type."_fields";
  global ${$field_type_array};

  if (!empty(${$field_type_array})) {
    $table_fields = $site_db->get_table_fields($table);
    foreach (${$field_type_array} as $key => $val) {
      if (!isset($table_fields[$key])) {
        continue;
      }
      $field_name = ($i) ? $key."_".$i : $key;
      $value = (isset($image_row[$key])) ? $image_row[$key] : "";
      switch($val[1]) {
      case "textarea":
        show_textarea_row($val[0], $field_name, $value);
        break;
      case "radio":
        show_radio_row($val[0], $field_name, ($value == "") ? 1 : $value);
        break;
      case "text":
      default:
        show_input_row($val[0], $field_name, $value);
      } // end switch
    }
  }
}
?>
