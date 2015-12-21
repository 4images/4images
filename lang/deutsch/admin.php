<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: admin.php                                            *
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
 *    bedingungen (Lizenz.txt) f�r weitere Informationen.                 *
 *    ---------------------------------------------------------------     *
 *    This script is NOT freeware! Please read the Copyright Notice       *
 *    (Licence.txt) for further information.                              *
 *                                                                        *
 *************************************************************************/
$lang['user_integration_delete_msg'] = "Es wird nicht die User-Datenbank von 4images verwendet. User nicht gel�scht.";

//-----------------------------------------------------
//--- Main --------------------------------------------
//-----------------------------------------------------
$lang['no_admin'] = "Sie sind kein Administrator oder haben sich nicht eingeloggt.";
$lang['admin_login_redirect'] = "Erfolgreich eingeloggt. Sie werden weitergeleitet...";
$lang['admin_no_lang'] = "Es wurde kein Language-Pack gefunden. Bitte laden Sie mindestens das mitgelieferte Language Pack <b>\"deutsch\"</b> in Ihren Ordner <b>\"lang\"</b>.";
$lang['admin_login'] = "Einloggen";
$lang['goto_homepage'] = "Gehe zur Galerie Homepage";
$lang['online_users'] = "{num_total} User online ({num_registered} registrierte(r) Benutzer und {num_guests} Besucher).";
$lang['homestats_total'] = "Total:";
$lang['top_cat_hits'] = "Die 5 Kategorien mit den meisten Hits";
$lang['top_image_hits'] = "Die 5 Bilder mit den meisten Hits";
$lang['top_image_downloads'] = "5 Bilder mit den meisten Downloads";
$lang['top_image_rating'] = "5 Bilder mit der h�chsten Bewertung";
$lang['top_image_votes'] = "5 Bilder mit den meisten Bewertungen";
$lang['yes'] = "Ja";
$lang['no'] = "Nein";
$lang['search'] = "Suchen";
$lang['search_next_page'] = "N�chste Seite";
$lang['save_changes'] = "�nderungen speichern";
$lang['reset'] = "Zur�cksetzen";
$lang['add'] = "Hinzuf�gen";
$lang['edit'] = "Bearbeiten";
$lang['delete'] = "L�schen";
$lang['options'] = "Optionen";
$lang['back_overview'] = "Zur�ck zur �bersicht";
$lang['back'] = "Zur�ck";
$lang['sort_options'] = "Anzeige Optionen";
$lang['order_by'] = "Sortieren nach";
$lang['results_per_page'] = "Ergebnisse pro Seite";
$lang['asc'] = "Aufsteigend";
$lang['desc'] = "Absteigend";
$lang['found'] = "Gefunden: ";
$lang['showing'] = "Angezeigt: ";
$lang['date_format'] = "<br /><span class=\"smalltext\">(Format: jjjj-mm-tt hh:mm:ss)</span>";
$lang['date_desc'] = "<br /><span class=\"smalltext\">Um das aktuelle Datum zu verwenden, Feld leer lassen.</span>";

$lang['userlevel_admin'] = "Administratoren";
$lang['userlevel_registered'] = "Registrierte User";
$lang['userlevel_registered_awaiting'] = "Registrierte User (noch nicht aktiviert)";

$lang['headline_whosonline'] = "Wer ist online?";
$lang['headline_stats'] = "Statistik";

$lang['images'] = "Bilder";
$lang['users'] = "Users";
$lang['database'] = "Datenbank";
$lang['media_directory'] = "Media Verzeichnis";
$lang['thumb_directory'] = "Thumbnail Verzeichnis";
$lang['validate'] = "Freischalten";
$lang['images_awaiting_validation'] = "<b>{num_images}</b> Bilder erwarten Freischaltung";

$lang['permissions'] = "Befugnisse";
$lang['all'] = "Alle";
$lang['private'] = "Privat";
$lang['all_categories'] = "Alle Kategorien";
$lang['no_category'] = "Keine Kategorie";

$lang['reset_stats_desc'] = "Wollen Sie alle entsprechenden Eintr�ge auf einen bestimmten Wert setzen, geben Sie bitte eine Zahl ein. M�chten Sie die entsprechenden Eintr�ge nicht ver�ndern, lassen Sie das Feld leer.";

//-----------------------------------------------------
//--- Email -------------------------------------------
//-----------------------------------------------------
$lang['send_emails'] = "Emails an User versenden";
$lang['send_emails_subject'] = "Betreff";
$lang['send_emails_message'] = "Text";
$lang['select_email_user'] = "User ausw�hlen";
$lang['send_emails_success'] = "Emails erfolgreich versendet";
$lang['send_emails_error'] = "Fehler beim versenden der Emails!";

//-----------------------------------------------------
//--- Error Messages ----------------------------------
//-----------------------------------------------------
$lang['error'] = "FEHLER:";
$lang['error_log_desc'] = "Es sind Fehler bei folgenden Aktionen aufgetreten:";
$lang['lostfield_error'] = "Bitte �berpr�fen Sie die markierten Felder.";
$lang['parent_cat_error'] = "Sie k�nnen eine Kategorie nicht sich selbst als Subkategorie zuweisen!";
$lang['invalid_email_error'] = "Bitte �berpr�fen Sie das Email-Format!";
$lang['no_search_results'] = "Keine Entr�ge gefunden.";

