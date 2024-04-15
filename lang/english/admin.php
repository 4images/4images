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

/**************************************************************************
 *                                                                        *
 *    English translation by Thomas (http://www.japanreference.com)       *
 *                                                                        *
 *************************************************************************/

$lang['user_integration_delete_msg'] = "You are not using the 4images User-Database. User not deleted.";

//-----------------------------------------------------
//--- Main --------------------------------------------
//-----------------------------------------------------
$lang['no_admin'] = "You are either no administrator or not logged in.";
$lang['admin_login_redirect'] = "Logged in. You will be redirected...";
$lang['admin_no_lang'] = "No language set found. Please upload the <b>\"english\"</b> language set into your <b>\"lang\"</b> directory.";
$lang['admin_login'] = "Log in";
$lang['goto_homepage'] = "Go to the Gallery Home Page";
$lang['online_users'] = "{num_total} user(s) online ({num_registered} registered user(s) and {num_guests} guests).";
$lang['homestats_total'] = "Total:";
$lang['top_cat_hits'] = "Top 5 categories by hits";
$lang['top_image_hits'] = "Top 5 images by hits";
$lang['top_image_downloads'] = "Top 5 images by downloads";
$lang['top_image_rating'] = "Top 5 images by rating";
$lang['top_image_votes'] = "Top 5 images by votes";
$lang['yes'] = "Yes";
$lang['no'] = "No";
$lang['search'] = "Search";
$lang['search_next_page'] = "Next page";
$lang['save_changes'] = "Save changes";
$lang['reset'] = "Reset";
$lang['add'] = "Add";
$lang['edit'] = "Edit";
$lang['delete'] = "Delete";
$lang['options'] = "Options";
$lang['back_overview'] = "Back to overview";
$lang['back'] = "Back";
$lang['sort_options'] = "Options";
$lang['order_by'] = "Order by";
$lang['results_per_page'] = "Results per page";
$lang['asc'] = "Ascending";
$lang['desc'] = "Descending";
$lang['found'] = "Found: ";
$lang['showing'] = "Displayed: ";
$lang['date_format'] = "<br /><span class=\"smalltext\">(Format of date: yyyy-mm-dd hh:mm:ss)</span>";
$lang['date_desc'] = "<br /><span class=\"smalltext\">Leave field blank in order to use default date.</span>";

$lang['userlevel_admin'] = "Administrators";
$lang['userlevel_registered'] = "Registered Users";
$lang['userlevel_registered_awaiting'] = "Registered Users (not activated)";

$lang['headline_whosonline'] = "Who's online?";
$lang['headline_stats'] = "Stats";

$lang['images'] = "Images";
$lang['users'] = "Users";
$lang['database'] = "Database";
$lang['media_directory'] = "Media Directory";
$lang['thumb_directory'] = "Thumbnail Directory";
$lang['validate'] = "Validate";
$lang['ignore'] = "Ignore";
$lang['images_awaiting_validation'] = "<b>{num_images}</b> images awaiting validation";

$lang['permissions'] = "Permissions";
$lang['all'] = "All";
$lang['private'] = "Private";
$lang['all_categories'] = "All Categories";
$lang['no_category'] = "No Category";

$lang['reset_stats_desc'] = "If you want to reset stats to a specific value, enter a number. Leave field empty if you do not want to modify the stats.";

//-----------------------------------------------------
//--- Email -------------------------------------------
//-----------------------------------------------------
$lang['send_emails'] = "Send email to users";
$lang['send_emails_subject'] = "Subject";
$lang['send_emails_message'] = "Message";
$lang['select_email_user'] = "Select User";
$lang['send_emails_success'] = "Emails sent";
$lang['send_emails_error'] = "Error sending email!";

