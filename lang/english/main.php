<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: main.php                                             *
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

$lang['no_settings'] = "ERROR: Could not load configuration settings!";

//-----------------------------------------------------
//--- CAPTCHA -----------------------------------------
//-----------------------------------------------------
$lang['captcha'] = "Verification code:";
$lang['captcha_desc'] = "Please enter the letters or digits that appear in the image. If you have problems identifying the image, click on it to get a new one.";
$lang['captcha_required'] = 'Please enter the verification code.';

//-----------------------------------------------------
//--- Templates ---------------------------------------
//-----------------------------------------------------
$lang['charset'] = "UTF-8";
$lang['direction'] = "ltr";

//-----------------------------------------------------
//--- Userlevel ---------------------------------------
//-----------------------------------------------------
$lang['userlevel_admin'] = "Admin";
$lang['userlevel_user'] = "Member";
$lang['userlevel_guest'] = "Guest";

//-----------------------------------------------------
//--- Categories --------------------------------------
//-----------------------------------------------------
$lang['no_categories'] = "No categories found.";
$lang['no_images'] = "There are no images in this category.";
$lang['select_category'] = "Select category";

//-----------------------------------------------------
//--- Comments ----------------------------------------
//-----------------------------------------------------
$lang['name_required'] = "Please enter a name.";
$lang['headline_required'] = "Please enter a headline.";
$lang['comment_required'] = "Please add a comment.";
$lang['spamming'] = "You cannot repost so soon, please try again after a short while.";
$lang['comments'] = "Comments:";
$lang['no_comments'] = "There are no comments for this image";
$lang['comments_deactivated'] = "Comment deactivated!";
$lang['post_comment'] = "Post comment";
$lang['comment_success'] = "Your comment has been saved";

//-----------------------------------------------------
//--- BBCode ------------------------------------------
//-----------------------------------------------------
$lang['bbcode'] = "BBCode";
$lang['tag_prompt'] = "Enter the text to be formatted:";
$lang['link_text_prompt'] = "Enter the text to be displayed for the link (optional)";
$lang['link_url_prompt'] = "Enter the full URL for the link";
$lang['link_email_prompt'] = "Enter the email address for the link";
$lang['list_type_prompt'] = "What type of list do you want? Enter '1' for a numbered list, enter 'a' for an alphabetical list, or leave blank for a list with bullet points.";
$lang['list_item_prompt'] = "Enter a list item. Leave the box empty or click 'Cancel' to complete the list.";

//-----------------------------------------------------
//--- Image Details -----------------------------------
//-----------------------------------------------------
$lang['download_error'] = "Download error!";
$lang['register_download'] = "Please register to download images.<br />&raquo; <a href=\"{url_register}\">Register now</a>";
$lang['voting_success'] = "Thank you for rating this image";
$lang['voting_error'] = "Rating invalid!";
$lang['already_voted'] = "Sorry, you've already rated for this image once recently.";
$lang['prev_image'] = "Previous image:";
$lang['next_image'] = "Next image:";
$lang['category'] = "Category:";
$lang['description'] = "Description:";
$lang['keywords'] = "Keywords:";
$lang['date'] = "Date:";
$lang['hits'] = "Hits:";
$lang['downloads'] = "Downloads:";
$lang['rating'] = "Rating:";
$lang['votes'] = "Vote(s)";
$lang['file_size'] = "File size:";
$lang['author'] = "Author:";
$lang['name'] = "Name:";
$lang['headline'] = "Headline:";
$lang['comment'] = "Comment:";
$lang['added_by'] = "Added by:";
$lang['allow_comments'] = "Allow comments:";

// IPTC Tags
$lang['iptc_caption'] = "Caption:";
$lang['iptc_caption_writer'] = "Caption writer:";
$lang['iptc_headline'] = "Headline:";
$lang['iptc_special_instructions'] = "Special instructions:";
$lang['iptc_byline'] = "Byline:";
$lang['iptc_byline_title'] = "Byline title:";
$lang['iptc_credit'] = "Credit:";
$lang['iptc_source'] = "Source:";
$lang['iptc_object_name'] = "Object name:";
$lang['iptc_date_created'] = "Date created:";
$lang['iptc_city'] = "City:";
$lang['iptc_state'] = "State:";
$lang['iptc_country'] = "Country:";
$lang['iptc_original_transmission_reference'] = "Original transmission reference:";
$lang['iptc_category'] = "Category:";
$lang['iptc_supplemental_category'] = "Supplemental category:";
$lang['iptc_keyword'] = "Keywords:";
$lang['iptc_copyright_notice'] = "Copyright Notice:";