//-----------------------------------------------------
//--- Fields ------------------------------------------
//-----------------------------------------------------
$lang['field_image_name'] = "Bild Name";
$lang['field_category_name'] = "Kategorie Name";
$lang['field_username'] = "Username";
$lang['field_password'] = "Passwort";
$lang['field_userlevel'] = "Userlevel";
$lang['field_password'] = "Passwort";
$lang['field_password_ext'] = "Passwort:<br /><span class=\"smalltext\">Um das aktuelle Passwort beizubehalten, Feld leer lassen.</span>";
$lang['field_headline'] = "�berschrift";
$lang['field_email'] = "Email";
$lang['field_homepage'] = "Homepage";
$lang['field_icq'] = "ICQ";
$lang['field_showemail'] = "Email anzeigen";
$lang['field_allowemails'] = "Emails von Administratoren erhalten";
$lang['field_invisible'] = "Unsichtbar sein";
$lang['field_date'] = "Datum";
$lang['field_joindate'] = "Registrierungs-Datum";
$lang['field_lastaction'] = "Letzte Aktion";
$lang['field_ip'] = "IP";
$lang['field_comment'] = "Kommentar";
$lang['field_description'] = "Beschreibung";
$lang['field_description_ext'] = "Beschreibung<br /><span class=\"smalltext\">Sie k�nnen HTML-Code verwenden.</span>";
$lang['field_parent'] = "Unterkategorie von";
$lang['field_hits'] = "Anzahl Aufrufe";
$lang['field_downloads'] = "Anzahl Downloads";
$lang['field_votes'] = "Anzahl Stimmen";
$lang['field_rating'] = "Bewertung";
$lang['field_category'] = "Kategorie";
$lang['field_keywords'] = "Schl�sselw�rter";
$lang['field_keywords_ext'] = "Schl�sselw�rter<br /><span class=\"smalltext\">Schl�sselw�rter durch Komma getrennt eingeben.</span>"; 
$lang['field_free'] = "Aktivieren";
$lang['field_allow_comments'] = "Kommentare erlauben";
$lang['field_image_file'] = "Bild-Dateiname";
$lang['field_thumb_file'] = "Thumbnail-Dateiname";
$lang['field_download_url'] = "Download URL";
$lang['field_usergroup_name'] = "Name der Usergruppe";

//-----------------------------------------------------
//--- Searchform Fields -------------------------------
//-----------------------------------------------------
$lang['field_image_id_contains'] = "Bild ID enth�lt";
$lang['field_image_name_contains'] = "Bild Name enth�lt";
$lang['field_description_contains'] = "Beschreibung enth�lt";
$lang['field_keywords_contains'] = "Schl�sselw�rter enth�lt";
$lang['field_username_contains'] = "Username enth�lt";
$lang['field_email_contains'] = "Email enth�lt";
$lang['field_headline_contains'] = "�berschrift enth�lt";
$lang['field_comment_contains'] = "Kommentar enth�lt";
$lang['field_date_before'] = "Datum vor dem";
$lang['field_date_after'] = "Datum nach dem";
$lang['field_joindate_before'] = "Registriert vor dem";
$lang['field_joindate_after'] = "Registriert nach dem";
$lang['field_lastaction_before'] = "Zuletzt aktiv vor dem";
$lang['field_lastaction_after'] = "Zuletzt aktiv nach dem";
$lang['field_image_file_contains'] = "Bild-Dateiname enth�lt";
$lang['field_thumb_file_contains'] = "Thumbnail-Dateiname enth�lt";
$lang['field_downloads_upper'] = "Anzahl Downloads gr��er als";
$lang['field_downloads_lower'] = "Anzahl Downloads kleiner als";
$lang['field_rating_upper'] = "Bewertung h�her als";
$lang['field_rating_lower'] = "Bewertung niedriger als";
$lang['field_votes_upper'] = "Anzahl Bewertungen gr��er als";
$lang['field_votes_lower'] = "Anzahl Bewertungen kleiner als";
$lang['field_hits_upper'] = "Anzahl Hits gr��er als";
$lang['field_hits_lower'] = "Anzahl Hits kleiner als";

//-----------------------------------------------------
//--- Navigation --------------------------------------
//-----------------------------------------------------
$lang['nav_categories_main'] = "Kategorien verwalten";
$lang['nav_categories_edit'] = "Kategorien bearbeiten";
$lang['nav_categories_add'] = "Kategorien hinzuf�gen";

$lang['nav_images_main'] = "Bilder verwalten";
$lang['nav_images_edit'] = "Bilder bearbeiten";
$lang['nav_images_add'] = "Bilder hinzuf�gen";
$lang['nav_images_validate'] = "Bilder freischalten";
$lang['nav_images_check'] = "Neue Bilder checken";
$lang['nav_images_thumbnailer'] = "Auto-Thumbnailer";
$lang['nav_images_resizer'] = "Auto-Image-Resizer";

$lang['nav_comments_main'] = "Kommentare verwalten";
$lang['nav_comments_edit'] = "Kommentare bearbeiten";

