<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: main.php                                             *
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

$lang['no_settings'] = "ERROR: �No se pueden cargar los valores de configuraci�n!";

//-----------------------------------------------------
//--- CAPTCHA -----------------------------------------
//-----------------------------------------------------
$lang['captcha'] = "C�digo de verficaci�n:";
$lang['captcha_desc'] = "Por favor introduce las letras o n�meros que aparecen en la imagen. Si tienes problemas para identificarlas, haz clic en ella para mostrar otra.";
$lang['captcha_required'] = 'Por favor introduce el c�digo de verificaci�n.';

//-----------------------------------------------------
//--- Templates ---------------------------------------
//-----------------------------------------------------
$lang['charset'] = "iso-8859-1";
$lang['direction'] = "ltr";

//-----------------------------------------------------
//--- Userlevel ---------------------------------------
//-----------------------------------------------------
$lang['userlevel_admin'] = "Administrador";
$lang['userlevel_user'] = "Miembro";
$lang['userlevel_guest'] = "Invitado";

//-----------------------------------------------------
//--- Categor�as --------------------------------------
//-----------------------------------------------------
$lang['no_categories'] = "No se encuentran categor�as.";
$lang['no_images'] = "No hay im�genes en esta categor�a.";
$lang['select_category'] = "Seleccionar categor�a";

//-----------------------------------------------------
//--- Comments ---------------------------------------
//-----------------------------------------------------
$lang['name_required'] = "Introduzca el nombre.";
$lang['headline_required'] = "Escriba un titular.";
$lang['comment_required'] = "Escriba un comentario.";
$lang['spamming'] = "No puede repostear tan r�pido, por favor vuelve a intentarlo dentro de un rato.";
$lang['comments'] = "Comentarios:";
$lang['no_comments'] = "No hay comentarios para esta imagen";
$lang['comments_deactivated'] = "�Comentarios desactivados!";
$lang['post_comment'] = "Agregar comentario";
$lang['comment_success'] = "Tu comentario se ha guardado";

//-----------------------------------------------------
//--- BBCode ------------------------------------------
//-----------------------------------------------------
$lang['bbcode'] = "BBCode";
$lang['tag_prompt'] = "Introducir texto para formatearlo:";
$lang['link_text_prompt'] = "Introducir el texto que se muestra con el enlace (opcional)";
$lang['link_url_prompt'] = "Introducir la URL completa del enlace";
$lang['link_email_prompt'] = "Introducir el Email del enlace";
$lang['list_type_prompt'] = "�Qu� tipo de lista desea? Teclee '1' para una lista numerada, teclee 'a' para un listado alfab�tico, o d�jelo en blanco para un listado con puntos.";
$lang['list_item_prompt'] = "Introcuzca una lista. Deje el campo en blanco o haga click en 'Cancel' para completar la lista.";

//-----------------------------------------------------
//--- Image Details -----------------------------------
//-----------------------------------------------------
$lang['download_error'] = "�Error de descarga!";
$lang['register_download'] = "�Reg�strese para descargar im�genes!.<br />&raquo; <a href=\"{url_register}\">Reg�strarse</a>";
$lang['voting_success'] = "Gracias por puntuar esta imagen";
$lang['voting_error'] = "�Calificaci�n invalida!";
$lang['already_voted'] = "Lo sentimos, usted ha calificado esta imagen recientemente.";
$lang['prev_image'] = "Imagen anterior:";
$lang['next_image'] = "Siguiente imagen:";
$lang['category'] = "Categor�a:";
$lang['description'] = "Descripci�n:";
$lang['keywords'] = "Palabras clave:";
$lang['date'] = "Fecha:";
$lang['hits'] = "Impactos:";
$lang['downloads'] = "Descargas:";
$lang['rating'] = "Puntuaci�n:";
$lang['votes'] = "Votos";
$lang['file_size'] = "Tama�o de archivo:";
$lang['author'] = "Autor:";
$lang['name'] = "Nombre:";
$lang['headline'] = "Descripci�n breve:";
$lang['comment'] = "Comentario:";
$lang['added_by'] = "Env�ado por:";
$lang['allow_comments'] = "Permitir comentarios:";