// EXIF Tags
$lang['exif_make'] = "Make:";
$lang['exif_model'] = "Model:";
$lang['exif_datetime'] = "Date created:";
$lang['exif_isospeed'] = "ISO speed:";
$lang['exif_exposure'] = "Exposure time:";
$lang['exif_aperture'] = "Aperture value:";
$lang['exif_focallen'] = "Focal length:";

//-----------------------------------------------------
//--- Postcards ---------------------------------------
//-----------------------------------------------------
$lang['send_postcard'] = "Send eCard";
$lang['edit_postcard'] = "Modify eCard";
$lang['preview_postcard'] = "eCard preview";
$lang['bg_color'] = "Background Color:";
$lang['border_color'] = "Border Color:";
$lang['font_color'] = "Font Color:";
$lang['font_face'] = "Font Face:";
$lang['recipient'] = "Recipient";
$lang['sender'] = "Sender";
$lang['send_postcard_emailsubject'] = "An eCard for you!";
$lang['send_postcard_success'] = "Thank you! Your eCard has been sent!";
$lang['back_to_gallery'] = "Back to Gallery";
$lang['invalid_postcard_id'] = "Invalid eCard ID.";

//-----------------------------------------------------
//--- Top Images --------------------------------------
//-----------------------------------------------------
$lang['top_image_hits'] = "Top 10 images by hits";
$lang['top_image_downloads'] = "Top 10 images by downloads";
$lang['top_image_rating'] = "Top 10 images by rating";
$lang['top_image_votes'] = "Top 10 images by votes";

//-----------------------------------------------------
//--- Users -------------------------------------------
//-----------------------------------------------------
$lang['send_password_emailsubject'] = "Send password for {site_name}";  // Mail subject for password.
$lang['update_email_emailsubject'] = "Update email for {site_name}";    // Mail subject for activation code when changing email address
$lang['register_success_emailsubject'] = "Register at {site_name}";     // Mail subject for activation code
$lang['admin_activation_emailsubject'] = "Account Activation";          // Mail subject for account activation by admin.
$lang['activation_success_emailsubject'] = "Account activated";         // Mail subject after account activation by admin.

$lang['no_permission'] = "You are not logged in or do not have permissions to enter this site!";
$lang['already_registered'] = "You have already registered. If you forgot your password please click <a href=\"{url_lost_password}\">here</a>.";
$lang['username_exists'] = "User name already exists.";
$lang['email_exists'] = "Email address already exists.";
$lang['invalid_email_format'] = "Please enter a valid email address.";
$lang['register_success'] = "You are now registered. You'll shortly receive an email with your activation code.";
$lang['register_success_admin'] = "You are now registered. Your account is currently inactive, the administrator will need to activate it before you can log in. You will receive a notice once your account has been activated.";
$lang['register_success_none'] = "You are now registered. Please log in.";
$lang['missing_activationkey'] = "Your activation key is missing.";
$lang['invalid_activationkey'] = "Account inactive. Please register once again.</>";
$lang['activation_success'] = "Thanks! Your account has been activated. Please log in.";
$lang['general_error'] = "An error has occurred. Please <a href=javascript:history.go(-1)>return</a> and try again. If the problem persists, contact the administrator.";
$lang['invalid_login'] = "You have specified an invalid username or password.";
$lang['update_email_error'] = "Please enter your email address once again!";
$lang['update_email_confirm_error'] = "The email addresses you entered do not correspond!";
$lang['update_profile_success'] = "Your profile has been updated!";
$lang['update_email_instruction'] = "As you have changed your email address, please reactivate your account. The activation code has been sent to your new email address!";
$lang['update_email_admin_instruction'] = "As you have changed your email address, the administrator will need to reactivate your account. You will receive a notice once your account has been reactivated.";
$lang['invalid_email'] = "Invalid email address.";
$lang['send_password_success'] = "Your password has been sent to your email address.";
$lang['update_password_error'] = "You entered an invalid password.";
$lang['update_password_confirm_error'] = "The two passwords you entered do not correspond!";
$lang['update_password_success'] = "Your password has been changed.";
$lang['invalid_user_id'] = "No user found!";
$lang['emailuser_success'] = "The email has been sent";
$lang['send_email_to'] = "Send an email message to:";
$lang['subject'] = "Subject:";
$lang['message'] = "Message:";
$lang['profile_of'] = "User profile of:";
$lang['edit_profile_msg'] = "Allows you to change your user profile and password.";
$lang['edit_profile_email_msg'] = "<br />Note: If you change your email address you have to reactivate your account. The activation code will be sent to your new email address.";
$lang['edit_profile_email_msg_admin'] = "<br />Note: If you change your email address the administrator will need to reactivate your account.";
$lang['join_date'] = "Join Date:";
$lang['last_action'] = "Last Activity:";
$lang['email'] = "Email:";
$lang['email_confirm'] = "Confirm email:";
$lang['homepage'] = "Homepage:";
$lang['icq'] = "ICQ:";
$lang['show_email'] = "Show my email address:";
$lang['allow_emails'] = "Receive emails from administrators:";
$lang['invisible'] = "Hide your online status:";
$lang['optional_infos'] = "Optional";
$lang['change_password'] = "Change password";
$lang['old_password'] = "Old password:";
$lang['new_password'] = "New password:";
$lang['new_password_confirm'] = "Confirm new password:";
$lang['lost_password'] = "Enter password again";
$lang['lost_password_msg'] = "In case you forgot your password, enter the email address you have used for registration.";
$lang['user_name'] = "Username:";
$lang['password'] = "Password:";

