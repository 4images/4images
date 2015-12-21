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

$lang['start_install'] = "Installation starten";
$lang['start_install_desc'] = "Tragen Sie hier Ihre Zugangsdaten für Ihren Datenbank Server ein und wählen Sie einen Usernamen und ein Passwort für den Administrator.";
$lang['lostfield_error'] = "Bitte überprüfen Sie die markierten Felder!";

$lang['db'] = "Datenbank";
$lang['db_servertype'] = "Datenbank Servertyp";
$lang['db_host'] = "Datenbank Host";
$lang['db_name'] = "Datenbank Name";
$lang['db_user'] = "Datenbank Username";
$lang['db_password'] = "Datenbank Passwort";
$lang['table_prefix'] = "Präfix für die Tabellen";

$lang['admin'] = "Administrator";
$lang['admin_user'] = "Administrator Username";
$lang['admin_password'] = "Administrator Passwort";
$lang['admin_password2'] = "Administrator Passwort (Bestätigung)";

$lang['database_error'] = "Es sind Fehler bei der Erstellung der Datenbankeinträge aufgetreten:";
$lang['install_success'] = "Die Installation war erfolgreich!";
$lang['install_success_login'] = "Ihr Administor Benutzername und Passwort wurde erstellt. Die Basis Konfiguration ist nun beendet. Sie können Sich jetzt in Ihren Administrationsbereich einloggen und weitere Einstellungen vornehmen.<br /><b>&raquo; <a href=\"".ROOT_PATH."admin/index.php\">Zum Adminstrationsbereich</a></b>";
$lang['config_download'] = "Konfigurations-Datei downloaden";
$lang['config_download_desc'] = "Die Konfigurations-Datei \"config.php\" konnte nicht direkt auf dem Server gespeichert werden. Bitte laden Sie sich die Datei herunter und übertragen Sie diese ins Haupverzeichnis der Galerie auf Ihrem Web-Server. Danach können Sie Sich in Ihren Administrationsbereich einloggen und weitere Einstellungen vornehmen.";
$lang['timezone_select'] = "Bitte wählen Sie Ihre Zeitzone";
?>