$lang['nav_users_main'] = "User verwalten";
$lang['nav_users_edit'] = "User bearbeiten";
$lang['nav_users_add'] = "User hinzuf�gen";
$lang['nav_usergroups'] = "Usergruppen";
$lang['nav_users_email'] = "Email versenden";

$lang['nav_general_main'] = "Allgemein";
$lang['nav_general_settings'] = "Einstellungen";
$lang['nav_general_templates'] = "Templates bearbeiten";
$lang['nav_general_backup'] = "Datenbank Backup";
$lang['nav_general_stats'] = "Statistiken zur�cksetzen";

//-----------------------------------------------------
//--- Categories --------------------------------------
//-----------------------------------------------------
$lang['category'] = "Kategorie";
$lang['main_category'] = "Hauptkategorie";
$lang['sub_categories'] = "Unterkategorien";
$lang['no_categories'] = "Es wurden noch keine Kategorien eingerichtet";
$lang['select_category'] = "Kategorie w�hlen";
$lang['add_subcategory'] = "Unterkategorie hinzuf�gen";
$lang['no_subcategories'] = "Keine Unterkategorien vorhanden";
$lang['delete_cat_confirm'] = "Wollen Sie diese Kategorie wirklich l�schen?<br />Es werden alle zugeh�rigen Subkategorien und alle Bildeintr�ge, Bilddateien und Kommentare unwiderruflich gel�scht!";
$lang['delete_cat_files_confirm'] = "Alle Bilddateien auch vom Server l�schen?";
$lang['cat_add_success'] = "Kategorie erfolgreich hinzugef�gt";
$lang['cat_add_error'] = "Fehler beim Hinzuf�gen der Kategorie";
$lang['cat_edit_success'] = "Kategorie erfolgreich bearbeitet";
$lang['cat_edit_error'] = "Fehler beim Bearbeiten der Kategorie";
$lang['cat_delete_success'] = "Kategorie erfolgreich gel�scht";
$lang['cat_delete_error'] = "Fehler beim L�schen der Kategorie";
$lang['permissions_inherited'] = "Die voreingestellten Befugnisse wurden von der �bergeordneten Kategorie �bernommen";
$lang['cat_order'] = "Kategorie-Reihenfolge";
$lang['at_beginning'] = "An den Anfang";
$lang['at_end'] = "An das Ende";
$lang['after'] = "Nach";

//-----------------------------------------------------
//--- Images ------------------------------------------
//-----------------------------------------------------
$lang['image'] = "Bild";
$lang['image_file'] = "Bild-Datei";
$lang['thumb'] = "Thumbnail";
$lang['thumb_file'] = "Thumbnail-Datei";
$lang['delete_image_confirm'] = "Wollen Sie diesen Bild-Eintrag wirklich l�schen? Es werden auch alle zugeh�rigen Kommentare gel�scht.";
$lang['delete_image_files_confirm'] = "Alle Bild-Dateien auch vom Server l�schen?";
$lang['file_upload_error'] = "Fehler beim Upload der Bild-Datei";
$lang['thumb_upload_error'] = "Fehler beim Upload der Thumbnail-Bilddatei";
$lang['no_image_file'] = "Bitte w�hlen Sie ein Bilddatei";
$lang['invalid_file_type'] = "Die Datei hat ein ung�ltiges Format";
$lang['invalid_image_width'] = "Die Bildbreite ist unzul�ssig";
$lang['invalid_image_height'] = "Die Bildh�he ist unzul�ssig";
$lang['invalid_file_size'] = "Die Dateigr��e ist unzul�ssig";
$lang['file_already_exists'] = "Es existiert bereits eine Datei mit diesem Dateinamen";
$lang['file_copy_error'] = "Datei konnte nicht auf den Server kopiert werden. Bitte �berpr�fen Sie ob die Zugriffsrechte des Zielordners richtig gesetzt sind";
$lang['file_upload_success'] = "Bild-Datei erfolgreich upgeloadet";
$lang['file_delete_success'] = "Bild-Datei erfolgreich gel�scht";
$lang['file_delete_error'] = "Fehler beim L�schen der Bild-Datei";
$lang['error_image_deleted'] = "Bild-Datei wieder gel�scht";
$lang['thumb_upload_success'] = "Thumbnail erfolgreich upgeloadet";
$lang['thumb_delete_success'] = "Thumbnail-Datei erfolgreich gel�scht";
$lang['thumb_delete_error'] = "Fehler beim L�schen der Thumbnail-Datei";
$lang['image_add_success'] = "Bild erfolgreich hinzugef�gt";
$lang['image_add_error'] = "Fehler beim Hinzuf�gen des Bildes";
$lang['image_edit_success'] = "Bild erfolgreich bearbeitet";
$lang['image_edit_error'] = "Fehler beim Bearbeiten des Bildes";
$lang['image_delete_success'] = "Bild erfolgreich gel�scht";
$lang['image_delete_error'] = "Fehler beim L�schen des Bildes";
$lang['allowed_mediatypes_desc'] = "Erlaubte Dateitypen: ";
$lang['no_thumb_found'] = "Kein Thumbnail vorhanden";
$lang['no_db_entry'] = "Daten nicht in die Datenbank geschrieben";
$lang['check_all'] = "Alle ausw�hlen";
$lang['detailed_version'] = "Ausf�hrliche Version";
$lang['num_newimages_desc'] = "Anzahl: ";
$lang['num_addnewimages_desc'] = "Wieviele neue Bilder wollen Sie hinzuf�gen: ";
$lang['no_newimages'] = "Keine neuen Bilder gefunden";
$lang['thumb_newimages_exists'] = "Thumbnail gefunden";
$lang['no_thumb_newimages'] = "Kein Thumbnail gefunden";
$lang['no_thumb_newimages_ext'] = "Es wurde kein Thumbnail gefunden. Sp�ter wird daf�r ein Standard-Icon verwendet.";
$lang['no_newimages_added'] = "Keine Bilder hinzugef�gt!";
$lang['no_image_found'] = "Ein <b class=\"marktext\">!</b> hinter dem Bild-Datei Namen bedeutet, dass die zugeh�rige Bild-Datei nicht mehr auf dem Server vorhanden ist";
$lang['upload_progress'] = "File upload in progress....";
$lang['upload_progress_desc'] = "Dieses Fenster schliesst sich automatisch, wenn der Datei-Upload beendet ist.";
$lang['upload_note'] = "<b>ACHTUNG:</b> Hat die Thumbnail-Datei nicht den gleichen Namen wie die Bilddatei, wird der Name der Thumbnail-Datei angepasst.";
$lang['checkimages_note'] = "Hier werden die Bilder (<b>{num_all_newimages}</b>) angezeigt, die keinen entsprechenden Eintrag in der Datenbank haben.";
$lang['download_url_desc'] = "<br /><span class=\"smalltext\">F�llen Sie dieses Feld aus, verweist der Download-Button auf diese URL,<br> andernfalls auf die Bilddatei.</span>";
$lang['images_delete_success'] = "Bilder erfolgreich gel�scht";
$lang['images_delete_error'] = "Fehler beim L�schen der Bilder";