$lang['register_msg'] = "Please fill out all fields. Enter a valid email address so we can provide you with your activation code.";
$lang['agreement'] = "Terms of Registration:";
$lang['agreement_terms'] = "
            While the administrators of this website will attempt to remove or
            to edit objectionable material as fast as possible, it is impossible
            to review each post. Therefore you acknowledge that all messages
            posted on this website solely express their authors views and opinions.
            Therefore, administrators, moderators or webmasters can only be held
            liable for their own posts.
            <br /><br />
            You agree not to post any abusive, obscene, vulgar, slanderous, hateful,
            threatening, sexually-orientated or any other material that may violate
            any applicable laws. You agree that the webmaster and administrator of
            this website have the right to remove or edit any topic at any time they
            see fit.
            As user you agree to any information you have entered above being stored
            in a database. While this information will not be disclosed to any third
            party without your consent the webmaster and administrator cannot be held
            responsible for hacking attempts that may lead to the data being compromised.
            <br /><br />
            This system uses cookies to store information on your local computer. These
            cookies do not contain any personal data, they only serve to provide you
            with information tailored to your individual needs.
            <br /><br />
            By clicking the button below you accept these terms of service.";

$lang['agree'] = "Agree";
$lang['agree_not'] = "Decline";
$lang['show_user_images'] = "Display all images added by {user_name}";

//-----------------------------------------------------
//--- Edit Image --------------------------------------
//-----------------------------------------------------
$lang['image_edit'] = "Edit image";
$lang['image_edit_success'] = "Image edited";
$lang['image_edit_error'] = "Error editing image";
$lang['image_delete'] = "Delete image";
$lang['image_delete_success'] = "Image deleted";
$lang['image_delete_error'] = "Error deleting image";
$lang['image_delete_confirm'] = "Do you want to delete this image file?";

//-----------------------------------------------------
//--- Edit Comments -----------------------------------
//-----------------------------------------------------
$lang['comment_edit'] = "Edit comment";
$lang['comment_edit_success'] = "Comment edited";
$lang['comment_edit_error'] = "Error editing comment.";
$lang['comment_delete'] = "Delete comment";
$lang['comment_delete_success'] = "Comment deleted";
$lang['comment_delete_error'] = "Error deleting comment";
$lang['comment_delete_confirm'] = "Delete this comment?";

//-----------------------------------------------------
//--- Image Upload ------------------------------------
//-----------------------------------------------------
$lang['field_required'] = "Please fill out the {field_name} field!";
$lang['kb'] = "kb";
$lang['px'] = "px";
$lang['file_upload_error'] = "Error uploading image file";
$lang['thumb_upload_error'] = "Error uploading thumbnail file";
$lang['invalid_file_type'] = "Invalid file type";
$lang['invalid_image_width'] = "Image width invalid";
$lang['invalid_image_height'] = "Image heigth invalid";
$lang['invalid_file_size'] = "Image size invalid";
$lang['image_add_success'] = "Image added";
$lang['allowed_mediatypes_desc'] = "Valid extensions: ";
$lang['keywords_ext'] = "Keywords:<br /><span class=\"smalltext\">Keywords must be separated by commas.</span>";
$lang['user_upload'] = "Upload Image";
$lang['image_name'] = "Image Name:";
$lang['media_file'] = "Image File:";
$lang['thumb_file'] = "Thumbnail File:";
$lang['max_filesize'] = "Max. File Size: ";
$lang['max_imagewidth'] = "Max. Image Width: ";
$lang['max_imageheight'] = "Max. Image Height: ";
$lang['image_file_required'] = "Select an Image File!";
$lang['new_upload_emailsubject'] = "New Upload to {site_name}";
$lang['new_upload_validate_desc'] = "Your image will be validated once it has been reviewed.";

