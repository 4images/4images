<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: admin.php                                            *
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

$lang['user_integration_delete_msg'] = "Usted no está usando la 4images base de datos. El usuario no se elimina.";

//-----------------------------------------------------
//--- Main --------------------------------------------
//-----------------------------------------------------
$lang['no_admin'] = "Usted no es el administrador o no ha iniciado sesión.";
$lang['admin_login_redirect'] = "Sesión Iniciada. Será redireccionado...";
$lang['admin_no_lang'] = "No se encuentra el idioma. Por favor, suba el fichero de idioma  <b>\"english\"</b> en su directorio <b>\"lang\"</b>.";
$lang['admin_login'] = "Sesión Iniciada";
$lang['goto_homepage'] = "Ir a la Página Inicial de la Galería";
$lang['online_users'] = "{num_total} usuario(s) en línea ({num_registered} usuario(s) registrado(s) y {num_guests} invitados).";
$lang['homestats_total'] = "Total:";
$lang['top_cat_hits'] = "Top 5 categorías por visitas";
$lang['top_image_hits'] = "Top 5 imágenes por visitas";
$lang['top_image_downloads'] = "Top 5 imágenes por descargas";
$lang['top_image_rating'] = "Top 5 imágenes por puntuación";
$lang['top_image_votes'] = "Top 5 imágenes por votos";
$lang['yes'] = "Si";
$lang['no'] = "No";
$lang['search'] = "Buscar";
$lang['search_next_page'] = "Página Siguiente";
$lang['save_changes'] = "Guardar Cambios";
$lang['reset'] = "Restablecer";
$lang['add'] = "Añadir";
$lang['edit'] = "Editar";
$lang['delete'] = "Borrar";
$lang['options'] = "Opciones";
$lang['back_overview'] = "Retornar a vista previa";
$lang['back'] = "Retornar";
$lang['sort_options'] = "Opciones";
$lang['order_by'] = "Ordenar por";
$lang['results_per_page'] = "Resultados por página";
$lang['asc'] = "Ascendente";
$lang['desc'] = "Descendente";
$lang['found'] = "Encontrados: ";
$lang['showing'] = "Mostrados: ";
$lang['date_format'] = "<br /><span class=\"smalltext\">(Formato de fecha: aaaa-mm-dd hh:mm:ss)</span>";
$lang['date_desc'] = "<br /><span class=\"smalltext\">Dejar el campo en blanco para usar el formato de fecha por defecto.</span>";

$lang['userlevel_admin'] = "Administradores";
$lang['userlevel_registered'] = "Usuarios Registrados";
$lang['userlevel_registered_awaiting'] = "Usuarios Registrados (no activado)";

$lang['headline_whosonline'] = "¿Quién está en línea?";
$lang['headline_stats'] = "Estadísticas";

$lang['images'] = "Imágenes";
$lang['users'] = "Usuarios";
$lang['database'] = "Base de datos";
$lang['media_directory'] = "Directorio imágenes";
$lang['thumb_directory'] = "Directorio imágenes reducidas";
$lang['validate'] = "Validar";
$lang['ignore'] = "Ignorar";
$lang['images_awaiting_validation'] = "<b>{num_images}</b> Imágenes esperando validación";

$lang['permissions'] = "Permisos";
$lang['all'] = "Todos";
$lang['private'] = "Privado";
$lang['all_categories'] = "Todas las categorías";
$lang['no_category'] = "No Categoría";

$lang['reset_stats_desc'] = "Si deseas restablecer las estadísticas a un valor específico, introduzca un número. Deja el campo en blanco si no deseas modificar las estadísticas.";

//-----------------------------------------------------
//--- Email -------------------------------------------
//-----------------------------------------------------
$lang['send_emails'] = "Enviar correo a usuarios";
$lang['send_emails_subject'] = "Asunto";
$lang['send_emails_message'] = "Mensaje";
$lang['select_email_user'] = "Seleccionar usuario";
$lang['send_emails_success'] = "correos enviados";
$lang['send_emails_error'] = "¡Error enviando correo!";