//-----------------------------------------------------
//--- Comments ----------------------------------------
//-----------------------------------------------------
$lang['comment'] = "Kommentar";
$lang['comments'] = "Kommentare";
$lang['delete_comment_confirm'] = "Wollen Sie diesen Kommentar wirklich l�schen?";
$lang['comment_edit_success'] = "Kommentar erfolgreich bearbeitet";
$lang['comment_edit_error'] = "Fehler beim Bearbeiten der Kommentar";
$lang['comment_delete_success'] = "Kommentar erfolgreich gel�scht";
$lang['comment_delete_error'] = "Fehler beim L�schen der Kommentar";
$lang['comments_delete_success'] = "Kommentare erfolgreich gel�scht";
$lang['comments_delete_error'] = "Fehler beim L�schen der Kommentare";

//-----------------------------------------------------
//--- User --------------------------------------------
//-----------------------------------------------------
$lang['user'] = "User";
$lang['user_delete_confirm'] = "Wollen Sie diesen User wirklich l�schen?";
$lang['user_delete_comments_confirm'] = "Alle vom User geschriebenen Kommentare l�schen?";
$lang['user_add_success'] = "User erfolgreich hinzugef�gt";
$lang['user_add_error'] = "Fehler beim Hinzuf�gen des Users";
$lang['user_edit_success'] = "User erfolgreich bearbeitet";
$lang['user_edit_error'] = "Fehler beim Bearbeiten des Users";
$lang['user_delete_success'] = "User erfolgreich gel�scht";
$lang['user_delete_error'] = "Fehler beim L�schen des Users";
$lang['user_comments_update_success'] = "Kommentare des Users upgedatet (User ID auf \"Gast\" gesetzt)";
$lang['user_comments_update_error'] = "Fehler beim Update der Kommentare (User ID nicht auf \"Gast\" gesetzt)";
$lang['user_name_exists'] = "Es existiert bereits ein User mit dem Username";
$lang['user_email_exists'] = "Es existiert bereits ein User mit dieser Emailadresse";
$lang['num_newusers_desc'] = "Wieviele neue User wollen Sie hinzuf�gen: ";
$lang['user_delete_images_confirm'] = "Alle vom User hinzugef�gten Bilder l�schen?";
$lang['user_images_update_success'] = "Bildeintr�ge des Users upgedatet (User ID auf \"Gast\" gesetzt)";
$lang['user_images_update_error'] = "Fehler beim Update der Bildeintr�ge (User ID nicht auf \"Gast\" gesetzt)";

