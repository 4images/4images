<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: admin.php                                            *
 *        Copyright: (C) 2002-2015 4homepages.de                          *
 *            correo: jan@4homepages.de                                   *
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
$lang['user_integration_delete_msg'] = "Usted no est� usando la 4images base de datos. El usuario no se elimina.";

//-----------------------------------------------------
//--- Main --------------------------------------------
//-----------------------------------------------------
$lang['no_admin'] = "Usted no es el administrador o no ha iniciado sesi�n.";
$lang['admin_login_redirect'] = "Sesi�n Iniciada. Ser� redireccionado...";
$lang['admin_no_lang'] = "No se encuentra el idioma. Por favor, suba el fichero de idioma  <b>\"english\"</b> en su directorio <b>\"lang\"</b>.";
$lang['admin_login'] = "Sesi�n Iniciada";
$lang['goto_homepage'] = "Ir a la P�gina Inicial de la Galer�a";
$lang['online_users'] = "{num_total} usuario(s) en l�nea ({num_registered} usuario(s) registrado(s) y {num_guests} invitados).";
$lang['homestats_total'] = "Total:";
$lang['top_cat_hits'] = "Top 5 categor�as por visitas";
$lang['top_image_hits'] = "Top 5 im�genes por visitas";
$lang['top_image_downloads'] = "Top 5 im�genes por descargas";
$lang['top_image_rating'] = "Top 5 im�genes por puntuaci�n";
$lang['top_image_votes'] = "Top 5 im�genes por votos";
$lang['yes'] = "Si";
$lang['no'] = "No";
$lang['search'] = "Buscar";
$lang['search_next_page'] = "P�gina Siguiente";
$lang['save_changes'] = "Guardar Cambios";
$lang['reset'] = "Restablecer";
$lang['add'] = "A�adir";
$lang['edit'] = "Editar";
$lang['delete'] = "Borrar";
$lang['options'] = "Opciones";
$lang['back_overview'] = "Retornar a vista previa";
$lang['back'] = "Retornar";
$lang['sort_options'] = "Opciones";
$lang['order_by'] = "Ordenar por";
$lang['results_per_page'] = "Resultados por p�gina";
$lang['asc'] = "Ascendente";
$lang['desc'] = "Descendente";
$lang['found'] = "Encontrados: ";
$lang['showing'] = "Mostrados: ";
$lang['date_format'] = "<br /><span class=\"smalltext\">(Formato de fecha: aaaa-mm-dd hh:mm:ss)</span>";
$lang['date_desc'] = "<br /><span class=\"smalltext\">Dejar el campo en blanco para usar el formato de fecha por defecto.</span>";

$lang['userlevel_admin'] = "Administradores";
$lang['userlevel_registered'] = "Usuarios Registrados";
$lang['userlevel_registered_awaiting'] = "Usuarios Registrados (no activado)";

$lang['headline_whosonline'] = "�Qui�n est� en l�nea?";
$lang['headline_stats'] = "Estad�sticas";

$lang['images'] = "Im�genes";
$lang['users'] = "Usuarios";
$lang['database'] = "Base de datos";
$lang['media_directory'] = "Directorio im�genes";
$lang['thumb_directory'] = "Directorio im�genes reducidas";
$lang['validate'] = "Validar";
$lang['images_awaiting_validation'] = "<b>{num_images}</b> Im�genes esperando validaci�n";

$lang['permissions'] = "Permisos";
$lang['all'] = "Todos";
$lang['private'] = "Privado";
$lang['all_categories'] = "Todas las categor�as";
$lang['no_category'] = "No Categor�a";

$lang['reset_stats_desc'] = "Si deseas restablecer las estad�sticas a un valor espec�fico, introduzca un n�mero. Deja el campo en blanco si no deseas modificar las estad�sticas.";

//-----------------------------------------------------
//--- Email -------------------------------------------
//-----------------------------------------------------
$lang['send_emails'] = "Enviar correo a usuarios";
$lang['send_emails_subject'] = "Asunto";
$lang['send_emails_message'] = "Mensaje";
$lang['select_email_user'] = "Seleccionar usuario";
$lang['send_emails_success'] = "correos enviados";
$lang['send_emails_error'] = "�Error enviando correo!";