//-----------------------------------------------------
//--- Error Messages ----------------------------------
//-----------------------------------------------------
$lang['error'] = "ERROR:";
$lang['error_log_desc'] = "Los siguientes errores han aparecido:";
$lang['lostfield_error'] = "Por favor, examine los campos rellenados.";
$lang['parent_cat_error'] = "¡No puedes asignar una categoría como subcategoría!";
$lang['invalid_email_error'] = "¡Por favor, compruebe el formato de la dirección de correo!";
$lang['no_search_results'] = "No aparecen resultados.";

//-----------------------------------------------------
//--- Fields ------------------------------------------
//-----------------------------------------------------
$lang['field_image_name'] = "Nombre de imagen";
$lang['field_category_name'] = "Nombre de categoría";
$lang['field_username'] = "Nombre de usuario";
$lang['field_password'] = "Contraseña";
$lang['field_userlevel'] = "Nivel de usuario";
$lang['field_password'] = "Contraseña";
$lang['field_password_ext'] = "Contraseña:<br /><span class=\"smalltext\">Deje el campo Contraseña en blanco si no quiere cambiarla.</span>";
$lang['field_headline'] = "Titular";
$lang['field_email'] = "Correo";
$lang['field_homepage'] = "Página web";
$lang['field_icq'] = "ICQ";
$lang['field_showemail'] = "Mostrar correo";
$lang['field_allowemails'] = "Reciba los correos de administradores:";
$lang['field_invisible'] = "Invisible";
$lang['field_date'] = "Fecha";
$lang['field_joindate'] = "Fecha de registro";
$lang['field_lastaction'] = "Última actividad";
$lang['field_ip'] = "IP";
$lang['field_comment'] = "Comentario";
$lang['field_description'] = "Descripción";
$lang['field_description_ext'] = "Descripción<br /><span class=\"smalltext\">HTML permitido.</span>";
$lang['field_parent'] = "Subcategoría padre";
$lang['field_hits'] = "Impactos";
$lang['field_downloads'] = "Descargas";
$lang['field_votes'] = "Votos";
$lang['field_rating'] = "Puntuación";
$lang['field_category'] = "Categoría";
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
$lang['field_description_contains'] = "La descripción contiene";
$lang['field_keywords_contains'] = "Palabra clave contiene";
$lang['field_username_contains'] = "Nombre de usuario contiene";
$lang['field_email_contains'] = "Correo contiene";
$lang['field_headline_contains'] = "Titular contiene";
$lang['field_comment_contains'] = "Comentario contiene";
$lang['field_date_before'] = "Fecha anterior a";
$lang['field_date_after'] = "Fecha posterior a";
$lang['field_joindate_before'] = "Registrado después de";
$lang['field_joindate_after'] = "Registrado antes de";
$lang['field_lastaction_before'] = "Última actividad después de";
$lang['field_lastaction_after'] = "Última actividad antes de";
$lang['field_image_file_contains'] = "El archivo de imagen contiene";
$lang['field_thumb_file_contains'] = "Archivo Thumbnail contiene";
$lang['field_downloads_upper'] = "Descargas superiores a";
$lang['field_downloads_lower'] = "Descargas inferiores a";
$lang['field_rating_upper'] = "Puntuación superior a";
$lang['field_rating_lower'] = "Puntuación inferior a";
$lang['field_votes_upper'] = "Número de votos superior a";
$lang['field_votes_lower'] = "Número de votos inferior a";
$lang['field_hits_upper'] = "Número de impactos superior a";
$lang['field_hits_lower'] = "Número de impactos inferior a";

//-----------------------------------------------------
//--- Navigation --------------------------------------
//-----------------------------------------------------
$lang['nav_categories_main'] = "Categorías";
$lang['nav_categories_edit'] = "Editar categorías";
$lang['nav_categories_add'] = "Agregar categorías";