//-----------------------------------------------------
//--- Error Messages ----------------------------------
//-----------------------------------------------------
$lang['error'] = "ERROR:";
$lang['error_log_desc'] = "The following errors occurred:";
$lang['lostfield_error'] = "Please recheck the marked fields.";
$lang['parent_cat_error'] = "You cannot assign a category as subcategory!";
$lang['invalid_email_error'] = "Please recheck the email format!";
$lang['no_search_results'] = "No entries found.";

//-----------------------------------------------------
//--- Fields ------------------------------------------
//-----------------------------------------------------
$lang['field_image_name'] = "Image name";
$lang['field_category_name'] = "Category name";
$lang['field_username'] = "Username";
$lang['field_password'] = "Password";
$lang['field_userlevel'] = "User level";
$lang['field_password'] = "Password";
$lang['field_password_ext'] = "Password:<br /><span class=\"smalltext\">Leave password field blank unless you want to change it.</span>";
$lang['field_headline'] = "Headline";
$lang['field_email'] = "Email";
$lang['field_homepage'] = "Homepage";
$lang['field_icq'] = "ICQ";
$lang['field_showemail'] = "Show Email";
$lang['field_allowemails'] = "Receive emails from administrators:";
$lang['field_invisible'] = "Invisible";
$lang['field_date'] = "Date";
$lang['field_joindate'] = "Date of registration";
$lang['field_lastaction'] = "Last activity";
$lang['field_ip'] = "IP";
$lang['field_comment'] = "Comment";
$lang['field_description'] = "Description";
$lang['field_description_ext'] = "Description<br /><span class=\"smalltext\">HTML allowed.</span>";
$lang['field_parent'] = "Subcategory under";
$lang['field_hits'] = "Hits";
$lang['field_downloads'] = "Downloads";
$lang['field_votes'] = "Votes";
$lang['field_rating'] = "Rating";
$lang['field_category'] = "Category";
$lang['field_keywords'] = "Keywords";
$lang['field_keywords_ext'] = "Keywords<br /><span class=\"smalltext\">Keywords must be separated by commas.</span>";
$lang['field_free'] = "Activate";
$lang['field_allow_comments'] = "Allow comments";
$lang['field_image_file'] = "Image name";
$lang['field_thumb_file'] = "Thumbnail file";
$lang['field_download_url'] = "Download URL";
$lang['field_usergroup_name'] = "Name of User Group";

//-----------------------------------------------------
//--- Searchform Fields -------------------------------
//-----------------------------------------------------
$lang['field_image_id_contains'] = "Image ID contains";
$lang['field_image_name_contains'] = "Image name contains";
$lang['field_description_contains'] = "Description contains";
$lang['field_keywords_contains'] = "Keywords contain";
$lang['field_username_contains'] = "Username contains";
$lang['field_email_contains'] = "Email contains";
$lang['field_headline_contains'] = "Headline contains";
$lang['field_comment_contains'] = "Comment contains";
$lang['field_date_before'] = "Date before";
$lang['field_date_after'] = "Date after";
$lang['field_joindate_before'] = "Registered before";
$lang['field_joindate_after'] = "Registered after";
$lang['field_lastaction_before'] = "Last activity before";
$lang['field_lastaction_after'] = "Last activity after";
$lang['field_image_file_contains'] = "Image file contains";
$lang['field_thumb_file_contains'] = "Thumbnail file contains";
$lang['field_downloads_upper'] = "Downloads higher than";
$lang['field_downloads_lower'] = "Downloads lower than";
$lang['field_rating_upper'] = "Rating higher than";
$lang['field_rating_lower'] = "Rating lower than";
$lang['field_votes_upper'] = "Votes higher than";
$lang['field_votes_lower'] = "Votes lower than";
$lang['field_hits_upper'] = "Hits higher than";
$lang['field_hits_lower'] = "Hits lower than";

//-----------------------------------------------------
//--- Navigation --------------------------------------
//-----------------------------------------------------
$lang['nav_categories_main'] = "Categories";
$lang['nav_categories_edit'] = "Edit categories";
$lang['nav_categories_add'] = "Add categories";

