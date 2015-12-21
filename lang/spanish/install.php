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
 *    bedingungen (Lizenz.txt) f�r weitere Informationen.                 *
 *    ---------------------------------------------------------------     *
 *    This script is NOT freeware! Please read the Copyright Notice       *
 *    (Licence.txt) for further information.                              *
 *                                                                        *
 *************************************************************************/

$lang['start_install'] = "Iniciar Instalaci�n";
$lang['start_install_desc'] = "Para iniciar la instalaci�n por favor rellene todos los campos siguientes.";
$lang['lostfield_error'] = "Ha ocurrido un error. Por favor compruebe los campos marcados.";

$lang['db'] = "Configuraci�n base de datos";
$lang['db_servertype'] = "Tipo de servidor de base de datos";
$lang['db_host'] = "Servidor de base de datos";
$lang['db_name'] = "Nombre de base de datos";
$lang['db_user'] = "Nombre de usuario de la base de datos";
$lang['db_password'] = "Contrase�a de la base de datos";
$lang['table_prefix'] = "Prefijo para usar en las tablas de la base de datos";

$lang['admin'] = "Configuraci�n administrador";
$lang['admin_user'] = "Nombre de administrador";
$lang['admin_password'] = "Contrase�a administrador";
$lang['admin_password2'] = "Contrase�a administrador (confirmar)";

$lang['database_error'] = "Ha sucedido un error durante la actualizaci�n de la base de datos:";
$lang['install_success'] = "�Instalaci�n satisfactoria!";
$lang['install_success_login'] = "Su nombre de administrador ha sido creado. En este punto, la instalaci�n b�sica ha sido completada. Por favor, aseg�rese de comprobar los detalles de la configuraci�n general y realice todos los cambios necesarios en su Panel de Control.<br /><b>&raquo; <a href=\"".ROOT_PATH."admin/index.php\">4images Panel de Control</a></b>";
$lang['config_download'] = "Download configuraci�n";
$lang['config_download_desc'] = "Su archivo de configuraci�n \"config.php\" no se puede sobreescribir por el momento. Una copia del archivo de configuraci�n puede descargarse cuando haga click en el siguiente bot�n. Obligatoriamente usted debe subir este archivo en el mismo directorio donde se encuentra 4images. Una vez efectuada esta acci�n usted puede iniciar sesi�n usando su nombre de administrador y la contrase�a que usted ha indicado en el anterior formulario y visitar el Panel de Control para comprobar o modificar su configuraci�n general.";
$lang['timezone_select'] = "Por favor seleccione su zona horaria";
?>