//-----------------------------------------------------
//--- Error Messages ----------------------------------
//-----------------------------------------------------
$lang['error'] = "ERROR:";
$lang['error_log_desc'] = "Los siguientes errores han aparecido:";
$lang['lostfield_error'] = "Por favor, examine los campos rellenados.";
$lang['parent_cat_error'] = "�No puedes asignar una categor�a como subcategor�a!";
$lang['invalid_email_error'] = "�Por favor, compruebe el formato de la direcci�n de correo!";
$lang['no_search_results'] = "No aparecen resultados.";

//-----------------------------------------------------
//--- Fields ------------------------------------------
//-----------------------------------------------------
$lang['field_image_name'] = "Nombre de imagen";
$lang['field_category_name'] = "Nombre de categor�a";
$lang['field_username'] = "Nombre de usuario";
$lang['field_password'] = "Contrase�a";
$lang['field_userlevel'] = "Nivel de usuario";
$lang['field_password'] = "Contrase�a";
$lang['field_password_ext'] = "Contrase�a:<br /><span class=\"smalltext\">Deje el campo Contrase�a en blanco si no quiere cambiarla.</span>";
$lang['field_headline'] = "Titular";
$lang['field_email'] = "Correo";
$lang['field_homepage'] = "P�gina web";
$lang['field_icq'] = "ICQ";
$lang['field_showemail'] = "Mostrar correo";
$lang['field_allowemails'] = "Reciba los correos de administradores:";
$lang['field_invisible'] = "Invisible";
$lang['field_date'] = "Fecha";
$lang['field_joindate'] = "Fecha de registro";
$lang['field_lastaction'] = "�ltima actividad";
$lang['field_ip'] = "IP";
$lang['field_comment'] = "Comentario";
$lang['field_description'] = "Descripci�n";
$lang['field_description_ext'] = "Descripci�n<br /><span class=\"smalltext\">HTML permitido.</span>";
$lang['field_parent'] = "Subcategor�a padre";
$lang['field_hits'] = "Impactos";
$lang['field_downloads'] = "Descargas";
$lang['field_votes'] = "Votos";
$lang['field_rating'] = "Puntuaci�n";
$lang['field_category'] = "Categor�a";
$lang['field_keywords'] = "Palabras clave";
$lang['field_keywords_ext'] = "Palabras clave<br /><span class=\"smalltext\">Introducir palabras separadas por comas.</span>";
$lang['field_free'] = "Activar";
$lang['field_allow_comments'] = "Permitir comentarios";
$lang['field_image_file'] = "Nombre de la imagen";
$lang['field_thumb_file'] = "Archivo thumbnail";
$lang['field_download_url'] = "URL de descarga";
$lang['field_usergroup_name'] = "Nombre del grupo de usuarios";

//-----------------------------------------------------
//--- Searchform Fields -------------------------------
//-----------------------------------------------------
$lang['field_image_id_contains'] = "El ID de la imagen contiene";
$lang['field_image_name_contains'] = "El nombre de la imagen contiene";
$lang['field_description_contains'] = "La descripci�n contiene";
$lang['field_keywords_contains'] = "Palabra clave contiene";
$lang['field_username_contains'] = "Nombre de usuario contiene";
$lang['field_email_contains'] = "Correo contiene";
$lang['field_headline_contains'] = "Titular contiene";
$lang['field_comment_contains'] = "Comentario contiene";
$lang['field_date_before'] = "Fecha anterior a";
$lang['field_date_after'] = "Fecha posterior a";
$lang['field_joindate_before'] = "Registrado despu�s de";
$lang['field_joindate_after'] = "Registrado antes de";
$lang['field_lastaction_before'] = "�ltima actividad despu�s de";
$lang['field_lastaction_after'] = "�ltima actividad antes de";
$lang['field_image_file_contains'] = "El archivo de imagen contiene";
$lang['field_thumb_file_contains'] = "Archivo Thumbnail contiene";
$lang['field_downloads_upper'] = "Descargas superiores a";
$lang['field_downloads_lower'] = "Descargas inferiores a";
$lang['field_rating_upper'] = "Puntuaci�n superior a";
$lang['field_rating_lower'] = "Puntuaci�n inferior a";
$lang['field_votes_upper'] = "N�mero de votos superior a";
$lang['field_votes_lower'] = "N�mero de votos inferior a";
$lang['field_hits_upper'] = "N�mero de impactos superior a";
$lang['field_hits_lower'] = "N�mero de impactos inferior a";