$lang['nav_images_main'] = "Images";
$lang['nav_images_edit'] = "Edit images";
$lang['nav_images_add'] = "Add images";
$lang['nav_images_validate'] = "Validate images";
$lang['nav_images_check'] = "Check new images";
$lang['nav_images_thumbnailer'] = "Auto-Thumbnailer";
$lang['nav_images_resizer'] = "Auto-Image-Resizer";

$lang['nav_comments_main'] = "Comments";
$lang['nav_comments_edit'] = "Edit comments";

$lang['nav_users_main'] = "Users";
$lang['nav_users_edit'] = "Edit users";
$lang['nav_users_add'] = "Add user";
$lang['nav_usergroups'] = "User Groups";
$lang['nav_users_email'] = "Send email";

$lang['nav_general_main'] = "General";
$lang['nav_general_settings'] = "Settings";
$lang['nav_general_templates'] = "Edit Templates";
$lang['nav_general_backup'] = "Backup database";
$lang['nav_general_stats'] = "Reset Stats";

//-----------------------------------------------------
//--- Categories --------------------------------------
//-----------------------------------------------------
$lang['category'] = "Category";
$lang['main_category'] = "Main category";
$lang['sub_categories'] = "Subcategories";
$lang['no_categories'] = "No categories added";
$lang['select_category'] = "Select category";
$lang['add_subcategory'] = "Add subcategory";
$lang['no_subcategories'] = "No subcategories added";
$lang['delete_cat_confirm'] = "Do you want to delete this category?<br />All subcategories as well as attached images and comments will be deleted!";
$lang['delete_cat_files_confirm'] = "Delete all image files from the server?";
$lang['cat_add_success'] = "Category added";
$lang['cat_add_error'] = "Error adding category";
$lang['cat_edit_success'] = "Category edited";
$lang['cat_edit_error'] = "Error editing category";
$lang['cat_delete_success'] = "Category deleted";
$lang['cat_delete_error'] = "Error while deleting category";
$lang['permissions_inherited'] = "Using permissions inherited from parent category.";
$lang['cat_order'] = "Category Order";
$lang['at_beginning'] = "At Beginning";
$lang['at_end'] = "At End";
$lang['after'] = "After";