// IPTC Tags
$lang['iptc_caption'] = "T�tulo:";
$lang['iptc_caption_writer'] = "Autor:";
$lang['iptc_headline'] = "Titular:";
$lang['iptc_special_instructions'] = "Instrucciones especiales:";
$lang['iptc_byline'] = "Datos:";
$lang['iptc_byline_title'] = "Datos t�tulo:";
$lang['iptc_credit'] = "Creditos:";
$lang['iptc_source'] = "C�digo:";
$lang['iptc_object_name'] = "Nombre de objeto:";
$lang['iptc_date_created'] = "Fecha de creaci�n:";
$lang['iptc_city'] = "Ciudad:";
$lang['iptc_state'] = "Estado/Provincia:";
$lang['iptc_country'] = "Pa�s:";
$lang['iptc_original_transmission_reference'] = "Referencia de transmisi�n original:";
$lang['iptc_category'] = "Categor�a:";
$lang['iptc_supplemental_category'] = "Categor�a adicional:";
$lang['iptc_keyword'] = "Palabras clave:";
$lang['iptc_copyright_notice'] = "Informaci�n de copyright:";

// EXIF Tags
$lang['exif_make'] = "Fabricante:";
$lang['exif_model'] = "Modelo:";
$lang['exif_datetime'] = "Fecha de creaci�n:";
$lang['exif_isospeed'] = "Velocidad ISO:";
$lang['exif_exposure'] = "Tiempo de exposici�n:";
$lang['exif_aperture'] = "Valor de apertura:";
$lang['exif_focallen'] = "Longitud focal:";

//-----------------------------------------------------
//--- Postcards ---------------------------------------
//-----------------------------------------------------
$lang['send_postcard'] = "Mandar postal";
$lang['edit_postcard'] = "Moficiar postal";
$lang['preview_postcard'] = "Previsualizar postal";
$lang['bg_color'] = "Color de dondo:";
$lang['border_color'] = "Color del borde:";
$lang['font_color'] = "Color del tipo de letra:";
$lang['font_face'] = "Tipo de letra:";
$lang['recipient'] = "Destinatario";
$lang['sender'] = "Remitente";
$lang['send_postcard_emailsubject'] = "�Has recibido una tarjeta postal para t�!";
$lang['send_postcard_success'] = "�Muchas gracias! �Tu postal ha sido enviada!";
$lang['back_to_gallery'] = "Volver a la galer�a";
$lang['invalid_postcard_id'] = "Identificador de postal no v�lido.";

//-----------------------------------------------------
//--- Top Images --------------------------------------
//-----------------------------------------------------
$lang['top_image_hits'] = "Top 5 im�genes por visitas";
$lang['top_image_downloads'] = "Top 5 im�genes por descargas";
$lang['top_image_rating'] = "Top 5 im�genes por puntuaci�n";
$lang['top_image_votes'] = "Top 5 im�genes por votos";

//-----------------------------------------------------
//--- Usuarios ----------------------------------------
//-----------------------------------------------------
$lang['send_password_emailsubject'] = "Env�o de contrase�a de {site_name}";  // Mail subject for password.
$lang['update_email_emailsubject'] = "Actualizar Email de {site_name}";      // Mail subject for activation code when changing email address
$lang['register_success_emailsubject'] = "Registro en {site_name}";          // Mail subject for activation code
$lang['admin_activation_emailsubject'] = "Cuenta Activada";                  // Mail subject for account activation by admin.
$lang['activation_success_emailsubject'] = "Cuenta Activada";                // Mail subject after account activation by admin.