//-----------------------------------------------------
//--- Navigation --------------------------------------
//-----------------------------------------------------
$lang['nav_categories_main'] = "Categor�as";
$lang['nav_categories_edit'] = "Editar categor�as";
$lang['nav_categories_add'] = "Agregar categor�as";

$lang['nav_images_main'] = "Im�genes";
$lang['nav_images_edit'] = "Editar im�genes";
$lang['nav_images_add'] = "Agregar im�genes";
$lang['nav_images_validate'] = "Im�genes Validadas";
$lang['nav_images_check'] = "Comprobar nuevas im�genes";
$lang['nav_images_thumbnailer'] = "Auto-Thumbnailer";
$lang['nav_images_resizer'] = "Auto-Dimensionador";

$lang['nav_comments_main'] = "Comentarios";
$lang['nav_comments_edit'] = "Editar comentarios";

$lang['nav_users_main'] = "Usuarios";
$lang['nav_users_edit'] = "Editar usuarios";
$lang['nav_users_add'] = "Agregar usuario";
$lang['nav_usergroups'] = "Grupos de usuarios";
$lang['nav_users_email'] = "Enviar correo";

$lang['nav_general_main'] = "General";
$lang['nav_general_settings'] = "Opciones";
$lang['nav_general_templates'] = "Plantillas";
$lang['nav_general_backup'] = "Guardar Base de Datos";
$lang['nav_general_stats'] = "Restablecer estad�sticas";

//-----------------------------------------------------
//--- Categories --------------------------------------
//-----------------------------------------------------
$lang['category'] = "Categor�a";
$lang['main_category'] = "Categor�a Principal";
$lang['sub_categories'] = "Subcategor�as";
$lang['no_categories'] = "No se agregaron categor�as";
$lang['select_category'] = "Selecionar categor�a";
$lang['add_subcategory'] = "Agregar subcategor�a";
$lang['no_subcategories'] = "No se agregaron subcategor�as";
$lang['delete_cat_confirm'] = "�Realmente desea eliminar esta categor�a?<br />�Todas las subcategor�as y las im�genes y comentarios contenidas en ellas ser�n eliminadas!";
$lang['delete_cat_files_confirm'] = "�Eliminar todas las im�genes del servidor?";
$lang['cat_add_success'] = "Categor�a agregada";
$lang['cat_add_error'] = "Error agregando categor�a";
$lang['cat_edit_success'] = "Categor�a editada";
$lang['cat_edit_error'] = "Error editando categor�a";
$lang['cat_delete_success'] = "Categor�a eliminada";
$lang['cat_delete_error'] = "Error eliminado categor�a";
$lang['permissions_inherited'] = "Usando los permisos heredados de categor�a padre.";
$lang['cat_order'] = "Orden de categor�a";
$lang['at_beginning'] = "Al comienzo";
$lang['at_end'] = "Al final";
$lang['after'] = "Despu�s";

