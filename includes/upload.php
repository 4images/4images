<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: upload.php                                           *
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

if (!function_exists("is_uploaded_file")) {
  function is_uploaded_file($file_name) {
    if (!$tmp_file = @get_cfg_var('upload_tmp_dir')) {
      $tmp_file = tempnam('','');
      $deleted = @unlink($tmp_file);
      $tmp_file = dirname($tmp_file);
    }
    $tmp_file .= '/'.get_basefile($file_name);
    return (preg_replace('#/+#', '/', $tmp_file) == $file_name) ? 1 : 0;
  }

  function move_uploaded_file($file_name, $destination) {
    return (is_uploaded_file($file_name)) ? ((copy($file_name, $destination)) ? 1 : 0) : 0;
  }
}

class Upload {

  var $upload_errors = array();
  var $accepted_mime_types = array();
  var $accepted_extensions = array();
  var $upload_mode = 3;

  var $image_type = "";
  var $max_width = array();
  var $max_height = array();
  var $max_size = array();
  var $upload_path = array();

  var $field_name;
  var $file_name;
  var $extension;

  var $image_size = 0;
  var $image_size_ok = 0;
  var $lang = array();

  function __construct() {
    global $config, $lang;

    $this->max_width['thumb'] = $config['max_thumb_width'];
    $this->max_width['media'] = $config['max_image_width'];
    $this->max_height['thumb'] = $config['max_thumb_height'];
    $this->max_height['media'] = $config['max_image_height'];

    $this->max_size['thumb'] = $config['max_thumb_size'] * 1024;
    $this->max_size['media'] = $config['max_media_size'] * 1024;

    $this->upload_mode = $config['upload_mode'];
    $this->lang = $lang;

    $this->set_allowed_filetypes();
  }

  function check_image_size() {
    $this->image_size = @getimagesize($this->upload_file);
    $ok = 1;
    if ($this->image_size[0] > $this->max_width[$this->image_type]) {
      $ok = 0;
      $this->set_error($this->lang['invalid_image_width']);
    }

    if ($this->image_size[1] > $this->max_height[$this->image_type]) {
      $ok = 0;
      $this->set_error($this->lang['invalid_image_height']);
    }
    return $ok;
  }

  function copy_file() {
    switch ($this->upload_mode) {
    case 1: // overwrite mode
      if (file_exists($this->upload_path[$this->image_type]."/".$this->file_name)) {
        @unlink($this->upload_path[$this->image_type]."/".$this->file_name);
      }
      $ok = move_uploaded_file($this->upload_file, $this->upload_path[$this->image_type]."/".$this->file_name);
      break;
    case 2: // create new with incremental extention
      $n = 2;
      $copy = "";
      while (file_exists($this->upload_path[$this->image_type]."/".$this->name.$copy.".".$this->extension)) {
        $copy = "_".$n;
        $n++;
      }
      $this->file_name = $this->name.$copy.".".$this->extension;
      $ok = move_uploaded_file($this->upload_file, $this->upload_path[$this->image_type]."/".$this->file_name);
      break;
    case 3: // do nothing if exists, highest protection
    default:
      if (file_exists($this->upload_path[$this->image_type]."/".$this->file_name)) {
       $this->set_error($this->lang['file_already_exists']);
       $ok = 0;
      }
      else {
        $ok = move_uploaded_file($this->upload_file, $this->upload_path[$this->image_type]."/".$this->file_name);
      }
      break;
    }
    @chmod($this->upload_path[$this->image_type]."/".$this->file_name, CHMOD_FILES);
    return $ok;
  }

  function check_max_filesize() {
    if ($this->HTTP_POST_FILES[$this->field_name]['size'] > $this->max_size[$this->image_type]) {
      return false;
    }
    else {
      return true;
    }
  }