//-----------------------------------------------------
//--- Lightbox ----------------------------------------
//-----------------------------------------------------
$lang['lightbox_no_images'] = "No images stored in your lightbox.";
$lang['lightbox_add_success'] = "Image added.";
$lang['lightbox_add_error'] = "Error adding image!";
$lang['lightbox_remove_success'] = "Image deleted from lightbox.";
$lang['lightbox_remove_error'] = "Error deleting image!";
$lang['lightbox_register'] = "In order to use the lightbox, you have to register.<br />&raquo; <a href=\"{url_register}\">Register now</a>";
$lang['lightbox_delete_success'] = "Lightbox deleted.";
$lang['lightbox_delete_error'] = "Error deleting lightbox!";
$lang['delete_lightbox'] = "Delete lightbox";
$lang['lighbox_lastaction'] = "Lightbox last updated:";
$lang['delete_lightbox_confirm'] = "Do you really want to delete the lightbox?";

//-----------------------------------------------------
//--- Misc --------------------------------------------
//-----------------------------------------------------
$lang['new'] = "new"; // Marks categories and images as "NEW"
$lang['home'] = "Home";
$lang['categories'] = "Categories";
$lang['sub_categories'] = "Subcategories";
$lang['lightbox'] = "Lightbox";
$lang['error'] = "Error";
$lang['register'] = "Registration";
$lang['control_panel'] = "Control Panel";
$lang['profile'] = "User profile";
$lang['search'] = "Search";
$lang['advanced_search'] = "Advanced search";
$lang['new_images'] = "New images";
$lang['top_images'] = "Top images";
$lang['registered_user'] = "Registered users";
$lang['logout'] = "Log Out";
$lang['login'] = "Log In";
$lang['lang_auto_login'] = "Log me on automatically next visit?";
$lang['lost_password'] = "Forgot password";
$lang['random_image'] = "Random image";
$lang['site_stats'] = "<b>{total_images}</b> images in <b>{total_categories}</b> categories.";
$lang['lang_loggedin_msg'] = "Logged in as: <b>{loggedin_user_name}</b>";
$lang['go'] = "Go";
$lang['submit'] = "Submit";
$lang['reset'] = "Reset";
$lang['save'] = "Save";
$lang['yes'] = "Yes";
$lang['no'] = "No";
$lang['images_per_page'] = "Images per page:";
$lang['user_online'] = "Currently active users: {num_total_online}";
$lang['user_online_detail'] = "There are currently <b>{num_registered_online}</b> registered user(s) ({num_invisible_online} among them invisible) and <b>{num_guests_online}</b> guest(s) online.";
$lang['lostfield_error'] = "Please fill out all fields!";
$lang['rate'] = "Rate";

//-----------------------------------------------------
//--- Paging ------------------------------------------
//-----------------------------------------------------
$lang['paging_stats'] = "Found: {total_cat_images} image(s) on {total_pages} page(s). Displayed: image {first_page} to {last_page}.";
$lang['paging_next'] = "&raquo;";
$lang['paging_previous'] = "&laquo;";
$lang['paging_lastpage'] = "Last page &raquo;";
$lang['paging_firstpage'] = "&laquo; First page";

//-----------------------------------------------------
//--- Search ------------------------------------------
//-----------------------------------------------------
$lang['search_no_results'] = "Your search resulted in no matching records.";
$lang['search_by_keyword'] = "Search by Keyword:<br /><span class=\"smalltext\">Use terms such as AND, OR and NOT to control your search in more detail. Use asterisks (*) as a wildcard for partial matches.</span>";
$lang['search_by_username'] = "Search by Username:<br /><span class=\"smalltext\">Use asterisks (*) as a wildcard for partial matches.</span>";
$lang['search_terms'] = "Search term:";
$lang['search_fields'] = "Search the following fields:";
$lang['new_images_only'] = "Display new images only";
$lang['all_fields'] = "All fields";
$lang['name_only'] = "Only image name";
$lang['description_only'] = "Only description";
$lang['keywords_only'] = "Only keywords";
$lang['and'] = "AND";
$lang['or'] = "OR";

//-----------------------------------------------------
//--- New Images --------------------------------------
//-----------------------------------------------------
$lang['no_new_images'] = "Currently there are no new images.";

//-----------------------------------------------------
//--- Admin Links -------------------------------------
//-----------------------------------------------------
$lang['edit'] = "[Edit]";
$lang['delete'] = "[Delete]";
?>
