<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: install.php                                          *
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

/**************************************************************************
 *                                                                        *
 *    English translation by Thomas (http://www.japanreference.com)       *
 *                                                                        *
 *************************************************************************/

$lang['start_install'] = "Start Installation";
$lang['start_install_desc'] = "Please fill out the requested fields below.";
$lang['lostfield_error'] = "An error has occurred. Please check the marked fields!";

$lang['db'] = "Database configuration";
$lang['db_servertype'] = "Database server type";
$lang['db_host'] = "Database server hostname";
$lang['db_name'] = "Database name";
$lang['db_user'] = "Database username";
$lang['db_password'] = "Database password";
$lang['table_prefix'] = "Prefix for tables in database";

$lang['admin'] = "Admin configuration";
$lang['admin_user'] = "Admin username";
$lang['admin_password'] = "Admin password";
$lang['admin_password2'] = "Admin password (Confirm)";

$lang['database_error'] = "An error occurred while updating the database:";
$lang['install_success'] = "Install success!";
$lang['install_success_login'] = "The basic installation is now complete and your admin account has been created. You should now log in to your <a href=\"".ROOT_PATH."admin/index.php\">4images Control Panel</a> and check the general configuration for any required changes.</b>";
$lang['config_download'] = "Download Configuration File";
$lang['config_download_desc'] = "The install script is unable to write the config file \"config.php\" to the server. Click the button below to download a copy of the configuration file and upload this file via FTP to the 4images directory. Once this is done you should log in using the admin username and password and visit the Control Panel to check the general configuration for any required changes.";
$lang['timezone_select'] = "Please select your timezone";
?>
