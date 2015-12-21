<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: security_utils.php                                   *
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

function compare_passwords($plain, $hashed) {
  // Backwards compatibility
  if (strpos($hashed, ':') === false) {
    return secure_compare(md5($plain), $hashed);
  }

  return secure_compare(salted_hash($plain, $hashed), $hashed);
}

function salted_hash($value, $salt = null, $length = PASSWORD_SALT_LENGTH, $hash_algo = PASSWORD_HASH_ALGO) {
  if ($salt === null) {
    $salt = random_string($length);
  }

  $salt = substr($salt, 0, $length);

  if (!function_exists('hash') && $hash_algo == 'md5') {
    $hash = md5($salt . $value);
  } else {
    $hash = hash($hash_algo, $salt . $value);
  }

  return $salt . ':' . $hash;
}

function random_string($length, $letters_only = false) {
  $str = '';

  if (!$letters_only) {
    while (strlen($str) <= $length) {
      $str .= md5(uniqid(rand(), true));
    }

    return substr($str, 0, $length);
  }

  for ($i = 0; $i < $length; $i++) {
    switch (mt_rand(1, 2)) {
      case 1:
        $str .= chr(mt_rand(65, 90));
        break;
      case 2:
        $str .= chr(mt_rand(97, 122));
        break;
    }
  }

  return $str;
}

function secure_compare($a, $b) {
  if (strlen($a) !== strlen($b)) {
    return false;
  }
  $result = 0;
  for ($i = 0; $i < strlen($a); $i++) {
    $result |= ord($a[$i]) ^ ord($b[$i]);
  }
  return $result == 0;
}

?>