$lang['nav_images_main'] = "Imágenes";
$lang['nav_images_edit'] = "Editar imágenes";
$lang['nav_images_add'] = "Agregar imágenes";
$lang['nav_images_validate'] = "Imágenes Validadas";
$lang['nav_images_check'] = "Comprobar nuevas imágenes";
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
$lang['nav_general_stats'] = "Restablecer estadísticas";

//-----------------------------------------------------
//--- Categories --------------------------------------
//-----------------------------------------------------
$lang['category'] = "Categoría";
$lang['main_category'] = "Categoría Principal";
$lang['sub_categories'] = "Subcategorías";
$lang['no_categories'] = "No se agregaron categorías";
$lang['select_category'] = "Selecionar categoría";
$lang['add_subcategory'] = "Agregar subcategoría";
$lang['no_subcategories'] = "No se agregaron subcategorías";
$lang['delete_cat_confirm'] = "¿Realmente desea eliminar esta categoría?<br />¡Todas las subcategorías y las imágenes y comentarios contenidas en ellas serán eliminadas!";
$lang['delete_cat_files_confirm'] = "¿Eliminar todas las imágenes del servidor?";
$lang['cat_add_success'] = "Categoría agregada";
$lang['cat_add_error'] = "Error agregando categoría";
$lang['cat_edit_success'] = "Categoría editada";
$lang['cat_edit_error'] = "Error editando categoría";
$lang['cat_delete_success'] = "Categoría eliminada";
$lang['cat_delete_error'] = "Error eliminado categoría";
$lang['permissions_inherited'] = "Usando los permisos heredados de categoría padre.";
$lang['cat_order'] = "Orden de categoría";
$lang['at_beginning'] = "Al comienzo";
$lang['at_end'] = "Al final";
$lang['after'] = "Después";

//-----------------------------------------------------
//--- Images ------------------------------------------
//-----------------------------------------------------
$lang['image'] = "Imagen";
$lang['image_file'] = "Archivo de imagen";
$lang['thumb'] = "Thumbnail";
$lang['thumb_file'] = "Archivo Thumbnail";
$lang['delete_image_confirm'] = "¿Realmente quiere eliminar este archivo de imagen? Todos los comentarios relativos a esta imagen serán eliminados.";
$lang['delete_image_files_confirm'] = "¿Eliminar los archivos de imagen del servidor?";
$lang['file_upload_error'] = "Error enviando archivo de imagen";
$lang['thumb_upload_error'] = "Error enviando archivo thumbnail";
$lang['no_image_file'] = "Por favor, seleccione el archivo de imagen";
$lang['invalid_file_type'] = "Tipo de archivo incorrecto";
$lang['invalid_image_width'] = "Anchura de imagen incorrecta";
$lang['invalid_image_height'] = "Altura de imagen incorrecta";
$lang['invalid_file_size'] = "Tamaño de imagen incorrecto";
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
$lang['allowed_mediatypes_desc'] = "Extensiones válidas";
$lang['no_thumb_found'] = "No se encuentra thumbnail";
$lang['no_db_entry'] = "¡Ninguna entrada de base de datos!";
$lang['check_all'] = "Comprobar todo";
$lang['detailed_version'] = "Versión detallada";
$lang['num_newimages_desc'] = "Imágenes mostradas";
$lang['num_addnewimages_desc'] = "Nuevas imágenes mostradas";
$lang['no_newimages'] = "No existen nuevas imágenes";
$lang['thumb_newimages_exists'] = "Thumbnail encontrado";
$lang['no_thumb_newimages'] = "No se encuentran thumbnails";
$lang['no_thumb_newimages_ext'] = "No hay nuevos thumbnails. Se muestre el icono por defecto.";
$lang['no_newimages_added'] = "¡No se agregaron nuevas imágenes!";
$lang['no_image_found'] = "Imágenes marcadas con <b class=\"marktext\">!</b> indican que no se encuentra el archivo de imagen en el servidor.";
$lang['upload_progress'] = "Enviando archivo...";
$lang['upload_progress_desc'] = "Esta ventana se cerrará automáticamente cuando el envío haya finalizado.";
$lang['upload_note'] = "<b>NOTA:</b> En caso de que el nombre del archivo thumbnail no se corresponda con el nombre del archivo de imagen, éste será adaptado al nombre del archivo de imagen.";
$lang['checkimages_note'] = "Las siguientes imágenes (<b>{num_all_newimages}</b>) no se han introducido en la base de datos.";
$lang['download_url_desc'] = "<br /><span class=\"smalltext\">Si rellenas este campo, el botón de descarga apuntará a la URL que introduzcas, <br> en caso contrario apuntará directamente al archivo de imagen.</span>";
$lang['images_delete_success'] = "Imágenes eliminadas";
$lang['images_delete_error'] = "Error eliminando imágenes";