//-----------------------------------------------------
//--- Usergroups --------------------------------------
//-----------------------------------------------------
$lang['add_usergroup'] = "Usergruppe hinzuf�gen";
$lang['member_of_usergroup'] = "Mitglied bei folgenden Usergruppen";
$lang['usergroup_add_success'] = "Usergruppe erfolgreich hinzugef�gt";
$lang['usergroup_add_error'] = "Fehler beim Hinzuf�gen der Usergruppe";
$lang['usergroup_edit_success'] = "Usergruppe erfolgreich bearbeitet";
$lang['usergroup_edit_error'] = "Fehler beim Bearbeiten der Usergruppe";
$lang['usergroup_delete_success'] = "Usergruppe erfolgreich gel�scht";
$lang['usergroup_delete_error'] = "Fehler beim L�schen der Usergruppe";
$lang['delete_group_confirm'] = "Wollen Sie die Usergruppe wirklich l�schen?";
$lang['auth_viewcat'] = "Kategorie sehen";
$lang['auth_viewimage'] = "Bilddetails sehen";
$lang['auth_download'] = "Download";
$lang['auth_upload'] = "Upload";
$lang['auth_directupload'] = "Direkter Upload";
$lang['auth_vote'] = "Bewerten";
$lang['auth_sendpostcard'] = "Postkarten versenden";
$lang['auth_readcomment'] = "Kommentare lesen";
$lang['auth_postcomment'] = "Kommentare posten";
$lang['permissions_edit_success'] = "Berechtigungen erfolgreich aktualisiert";
$lang['activate_date'] = "Aktivierungsdatum";
$lang['expire_date'] = "Ablaufdatum";
$lang['expire_date_desc'] = "<br /><span class=\"smalltext\">Soll die Mitgliedschaft nie enden, tragen Sie eine 0 ein.</span>";

//-----------------------------------------------------
//--- Templates ---------------------------------------
//-----------------------------------------------------
$lang['no_template'] = "Keine Templates gefunden";
$lang['no_themes'] = "Keine Template-Packs gefunden";
$lang['edit_template'] = "Template bearbeiten";
$lang['edit_templates'] = "Templates bearbeiten";
$lang['choose_template'] = "Templates w�hlen";
$lang['choose_theme'] = "Template-Pack w�hlen";
$lang['load_theme'] = "Template-Pack laden";
$lang['template_edit_success'] = "�nderungen erfolgreich gespeichert!";
$lang['template_edit_error'] = "Fehler beim Speichern des Templates! �berpr�fen Sie ob die Zugriffsrechte korrekt gesetzt sind (chmod 666).";

//-----------------------------------------------------
//--- Backup ------------------------------------------
//-----------------------------------------------------
$lang['do_backup'] = "Backup erstellen";
$lang['do_backup_desc'] = "Hier k�nnen Sie ein Backup Ihrer aktuellen Datenbank erstellen.<br /><span class=\"smalltext\">In der Auswahl-Liste sehen Sie alle Tabellen Ihrer Datenbank. Die von der Galerie ben�tigten Tabellen sind vorausgew�hlt.";
$lang['list_backups'] = "Vorhandene Backups";
$lang['no_backups'] = "Keine Backups vorhanden";
$lang['restore_backup'] = "Wiederherstellen";
$lang['delete_backup'] = "L�schen";
$lang['download_backup'] = "Downloaden";
$lang['show_backup'] = "Anzeigen";
$lang['make_backup_success'] = "Backup erfolgreich erstellt.";
$lang['make_backup_error'] = "Fehler beim Erstellen des Backups. �berpr�fen Sie ob die Zugriffsrechte richtig gesetzt sind (chmod 777).";
$lang['backup_delete_confirm'] = "Wollen Sie diese Backup-Datei wirklich l�schen:";
$lang['backup_delete_success'] = "Backup-Datei erfolgreich gel�scht";
$lang['backup_delete_error'] = "Fehler beim L�schen der Backup-Datei";
$lang['backup_restore_confirm'] = "Wollen Sie die Datenbank wirklich wiederherstellen:";
$lang['backup_restore_success'] = "Datenbank erfolgreich wiederhergestellt";
$lang['backup_restore_error'] = "Folgende Fehler sind beim Wiederherstellen der Datenbank aufgetreten:";

//-----------------------------------------------------
//--- Thumbnailer & Resizer ---------------------------
//-----------------------------------------------------
$lang['im_error'] = "ImageMagick konnte nicht erkannt werden. Entweder sie haben den Pfad falsch angegeben oder ImageMagick ist nicht installiert.";
$lang['gd_error'] = "Die GD Bibliothek konnte nicht erkannt werden.";
$lang['netpbm_error'] = "NetPBM konnte nicht erkannt werden. Entweder sie haben den Pfad falsch angegeben oder NetPBM ist nicht installiert.";
$lang['no_convert_module'] = "Sie haben kein Modul zum Erstellen von Thumbnail-Bildern ausgew�hlt.";
$lang['check_module_settings'] = "Bitte �berpr�fen Sie Ihre Einstellungen f�r die Konvertierungs-Module.";
$lang['check_thumbnails'] = "Thumbnails checken";
$lang['check_thumbnails_desc'] = "Hier k�nnen Sie Ihre Datenbank nach fehlenden Thumbnails durchsuchen.";
$lang['create_thumbnails'] = "Thumbnails erstellen";
$lang['creating_thumbnail'] = "Erstelle Thumbnail f�r: ";
$lang['creating_thumbnail_success'] = "Fertig!";
$lang['creating_thumbnail_error'] = "Fehler beim Erstellen des Thumbnails!";
$lang['convert_thumbnail_dimension'] = "Gr��e der l�ngsten Seite des erstellten Thumbnails in Pixel";
// <br /><span class=\"smalltext\">Die Bilder werden je nach Format proportional verkleinert</span>
$lang['convert_thumbnail_quality'] = "Bild-Qualit�t des erstellten Thumbnails<br /><span class=\"smalltext\">von 0 bis 100</span>";
$lang['convert_options'] = "Konvertierungs Einstellungen";
$lang['resize_images'] = "Bildgr��en konvertieren";
$lang['resize_image_files'] = "Bild-Dateien konvertieren";
$lang['resize_thumb_files'] = "Thumbnail-Dateien konvertieren";
$lang['resize_org_size'] = "Original Bildgr��e";
$lang['resize_new_size'] = "Neue Bildgr��e";
$lang['resize_new_quality'] = "Bildqualit�t";
$lang['resize_image_type_desc'] = "W�hlen Sie hier ob Sie Bild-Dateien oder Thumbnail-Dateien konvertieren m�chten";