  function save_file() {
    global $user_info;

    $this->upload_file = $this->HTTP_POST_FILES[$this->field_name]['tmp_name'];
    $ok = 1;
    if (empty($this->upload_file) || $this->upload_file == "none") {
      $this->set_error($this->lang['no_image_file']);
      $ok = 0;
    }

    if ($user_info['user_level'] != ADMIN) {
      if (!$this->check_max_filesize()) {
        $this->set_error($this->lang['invalid_file_size']);
        $ok = 0;
      }
      if (preg_match("/image/i", $this->HTTP_POST_FILES[$this->field_name]['type'])) {
        if (!$this->check_image_size()) {
          $ok = 0;
        }
      }
    }

    if (!$this->check_file_extension() || !$this->check_mime_type()) {
      $this->set_error($this->lang['invalid_file_type']. " (".$this->extension.", ".$this->mime_type.")");
      $ok = 0;
    }
    if ($ok) {
      if (!$this->copy_file()) {
        if (isset($this->lang['file_copy_error'])) {
          $this->set_error($this->lang['file_copy_error']);
        }
        $ok = 0;
      }
    }
    return $ok;
  }

  function upload_file($field_name, $image_type, $cat_id = 0, $file_name = "") {
    global $HTTP_COOKIE_VARS, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_POST_FILES;

    // Bugfix for: http://www.securityfocus.com/archive/1/80106
    if (isset($HTTP_COOKIE_VARS[$field_name]) || isset($HTTP_POST_VARS  [$field_name]) || isset($HTTP_GET_VARS   [$field_name])) {
      die("Security violation");
    }

    $this->HTTP_POST_FILES = $HTTP_POST_FILES;
    $this->image_type = $image_type;
    $this->field_name = $field_name;

    if ($cat_id) {
      $this->upload_path['thumb'] = THUMB_PATH."/".$cat_id;
      $this->upload_path['media'] = MEDIA_PATH."/".$cat_id;
    }
    else {
      $this->upload_path['thumb'] = THUMB_TEMP_PATH;
      $this->upload_path['media'] = MEDIA_TEMP_PATH;
    }

    if ($file_name != "") {
      preg_match("/(.+)\.(.+)/", $file_name, $regs);
      $this->name = $regs[1];
      preg_match("/(.+)\.(.+)/", $this->HTTP_POST_FILES[$this->field_name]['name'], $regs);
      $this->extension = strtolower($regs[2]);
      $this->file_name = $this->name.".".$this->extension ;
    }
    else {
      $this->file_name = $this->HTTP_POST_FILES[$this->field_name]['name'];
      $this->file_name = str_replace(" ", "_", $this->file_name);
      $this->file_name = str_replace("%20", "_", $this->file_name);
      $this->file_name = preg_replace("/[^-\._a-zA-Z0-9]/", "", $this->file_name);

      preg_match("/(.+)\.(.+)/", $this->file_name, $regs);
      $this->name = $regs[1];
      $this->extension = strtolower($regs[2]);
    }

    $this->mime_type = $this->HTTP_POST_FILES[$this->field_name]['type'];
    preg_match("/([a-z]+\/[a-z\-]+)/", $this->mime_type, $this->mime_type);
    $this->mime_type = $this->mime_type[1];

    if ($this->save_file()) {
      return $this->file_name;
    }
    else {
      return false;
    }
  }

  function check_file_extension($extension = "") {
    if ($extension == "") {
      $extension = $this->extension;
    }
    if (!in_array(strtolower($extension), $this->accepted_extensions[$this->image_type])) {
      return false;
    }
    else {
      return true;
    }
  }

  function check_mime_type()
  {
    if (!isset($this->accepted_mime_types[$this->extension]))
      return false;

    if (!is_array($this->accepted_mime_types[$this->extension]))
      return ($this->accepted_mime_types[$this->extension] == $this->mime_type);

    return (in_array($this->mime_type, $this->accepted_mime_types[$this->extension]));
  }

  function set_allowed_filetypes() {
    global $config;

    //Thumbnails
    $this->accepted_extensions['thumb'] = array(
      "jpg",
      "jpeg",
      "gif",
      "png"
    );

    //Media
    $this->accepted_extensions['media'] = $config['allowed_mediatypes_array'];

    $mime_type_match = array();
    include_once(ROOT_PATH.'includes/upload_definitions.php');
    $this->accepted_mime_types = $mime_type_match;
  }

  function get_upload_errors() {
    if (empty($this->upload_errors[$this->file_name])) {
      return "";
    }
    $error_msg = "";
    foreach ($this->upload_errors[$this->file_name] as $msg) {
      $error_msg .= "<b>".$this->file_name.":</b> ".$msg."<br />";
    }
    return $error_msg;
  }

  function set_error($error_msg) {
    $this->upload_errors[$this->file_name][] = $error_msg;
  }
} //end of class
?>