//-----------------------------------------------------
//--- Comments ----------------------------------------
//-----------------------------------------------------
$lang['comment'] = "Comentario";
$lang['comments'] = "Comentarios";
$lang['delete_comment_confirm'] = "¿Eliminar este comentario?";
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
$lang['user_delete_confirm'] = "¿Eliminar usuario?";
$lang['user_delete_comments_confirm'] = "¿Eliminar todos los comentarios del usuario?";
$lang['user_add_success'] = "Usuario agregado";
$lang['user_add_error'] = "Error agregando usuario";
$lang['user_edit_success'] = "Usuario editado";
$lang['user_edit_error'] = "Error editando usuario";
$lang['user_delete_success'] = "Usuario eliminado";
$lang['user_delete_error'] = "Error eliminando usuario";
$lang['user_comments_update_success'] = "Comentarios del usuario actualizados (ID de usuario restablecido a \"Invitado\")";
$lang['user_comments_update_error'] = "Error actualizando comentarios del usuario (ID del usuario no restablecido a \"Invitado\")";
$lang['user_name_exists'] = "¡El usuario ya existe!";
$lang['user_email_exists'] = "¡El email del usuario ya existe!";
$lang['num_newusers_desc'] = "Número de usuarios nuevos para añadir";
$lang['user_delete_images_confirm'] = "¿Eliminar todas las imágenes agregadas por el usuario?";
$lang['user_images_update_success'] = "Imágenes de usuario actualizadas (ID de usuario restablecido a \"Invitado\")";
$lang['user_images_update_error'] = "Error subiendo imágenes de usuario (ID del usuario no restablecido a \"Invitado\")";

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
$lang['delete_group_confirm'] = "¿Eliminar grupo de usuarios?";
$lang['auth_viewcat'] = "Ver categoría";
$lang['auth_viewimage'] = "Ver imagen";
$lang['auth_download'] = "Descargar";
$lang['auth_upload'] = "Subir";
$lang['auth_directupload'] = "Subir directamente";
$lang['auth_vote'] = "Votar";
$lang['auth_sendpostcard'] = "Enviar postal";
$lang['auth_readcomment'] = "Leer comentarios";
$lang['auth_postcomment'] = "Publicar comentario";
$lang['permissions_edit_success'] = "Permisos actualizados";
$lang['activate_date'] = "Fecha de activación";
$lang['expire_date'] = "Fecha de expiración";
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
$lang['template_edit_success'] = "¡Plantilla editada!";
$lang['template_edit_error'] = "'¡Error guardando plantilla! Compruebe los permisos (chmod 666).";

//-----------------------------------------------------
//--- Backup ------------------------------------------
//-----------------------------------------------------
$lang['do_backup'] = "Copia de seguridad de base de datos";
$lang['do_backup_desc'] = "Realizar copia de seguridad de base de datos.<br /> <span class=\"smalltext\">Seleccione las tablas de la base de datos que quiere actualizar. Las tablas requeridas están preseleccionadas.";
$lang['list_backups'] = "Listar copias de seguridad";
$lang['no_backups'] = "No hay copias de seguridad";
$lang['restore_backup'] = "Restaurar";
$lang['delete_backup'] = "Eliminar";
$lang['download_backup'] = "Descargar";
$lang['show_backup'] = "Mostrar";
$lang['make_backup_success'] = "Copia de seguridad guardada satisfactoriamente.";
$lang['make_backup_error'] = "Error guardando copia de seguridad. Compruebe los permisos (chmod 777).";
$lang['backup_delete_confirm'] = "¿Eliminar copia de seguridad?";
$lang['backup_delete_success'] = "Copia de seguridad eliminada";
$lang['backup_delete_error'] = "Error eliminando copia de seguridad";
$lang['backup_restore_confirm'] = "¿Restaurar base de datos?";
$lang['backup_restore_success'] = "Base de datos restaurada";
$lang['backup_restore_error'] = "Errores restaurando la copia de seguridad:";