$lang['resize_dimension_desc'] = "Geben Sie eine Bildgr��e in Pixel an auf die die Bilder proportional verkleinert werden sollen.";
// <br /><span class=\"smalltext\">Geben Sie z.B. 200 an werden alle Bilder deren l�ngste Seite 200 Pixel �berschreitet proportional auf diese Gr��e verkleinert.</span>
$lang['resize_proportions_desc'] = "Proportionen";
$lang['resize_proportionally'] = "Proportional verkleinern";
$lang['resize_fixed_width'] = "Mit fester Breite verkleinern";
$lang['resize_fixed_height'] = "Mit fester H�he verkleinern";

$lang['resize_quality_desc'] = "Bild-Qualit�t des angepassten Bildes<br /><span class=\"smalltext\">von 0 bis 100</span>";
$lang['resize_start'] = "Konvertierung starten";
$lang['resize_check'] = "Bilder anzeigen";
$lang['resizing_image'] = "Konvertiere Bild-Datei: ";
$lang['resizing_image_success'] = "Fertig!";
$lang['resizing_image_error'] = "Fehler beim Konvertieren der Bild-Datei!";

//-----------------------------------------------------
//--- Check New Images --------------------------------
//-----------------------------------------------------

$lang['add_as_user'] = "Hinzuf�gen als Benutzer:";
$lang['cni_max_dim'] = "Maximale Gr��e der Vorschaubilder:<br /><span class=\"smalltext\">Bei \"Ausf�hrlicher Anzeige\", wird das Bild auf Ihrem Bildschirm auf diesen Wert verkleinert.</span>";
$lang['cni_iptc_name'] = "Verwende Dateiname aus IPTC Daten:";
$lang['cni_iptc_description'] = "Verwende Beschreibung aus IPTC Daten:";
$lang['cni_iptc_keywords'] = "Verwende Schl�sselw�rter aus IPTC Daten:";
$lang['cni_iptc_date'] = "Verwende Datum aus IPTC Daten:";
$lang['cni_check_subcat'] = "Unterkategorien durchsuchen:";

$lang['cni_auto_resizer'] = "Automatisch verkleinern:";
$lang['cni_save_orig'] = "Originalbild speichern:";
$lang['cni_big_folder'] = "Ordnername in denen das Originalbild gespeichert werden soll:";
$lang['cni_add_ann'] = "Wasserzeichen hinzuf�gen:";
$lang['cni_auto_thumbnailer'] = "Thumbnails automatisch erstellen:";
$lang['cni_foundin'] = "Gefunden in";
$lang['cni_root_folder'] = "Stammverzeichniss";
$lang['on'] = "Ein";
$lang['off'] = "Aus";

$lang['cni_file_rename_success'] = "Datei wurde umbenannt von <b>{from}</b> zu <b>{to}</b>";
$lang['cni_file_rename_error'] = "<u>Fehler</u> bei Umbenennen der Datei von <b>{from}</b> zu <b>{to}</b>";

$lang['cni_thumbnail_rename_success'] = "Thumbnail umbenannt von <b>{from}</b> nach <b>{to}</b>";
$lang['cni_thumbnail_rename_error'] = "<u>Fehler</u> beim Umbenennen des Thumbnails von <b>{from}</b> nach <b>{to}</b>";
$lang['cni_copy_success'] = "Datei wurde in den Ordner <b>{name}</b> kopiert.";
$lang['cni_copy_error'] = "<u>Fehler</u> beim Kopieren der Datei in den Ordner <b>{name}</b>.";
$lang['cni_copy_thumb_success'] = "Thumbnail wurde in den Ordner <b>{name}</b> kopiert.";
$lang['cni_copy_thumb_error'] = "<u>Fehler</u> beim Kopieren des Thumbnails in den Ordner <b>{name}</b>.";

$lang['cni_backup_success'] = "Kopiere Original Datei in <b>{name}</b> Ordner.";
$lang['cni_backup_error'] = "<u>Fehler</u> bei kopieren der Original Datei in <b>{name}</b> Ordner.";
$lang['cni_annotation_success'] = "Wasserzeichen hinzuf�gen in <b>{name}</b> Datei.";
$lang['cni_annotation_error'] = "<u>Fehler</u> bei hinzuf�gen des Wasserzeichens in <b>{name}</b> Datei.";
$lang['cni_create_folder_success'] = "Erstelle <b>{name}/</b> Ordner.";
$lang['cni_create_folder_error'] = "<u>Fehler</u> bei erstellen des <b>{name}/</b> Ordner.";
$lang['cni_resized_success'] = "Bildgr��e erfolgreich ge�ndert.";
$lang['cni_resized_error'] = "<u>Fehler</u> bei �nderung der Bildgr��e";
$lang['cni_thumbnail_success'] = "Thumbnail erfolgreich erstellt.";
$lang['cni_thumbnail_error'] = "<u>Fehler</u> Thumbnail konnten nicht erstellt werden!";
$lang['cni_error'] = "<u>Fehler</u>";
$lang['cni_working'] = "Datei <b>{file}</b> wurde bearbeitet";

