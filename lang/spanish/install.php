<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: install.php                                          *
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

$lang['start_install'] = "Iniciar Instalación";
$lang['start_install_desc'] = "Para iniciar la instalación por favor rellene todos los campos siguientes.";
$lang['lostfield_error'] = "Ha ocurrido un error. Por favor compruebe los campos marcados.";

$lang['db'] = "Configuración base de datos";
$lang['db_servertype'] = "Tipo de servidor de base de datos";
$lang['db_host'] = "Servidor de base de datos";
$lang['db_name'] = "Nombre de base de datos";
$lang['db_user'] = "Nombre de usuario de la base de datos";
$lang['db_password'] = "Contraseña de la base de datos";
$lang['table_prefix'] = "Prefijo para usar en las tablas de la base de datos";

$lang['admin'] = "Configuración administrador";
$lang['admin_user'] = "Nombre de administrador";
$lang['admin_password'] = "Contraseña administrador";
$lang['admin_password2'] = "Contraseña administrador (confirmar)";

$lang['database_error'] = "Ha sucedido un error durante la actualización de la base de datos:";
$lang['install_success'] = "¡Instalación satisfactoria!";
$lang['install_success_login'] = "Su nombre de administrador ha sido creado. En este punto, la instalación básica ha sido completada. Por favor, asegúrese de comprobar los detalles de la configuración general y realice todos los cambios necesarios en su Panel de Control.<br /><b>&raquo; <a href=\"".ROOT_PATH."admin/index.php\">4images Panel de Control</a></b>";
$lang['config_download'] = "Download configuración";
$lang['config_download_desc'] = "Su archivo de configuración \"config.php\" no se puede sobreescribir por el momento. Una copia del archivo de configuración puede descargarse cuando haga click en el siguiente botón. Obligatoriamente usted debe subir este archivo en el mismo directorio donde se encuentra 4images. Una vez efectuada esta acción usted puede iniciar sesión usando su nombre de administrador y la contraseña que usted ha indicado en el anterior formulario y visitar el Panel de Control para comprobar o modificar su configuración general.";
$lang['timezone_select'] = "Por favor seleccione su zona horaria";
?>