//-----------------------------------------------------
//--- Thumbnailer & Resizer ---------------------------
//-----------------------------------------------------
$lang['im_error'] = "Error con ImageMagick. Ruta errónea o no está instalado ImageMagick.";
$lang['gd_error'] = "Error en la librería GD.";
$lang['netpbm_error'] = "Error con NetPBM. Ruta errónea o no está instalado NetPBM.";
$lang['no_convert_module'] = "Seleccione módulo para crear thumbnails.";
$lang['check_module_settings'] = "Comprobar configuraciones del módulo.";
$lang['check_thumbnails'] = "Comprobar thumbnails";
$lang['check_thumbnails_desc'] = "Comprobar base de datos buscando thumbnails perdidos.";
$lang['create_thumbnails'] = "Crear thumbnails";
$lang['creating_thumbnail'] = "Crear thumbnail para: ";
$lang['creating_thumbnail_success'] = "¡Thumbnail creado!";
$lang['creating_thumbnail_error'] = "Error creando thumbnail!";
$lang['convert_thumbnail_dimension'] = "Tamaño en pixels del thumbnail";
// <br /><span class=\"smalltext\">las dimensiones serán adaptadas</span>
$lang['convert_thumbnail_quality'] = "Calidad del thumbnail<br /><span class=\"smalltext\">de 0 a 100</span>";
$lang['convert_options'] = "Conversión";
$lang['resize_images'] = "Redimensionar imágenes";
$lang['resize_image_files'] = "Redimensionar archivos de imagen";
$lang['resize_thumb_files'] = "Redimensionar archivos thumbnail";
$lang['resize_org_size'] = "Tamaño original";
$lang['resize_new_size'] = "Nuevo tamaño de imagen";
$lang['resize_new_quality'] = "Calidad de imagen";
$lang['resize_image_type_desc'] = "¿Convertir los archivos de imagen o thumbnails?";

$lang['resize_dimension_desc'] = "Tamaño de archvo de imagen en pixels.";
// <br /><span class=\"smalltext\">Si introduce 200, la longitud máxima del archivo de imagen será establecido en 200 y redimensionado proporcionalmente.</span>
$lang['resize_proportions_desc'] = "Proporciones";
$lang['resize_proportionally'] = "Redimensionar proporcionalmente";
$lang['resize_fixed_width'] = "Redimensionar con ancho fijo";
$lang['resize_fixed_height'] = "Redimensionar con alto fijo";

$lang['resize_quality_desc'] = "Calidad de imagen<br /><span class=\"smalltext\">de 0 a 100</span>";
$lang['resize_start'] = "Iniciar conversión";
$lang['resize_check'] = "Mostrar imagen";
$lang['resizing_image'] = "Convertir archivo de imagen: ";
$lang['resizing_image_success'] = "¡Archivo de imagen convertido!";
$lang['resizing_image_error'] = "¡Error convirtiendo archivo de imagen!";

//-----------------------------------------------------
//--- Settings ----------------------------------------
//-----------------------------------------------------
$lang['save_settings_success'] = "Configuración guardada";