$lang['file_not_found'] = "Datei nicht gefunden";

//-----------------------------------------------------
//--- Settings ----------------------------------------
//-----------------------------------------------------
$lang['save_settings_success'] = "Einstellungen erfolgreich gespeichert";

/*-- Setting-Group 1 --*/
$setting_group[1] = "Allgemeine Einstellungen";
$setting['site_name'] = "Name der Galerie";
$setting['site_email'] = "Administrator Email";
$setting['use_smtp'] = "SMTP Server f�r den Versand von Emails verwenden";
$setting['smtp_host'] = "SMTP Server Adresse";
$setting['smtp_username'] = "SMTP Username";
$setting['smtp_password'] = "SMTP Passwort";
$setting['template_dir'] = "Template Ordner w�hlen";
$setting['language_dir'] = "Ordner der Sprachdateien w�hlen";
$setting['date_format'] = "Darstellungs-Format des Datums";
$setting['time_format'] = "Darstellungs-Format der Zeit";
$setting['convert_tool'] = "Modul zum Erstellen der Thumbnail-Bilder<br /><span class=\"smalltext\">ImageMagick (http://www.imagemagick.org)<br />GD (http://www.boutell.com/gd)<br />NetPBM (http://netpbm.sourceforge.net)</span>";
$convert_tool_optionlist = array(
  "none"   => "Deaktivieren",
  "im"     => "ImageMagick",
  "gd"     => "GD Bibliothek",
  "netpbm" => "NetPBM"
);
$setting['convert_tool_path'] = "Falls Sie das Modul \"ImageMagick\" oder \"NetPBM\" gew�hlt haben, geben Sie hier den Pfad zum Konvertierungs-Programm an";
$setting['gz_compress'] = "GZip Kompression aktivieren<br /><span class=\"smalltext\">\"Zlib\" muss auf dem Server installiert sein</span>";
$setting['gz_compress_level'] = "GZip Kompressionslevel<br /><span class=\"smalltext\">0-9, 0=keine, 9=maximal</span>";

/*-- Setting-Group 2 --*/
$setting_group[2]="Kategorie Einstellungen";
$setting['cat_order'] = "Kategorien sortieren nach";
$cat_order_optionlist = array(
    'cat_order'   	=> 'Standard',
    'cat_name'      => 'Name',
    'cat_id'        => 'Datum',
);
$setting['cat_sort'] = "Aufsteigend/Absteigend";
$cat_sort_optionlist = array(
    "ASC"  => "Aufsteigend",
    "DESC" => "Absteigend"
);
$setting['cat_cells'] = "Wieviele Zellen soll die Tabelle der Kategorien haben";
$setting['cat_table_width'] = "Wie breit soll die Tabelle der Kategorien sein<br /><span class=\"smalltext\">Prozentangaben erlaubt</span>";
$setting['cat_table_cellspacing'] = "Cellspacing der Kategorie Tabelle";
$setting['cat_table_cellpadding'] = "Cellpadding der Kategorie Tabelle";
$setting['num_subcats'] = "Wieviele Subkategorien sollen unter der Hauptkategorie dargestellt werden";

/*-- Setting-Group 3 --*/
$setting_group[3]="Bild Einstellungen";
$setting['image_order'] = "Bilder sortieren nach";
$image_order_optionlist = array(
  "image_name"      => "Name",
  "image_date"      => "Datum",
  "image_downloads" => "Downloads",
  "image_votes"     => "Anzahl Bewertungen",
  "image_rating"    => "Bewertung",
  "image_hits"	    => "Aufrufe"
);
$setting['image_sort'] = "Aufsteigend/Absteigend";
$image_sort_optionlist = array(
  "ASC"  => "Aufsteigend",
  "DESC" => "Absteigend"
);
$setting['new_cutoff'] = "Wieviele Tage soll ein Bild als neu gekennzeichnet werden";
$setting['image_border'] = "Randbreite der Thumbnail-Bilder";
$setting['image_cells'] = "Wieviele Zellen soll die Tabelle der Bilder haben";
$setting['default_image_rows'] = "Wieviele Zeilen soll die Tabelle der Bilder standardm��ig haben";
$setting['custom_row_steps'] = "Wieviel Stufen soll das Dropdown haben, mit dem sich der Besucher seine Bilder pro Seite w�hlen kann";
$setting['image_table_width'] = "Wie breit soll die Tabelle der Bilder sein<br /><span class=\"smalltext\">Prozentangaben erlaubt</span>";
$setting['image_table_cellspacing'] = "Cellspacing der Bilder Tabelle";
$setting['image_table_cellpadding'] = "Cellpadding der Bilder Tabelle";