$lang['no_permission'] = "�No ha iniciado sesi�n aqu�, o bien no tiene permiso para entrar en este sitio!";
$lang['already_registered'] = "�Usted ya est� registrado. Si ha perdido su contrase�a, por favor haga lcick <a href=\"{url_lost_password}\">aqu�</a>.";
$lang['username_exists'] = "El nombre de usuario ya est� registrado.";
$lang['email_exists'] = "La direcci�n de correo ya est� registrada.";
$lang['invalid_email_format'] = "Por favor, introduzca una direcci�n de correo v�lida.";
$lang['register_success'] = "Ahora estas registrado. En breve recibiras un email con su c�digo de activaci�n.";
$lang['register_success_admin'] = "Ahora estas registrado. Su cuenta ha sido desactivada, el administrador debe activarla antes de acceder. Recibira una notificacion una vez que sea activada.";
$lang['register_success_none'] = "Ahora estas registrado. Por favor identifiquese.";
$lang['missing_activationkey'] = "Su c�digo de activaci�n no se encuentra.";
$lang['invalid_activationkey'] = "Cuenta inactiva. Por favor, reg�strese de nuevo.</>";
$lang['activation_success'] = "�Gracias! Su cuenta ha sido activada. Por favor, inicie una sesi�n.";
$lang['general_error'] = "Ha habido un error. Por favor <a href=javascript:history.go(-1)>vuelva atr�s</a> e int�ntelo de nuevo. Si el problema persiste, contacte con el administrador.";
$lang['invalid_login'] = "Ha indicando un nombre de usuario o password inv�lido.";
$lang['update_email_error'] = "�Por favor, introduzca su correo de nuevo!";
$lang['update_email_confirm_error'] = "�Las direcciones de correo que ha introducido no se corresponden!";
$lang['update_profile_success'] = "�Su perfil ha sido actualizado!";
$lang['update_email_instruction'] = "Una vez modificado su correo, debe reactivar su cuenta. El c�digo de activaci�n ha sido enviado a su nueva direcci�n de correo";
$lang['update_email_admin_instruction'] = "Como su correo electr�nico ha cambiado, el administrador necesita reactivar su cuenta. Recibira una notificacion de reactivacion de su cuenta en breve.";
$lang['invalid_email'] = "Correo incorrecto.";
$lang['send_password_success'] = "Su contrase�a ha sido remitida a su direcci�n de correo.";
$lang['update_password_error'] = "�Ha introducido un Email incorrecto!";
$lang['update_password_confirm_error'] = "�Las dos contrase�as que ha tecleado no son iguales!";
$lang['update_password_success'] = "Su contrase�a ha sido modificada.";
$lang['invalid_user_id'] = "�No se encuentra usuario!";
$lang['emailuser_success'] = "El correo ha sido enviado";
$lang['send_email_to'] = "Enviar correo a:";
$lang['subject'] = "Asunto:";
$lang['message'] = "Mensaje:";
$lang['profile_of'] = "Perfil de usuario de:";
$lang['edit_profile_msg'] = "Se le permite modificar su perfil y su contrase�a.";
$lang['edit_profile_email_msg'] = "<br />Nota: si usted cambia su direcci�n de correo, debe reactivar su cuenta. Se le enviar�a un c�digo de activaci�n a su nueva direcci�n.";
$lang['edit_profile_email_msg_admin'] = "<br />Nota: Si cambia su correo electr�nico el administrador tendra que reactivar su cuenta.";
$lang['join_date'] = "Fecha de alta:";
$lang['last_action'] = "�ltima actividad:";
$lang['email'] = "Correo:";
$lang['email_confirm'] = "Confirmar correo:";
$lang['homepage'] = "Homepage:";
$lang['icq'] = "ICQ:";
$lang['show_email'] = "Mostrar mi direcci�n de correo:";
$lang['allow_emails'] = "Reciba los email de administradores:";
$lang['invisible'] = "Ocultar su presencia en l�nea:";
$lang['optional_infos'] = "Opcional";
$lang['change_password'] = "Cambiar contrase�a";
$lang['old_password'] = "Vieja contrase�a:";
$lang['new_password'] = "Nueva contrase�a:";
$lang['new_password_confirm'] = "Confirmar nueva contrase�a:";
$lang['lost_password'] = "Introduzca contrase�a de nuevo";
$lang['lost_password_msg'] = "En caso de haber perdido su contrase�a, introduzca la direcci�n de correo que utiliz� para registrarse.";
$lang['user_name'] = "Usuario:";
$lang['password'] = "Contrase�a:";

$lang['register_msg'] = "Por favor, rellene todos los campos. Introduzca una direcci�n de correo v�lida para poder proporcionarle su c�digo de activaci�n.";
$lang['agreement'] = "Condiciones del registro:";
$lang['agreement_terms'] = "
            Usted acepta que los administradores de este sitio web tienen la
            facultad de intentar eliminar o editar cualquier material que
            pudiera ser objeccionable en el tiempo m�s breve posible. Usted
            acepta que todos los mensajes publicados en este sitio expresan
            las opiniones y puntos de vista de sus autores, no son opiniones
            ni puntos de vista de los administradores, moderadores o webmasters
            (excepto aquellos mensajes creados expresamente por estas �ltimas
            personas) y por tanto, no pueden ser responsables de las opiniones
            publicadas por los visitantes.
            <br /><br />
            Usted acepta no publicar ning�n contenido abusivo, obsceno, vulgar,
            escandaloso, hiriente, amenazante, calumnioso, de contenido sexual
            o pornogr�fico o cualquier otro material que pueda violar las leyes
            vigentes. Usted acepta que al webmaster y administrador de este sitio
            les asiste el derecho de eliminar o editar cualquier tema en el
            momento que estime oportuno. Como usuario usted acepta que cualquier
            dato que usted nos facilite ser� almacenado en una base de datos.
            Esta informaci�n no ser� revelada a ning�n tercero sin su
            consentimiento. El webmaster y el administrador no pueden ser
            responsables de los intentos de acceso o ataques que puedan poner
            sus datos en compromiso.
            <br /><br />
            Este sistema utiliza cookies para almacenar informaci�n en
            su ordenador. Estos cookies no contienen informaciones personales,
            sirven �nicamente para hacer m�s placentera su experiencia de navegaci�n
            por este sitio.
            <br /><br />
            Haciendo click en Estoy de acuerdo usted acepta todas estas
            condiciones.";