/*-- Setting-Group 1 --*/
$setting_group[1]="Configuración general";
$setting['site_name'] = "Nombre del sitio";
$setting['site_email'] = "correo del administrador";
$setting['use_smtp'] = "Usar servidor SMTP";
$setting['smtp_host'] = "Servidor SMTP";
$setting['smtp_username'] = "Nombre de usuario SMTP";
$setting['smtp_password'] = "Contraseña SMTP";
$setting['template_dir'] = "Elegir directorio de plantillas";
$setting['language_dir'] = "Elegir directorio de idioma";
$setting['date_format'] = "Formato de fecha";
$setting['time_format'] = "Formato horario";
$setting['convert_tool'] = "Herramienta de conversión para thumbnails<br /><span class=\"smalltext\">ImageMagick (http://www.imagemagick.org)<br />GD (http://www.boutell.com/gd)<br />NetPBM (http://netpbm.sourceforge.net)</span>";
$convert_tool_optionlist = array(
  "none"   => "Ninguno",
  "im"     => "ImageMagick",
  "gd"     => "GD Biblioteca",
  "netpbm" => "NetPBM"
);
$setting['convert_tool_path'] = "Si usted ha seleccionado \"ImageMagick\" o \"NetPBM\", introduzca la ruta";
$setting['gz_compress'] = "Usar compresión GZip<br /><span class=\"smalltext\">\"Zlib\" debe estar instalado en su servidor</span>";
$setting['gz_compress_level'] = "Nivel de compresión GZip<br /><span class=\"smalltext\">0-9, 0=ninguno, 9=máximo</span>";

/*-- Setting-Group 2 --*/
$setting_group[2]="Configuración de categorías";
$setting['cat_order'] = "Ordenar categorías por";
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
$setting['cat_cells'] = "Número de celdas de tabla";
$setting['cat_table_width'] = "Anchura de tabla<br /><span class=\"smalltext\">anchura en pixels o en porcentaje</span>";
$setting['cat_table_cellspacing'] = "Espacio de celda";
$setting['cat_table_cellpadding'] = "Hueco de celda";
$setting['num_subcats'] = "Número de subcategorías";

/*-- Setting-Group 3 --*/
$setting_group[3]="Configuración de imágenes";
$setting['image_order'] = "Ordenar imágenes por";
$image_order_optionlist = array(
  "image_name"      => "Nombre",
  "image_date"      => "Fecha",
  "image_downloads" => "Descargas",
  "image_votes"     => "Votos",
  "image_rating"    => "Puntuación",
  "image_hits"      => "Impactos"
);
$setting['image_sort'] = "Ascendente/Descendente";
$image_sort_optionlist = array(
  "ASC"  => "Ascendente",
  "DESC" => "Descendente"
);
$setting['new_cutoff'] = "Número de días por el cual se consideran las imágenes como nuevas";
$setting['image_border'] = "Borde en los thumbnails";
$setting['image_cells'] = "Número de celdas en la tabla de imágenes";
$setting['default_image_rows'] = "Número de líneas en la tabla de imágenes";
$setting['custom_row_steps'] = "Numéro de opciones en el menú desplegable (permite a los usuarios elegir el número de imágenes por página)";
$setting['image_table_width'] = "Anchura de tabla<br /><span class=\"smalltext\">por pixels o por porcentaje</span>";
$setting['image_table_cellspacing'] = "Espacio de tabla";
$setting['image_table_cellpadding'] = "Hueco de tabla";

