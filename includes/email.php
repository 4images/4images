<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: email.php                                            *
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

class Email {

  var $auth_type = "LOGIN"; // Default: "LOGIN". Set to "PLAIN" if required.
  var $no_error = 0;
  var $use_smtp;
  var $to;
  var $subject;
  var $body;
  var $bcc = array();
  var $from = "";
  var $from_email = "";
  var $word_wrap = 76;
  var $template_extension = "html";
  var $start = "{";
  var $end = "}";
  var $key_cache = array();
  var $val_cache = array();
  var $crlf = "\r\n";

  function Email() {
    global $config;
    $this->use_smtp = ($config['use_smtp'] == 1) ? 1 : 0;
    $this->smtp_auth = (!empty($config['smtp_username']) && !empty($config['smtp_password'])) ? 1 : 0;
    $this->crlf = ($this->use_smtp) ? "\r\n" : "\n";
  }

  function set_from($email, $name = "") {
    $this->from_email = $email;
    $this->from = sprintf("Return-Path: %s".$this->crlf, $email);
    $this->from .= ($name != "") ? sprintf("From: %s <%s>".$this->crlf, $name, $email) : sprintf("From: %s".$this->crlf, $email);
  }

  function set_to($to) {
    $this->to = $to;
  }

  function set_subject($subject) {
    $this->subject = $subject;
  }

  function register_vars($var_name, $value = "") {
    if (!is_array($var_name)) {
      if (!empty($var_name)) {
        $this->key_cache[$var_name] = "/".$this->add_identifiers($var_name)."/";
        $this->val_cache[$var_name] = $value;
      }
    }
    else {
      foreach ($var_name as $key => $val) {
        if (!empty($key)) {
          $this->key_cache[$key] = "/".$this->add_identifiers($key)."/";
          $this->val_cache[$key] = $val;
        }
      }
    }
  }

  function add_identifiers($var_name) {
    return preg_quote($this->start.$var_name.$this->end);
  }

  function get_template($template, $lang) {
    $path = ROOT_PATH."lang/".$lang."/email/".$template.".".$this->template_extension;
    $line = @implode("", @file($path));
    if (empty($line)) {
      $this->error("Couldn't open Template ".$path);
    }
    return $line;
  }

  function prepare_text($message) {
    $message = preg_replace("/\r\n/si", "\n", $message);
    if ($this->word_wrap) {
      $lines = explode("\n", $message);
      $message = "";
      for ($i = 0 ;$i < sizeof($lines); $i++) {
        $line_part = explode(" ", trim($lines[$i]));
        $buf = "";
        for ($j = 0; $j < count($line_part); $j++)  {
          $buf_o = $buf;
          $buf .= (($j == 0) ? "" : " ").$line_part[$j];
          if (strlen($buf) > $this->word_wrap && $buf_o != "") {
            $message .= $buf_o.$this->crlf;
            $buf = $line_part[$j];
          }
        }
        $message .= $buf.$this->crlf;
      }
    }
    return $message;
  }

  function set_body($template_name = "", $lang = "deutsch") {
    $template_name = trim($template_name);
    $body = "";
    if ($template_name != "") {
      $template = $this->get_template($template_name, $lang);
      $body = preg_replace($this->key_cache, $this->val_cache, $template);
    }
    $this->body = ((!empty($this->body)) ? $this->body : "").$this->prepare_text($body);
  }

  function set_simple_body($body = "") {
    $this->body = ((!empty($this->body)) ? $this->body : "").$this->prepare_text($body);
  }

  function set_bcc($bcc) {
    foreach ($bcc as $val) {
      $val = trim($val);
      if (preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', $val)) {
        $this->bcc[] = $val;
      }
    }
  }

  function create_header() {
    global $config;
    $header = "";
    if (empty($this->from)) {
      $header .= sprintf("Return-Path: %s\r\n", $config['site_email']);
      $header .= sprintf("From: %s\r\n", $config['site_email']);
    }
    else {
      $header .= $this->from;
    }
    //$header .= sprintf("Reply-to: %s\r\n", $config['site_email']);
    //$header .= sprintf("To: %s\r\n", $this->to);
    if (!empty($this->bcc) && !$this->use_smtp) {
      $bcc_list = "";
      foreach ($this->bcc as $key => $val) {
        $bcc_list .= (($bcc_list != "") ? ", " : "").$val;
      }
      $header .= sprintf("Bcc: %s\r\n", $bcc_list);
    }
    //$header .= sprintf("Subject: %s\r\n", $this->subject);
    return $header;
  }

  function send_email() {
    if ($this->use_smtp) {
      return ($this->smtp_mail($this->to, $this->subject, $this->body, $this->create_header())) ? 1 : 0;
    }
    else {
      return (mail($this->to, $this->subject, $this->body, $this->create_header())) ? 1 : 0;
    }
  }