/*-- Setting-Group 4 --*/
$setting_group[4]="Upload Einstellungen";
$setting['upload_mode'] = "Upload-Modus";
$upload_mode_optionlist = array(
  "1" => "Dateien �berschreiben",
  "2" => "Dateien mit neuem Namen speichern",
  "3" => "Dateien nicht uploaden"
);
$setting['allowed_mediatypes'] = "Erlaubte Datei-Typen<br /><span class=\"smalltext\">Durch ein Komma getrennt, ohne Leerzeichen. Wenn Sie neue Dateitypen hinzuf�gen, erstellen Sie bitte ein entsprechendes Template im Templates-Verzeichnis</span>";
$setting['max_thumb_width'] = "Maximale Breite der Thumbnail-Bilder in Pixel";
$setting['max_thumb_height'] = "Maximale H�he der Thumbnail-Bilder in Pixel";
$setting['max_thumb_size'] = "Maximale Dateigr��e der Thumbnails in KB";
$setting['max_image_width'] = "Maximale Breite der Bilder in Pixel";
$setting['max_image_height'] = "Maximale H�he der Bilder in Pixel";
$setting['max_media_size'] = "Maximale Dateigr��e der Bilder in KB";
$setting['upload_notify'] = "Benachrichtigung bei Useruploads per Email";
$setting['upload_emails'] = "Zus�tzliche Emails an die die Benachrichtigung gesendet werden soll<br /><span class=\"smalltext\">Mehrere Emails durch Komma trennen</span>";
$setting['auto_thumbnail'] = "Automatisch Thumbnails erstellen";
$setting['auto_thumbnail_dimension'] = "Gr��e der l�ngsten Seite des erstellten Thumbnails in Pixel";
$setting['auto_thumbnail_resize_type'] = "Proportionen";
$auto_thumbnail_resize_type_optionlist = array(
  "1" => "Proportional verkleinern",
  "2" => "Mit fester Breite verkleinern",
  "3" => "Mit fester H�he verkleinern"
);
$setting['auto_thumbnail_quality'] = "Bild-Qualit�t des erstellten Thumbnails<br /><span class=\"smalltext\">von 0 bis 100</span>";

/*-- Setting-Group 5 --*/
$setting_group[5]="Kommentar Einstellungen";
$setting['badword_list'] = "Badword Liste<br /><span class=\"smalltext\">W�rter die zensiert werden sollen durch ein Leerzeichen getrennt eingeben (ohne Komma). Geben Sie hier das Wort \"test\" ein, werde alle Wortbestandteile die \"test\" enthalten zensiert. \"Attest\" wird zu \"At****\". M�chten sie das exakt erkannt wird, umschliessen sie das Wort mit geschweiften Klammern, z.B. {test}. Dann wird das Wort \"test\" zensiert, \"Attest\" aber nicht.</span>";
$setting['badword_replace_char'] = "Zeichen zum Ersetzen von Badwords";
$setting['wordwrap_comments'] = "Umbruch von W�rtern<br /><span class=\"smalltext\">Um ein aufspannen der Seite durch lange W�rter zu vermeiden, kann hier die Stelle der Zeichen angegeben werden, an der ein Umbruch erfolgen soll. Der Wert 0 schaltet die Funktion aus.</span>";
$setting['html_comments'] = "HTML-Code in den Kommentaren erlauben";
$setting['bb_comments'] = "BB-Code in den Kommentaren erlauben";
$setting['bb_img_comments'] = "Einbinden von Bilder per BB-Code in den Kommentaren erlauben<br /><span class=\"smalltext\">W�hlen Sie hier nein, wird lediglich ein Link zur Bilddatei dargestellt.</span>";

/*-- Setting-Group 6 --*/
$setting_group[6]="Paging und Navigations Einstellungen";
$setting['category_separator'] = "Zeichen zur Trennung der Kategorien in den Kategoriepfaden";
$setting['paging_range'] = "Wieviel Seitenzahlen sollen links und rechts der aktuellen Seite in der Seitennavigation angezeigt werden?";

/*-- Setting-Group 7 --*/
$setting_group[7]="Session und User Einstellungen";
$setting['user_edit_image'] = "D�rfen User Ihre eigenen Bilder bearbeiten";
$setting['user_delete_image'] = "D�rfen User Ihre eigenen Bilder l�schen";
$setting['user_edit_comments'] = "D�rfen User Kommentare zu Ihren eigenen Bildern bearbeiten";
$setting['user_delete_comments'] = "D�rfen User Kommentare zu Ihren eigenen Bildern l�schen";
$setting['account_activation'] = "Account-Aktivierung";
$account_activation_optionlist = array(
  "0" => "Keine",
  "1" => "Per Email",
  "2" => "Durch den Admin"
);
$setting['activation_time'] = "Zeitraum in Tagen, in der User ihren Account aktivieren m�ssen. Danach wird der Eintrag gel�scht.<br /><span class=\"smalltext\">0 schaltet die Funktion aus, d.h. Useraccounts die nicht aktiviert werden, werden nicht gel�scht.</span>";
$setting['session_timeout'] = "Ablaufzeit der Sessions bei Usern ohne Aktion in Minuten";
$setting['display_whosonline'] = "Anzeigen des Moduls \"Wer ist online\". Bei Deaktivierung nur sichtbar f�r Administratoren";
$setting['highlight_admin'] = "Sollen Administratoren im Modul \"Wer ist online\" fett dargestellt werden";
?>