/*-- Setting-Group 4 --*/
$setting_group[4]="Configuraciones de imágenes y envío de imágenes.";
$setting['upload_mode'] = "Modo de envío";
$upload_mode_optionlist = array(
  "1" => "Reemplazar archivos",
  "2" => "Guardar archivos con un nuevo nombre",
  "3" => "No subir archivos"
);
$setting['allowed_mediatypes'] = "Extensiones de archivo válidas<br /><span class=\"smalltext\">Delimitadas con una coma, no con espacios. Cuando incluya nuevos tipos de archivos, cree una nueva plantilla en el directorio de plantillas (templates).</span>";
$setting['max_thumb_width'] = "Max. anchura del thumbnail en pixel";
$setting['max_thumb_height'] = "Max. altura del thumbnail en pixel";
$setting['max_thumb_size'] = "Max. tamaño del thumbnail en KB";
$setting['max_image_width'] = "Max. anchura de la imagen en pixel";
$setting['max_image_height'] = "Max. altura de la imagen en pixel";
$setting['max_media_size'] = "Max. tamaño de la imagen en KB";
$setting['upload_notify'] = "Notificar mediante correo los uploads de usuarios";
$setting['upload_emails'] = "correos adicionales para notificaciones <br /><span class=\"smalltext\">Delimita los emails mediante comas.</span>";
$setting['auto_thumbnail'] = "Crear thumbnails";
$setting['auto_thumbnail_dimension'] = "Tamaño en pixels del thumbnail";
$setting['auto_thumbnail_resize_type'] = "Proporciones";
$auto_thumbnail_resize_type_optionlist = array(
  "1" => "Redimensionar proporcionalmente",
  "2" => "Redimensionar con ancho fijo",
  "3" => "Redimensionar con alto fijo"
);
$setting['auto_thumbnail_quality'] = "Calidad del Thumbnail<br /><span class=\"smalltext\">de 0 a 100</span>";

/*-- Setting-Group 5 --*/
$setting_group[5]="Configuraciones de comentarios";
$setting['badword_list'] = "Lista de palabras prohibidas<br /><span class=\"smalltext\">Introduzca palabras separadas por espacios (no comas). Si introduce la palabra \"prueba\", todas las palabras que contengan \"prueba\" serán censuradas. Por ejemplo, con \"Aprueba\" se mostrará \"A******\". Si prefiere ser más específico, se censurarán exactamente todos aquellos términos que usted encierre entre llaves {prueba}. De este modo la palabra \"prueba\" será censurada, pero no sucederá lo mismo con \"aprueba\".</span>";
$setting['badword_replace_char'] = "Caracter para reemplazar palabras censuradas";
$setting['wordwrap_comments'] = "Ajuste automático de línea <br /><span class=\"smalltext\">utilizado para evitar order lineas interminables que desajusten la pantalla, seleccione un número máximo de carácteres por línea. 0 desactiva el ajuste automático de línea.</span>";
$setting['html_comments'] = "Permitir HTML en los comentarios";
$setting['bb_comments'] = "Permitir Código-BB en los comentarios";
$setting['bb_img_comments'] = "Permitir imágenes (Código-BB) en los comentarios<br /><span class=\"smalltext\">Si selecciona \"No\", cualquier imagen será mostrada como un hiperenlace.</span>";

/*-- Setting-Group 6 --*/
$setting_group[6]="Configuración de página y navegación";
$setting['category_separator'] = "Delimitador de categorías (en la ruta de categorías)";
$setting['paging_range'] = "Número de páginas \"anterior\" y \"siguiente\" mostradas en la navegación";

/*-- Setting-Group 7 --*/
$setting_group[7]="Configuración de usuarios y sesiones";
$setting['user_edit_image'] = "Permitir a los usaurios editar sus propias imágenes";
$setting['user_delete_image'] = "Permitir a los usaurios borrar sus propias imágenes";
$setting['user_edit_comments'] = "Permitir a los usaurios editar los comentarios de sus propias imágenes";
$setting['user_delete_comments'] = "Permitir a los usuarios borrar loscomentarios de sus propias imágenes";
$setting['account_activation'] = "Activar cuenta";
$account_activation_optionlist = array(
  "0" => "Ninguna",
  "1" => "correo",
  "2" => "Administrador"
);
$setting['activation_time'] = "Periodo máximo para que un usuario active su cuenta, en días.<br /><span class=\"smalltext\">0  desactiva esta función y las cuentas de los usuarios no serán eliminadas.</span>";
$setting['session_timeout'] = "Tiempo máximo de una sesión inactiva en minutos";
$setting['display_whosonline'] = "Mostrar \"¿Quién está en línea?\". Solo visible para los administradores si se desactiva.";
$setting['highlight_admin'] = "Mostrar administradores en negrita en \"¿Quién está en línea?\" ";
?>