  function smtp_mail($mail_to, $subject, $body, $headers = "") {
    global $config;
    $ok = 1;

    if (empty($config['smtp_host'])) {
      $config['smtp_host'] = "localhost";
    }

    // open socket.
    $fp = fsockopen($config['smtp_host'], 25);
    $result = fgets($fp, 1024);
    if (substr($result, 0, 3) != 220) {
      $ok = 0;
      $this->error("Invalid mail server response (service not ready?): $result", 1);
    }

    // send helo
    if ($this->smtp_auth) {
      fputs($fp, "EHLO ".$config['smtp_host'].$this->crlf);

      /**
       * Patch by forum user blueshift. Thanks!
       */

      // not ok until first valid server response
      $ok = 0;

      // fetch response line after line
      while (!feof($fp)) {
        $result = fgets($fp, 1024);
        if (strlen($result)!=0) {
          if (substr($result, 0, 3) == 250) {
            $ok = 1;
            // lower timeout after first valid response
            if (function_exists('stream_set_timeout')) {
              stream_set_timeout($fp, 1);
            }
          }
          else {
            // reset ok on error
	        $ok = 0;
            break;
          }
	    }
        else {
          // EOF
          break;
	    }
      }

      if ($ok == 0) {
        $this->error("EHLO invalid mail server response: $result", 1);
      }

      if (function_exists('stream_set_timeout')) {
        // reset timeout for subsequent ops
        stream_set_timeout($fp, 30);
      }

      if (strtoupper($this->auth_type) == "PLAIN") {
        fputs($fp, "AUTH PLAIN ".base64_encode($config['smtp_username'].chr(0).$config['smtp_password']).$this->crlf);
        $result = fgets($fp, 1024);
        if (substr($result, 0, 3) != 235) {
          $ok = 0;
          $this->error("AUTH PLAIN invalid mail server response: $result<br /> Maybe your SMTP Server does'nt support authentification. Try to leave Username and Password blank in your settings.", 1);
        }
      }
      else {
        fputs($fp, "AUTH LOGIN".$this->crlf);
        $result = fgets($fp, 1024);
        if (substr($result, 0, 3) != 334) {
          $ok = 0;
          $this->error("AUTH LOGIN invalid mail server response: $result<br /> Maybe your SMTP Server does'nt support authentification. Try to leave Username and Password blank in your settings.", 1);
        }

        fputs($fp, base64_encode($config['smtp_username']).$this->crlf);
        $result = fgets($fp, 1024);
        if (substr($result, 0, 3) != 334) {
          $ok = 0;
          $this->error("USERNAME invalid mail server response: $result", 1);
        }

        fputs($fp, base64_encode($config['smtp_password']).$this->crlf);
        $result = fgets($fp, 1024);
        if (substr($result, 0, 3) != 235) {
          $ok = 0;
          $this->error("PASSWORD invalid mail server response: $result", 1);
        }
      }
    }
    else {
      fputs($fp, "HELO ".$config['smtp_host'].$this->crlf);
      $result = fgets($fp, 1024);
      if (substr($result, 0, 3) != 250) {
        $ok = 0;
        $this->error("HELO invalid mail server response: $result", 1);
      }
    }

    // MAIL FROM
    if (empty($this->from_email)) {
      $this->from_email = $config['site_email'];
    }
    fputs($fp, "MAIL FROM: ".$this->from_email.$this->crlf);
    $result = fgets($fp, 1024);
    if (substr($result, 0, 3) != 250) {
      $ok = 0;
      $this->error("MAIL FROM invalid mail server response: $result", 1);
    }

    // RCPT TO
    $mail_to_array = explode(",", $mail_to);
    $to_header = "To: ";
    foreach ($mail_to_array as $key => $val) {
      $val = trim($val);
      fputs($fp, "RCPT TO: <$val>".$this->crlf);
      $result = fgets($fp, 1024);
      if (substr($result, 0, 3) != 250) {
        $ok = 0;
        $this->error("RCPT TO invalid mail server response: $result", 1);
      }
      $to_header .= "<$val>, ";
    }
    $to_header = preg_replace("/, $/", "", $to_header);

    if (!empty($this->bcc)) {
      foreach ($this->bcc as $key => $val) {
        fputs($fp, "RCPT TO: <$val>".$this->crlf);
        $result = fgets($fp, 1024);
        if (substr($result, 0, 3) != 250) {
          $ok = 0;
          $this->error("RCPT TO invalid mail server response: $result", 1);
        }
        //$to_header .= "<$val>, ";
      }
      //$to_header = preg_replace("/, $/", "", $to_header);
    }

    // DATA
    fputs($fp, "DATA".$this->crlf);
    $result = fgets($fp, 1024);
    if (substr($result, 0, 3) != 354) {
      $ok = 0;
      $this->error("DATA invalid mail server response: $result", 1);
    }

    // Send subject
    fputs($fp, "Subject: $subject".$this->crlf);

    // Send headers
    fputs($fp, $to_header.$this->crlf);
    $headers = preg_replace("/([^\r]{1})\n/", "\\1\r\n", $headers);
    fputs($fp, $headers.$this->crlf.$this->crlf);

    // Send body
    $body = preg_replace("/([^\r]{1})\n/", "\\1\r\n", $body);
		$body = preg_replace("/\n\n/", "\n\r\n", $body);
		$body = preg_replace("/\n\./", "\n..", $body);
    fputs($fp, $body.$this->crlf);

    // End of DATA: CRLF.CRLF
    fputs($fp, $this->crlf.".".$this->crlf);
    $result = fgets($fp, 1024);
    if (substr($result, 0, 3) != 250) {
      $ok = 0;
      $this->error("DATA(end): invalid mail server response: $result", 1);
    }

    // QUIT
    fputs($fp, "QUIT".$this->crlf);
    $result = fgets($fp, 1024);
    if (substr($result, 0, 3) != 221) {
      $ok = 0;
      $this->error("QUIT: invalid mail server response: $result", 1);
    }

    // Close connection
    fclose($fp);

    return $ok;
  }

  function reset($reset_template_vars = 0) {
    $this->to = "";
    $this->subject = "";
    $this->body = "";
    $this->bcc = array();
    $this->from = "";
    $this->from_email = "";
    if ($reset_template_vars) {
      $this->key_cache = array();
      $this->val_cache = array();
    }
  }

  function error($errmsg, $halt = 0) {
    if (!$this->no_error) {
      echo "<br /><font color='#FF0000'><b>Email Error</b></font>: ".$errmsg."<br />";
      if ($halt) {
        exit;
      }
    }
  }
} // end of class
?>