//-----------------------------------------------------
//--- Images ------------------------------------------
//-----------------------------------------------------
$lang['image'] = "Image";
$lang['image_file'] = "Image file";
$lang['thumb'] = "Thumbnail";
$lang['thumb_file'] = "Thumbnail file";
$lang['delete_image_confirm'] = "Do you want to delete this image file? All attached comments will be deleted as well.";
$lang['delete_image_files_confirm'] = "Delete image files from server?";
$lang['file_upload_error'] = "Error uploading image file";
$lang['thumb_upload_error'] = "Error uploading thumbnail file";
$lang['no_image_file'] = "Please select image file";
$lang['invalid_file_type'] = "Invalid file type";
$lang['invalid_image_width'] = "Image width invalid";
$lang['invalid_image_height'] = "Image heigth invalid";
$lang['invalid_file_size'] = "Image size invalid";
$lang['file_already_exists'] = "This image file already exists";
$lang['file_copy_error'] = "Copy error. Please check the directory permissions.";
$lang['file_upload_success'] = "Image file uploaded";
$lang['file_delete_success'] = "Image file deleted";
$lang['file_delete_error'] = "Error deleting image file.";
$lang['error_image_deleted'] = "Image file deleted";
$lang['thumb_upload_success'] = "Thumbnail file uploaded";
$lang['thumb_delete_success'] = "Thumbnail file deleted";
$lang['thumb_delete_error'] = "Error deleting thumbnail file";
$lang['image_add_success'] = "Image added";
$lang['image_add_error'] = "Error adding image";
$lang['image_edit_success'] = "Image edited";
$lang['image_edit_error'] = "Error editing image";
$lang['image_delete_success'] = "Image deleted";
$lang['image_delete_error'] = "Error deleting image";
$lang['allowed_mediatypes_desc'] = "Valid extensions: ";
$lang['no_thumb_found'] = "No thumbnail found";
$lang['no_db_entry'] = "No database entry!";
$lang['check_all'] = "Check all";
$lang['detailed_version'] = "Detailed version";
$lang['num_newimages_desc'] = "Images displayed: ";
$lang['num_addnewimages_desc'] = "New images displayed: ";
$lang['no_newimages'] = "No new images found";
$lang['thumb_newimages_exists'] = "Thumbnail found";
$lang['no_thumb_newimages'] = "No thumbnail found";
$lang['no_thumb_newimages_ext'] = "No new thumbnails found. Default icon will be displayed.";
$lang['no_newimages_added'] = "No new images added!";
$lang['no_image_found'] = "Images marked with <b class=\"marktext\">!</b> denote image file that cannot be found on the server.";
$lang['upload_progress'] = "File upload in progress....";
$lang['upload_progress_desc'] = "This window will close automatically once the upload is completed.";
$lang['upload_note'] = "<b>NOTE:</b> In case the thumbnail file name does not correspond with the image file name it will be adapted to the image file name.";
$lang['checkimages_note'] = "The following images (<b>{num_all_newimages}</b>) have not been entered to the database.";
$lang['download_url_desc'] = "<br /><span class=\"smalltext\">If you fill out this field, the download button will point to the URL you entered,<br> otherwise it will point directly to the image file.</span>";
$lang['images_delete_success'] = "Images deleted";
$lang['images_delete_error'] = "Error while deleting images";

//-----------------------------------------------------
//--- Comments ----------------------------------------
//-----------------------------------------------------
$lang['comment'] = "Comment";
$lang['comments'] = "Comments";
$lang['delete_comment_confirm'] = "Delete this comment?";
$lang['comment_edit_success'] = "Comment edited";
$lang['comment_edit_error'] = "Error editing comment.";
$lang['comment_delete_success'] = "Comment deleted";
$lang['comment_delete_error'] = "Error deleting comment";
$lang['comments_delete_success'] = "Comments deleted";
$lang['comments_delete_error'] = "Error deleting comments";

//-----------------------------------------------------
//--- User --------------------------------------------
//-----------------------------------------------------
$lang['user'] = "User";
$lang['user_delete_confirm'] = "Delete user?";
$lang['user_delete_comments_confirm'] = "Delete all comments by user?";
$lang['user_add_success'] = "User added";
$lang['user_add_error'] = "Error adding user";
$lang['user_edit_success'] = "User edited";
$lang['user_edit_error'] = "Error editing user";
$lang['user_delete_success'] = "User deleted";
$lang['user_delete_error'] = "Error deleting user";
$lang['user_comments_update_success'] = "User comments updated (User ID reset to \"Guest\")";
$lang['user_comments_update_error'] = "Error updating user comments (User ID not reset to \"Guest\")";
$lang['user_name_exists'] = "Username already exists!";
$lang['user_email_exists'] = "User email already exists!";
$lang['num_newusers_desc'] = "How many new users to add: ";
$lang['user_delete_images_confirm'] = "Delete all images added by user?";
$lang['user_images_update_success'] = "User images updated (User ID reset to \"Guest\")";
$lang['user_images_update_error'] = "Error while updating user images (User ID not reset to \"Guest\")";

