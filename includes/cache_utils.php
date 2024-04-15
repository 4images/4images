<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: cache_utils.php                                      *
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

function create_cache_id($group, $params = null)
{
    $cache_id = $group;
    if (is_array($params)) {
        $cache_id .= '.' . implode('.', $params);
    } elseif (is_string($params)) {
        $cache_id .= '.' . $params;
    }
    return $cache_id;
}

function get_cache_file($cache_id, $lifetime = null)
{
    global $cache_enable, $cache_lifetime, $cache_path;

    if (!$cache_enable) {
        return false;
    }

    if (!$lifetime) {
        $lifetime = $cache_lifetime;
    }

    $file = $cache_path . '/' . $cache_id;

    if (!@is_readable($file)) {
        return false;
    }

    if ($lifetime == -1 || (filemtime($file) + $lifetime) > time()) {
        if (!$fp = @fopen($file, 'rb')) {
            return false;
        }

        $data = @fread($fp, filesize($file));
        @fclose($fp);

        if (defined('PRINT_CACHE_MESSAGES') && PRINT_CACHE_MESSAGES == 1) {
            echo "Cache file '$cache_id' <span style='color:green'>used</span><br>";
        }

        // Replace session ids
        global $site_sess;
        $replace = $site_sess->mode == 'cookie' ? '?' : '?'.SESSION_NAME.'='.$site_sess->session_id . '&\1';
        $data = preg_replace(
            '#\?+%%%SID%%%&(amp;)?#',
            $replace,
            $data
        );

        $replace = $site_sess->mode == 'cookie' ? '' : '\1\2'.SESSION_NAME.'='.$site_sess->session_id;
        $data = preg_replace(
            '#([\?|&])+(amp;)?%%%SID%%%#',
            $replace,
            $data
        );

        return $data;
    }

    if (defined('PRINT_CACHE_MESSAGES') && PRINT_CACHE_MESSAGES == 1) {
        echo "Cache file '$cache_id' <span style='color:purple'>expired</span><br>";
    }

    return false;
}

function save_cache_file($cache_id, $data, $remove_session_ids = false)
{
    global $cache_enable, $cache_lifetime, $cache_path;

    if (!$cache_enable) {
        return false;
    }

    $file = $cache_path . '/' . $cache_id;

    if ($fp = @fopen($file, 'wb')) {
        // Replace session ids
        global $site_sess;
        $replacement = $remove_session_ids ? '' : '%%%SID%%%';
        $data = str_replace(
            SESSION_NAME.'='.$site_sess->session_id,
            $replacement,
            $data
        );

        @flock($fp, LOCK_EX);
        @fwrite($fp, $data);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        if (defined('PRINT_CACHE_MESSAGES') && PRINT_CACHE_MESSAGES == 1) {
            echo "Cache file '$cache_id' <span style='color:red'>stored</span><br>";
        }

        return true;
    }

    @fclose($fp);

    return false;
}

function delete_cache_file($cache_id)
{
    global $cache_enable, $cache_lifetime, $cache_path;

    if (defined('PRINT_CACHE_MESSAGES') && PRINT_CACHE_MESSAGES == 1) {
        echo "Cache file '$cache_id' <span style='color:red'>deleted</span><br>";
    }

    return @unlink($cache_path . '/' . $cache_id);
}

function delete_cache_group($group)
{
    global $cache_enable, $cache_lifetime, $cache_path;

    $handle = @opendir($cache_path);

    while ($file = @readdir($handle)) {
        if (is_dir($file) || $file[0] == ".") {
            continue;
        }

        if (strpos($file, $group) === 0) {
            unlink($cache_path . '/' . $file);
        }
    }

    if (defined('PRINT_CACHE_MESSAGES') && PRINT_CACHE_MESSAGES == 1) {
        echo "Cache group '$group' <span style='color:red'>deleted</span><br>";
    }
}

function clear_cache()
{
    global $cache_enable, $cache_lifetime, $cache_path;

    $handle = opendir($cache_path);

    while ($file = @readdir($handle)) {
        if (is_dir($file) || $file[0] == ".") {
            continue;
        }

        unlink($cache_path . '/' . $file);
    }
}