$lang['agree'] = "Estoy de acuerdo";
$lang['agree_not'] = "No estoy de acuerdo";
$lang['show_user_images'] = "Mostrar todas las im�genes agregadas por {user_name}";

//-----------------------------------------------------
//--- Edit Images -------------------------------------
//-----------------------------------------------------
$lang['image_edit'] = "Editar imagen";
$lang['image_edit_success'] = "Imagen editada";
$lang['image_edit_error'] = "Error editando imagen";
$lang['image_delete'] = "Borrar imagen";
$lang['image_delete_success'] = "Imagen borrada";
$lang['image_delete_error'] = "Error borrando imagen";
$lang['image_delete_confirm'] = "�Desea borrar este fichero de imagen?";

//-----------------------------------------------------
//--- Edit Comments -----------------------------------
//-----------------------------------------------------
$lang['comment_edit'] = "Editar comentario";
$lang['comment_edit_success'] = "Comentario editado";
$lang['comment_edit_error'] = "Error editando comentario.";
$lang['comment_delete'] = "Borrar comentario";
$lang['comment_delete_success'] = "Comentario borrado";
$lang['comment_delete_error'] = "Error borrando comentario.";
$lang['comment_delete_confirm'] = "�Borrar este comentario?";

//-----------------------------------------------------
//--- Image Upload ------------------------------------
//-----------------------------------------------------
$lang['field_required'] = "�Rellena el campo {field_name}!";
$lang['kb'] = "kb";
$lang['px'] = "px";
$lang['file_upload_error'] = "Error enviando archivo de imagen";
$lang['thumb_upload_error'] = "Error enviando archivo thumbnail";
$lang['invalid_file_type'] = "Tipo de archivo incorrecto";
$lang['invalid_image_width'] = "Anchura de imagen incorrecta";
$lang['invalid_image_height'] = "Altura de imagen incorrecta";
$lang['invalid_file_size'] = "Tama�o de imagen incorrecto";
$lang['image_add_success'] = "Imagen agregada";
$lang['allowed_mediatypes_desc'] = "Extensiones v�lidas: ";
$lang['keywords_ext'] = "Palabras clave::<br /><span class=\"smalltext\">Introducir palabras separadas por comas.</span>";
$lang['user_upload'] = "Enviar Imagen";
$lang['image_name'] = "Nombre de Imagen:";
$lang['media_file'] = "Archivo de Imagen:";
$lang['thumb_file'] = "Archivo Thumbnail:";
$lang['max_filesize'] = "Tama�o M�ximo de Archivo: ";
$lang['max_imagewidth'] = "Anchura de Imagen M�xima: ";
$lang['max_imageheight'] = "Altura de Imagen M�xima: ";
$lang['image_file_required'] = "�Elige un Archivo de Imagen!";
$lang['new_upload_emailsubject'] = "Nuevo env�o a {site_name}";
$lang['new_upload_validate_desc'] = "Tu imagen ser� validada una vez que esto sea revisado.";

//-----------------------------------------------------
//--- Lightbox ----------------------------------------
//-----------------------------------------------------
$lang['lightbox_no_images'] = "No hay im�genes guardadas en su caja de favoritos.";
$lang['lightbox_add_success'] = "Imagen guardada.";
$lang['lightbox_add_error'] = "�Error agregando imagen!";
$lang['lightbox_remove_success'] = "Imagen eliminada de su caja.";
$lang['lightbox_remove_error'] = "�Error borrando imagen!";
$lang['lightbox_register'] = "Para poder usar su caja de favoritos, debe registrarse.<br />&raquo; <a href=\"{url_register}\">Registrarse ahora</a>";
$lang['lightbox_delete_success'] = "Caja de favoritos eliminada.";
$lang['lightbox_delete_error'] = "�Error eliminando caja de favoritos!";
$lang['delete_lightbox'] = "Eliminar caja de favoritos";
$lang['lighbox_lastaction'] = "�ltima actualizaci�n de su caja:";
$lang['delete_lightbox_confirm'] = "�Esta seguro de eliminar su caja de favoritos?";