//-----------------------------------------------------
//--- Usergroups --------------------------------------
//-----------------------------------------------------
$lang['add_usergroup'] = "Add User Group";
$lang['member_of_usergroup'] = "Member of following User Groups";
$lang['usergroup_add_success'] = "User Group added";
$lang['usergroup_add_error'] = "Error while adding User Group";
$lang['usergroup_edit_success'] = "User Group modified";
$lang['usergroup_edit_error'] = "Error while modifying User Group";
$lang['usergroup_delete_success'] = "User Group deleted";
$lang['usergroup_delete_error'] = "Error while deleting User Group";
$lang['delete_group_confirm'] = "Delete User Group?";
$lang['auth_viewcat'] = "View Category";
$lang['auth_viewimage'] = "View Image";
$lang['auth_download'] = "Download";
$lang['auth_upload'] = "Upload";
$lang['auth_directupload'] = "Direct Upload";
$lang['auth_vote'] = "Vote";
$lang['auth_sendpostcard'] = "Send eCard";
$lang['auth_readcomment'] = "Read Comments";
$lang['auth_postcomment'] = "Post Comment";
$lang['permissions_edit_success'] = "Permissions Updated";
$lang['activate_date'] = "Date of Activation";
$lang['expire_date'] = "Date of Expiration";
$lang['expire_date_desc'] = "<br /><span class=\"smalltext\">If the account is supposed to be non-expiring, set to 0.</span>";

//-----------------------------------------------------
//--- Templates ---------------------------------------
//-----------------------------------------------------
$lang['no_template'] = "No template found";
$lang['no_themes'] = "No Template-Pack found";
$lang['edit_template'] = "Edit template";
$lang['edit_templates'] = "Edit templates";
$lang['choose_template'] = "Select template";
$lang['choose_theme'] = "Select Template-Pack";
$lang['load_theme'] = "Load theme";
$lang['template_edit_success'] = "Template edited!";
$lang['template_edit_error'] = "Error saving template! Check your permissions (chmod 666).";

//-----------------------------------------------------
//--- Backup ------------------------------------------
//-----------------------------------------------------
$lang['do_backup'] = "Database Backup";
$lang['do_backup_desc'] = "Backup database.<br /> <span class=\"smalltext\">Select database tables to be updated. Required tables are preselected.";
$lang['list_backups'] = "List backups";
$lang['no_backups'] = "No backups";
$lang['restore_backup'] = "Restore";
$lang['delete_backup'] = "Delete";
$lang['download_backup'] = "Download";
$lang['show_backup'] = "Show";
$lang['make_backup_success'] = "Database backed up.";
$lang['make_backup_error'] = "Error backing up. Check your permissions (chmod 777).";
$lang['backup_delete_confirm'] = "Delete backup?";
$lang['backup_delete_success'] = "Backup deleted";
$lang['backup_delete_error'] = "Error deleting backup";
$lang['backup_restore_confirm'] = "Restore database?";
$lang['backup_restore_success'] = "Database restored";
$lang['backup_restore_error'] = "Errors while backing up:";

//-----------------------------------------------------
//--- Thumbnailer & Resizer ---------------------------
//-----------------------------------------------------
$lang['im_error'] = "ImageMagick error. Wrong path or ImageMagick not installed.";
$lang['gd_error'] = "GD library error.";
$lang['netpbm_error'] = "NetPBM error. Wrong path or NetPBM not installed.";
$lang['no_convert_module'] = "Select module to create thumbnail.";
$lang['check_module_settings'] = "Check module settings.";
$lang['check_thumbnails'] = "Check thumbnails";
$lang['check_thumbnails_desc'] = "Check database for missing thumbnails.";
$lang['create_thumbnails'] = "Create thumbnails";
$lang['creating_thumbnail'] = "Create thumbnail for: ";
$lang['creating_thumbnail_success'] = "Thumbnail created!";
$lang['creating_thumbnail_error'] = "Error creating thumbnail!";
$lang['convert_thumbnail_dimension'] = "Thumbnail size in pixel";
// <br /><span class=\"smalltext\">proportions will be constrained</span>
$lang['convert_thumbnail_quality'] = "Thumbnail quality<br /><span class=\"smalltext\">0 to 100</span>";
$lang['convert_options'] = "Conversion";
$lang['resize_images'] = "Resize images";
$lang['resize_image_files'] = "Resize image files";
$lang['resize_thumb_files'] = "Resize thumbnail files";
$lang['resize_org_size'] = "Original size";
$lang['resize_new_size'] = "New image size";
$lang['resize_new_quality'] = "Image quality";
$lang['resize_image_type_desc'] = "Convert image files or thumbnails?";