//-----------------------------------------------------
//--- Images ------------------------------------------
//-----------------------------------------------------
$lang['image'] = "Imagen";
$lang['image_file'] = "Archivo de imagen";
$lang['thumb'] = "Thumbnail";
$lang['thumb_file'] = "Archivo Thumbnail";
$lang['delete_image_confirm'] = "�Realmente quiere eliminar este archivo de imagen? Todos los comentarios relativos a esta imagen ser�n eliminados.";
$lang['delete_image_files_confirm'] = "�Eliminar los archivos de imagen del servidor?";
$lang['file_upload_error'] = "Error enviando archivo de imagen";
$lang['thumb_upload_error'] = "Error enviando archivo thumbnail";
$lang['no_image_file'] = "Por favor, seleccione el archivo de imagen";
$lang['invalid_file_type'] = "Tipo de archivo incorrecto";
$lang['invalid_image_width'] = "Anchura de imagen incorrecta";
$lang['invalid_image_height'] = "Altura de imagen incorrecta";
$lang['invalid_file_size'] = "Tama�o de imagen incorrecto";
$lang['file_already_exists'] = "Esta imagen ya existe";
$lang['file_copy_error'] = "Error de copia. Por favor, compruebe los permisos de los directorios.";
$lang['file_upload_success'] = "Archivo de imagen enviado";
$lang['file_delete_success'] = "Archivo de imagen eliminado";
$lang['file_delete_error'] = "Error eliminando archivo de imagen.";
$lang['error_image_deleted'] = "Image file deleted";
$lang['thumb_upload_success'] = "Archivo Thumbnail enviado";
$lang['thumb_delete_success'] = "Archivo Thumbnail eliminado";
$lang['thumb_delete_error'] = "Error eliminando archivo thumbnail";
$lang['image_add_success'] = "Imagen agregada";
$lang['image_add_error'] = "Error agregando imagen";
$lang['image_edit_success'] = "Image editada";
$lang['image_edit_error'] = "Error editando imagen";
$lang['image_delete_success'] = "Image eliminada";
$lang['image_delete_error'] = "Error eliminando imagen";
$lang['allowed_mediatypes_desc'] = "Extensiones v�lidas";
$lang['no_thumb_found'] = "No se encuentra thumbnail";
$lang['no_db_entry'] = "�Ninguna entrada de base de datos!";
$lang['check_all'] = "Comprobar todo";
$lang['detailed_version'] = "Versi�n detallada";
$lang['num_newimages_desc'] = "Im�genes mostradas";
$lang['num_addnewimages_desc'] = "Nuevas im�genes mostradas";
$lang['no_newimages'] = "No existen nuevas im�genes";
$lang['thumb_newimages_exists'] = "Thumbnail encontrado";
$lang['no_thumb_newimages'] = "No se encuentran thumbnails";
$lang['no_thumb_newimages_ext'] = "No hay nuevos thumbnails. Se muestre el icono por defecto.";
$lang['no_newimages_added'] = "�No se agregaron nuevas im�genes!";
$lang['no_image_found'] = "Im�genes marcadas con <b class=\"marktext\">!</b> indican que no se encuentra el archivo de imagen en el servidor.";
$lang['upload_progress'] = "Enviando archivo...";
$lang['upload_progress_desc'] = "Esta ventana se cerrar� autom�ticamente cuando el env�o haya finalizado.";
$lang['upload_note'] = "<b>NOTA:</b> En caso de que el nombre del archivo thumbnail no se corresponda con el nombre del archivo de imagen, �ste ser� adaptado al nombre del archivo de imagen.";
$lang['checkimages_note'] = "Las siguientes im�genes (<b>{num_all_newimages}</b>) no se han introducido en la base de datos.";
$lang['download_url_desc'] = "<br /><span class=\"smalltext\">Si rellenas este campo, el bot�n de descarga apuntar� a la URL que introduzcas, <br> en caso contrario apuntar� directamente al archivo de imagen.</span>";
$lang['images_delete_success'] = "Im�genes eliminadas";
$lang['images_delete_error'] = "Error eliminando im�genes";

//-----------------------------------------------------
//--- Comments ----------------------------------------
//-----------------------------------------------------
$lang['comment'] = "Comentario";
$lang['comments'] = "Comentarios";
$lang['delete_comment_confirm'] = "�Eliminar este comentario?";
$lang['comment_edit_success'] = "Comentario editado";
$lang['comment_edit_error'] = "Error editando comentario.";
$lang['comment_delete_success'] = "Comentario eliminado";
$lang['comment_delete_error'] = "Error eliminando comentario";
$lang['comments_delete_success'] = "Comentarios eliminados";
$lang['comments_delete_error'] = "Error eliminando comentarios";

