<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: captcha_utils.php                                    *
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

$captcha_enable = $captcha_enable && function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled");
$captcha_ttf    = $captcha_ttf && function_exists("imagettfbbox") && function_exists("imagettftext");
$captcha_length = max((int)$captcha_length, 1);

$captcha_width  = max((int)$captcha_width, 1);
$captcha_height = max((int)$captcha_height, 1);

srand((double)microtime()*1000000);

function captcha_validate($code)
{
    global $site_sess, $captcha_enable, $user_info;

    if (!$captcha_enable || $user_info['user_level'] == ADMIN) {
        return true;
    }

    $sess_code = trim($site_sess->get_session_var('captcha'));

    $valid = $sess_code != '' && secure_compare($sess_code, $code);
    $site_sess->drop_session_var('captcha');

    return $valid;
}

function captcha_image()
{
    global $site_sess, $captcha_enable, $captcha_path, $captcha_ttf, $captcha_width, $captcha_height, $captcha_text_transparency;

    if (!$captcha_enable) {
        return;
    }

    $code = captcha_get_code();
    $site_sess->set_session_var('captcha', $code);

    $bg = captcha_get_background();

    if ($captcha_ttf) {
        $text = captcha_get_text($code);
    } else {
        $text = captcha_get_text_system($code);
    }

    $bg_width  = imagesx($bg);
    $bg_height = imagesy($bg);

    $image = imagecreatetruecolor($captcha_width, $captcha_height);

    imagecopyresampled(
        $image,
        $bg,
        0,
        0,
        0,
        0,
        $captcha_width,
        $captcha_height,
        $captcha_width,
        $captcha_height
    );

    imagecopymerge(
        $image,
        $text,
        0,
        0,
        0,
        0,
        $captcha_width,
        $captcha_height,
        $captcha_text_transparency
    );

    header("Content-type: image/jpeg");
    imagejpeg($image);
    imagedestroy($image);
}

function captcha_get_text_system($code)
{
    global $captcha_path, $captcha_text_color, $captcha_text_size, $captcha_width, $captcha_height, $captcha_filter_text;

    $image = imagecreatetruecolor($captcha_width, $captcha_height);

    $text_font = rand(1, 5);
    $text_width = imagefontwidth($text_font) * strlen($code);
    $text_height = imagefontheight($text_font);
    $margin = $text_width * 0.3;
    $image_string = imagecreatetruecolor($text_width + $margin, $text_height + $margin);

    $rgb_color  = captcha_hex2rgb($captcha_text_color);
    $text_color = imagecolorallocate($image, $rgb_color['r'], $rgb_color['g'], $rgb_color['b']);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    imagefill($image_string, 0, 0, $background_color); // For GD2+

    $text_x = $margin / 2;
    $text_y = $margin / 2;

    imagestring($image_string, $text_font, $text_x, $text_y, $code, $text_color);

    imagecopyresampled(
        $image,
        $image_string,
        0,
        0,
        0,
        0,
        $captcha_width,
        $captcha_height,
        $text_width+$margin,
        $text_height+$margin
    );

    if ($captcha_filter_text) {
        $image = captcha_filter_image($image);
    }

    imagecolortransparent($image, $background_color);

    return $image;
}

function captcha_get_text($code)
{
    global $captcha_path, $captcha_text_color, $captcha_text_size, $captcha_width, $captcha_height, $captcha_filter_text;

    $image = imagecreatetruecolor($captcha_width, $captcha_height);

    $rgb_color  = captcha_hex2rgb($captcha_text_color);

    if ($rgb_color['r'] == 255 && $rgb_color['g'] == 255 && $rgb_color['b'] == 255) {
        $background_color = imagecolorallocate($image, 0, 0, 0);
    } else {
        $background_color = imagecolorallocate($image, 255, 255, 255);
    }

    imagefill($image, 0, 0, $background_color); // For GD2+

    $text_color = imagecolorallocate($image, $rgb_color['r'], $rgb_color['g'], $rgb_color['b']);

    $x = 20;

    for ($i = 0; $i < strlen($code); $i++) {
        imagettftext(
            $image,
            $captcha_text_size,
            rand(-30, 30),
            $x,
            $captcha_text_size + rand(10, 20),
            $text_color,
            captcha_get_font(),
            $code[$i]
        );
        $x += $captcha_text_size + 10;
    }

    if ($captcha_filter_text) {
        $image = captcha_filter_image($image);
    }

    imagecolortransparent($image, $background_color);

    return $image;
}