$lang['resize_dimension_desc'] = "Image file size in pixel";
// <br /><span class=\"smalltext\">If you enter 200 the max. image file length will be set to 200 and constrained in proportions.</span>
$lang['resize_proportions_desc'] = "Proportions";
$lang['resize_proportionally'] = "Resize proportionally";
$lang['resize_fixed_width'] = "Resize with fixed width";
$lang['resize_fixed_height'] = "Resize with fixed height";

$lang['resize_quality_desc'] = "Image quality<br /><span class=\"smalltext\">0 to 100</span>";
$lang['resize_start'] = "Start conversion";
$lang['resize_check'] = "Show image";
$lang['resizing_image'] = "Convert image file: ";
$lang['resizing_image_success'] = "Image file converted!";
$lang['resizing_image_error'] = "Error converting image file!";


//-----------------------------------------------------
//--- Check New Images --------------------------------
//-----------------------------------------------------

$lang['add_as_user'] = 'Add as user';
$lang['cni_max_dim'] = "Max dimensions for images preview<br /><span class=\"smalltext\">When checked \"Detailed\", image will be resized on your screen acording this value.</span>";
$lang['cni_iptc_name'] = "Use name from IPTC value";
$lang['cni_iptc_description'] = "Use description from IPTC value";
$lang['cni_iptc_keywords'] = "Use keywords from IPTC value";
$lang['cni_iptc_date'] = "Use date from IPTC value";
$lang['cni_check_subcat'] = "Check subcategories";

$lang['cni_auto_resizer'] = "Auto resizer";
$lang['cni_save_orig'] = "Save original size images";
$lang['cni_big_folder'] = "Folder name where to save original size images (big)";
$lang['cni_add_ann'] = "Add annotation (watermark)";
$lang['cni_auto_thumbnailer'] = "Auto thumbnailer";
$lang['cni_foundin'] = "Found in";
$lang['cni_root_folder'] = "root folder";
$lang['on'] = "On";
$lang['off'] = "Off";

$lang['cni_file_rename_success'] = "File renamed from <b>{from}</b> to <b>{to}</b>";
$lang['cni_file_rename_error'] = "<u>Error</u> renaming media file from <b>{from}</b> to <b>{to}</b>";

$lang['cni_thumbnail_rename_success'] = "Thumbnail file renamed from <b>{from}</b> to <b>{to}</b>";
$lang['cni_thumbnail_rename_error'] = "<u>Error</u> renaming thumbnail file from <b>{from}</b> to <b>{to}</b>";
$lang['cni_copy_success'] = "Copied file into <b>{name}</b> folder.";
$lang['cni_copy_error'] = "<u>Error</u> copying file into <b>{name}</b> folder.";
$lang['cni_copy_thumb_success'] = "Copied thumbnail into <b>{name}</b> folder.";
$lang['cni_copy_thumb_error'] = "<u>Error</u> copying thumbnail into <b>{name}</b> folder.";

$lang['cni_backup_success'] = "Backup original file into <b>{name}</b> folder.";
$lang['cni_backup_error'] = "<u>Error</u> copying original file into <b>{name}</b> folder.";
$lang['cni_annotation_success'] = "Added annotation in <b>{name}</b> file.";
$lang['cni_annotation_error'] = "<u>Error</u> adding annotation in <b>{name}</b> file.";
$lang['cni_create_folder_success'] = "Created <b>{name}/</b> folder.";
$lang['cni_create_folder_error'] = "<u>Error</u> creating <b>{name}/</b> folder.";
$lang['cni_resized_success'] = "Image resized.";
$lang['cni_resized_error'] = "<u>Error</u> resizing image.";
$lang['cni_thumbnail_success'] = "Thumbnail created.";
$lang['cni_thumbnail_error'] = "<u>Error</u> creating thumbnail.";
$lang['cni_error'] = "<u>Error</u> adding image.";
$lang['cni_working'] = "Working on <b>{file}</b> file";