//-----------------------------------------------------
//--- User --------------------------------------------
//-----------------------------------------------------
$lang['user'] = "Usuario";
$lang['user_delete_confirm'] = "�Eliminar usuario?";
$lang['user_delete_comments_confirm'] = "�Eliminar todos los comentarios del usuario?";
$lang['user_add_success'] = "Usuario agregado";
$lang['user_add_error'] = "Error agregando usuario";
$lang['user_edit_success'] = "Usuario editado";
$lang['user_edit_error'] = "Error editando usuario";
$lang['user_delete_success'] = "Usuario eliminado";
$lang['user_delete_error'] = "Error eliminando usuario";
$lang['user_comments_update_success'] = "Comentarios del usuario actualizados (ID de usuario restablecido a \"Invitado\")";
$lang['user_comments_update_error'] = "Error actualizando comentarios del usuario (ID del usuario no restablecido a \"Invitado\")";
$lang['user_name_exists'] = "�El usuario ya existe!";
$lang['user_email_exists'] = "�El email del usuario ya existe!";
$lang['num_newusers_desc'] = "N�mero de usuarios nuevos para a�adir";
$lang['user_delete_images_confirm'] = "�Eliminar todas las im�genes agregadas por el usuario?";
$lang['user_images_update_success'] = "Im�genes de usuario actualizadas (ID de usuario restablecido a \"Invitado\")";
$lang['user_images_update_error'] = "Error subiendo im�genes de usuario (ID del usuario no restablecido a \"Invitado\")";

//-----------------------------------------------------
//--- Usergroups --------------------------------------
//-----------------------------------------------------
$lang['add_usergroup'] = "Agregar grupo de usuarios";
$lang['member_of_usergroup'] = "Miembro de los siguientes grupos de usuarios";
$lang['usergroup_add_success'] = "Grupo de usuarios agregado";
$lang['usergroup_add_error'] = "Error agregando grupo de usuarios";
$lang['usergroup_edit_success'] = "Grupo de usuarios modificado";
$lang['usergroup_edit_error'] = "Error modificando grupo de usuarios";
$lang['usergroup_delete_success'] = "Grupo de usuarios eliminado";
$lang['usergroup_delete_error'] = "Error eliminando grupo de usuarios";
$lang['delete_group_confirm'] = "�Eliminar grupo de usuarios?";
$lang['auth_viewcat'] = "Ver categor�a";
$lang['auth_viewimage'] = "Ver imagen";
$lang['auth_download'] = "Descargar";
$lang['auth_upload'] = "Subir";
$lang['auth_directupload'] = "Subir directamente";
$lang['auth_vote'] = "Votar";
$lang['auth_sendpostcard'] = "Enviar postal";
$lang['auth_readcomment'] = "Leer comentarios";
$lang['auth_postcomment'] = "Publicar comentario";
$lang['permissions_edit_success'] = "Permisos actualizados";
$lang['activate_date'] = "Fecha de activaci�n";
$lang['expire_date'] = "Fecha de expiraci�n";
$lang['expire_date_desc'] = "<br /><span class=\"smalltext\">Si la cuenta se supone que no debe expirar, introduzca 0.</span>";

//-----------------------------------------------------
//--- Templates ---------------------------------------
//-----------------------------------------------------
$lang['no_template'] = "No se encuentra la plantilla";
$lang['no_themes'] = "No se encuentra el paquete de plantillas";
$lang['edit_template'] = "Editar plantilla";
$lang['edit_templates'] = "Editar plantillas";
$lang['choose_template'] = "Seleccionar plantilla";
$lang['choose_theme'] = "Seleccionar paquete de plantillas";
$lang['load_theme'] = "Cargar tema";
$lang['template_edit_success'] = "�Plantilla editada!";
$lang['template_edit_error'] = "'�Error guardando plantilla! Compruebe los permisos (chmod 666).";

//-----------------------------------------------------
//--- Backup ------------------------------------------
//-----------------------------------------------------
$lang['do_backup'] = "Copia de seguridad de base de datos";
$lang['do_backup_desc'] = "Realizar copia de seguridad de base de datos.<br /> <span class=\"smalltext\">Seleccione las tablas de la base de datos que quiere actualizar. Las tablas requeridas est�n preseleccionadas.";
$lang['list_backups'] = "Listar copias de seguridad";
$lang['no_backups'] = "No hay copias de seguridad";
$lang['restore_backup'] = "Restaurar";
$lang['delete_backup'] = "Eliminar";
$lang['download_backup'] = "Descargar";
$lang['show_backup'] = "Mostrar";
$lang['make_backup_success'] = "Copia de seguridad guardada satisfactoriamente.";
$lang['make_backup_error'] = "Error guardando copia de seguridad. Compruebe los permisos (chmod 777).";
$lang['backup_delete_confirm'] = "�Eliminar copia de seguridad?";
$lang['backup_delete_success'] = "Copia de seguridad eliminada";
$lang['backup_delete_error'] = "Error eliminando copia de seguridad";
$lang['backup_restore_confirm'] = "�Restaurar base de datos?";
$lang['backup_restore_success'] = "Base de datos restaurada";
$lang['backup_restore_error'] = "Errores restaurando la copia de seguridad:";