//-----------------------------------------------------
//--- Misc --------------------------------------------
//-----------------------------------------------------
$lang['new'] = "nuevo"; // Marcar im�genes y categor�as con "NUEVO"
$lang['home'] = "Principal";
$lang['categories'] = "Categor�as";
$lang['sub_categories'] = "Subcategor�as";
$lang['lightbox'] = "Caja de favoritos";
$lang['error'] = "Error";
$lang['register'] = "Registro";
$lang['control_panel'] = "Panel de Control";
$lang['profile'] = "Perfil de usuario";
$lang['search'] = "Buscar";
$lang['advanced_search'] = "B�squeda avanzada";
$lang['new_images'] = "Nuevas im�genes";
$lang['top_images'] = "Top im�genes";
$lang['registered_user'] = "Usuarios registrados";
$lang['logout'] = "Cerrar Sesi�n";
$lang['login'] = "Iniciar Sesi�n";
$lang['lang_auto_login'] = "�Iniciar sesi�n autom�ticamente en la siguiente visita?";
$lang['lost_password'] = "Contrase�a olvidada";
$lang['random_image'] = "Imagen aleatoria";
$lang['site_stats'] = "<b>{total_images}</b> im�genes en <b>{total_categories}</b> categor�as.";
$lang['lang_loggedin_msg'] = "Inicio de sesi�n como: <b>{loggedin_user_name}</b>";
$lang['go'] = "Ir";
$lang['submit'] = "Enviar";
$lang['reset'] = "Restablecer";
$lang['save'] = "Guardar";
$lang['yes'] = "Si";
$lang['no'] = "No";
$lang['images_per_page'] = "Im�genes por p�gina:";
$lang['user_online'] = "Usuarios activos actualmente: {num_total_online}";
$lang['user_online_detail'] = "En estos momentos hay <b>{num_registered_online}</b> usuario(s) registrado(s) ({num_invisible_online} de ellos invisibles) y <b>{num_guests_online}</b> invitado(s) conectados.";
$lang['lostfield_error'] = "�Por favor, rellene todos los campos!";
$lang['rate'] = "Calificaci�n";

//-----------------------------------------------------
//--- Paging ------------------------------------------
//-----------------------------------------------------
$lang['paging_stats'] = "Encontradas: {total_cat_images} im�gene(s) on {total_pages} p�gina(s). Mostrados: imagen {first_page} a {last_page}.";
$lang['paging_next'] = "&raquo;";
$lang['paging_previous'] = "&laquo;";
$lang['paging_lastpage'] = "�ltima p�gina &raquo;";
$lang['paging_firstpage'] = "&laquo; Primera p�gina";

//-----------------------------------------------------
//--- Search ------------------------------------------
//-----------------------------------------------------
$lang['search_no_results'] = "Su b�squeda no ha devuelto ning�n resultado.";
$lang['search_by_keyword'] = "B�squeda de Palabra Clave:<br /><span class=\"smalltext\">Puede usar AND para definir palabras que deben estar en los resultados, OR para definir palabras alternativas que pueden aparecer en los resultados y NOT para definir palabras que no quiere ver en los resultados. Utilice * como comod�n para comparaciones parciales.</span>";
$lang['search_by_username'] = "B�squeda por nombre del usuario:<br /><span class=\"smalltext\">Utilice * como comod�n para comparaciones parciales.</span>";
$lang['search_terms'] = "Criterio de b�squeda:";
$lang['search_fields'] = "Buscar los siguientes campos:";
$lang['new_images_only'] = "Solo im�genes nuevas";
$lang['all_fields'] = "Todos los campos";
$lang['name_only'] = "Solo nombres de im�genes";
$lang['description_only'] = "Solo descripciones";
$lang['keywords_only'] = "Solo palabras clave";
$lang['and'] = "Y";
$lang['or'] = "O";

//-----------------------------------------------------
//--- New Images --------------------------------------
//-----------------------------------------------------
$lang['no_new_images'] = "Actualmente no hay ninguna imagen nueva.";

//-----------------------------------------------------
//--- Admin Links -------------------------------------
//-----------------------------------------------------
$lang['edit'] = "[Editar]";
$lang['delete'] = "[Eliminar]";
?>