$lang['file_not_found'] = "File not found";

//-----------------------------------------------------
//--- Settings ----------------------------------------
//-----------------------------------------------------
$lang['save_settings_success'] = "Settings saved";

/*-- Setting-Group 1 --*/
$setting_group[1]="General settings";
$setting['site_name'] = "Site name";
$setting['site_email'] = "Admin email";
$setting['use_smtp'] = "Use SMTP server";
$setting['smtp_host'] = "SMTP server host";
$setting['smtp_username'] = "SMTP username";
$setting['smtp_password'] = "SMTP password";
$setting['template_dir'] = "Choose template directory";
$setting['language_dir'] = "Choose language directory";
$setting['date_format'] = "Format of date";
$setting['time_format'] = "Format of time";
$setting['convert_tool'] = "Conversion tool for thumbnails<br /><span class=\"smalltext\">ImageMagick (http://www.imagemagick.org)<br />GD (http://www.boutell.com/gd)<br />NetPBM (http://netpbm.sourceforge.net)</span>";
$convert_tool_optionlist = array(
  "none"   => "None",
  "im"     => "ImageMagick",
  "gd"     => "GD Library",
  "netpbm" => "NetPBM"
);
$setting['convert_tool_path'] = "If you have selected \"ImageMagick\" or \"NetPBM\", enter path and program name";
$setting['gz_compress'] = "Use GZip compression<br /><span class=\"smalltext\">\"Zlib\" needs to be installed on your server</span>";
$setting['gz_compress_level'] = "GZip level of compression<br /><span class=\"smalltext\">0-9, 0=none, 9=max</span>";

/*-- Setting-Group 2 --*/
$setting_group[2]="Category settings";
$setting['cat_order'] = "Sort categories by";
$cat_order_optionlist = array(
    'cat_order'   => 'Default',
    'cat_name'    => 'Name',
    'cat_id'      => 'Date',
);
$setting['cat_sort'] = "Ascending/Descending";
$cat_sort_optionlist = array(
    "ASC"  => "Ascending",
    "DESC" => "Descending"
);
$setting['cat_cells'] = "Number of table cells";
$setting['cat_table_width'] = "Table width<br /><span class=\"smalltext\">absolute width or percentage</span>";
$setting['cat_table_cellspacing'] = "Cellspacing";
$setting['cat_table_cellpadding'] = "Cellpadding";
$setting['num_subcats'] = "Number of subcategories";

/*-- Setting-Group 3 --*/
$setting_group[3]="Image settings";
$setting['image_order'] = "Sort images by";
$image_order_optionlist = array(
  "image_name"      => "Name",
  "image_date"      => "Date",
  "image_downloads" => "Downloads",
  "image_votes"     => "Votes",
  "image_rating"    => "Rating",
  "image_hits"      => "Hits"
);
$setting['image_sort'] = "Ascending/Descending";
$image_sort_optionlist = array(
  "ASC"  => "Ascending",
  "DESC" => "Descending"
);
$setting['new_cutoff'] = "Number of days each image is marked as new";
$setting['image_border'] = "Border of thumbnails";
$setting['image_cells'] = "Image table cells";
$setting['default_image_rows'] = "Image table rows";
$setting['custom_row_steps'] = "Number of dropdown options (allowing users to choose the number of images per page)";
$setting['image_table_width'] = "Table width<br /><span class=\"smalltext\">absolute width or percentage</span>";
$setting['image_table_cellspacing'] = "Cellspacing";
$setting['image_table_cellpadding'] = "Cellpadding";