//-----------------------------------------------------
//--- Thumbnailer & Resizer ---------------------------
//-----------------------------------------------------
$lang['im_error'] = "Error con ImageMagick. Ruta err�nea o no est� instalado ImageMagick.";
$lang['gd_error'] = "Error en la librer�a GD.";
$lang['netpbm_error'] = "Error con NetPBM. Ruta err�nea o no est� instalado NetPBM.";
$lang['no_convert_module'] = "Seleccione m�dulo para crear thumbnails.";
$lang['check_module_settings'] = "Comprobar configuraciones del m�dulo.";
$lang['check_thumbnails'] = "Comprobar thumbnails";
$lang['check_thumbnails_desc'] = "Comprobar base de datos buscando thumbnails perdidos.";
$lang['create_thumbnails'] = "Crear thumbnails";
$lang['creating_thumbnail'] = "Crear thumbnail para: ";
$lang['creating_thumbnail_success'] = "�Thumbnail creado!";
$lang['creating_thumbnail_error'] = "Error creando thumbnail!";
$lang['convert_thumbnail_dimension'] = "Tama�o en pixels del thumbnail";
// <br /><span class=\"smalltext\">las dimensiones ser�n adaptadas</span>
$lang['convert_thumbnail_quality'] = "Calidad del thumbnail<br /><span class=\"smalltext\">de 0 a 100</span>";
$lang['convert_options'] = "Conversi�n";
$lang['resize_images'] = "Redimensionar im�genes";
$lang['resize_image_files'] = "Redimensionar archivos de imagen";
$lang['resize_thumb_files'] = "Redimensionar archivos thumbnail";
$lang['resize_org_size'] = "Tama�o original";
$lang['resize_new_size'] = "Nuevo tama�o de imagen";
$lang['resize_new_quality'] = "Calidad de imagen";
$lang['resize_image_type_desc'] = "�Convertir los archivos de imagen o thumbnails?";

$lang['resize_dimension_desc'] = "Tama�o de archvo de imagen en pixels.";
// <br /><span class=\"smalltext\">Si introduce 200, la longitud m�xima del archivo de imagen ser� establecido en 200 y redimensionado proporcionalmente.</span>
$lang['resize_proportions_desc'] = "Proporciones";
$lang['resize_proportionally'] = "Redimensionar proporcionalmente";
$lang['resize_fixed_width'] = "Redimensionar con ancho fijo";
$lang['resize_fixed_height'] = "Redimensionar con alto fijo";

$lang['resize_quality_desc'] = "Calidad de imagen<br /><span class=\"smalltext\">de 0 a 100</span>";
$lang['resize_start'] = "Iniciar conversi�n";
$lang['resize_check'] = "Mostrar imagen";
$lang['resizing_image'] = "Convertir archivo de imagen: ";
$lang['resizing_image_success'] = "�Archivo de imagen convertido!";
$lang['resizing_image_error'] = "�Error convirtiendo archivo de imagen!";

//-----------------------------------------------------
//--- Settings ----------------------------------------
//-----------------------------------------------------
$lang['save_settings_success'] = "Configuraci�n guardada";