function captcha_filter_image($image)
{
    global $captcha_path, $captcha_text_color, $captcha_text_size, $captcha_width, $captcha_height;

    $width_extra  = rand(10, 15);
    $image_filtered = imagecreatetruecolor($captcha_width+$width_extra, $captcha_height+$width_extra);

    $dstX = 0;
    $dstY = 0;
    $dstW = $width_extra;
    $dstH = $captcha_width;
    $srcX = 0;
    $srcY = 0;
    $srcW = $width_extra;
    $srcH = $captcha_width - 2 * $width_extra;
    $h = rand(5, 10);

    for ($i = 0; $i < $captcha_width; $i++) {
        imagecopyresized(
            $image_filtered,
            $image,
            $dstX+$i,
            $dstY,
            $srcX+$i,
            $srcY,
            $dstW+$i,
            $dstH+$width_extra*(sin(deg2rad(2*$i*$h))+sin(deg2rad($i*$h))),
            $srcW+$i,
            $srcH
        );
    }

    return $image_filtered;
}

function captcha_get_font()
{
    global $captcha_path;
    static $files = array();

    if (empty($files)) {
        $path = $captcha_path.'/fonts';
        $res = opendir($path);
        $files = array();
        while ($file = readdir($res)) {
            if ($file[0] == '.') {
                continue;
            }
            $files[] = $path.'/'.$file;
        }
        closedir($res);
    }

    $font = array_rand($files);

    return $files[$font];
}

function captcha_get_background()
{
    global $captcha_path, $captcha_width, $captcha_height, $captcha_filter_bg;
    static $files = array();

    if (empty($files)) {
        $path = $captcha_path.'/backgrounds';
        $res = opendir($path);
        $files = array();
        while ($file = readdir($res)) {
            if ($file[0] == '.') {
                continue;
            }
            $files[] = $path.'/'.$file;
        }
        closedir($res);
    }

    $bg   = array_rand($files);
    $info = getimagesize($files[$bg]);

    $image = null;

    switch ($info[2]) {
    case 1:
      $image = imagecreatefromgif($files[$bg]);
      break;
    case 2:
      $image = imagecreatefromjpeg($files[$bg]);
      break;
    case 3:
      $image = imagecreatefrompng($files[$bg]);
      break;
  }

    $background = imagecreatetruecolor($captcha_width, $captcha_height);

    if ($image) {
        imagecopyresampled($background, $image, 0, 0, 0, 0, $captcha_width, $captcha_height, ImageSX($image), ImageSY($image));
    }

    if ($captcha_filter_bg) {
        $background = captcha_filter_image($background);
    }

    return $background;
}

function captcha_get_code()
{
    global $captcha_path, $captcha_chars, $captcha_wordfile, $captcha_length;

    if ($captcha_wordfile && file_exists($captcha_path . '/words.txt')) {
        return captcha_get_code_from_file($captcha_path . '/words.txt');
    }

    if (empty($captcha_chars)) {
        $captcha_chars = implode('', range('a', 'z'));
    }

    return captcha_get_code_from_string($captcha_chars, $captcha_length);
}

function captcha_get_code_from_string($str, $length = 5)
{
    if (!$str || !$length) {
        return '';
    }

    $code = '';
    srand((double)microtime()*1000000);

    while (strlen($code) < $length) {
        $code .= $str[mt_rand(0, strlen($str)-1)];
    }

    return $code;
}

function captcha_get_code_from_file($file)
{
    static $files = array();

    if (!isset($files[$file])) {
        $files[$file] = file($file);
    }

    srand((double)microtime()*1000000);
    return $files[$file][array_rand($files[$file])];
}

function captcha_hex2rgb($color)
{
    if (is_array($color)) {
        return $color;
    }

    $color = str_replace('#', '', $color);

    return array(
    0       => hexdec(substr($color, 0, 2)),
    1       => hexdec(substr($color, 2, 2)),
    2       => hexdec(substr($color, 4, 2)),
    'r'     => hexdec(substr($color, 0, 2)),
    'g'     => hexdec(substr($color, 2, 2)),
    'b'     => hexdec(substr($color, 4, 2)),
    'red'   => hexdec(substr($color, 0, 2)),
    'green' => hexdec(substr($color, 2, 2)),
    'blue'  => hexdec(substr($color, 4, 2))
  );
}

function captcha_rgb2hex()
{
    if (func_num_args() == 3) {
        $args = func_get_args();
        return sprintf("%02X%02X%02X", $args[0], $args[1], $args[2]);
    }

    $arg = func_get_arg(0);

    if (!is_array($arg)) {
        return sprintf("%06X", $arg);
    }

    if (isset($arg['red'])) {
        return sprintf("%02X%02X%02X", $arg['red'], $arg['green'], $arg['blue']);
    }

    if (isset($arg['r'])) {
        return sprintf("%02X%02X%02X", $arg['r'], $arg['g'], $arg['b']);
    }

    return sprintf("%02X%02X%02X", $arg[0], $arg[1], $arg[2]);
}

function captcha_hex2websafe($color)
{
    $color = str_replace('#', '', $color);
    $out   = '';

    for ($i = 0; $i <= 4; $i += 2) {
        $val = hexdec(substr($color, $i, 2));
        $val = round($val / 51) * 51;

        $out .=  sprintf("%02X", $val);
    }

    return $out;
}
