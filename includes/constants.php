<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: constants.php                                        *
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

// If 4images has problems to find out the right URL, define it here.
// define('SCRIPT_URL', 'http://www.yourdomain.com/4images'); //no trailing slash

// Table names
define('CATEGORIES_TABLE', $table_prefix.'categories');
define('COMMENTS_TABLE', $table_prefix.'comments');
define('GROUP_ACCESS_TABLE', $table_prefix.'groupaccess');
define('GROUP_MATCH_TABLE', $table_prefix.'groupmatch');
define('GROUPS_TABLE', $table_prefix.'groups');
define('IMAGES_TABLE', $table_prefix.'images');
define('IMAGES_TEMP_TABLE', $table_prefix.'images_temp');
define('LIGHTBOXES_TABLE', $table_prefix.'lightboxes');
define('POSTCARDS_TABLE', $table_prefix.'postcards');
define('SESSIONS_TABLE', $table_prefix.'sessions');
define('SESSIONVARS_TABLE', $table_prefix.'sessionvars');
define('SETTINGS_TABLE', $table_prefix.'settings');
define('USERS_TABLE', $table_prefix.'users');
define('WORDLIST_TABLE', $table_prefix.'wordlist');
define('WORDMATCH_TABLE', $table_prefix.'wordmatch');


// URL Parameters
define('URL_IMAGE_ID', 'image_id');
define('URL_CAT_ID', 'cat_id');
define('URL_USER_ID', 'user_id');
define('URL_POSTCARD_ID', 'postcard_id');
define('URL_COMMENT_ID', 'comment_id');
define('URL_PAGE', 'page');
define('URL_ID', 'id');


// User levels
define('GUEST', -1);
define('USER_AWAITING', 1);
define('USER', 2);
define('ADMIN', 9);


// Permission levels
define('AUTH_ALL', 0);
define('AUTH_USER', 2);
define('AUTH_ACL', 3);
define('AUTH_ADMIN', 9);


// Group types
define('GROUPTYPE_GROUP', 1);
define('GROUPTYPE_SINGLE', 2);

// Password
define('PASSWORD_HASH_ALGO', 'md5');
define('PASSWORD_SALT_LENGTH', 9);

// Chmod for files and directories created by 4images
define('CHMOD_FILES', 0666);
define('CHMOD_DIRS', 0777);


// Will be used to replace the {xxx} tage if the value is empty.
// Netscape Browser sometimes need this to display table cell background colors.
define('REPLACE_EMPTY', '&nbsp;');


// Max rating value
define('MAX_RATING', 5);


// Days postcards will be held in the database
define('POSTCARD_EXPIRY', 10);


// Time offset for your website. Sometimes usefull if your server is located
// in other timezones.
define('TIME_OFFSET', 0);


// All words <= MIN_SEARCH_KEYWORD_LENGTH and >= MAX_SEARCH_KEYWORD_LENGTH
// are not added to the search index
define('MIN_SEARCH_KEYWORD_LENGTH', 3);
define('MAX_SEARCH_KEYWORD_LENGTH', 60);

// If you set this to 1, admins will authenticated additionally with cookies.
// If you use "User Integration", you should set this to 0.
define('ADMIN_SAFE_LOGIN', 0);


// If you use GD higher 2.0.1 and PHP higher 4.0.6 set this to 1.
// Your thumbnails will be created with better quality
//define('CONVERT_IS_GD2', 0);


// If you have a lot of images in your database,
// the random image function could make your programm slow.
// Try first to set "SHOW_RANDOM_CAT_IMAGE" to 0.
define('SHOW_RANDOM_IMAGE', 1);
define('SHOW_RANDOM_CAT_IMAGE', 0);


// Check existence of remote image files.
// If you choose 1, you could get sometimes timeout errors
define('CHECK_REMOTE_FILES', 0);


// Allow execution of PHP code in templates
define('EXEC_PHP_CODE', 0);

// Data paths
define('MEDIA_DIR', 'data/media');
define('THUMB_DIR', 'data/thumbnails');
define('MEDIA_TEMP_DIR', 'data/tmp_media');
define('THUMB_TEMP_DIR', 'data/tmp_thumbnails');
define('DATABASE_DIR', 'data/database');
define('TEMPLATE_DIR', 'templates');


// Script version
define('SCRIPT_VERSION', '1.10');


// Debug contants
// define("PRINT_STATS", 1);
// define("PRINT_QUERIES", 1);
// define('PRINT_CACHE_MESSAGES', 1);