/*-- Setting-Group 1 --*/
$setting_group[1]="Configuraci�n general";
$setting['site_name'] = "Nombre del sitio";
$setting['site_email'] = "correo del administrador";
$setting['use_smtp'] = "Usar servidor SMTP";
$setting['smtp_host'] = "Servidor SMTP";
$setting['smtp_username'] = "Nombre de usuario SMTP";
$setting['smtp_password'] = "Contrase�a SMTP";
$setting['template_dir'] = "Elegir directorio de plantillas";
$setting['language_dir'] = "Elegir directorio de idioma";
$setting['date_format'] = "Formato de fecha";
$setting['time_format'] = "Formato horario";
$setting['convert_tool'] = "Herramienta de conversi�n para thumbnails<br /><span class=\"smalltext\">ImageMagick (http://www.imagemagick.org)<br />GD (http://www.boutell.com/gd)<br />NetPBM (http://netpbm.sourceforge.net)</span>";
$convert_tool_optionlist = array(
  "none"   => "Ninguno",
  "im"     => "ImageMagick",
  "gd"     => "GD Biblioteca",
  "netpbm" => "NetPBM"
);
$setting['convert_tool_path'] = "Si usted ha seleccionado \"ImageMagick\" o \"NetPBM\", introduzca la ruta";
$setting['gz_compress'] = "Usar compresi�n GZip<br /><span class=\"smalltext\">\"Zlib\" debe estar instalado en su servidor</span>";
$setting['gz_compress_level'] = "Nivel de compresi�n GZip<br /><span class=\"smalltext\">0-9, 0=ninguno, 9=m�ximo</span>";

/*-- Setting-Group 2 --*/
$setting_group[2]="Configuraci�n de categor�as";
$setting['cat_order'] = "Ordenar categor�as por";
$cat_order_optionlist = array(
    'cat_order'    => 'Default',
    'cat_name'      => 'Nombre',
    'cat_id'        => 'Fecha',
);
$setting['cat_sort'] = "Ascendente/Descendente";
$cat_sort_optionlist = array(
    "ASC"  => "Ascendente",
    "DESC" => "Descendente"
);
$setting['cat_cells'] = "N�mero de celdas de tabla";
$setting['cat_table_width'] = "Anchura de tabla<br /><span class=\"smalltext\">anchura en pixels o en porcentaje</span>";
$setting['cat_table_cellspacing'] = "Espacio de celda";
$setting['cat_table_cellpadding'] = "Hueco de celda";
$setting['num_subcats'] = "N�mero de subcategor�as";

/*-- Setting-Group 3 --*/
$setting_group[3]="Configuraci�n de im�genes";
$setting['image_order'] = "Ordenar im�genes por";
$image_order_optionlist = array(
  "image_name"      => "Nombre",
  "image_date"      => "Fecha",
  "image_downloads" => "Descargas",
  "image_votes"     => "Votos",
  "image_rating"    => "Puntuaci�n",
  "image_hits"      => "Impactos"
);
$setting['image_sort'] = "Ascendente/Descendente";
$image_sort_optionlist = array(
  "ASC"  => "Ascendente",
  "DESC" => "Descendente"
);
$setting['new_cutoff'] = "N�mero de d�as por el cual se consideran las im�genes como nuevas";
$setting['image_border'] = "Borde en los thumbnails";
$setting['image_cells'] = "N�mero de celdas en la tabla de im�genes";
$setting['default_image_rows'] = "N�mero de l�neas en la tabla de im�genes";
$setting['custom_row_steps'] = "Num�ro de opciones en el men� desplegable (permite a los usuarios elegir el n�mero de im�genes por p�gina)";
$setting['image_table_width'] = "Anchura de tabla<br /><span class=\"smalltext\">por pixels o por porcentaje</span>";
$setting['image_table_cellspacing'] = "Espacio de tabla";
$setting['image_table_cellpadding'] = "Hueco de tabla";