/*-- Setting-Group 4 --*/
$setting_group[4]="Upload settings";
$setting['upload_mode'] = "Upload mode";
$upload_mode_optionlist = array(
  "1" => "Replace files",
  "2" => "Save files with new name",
  "3" => "No file upload"
);
$setting['allowed_mediatypes'] = "Valid file extensions<br /><span class=\"smalltext\">Delimit with comma, not with spaces. When adding new file types, create a new template in the template directory.</span>";
$setting['max_thumb_width'] = "Max. width of thumbnail in pixel";
$setting['max_thumb_height'] = "Max. heigth of thumbnail in pixel";
$setting['max_thumb_size'] = "Max. thumbnail size in KB";
$setting['max_image_width'] = "Max. image width in pixel";
$setting['max_image_height'] = "Max. image heigth in pixel";
$setting['max_media_size'] = "Max. image size in KB";
$setting['upload_notify'] = "Notify by email upon user upload";
$setting['upload_emails'] = "Additional email addresses for notification<br /><span class=\"smalltext\">Delimit emails by comma.</span>";
$setting['auto_thumbnail'] = "Auto-create thumbnail";
$setting['auto_thumbnail_dimension'] = "Thumbnail size in pixel";
$setting['auto_thumbnail_resize_type'] = "Proportions";
$auto_thumbnail_resize_type_optionlist = array(
  "1" => "Resize proportionally",
  "2" => "Resize with fixed width",
  "3" => "Resize with fixed height"
);
$setting['auto_thumbnail_quality'] = "Thumbnail quality<br /><span class=\"smalltext\">0 to 100</span>";

/*-- Setting-Group 5 --*/
$setting_group[5]="Comment settings";
$setting['badword_list'] = "Badword list<br /><span class=\"smalltext\">Enter banned words delimited by spaces (no commata). If you enter the term \"test\", all words that contain \"test\" will be censored. \"Attest\" will be displayed as \"At****\". If you would like to ban specific terms surround them with curly brackets: {test}. The word \"test\" will be censored, \"attest\" will be displayed.</span>";
$setting['badword_replace_char'] = "Characters to replace badwords";
$setting['wordwrap_comments'] = "Word wrap<br /><span class=\"smalltext\">in order to prevent horizontal scrolling, set a max. number of chars per line. 0 disables word wrapping.</span>";
$setting['html_comments'] = "Allow HTML in comments";
$setting['bb_comments'] = "Allow BB-Code in comments";
$setting['bb_img_comments'] = "Allow images (BB-Code) in comments<br /><span class=\"smalltext\">If you select \"No\", any image will be displayed as hyperlink.</span>";

/*-- Setting-Group 6 --*/
$setting_group[6]="Page and Nav settings";
$setting['category_separator'] = "Category delimiter (in category paths)";
$setting['paging_range'] = "Number of \"previous\" and \"next\" pages displayed in site navigation";

/*-- Setting-Group 7 --*/
$setting_group[7]="Session and User Settings";
$setting['user_edit_image'] = "Allow users to edit their own images";
$setting['user_delete_image'] = "Allow users to delete their own images";
$setting['user_edit_comments'] = "Allow users to edit comments of their own images";
$setting['user_delete_comments'] = "Allow users to delete comments of their own images";
$setting['account_activation'] = "Account activation";
$account_activation_optionlist = array(
  "0" => "None",
  "1" => "by Email",
  "2" => "by Admin"
);
$setting['activation_time'] = "Period of user account activation in days.<br /><span class=\"smalltext\">0  disables function, user accounts will not be deleted.</span>";
$setting['session_timeout'] = "Session timeout in minutes";
$setting['display_whosonline'] = "Display \"Who's Online\". Only visible for admins when deactivated.";
$setting['highlight_admin'] = "Display admins bold in \"Who's online\" ";
?>