/*-- Setting-Group 4 --*/
$setting_group[4]="Configuraciones de im�genes y env�o de im�genes.";
$setting['upload_mode'] = "Modo de env�o";
$upload_mode_optionlist = array(
  "1" => "Reemplazar archivos",
  "2" => "Guardar archivos con un nuevo nombre",
  "3" => "No subir archivos"
);
$setting['allowed_mediatypes'] = "Extensiones de archivo v�lidas<br /><span class=\"smalltext\">Delimitadas con una coma, no con espacios. Cuando incluya nuevos tipos de archivos, cree una nueva plantilla en el directorio de plantillas (templates).</span>";
$setting['max_thumb_width'] = "Max. anchura del thumbnail en pixel";
$setting['max_thumb_height'] = "Max. altura del thumbnail en pixel";
$setting['max_thumb_size'] = "Max. tama�o del thumbnail en KB";
$setting['max_image_width'] = "Max. anchura de la imagen en pixel";
$setting['max_image_height'] = "Max. altura de la imagen en pixel";
$setting['max_media_size'] = "Max. tama�o de la imagen en KB";
$setting['upload_notify'] = "Notificar mediante correo los uploads de usuarios";
$setting['upload_emails'] = "correos adicionales para notificaciones <br /><span class=\"smalltext\">Delimita los emails mediante comas.</span>";
$setting['auto_thumbnail'] = "Crear thumbnails";
$setting['auto_thumbnail_dimension'] = "Tama�o en pixels del thumbnail";
$setting['auto_thumbnail_resize_type'] = "Proporciones";
$auto_thumbnail_resize_type_optionlist = array(
  "1" => "Redimensionar proporcionalmente",
  "2" => "Redimensionar con ancho fijo",
  "3" => "Redimensionar con alto fijo"
);
$setting['auto_thumbnail_quality'] = "Calidad del Thumbnail<br /><span class=\"smalltext\">de 0 a 100</span>";

/*-- Setting-Group 5 --*/
$setting_group[5]="Configuraciones de comentarios";
$setting['badword_list'] = "Lista de palabras prohibidas<br /><span class=\"smalltext\">Introduzca palabras separadas por espacios (no comas). Si introduce la palabra \"prueba\", todas las palabras que contengan \"prueba\" ser�n censuradas. Por ejemplo, con \"Aprueba\" se mostrar� \"A******\". Si prefiere ser m�s espec�fico, se censurar�n exactamente todos aquellos t�rminos que usted encierre entre llaves {prueba}. De este modo la palabra \"prueba\" ser� censurada, pero no suceder� lo mismo con \"aprueba\".</span>";
$setting['badword_replace_char'] = "Caracter para reemplazar palabras censuradas";
$setting['wordwrap_comments'] = "Ajuste autom�tico de l�nea <br /><span class=\"smalltext\">utilizado para evitar order lineas interminables que desajusten la pantalla, seleccione un n�mero m�ximo de car�cteres por l�nea. 0 desactiva el ajuste autom�tico de l�nea.</span>";
$setting['html_comments'] = "Permitir HTML en los comentarios";
$setting['bb_comments'] = "Permitir C�digo-BB en los comentarios";
$setting['bb_img_comments'] = "Permitir im�genes (C�digo-BB) en los comentarios<br /><span class=\"smalltext\">Si selecciona \"No\", cualquier imagen ser� mostrada como un hiperenlace.</span>";

/*-- Setting-Group 6 --*/
$setting_group[6]="Configuraci�n de p�gina y navegaci�n";
$setting['category_separator'] = "Delimitador de categor�as (en la ruta de categor�as)";
$setting['paging_range'] = "N�mero de p�ginas \"anterior\" y \"siguiente\" mostradas en la navegaci�n";

/*-- Setting-Group 7 --*/
$setting_group[7]="Configuraci�n de usuarios y sesiones";
$setting['user_edit_image'] = "Permitir a los usaurios editar sus propias im�genes";
$setting['user_delete_image'] = "Permitir a los usaurios borrar sus propias im�genes";
$setting['user_edit_comments'] = "Permitir a los usaurios editar los comentarios de sus propias im�genes";
$setting['user_delete_comments'] = "Permitir a los usuarios borrar loscomentarios de sus propias im�genes";
$setting['account_activation'] = "Activar cuenta";
$account_activation_optionlist = array(
  "0" => "Ninguna",
  "1" => "correo",
  "2" => "Administrador"
);
$setting['activation_time'] = "Periodo m�ximo para que un usuario active su cuenta, en d�as.<br /><span class=\"smalltext\">0  desactiva esta funci�n y las cuentas de los usuarios no ser�n eliminadas.</span>";
$setting['session_timeout'] = "Tiempo m�ximo de una sesi�n inactiva en minutos";
$setting['display_whosonline'] = "Mostrar \"�Qui�n est� en l�nea?\". Solo visible para los administradores si se desactiva.";
$setting['highlight_admin'] = "Mostrar administradores en negrita en \"�Qui�n est� en l�nea?\" ";
?>