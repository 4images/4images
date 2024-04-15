# --------------------------------------------------------
# 4images MySQL-Dump - DON'T EDIT THIS FILE!!!!!!!!!!!!!!!
# --------------------------------------------------------

#
# Table structure for table 4images_categories
#

#DROP TABLE IF EXISTS 4images_categories;
CREATE TABLE 4images_categories (
  cat_id int(10) unsigned NOT NULL auto_increment,
  cat_name varchar(255) NOT NULL default '',
  cat_description text NOT NULL,
  cat_parent_id int(10) unsigned NOT NULL default '0',
  cat_hits int(10) unsigned NOT NULL default '0',
  cat_order int(10) unsigned NOT NULL default '0',
  auth_viewcat tinyint(2) NOT NULL default '0',
  auth_viewimage tinyint(2) NOT NULL default '0',
  auth_download tinyint(2) NOT NULL default '0',
  auth_upload tinyint(2) NOT NULL default '0',
  auth_directupload tinyint(2) NOT NULL default '0',
  auth_vote tinyint(2) NOT NULL default '0',
  auth_sendpostcard tinyint(2) NOT NULL default '0',
  auth_readcomment tinyint(2) NOT NULL default '0',
  auth_postcomment tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (cat_id),
  KEY cat_parent_id (cat_parent_id),
  KEY cat_order (cat_order)
) ENGINE=MyISAM;

#
# Table structure for table 4images_comments
#

#DROP TABLE IF EXISTS 4images_comments;
CREATE TABLE 4images_comments (
  comment_id int(10) unsigned NOT NULL auto_increment,
  image_id int(10) unsigned NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  user_name varchar(100) NOT NULL default '',
  comment_headline varchar(255) NOT NULL default '',
  comment_text text NOT NULL,
  comment_ip varchar(20) NOT NULL default '',
  comment_date int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (comment_id),
  KEY image_id (image_id),
  KEY user_id (user_id),
  KEY comment_date (comment_date)
) ENGINE=MyISAM;

#
# Table structure for table 4images_groupaccess
#

#DROP TABLE IF EXISTS 4images_groupaccess;
CREATE TABLE 4images_groupaccess (
  group_id int(10) unsigned NOT NULL default '0',
  cat_id int(10) unsigned NOT NULL default '0',
  auth_viewcat tinyint(1) NOT NULL default '0',
  auth_viewimage tinyint(1) NOT NULL default '0',
  auth_download tinyint(1) NOT NULL default '0',
  auth_upload tinyint(1) NOT NULL default '0',
  auth_directupload tinyint(1) NOT NULL default '0',
  auth_vote tinyint(1) NOT NULL default '0',
  auth_sendpostcard tinyint(1) NOT NULL default '0',
  auth_readcomment tinyint(1) NOT NULL default '0',
  auth_postcomment tinyint(1) NOT NULL default '0',
  KEY group_id (group_id),
  KEY cat_id (cat_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_groupmatch
#

#DROP TABLE IF EXISTS 4images_groupmatch;
CREATE TABLE 4images_groupmatch (
  group_id int(10) unsigned NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  groupmatch_startdate int(11) unsigned NOT NULL default '0',
  groupmatch_enddate int(11) unsigned NOT NULL default '0',
  KEY group_id (group_id),
  KEY user_id (user_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_groups
#

#DROP TABLE IF EXISTS 4images_groups;
CREATE TABLE 4images_groups (
  group_id int(10) unsigned NOT NULL auto_increment,
  group_name varchar(100) NOT NULL default '',
  group_type tinyint(2) NOT NULL default '1',
  PRIMARY KEY  (group_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_images
#

#DROP TABLE IF EXISTS 4images_images;
CREATE TABLE 4images_images (
  image_id int(10) unsigned NOT NULL auto_increment,
  cat_id int(10) unsigned NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  image_name varchar(255) NOT NULL default '',
  image_description text NOT NULL,
  image_keywords text NOT NULL,
  image_date int(11) unsigned NOT NULL default '0',
  image_active tinyint(1) NOT NULL default '1',
  image_media_file varchar(255) NOT NULL default '',
  image_thumb_file varchar(255) NOT NULL default '',
  image_download_url varchar(255) NOT NULL default '',
  image_allow_comments tinyint(1) NOT NULL default '1',
  image_comments int(10) unsigned NOT NULL default '0',
  image_downloads int(10) unsigned NOT NULL default '0',
  image_votes int(10) unsigned NOT NULL default '0',
  image_rating decimal(4,2) NOT NULL default '0.00',
  image_hits int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (image_id),
  KEY cat_id (cat_id),
  KEY user_id (user_id),
  KEY image_date (image_date),
  KEY image_active (image_active)
) ENGINE=MyISAM;

#
# Table structure for table 4images_images_temp
#

#DROP TABLE IF EXISTS 4images_images_temp;
CREATE TABLE 4images_images_temp (
  image_id int(10) unsigned NOT NULL auto_increment,
  cat_id int(10) unsigned NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  image_name varchar(255) NOT NULL default '',
  image_description text NOT NULL,
  image_keywords text NOT NULL,
  image_date int(11) unsigned NOT NULL default '0',
  image_media_file varchar(255) NOT NULL default '',
  image_thumb_file varchar(255) NOT NULL default '',
  image_download_url varchar(255) NOT NULL default '',
  PRIMARY KEY  (image_id),
  KEY cat_id (cat_id),
  KEY user_id (user_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_lightboxes
#

#DROP TABLE IF EXISTS 4images_lightboxes;
CREATE TABLE 4images_lightboxes (
  lightbox_id varchar(32) NOT NULL default '',
  user_id int(11) NOT NULL default '0',
  lightbox_lastaction int(11) unsigned NOT NULL default '0',
  lightbox_image_ids text,
  KEY lightbox_id (lightbox_id),
  KEY user_id (user_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_postcards
#

#DROP TABLE IF EXISTS 4images_postcards;
CREATE TABLE 4images_postcards (
  postcard_id varchar(32) NOT NULL default '',
  image_id int(10) unsigned NOT NULL default '0',
  postcard_date int(11) unsigned NOT NULL default '0',
  postcard_bg_color varchar(100) NOT NULL default '',
  postcard_border_color varchar(100) NOT NULL default '',
  postcard_font_color varchar(100) NOT NULL default '',
  postcard_font_face varchar(100) NOT NULL default '',
  postcard_sender_name varchar(255) NOT NULL default '',
  postcard_sender_email varchar(255) NOT NULL default '',
  postcard_recipient_name varchar(255) NOT NULL default '',
  postcard_recipient_email varchar(255) NOT NULL default '',
  postcard_headline varchar(255) NOT NULL default '',
  postcard_message text NOT NULL,
  PRIMARY KEY  (postcard_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_sessions
#

#DROP TABLE IF EXISTS 4images_sessions;
CREATE TABLE 4images_sessions (
  session_id varchar(32) NOT NULL default '',
  session_user_id int(11) NOT NULL default '0',
  session_lastaction int(11) unsigned NOT NULL default '0',
  session_location varchar(255) NOT NULL default '',
  session_ip varchar(15) NOT NULL default '',
  PRIMARY KEY  (session_id),
  KEY session_user_id (session_user_id),
  KEY session_id_ip_user_id (session_id,session_ip,session_user_id)
) ENGINE=HEAP;

#
# Table structure for table `4images_sessionvars`
#

DROP TABLE IF EXISTS 4images_sessionvars;
CREATE TABLE 4images_sessionvars (
  session_id varchar(32) NOT NULL default '',
  sessionvars_name varchar(30) NOT NULL default '',
  sessionvars_value text,
  KEY session_id (session_id)
) ENGINE=MyISAM;

#
# Table structure for table 4images_settings
#

#DROP TABLE IF EXISTS 4images_settings;
CREATE TABLE 4images_settings (
  setting_name varchar(255) NOT NULL default '',
  setting_value mediumtext NOT NULL,
  PRIMARY KEY  (setting_name)
) ENGINE=MyISAM;

#
# Dumping data for table 4images_settings
#

INSERT INTO 4images_settings VALUES ('site_name', '4images - Image Gallery Management System');
INSERT INTO 4images_settings VALUES ('site_email', 'admin@yourdomain.com');
INSERT INTO 4images_settings VALUES ('use_smtp', '0');
INSERT INTO 4images_settings VALUES ('smtp_host', '');
INSERT INTO 4images_settings VALUES ('smtp_username', '');
INSERT INTO 4images_settings VALUES ('smtp_password', '');
INSERT INTO 4images_settings VALUES ('template_dir', 'default_960px');
INSERT INTO 4images_settings VALUES ('language_dir', 'deutsch');
INSERT INTO 4images_settings VALUES ('date_format', 'd.m.Y');
INSERT INTO 4images_settings VALUES ('time_format', 'H:i');
INSERT INTO 4images_settings VALUES ('convert_tool', 'gd');
INSERT INTO 4images_settings VALUES ('convert_tool_path', '');
INSERT INTO 4images_settings VALUES ('gz_compress', '0');
INSERT INTO 4images_settings VALUES ('gz_compress_level', '6');
INSERT INTO 4images_settings VALUES ('cat_cells', '2');
INSERT INTO 4images_settings VALUES ('cat_table_width', '100%');
INSERT INTO 4images_settings VALUES ('cat_table_cellspacing', '1');
INSERT INTO 4images_settings VALUES ('cat_table_cellpadding', '3');
INSERT INTO 4images_settings VALUES ('num_subcats', '3');
INSERT INTO 4images_settings VALUES ('image_order', 'image_name');
INSERT INTO 4images_settings VALUES ('image_sort', 'ASC');
INSERT INTO 4images_settings VALUES ('new_cutoff', '10');
INSERT INTO 4images_settings VALUES ('image_border', '1');
INSERT INTO 4images_settings VALUES ('image_cells', '3');
INSERT INTO 4images_settings VALUES ('default_image_rows', '3');
INSERT INTO 4images_settings VALUES ('custom_row_steps', '10');
INSERT INTO 4images_settings VALUES ('image_table_width', '100%');
INSERT INTO 4images_settings VALUES ('image_table_cellspacing', '1');
INSERT INTO 4images_settings VALUES ('image_table_cellpadding', '3');
INSERT INTO 4images_settings VALUES ('upload_mode', '2');
INSERT INTO 4images_settings VALUES ('allowed_mediatypes', 'jpg,gif,png,aif,au,avi,mid,mov,mp3,mpg,swf,wav,ra,rm,zip,pdf');
INSERT INTO 4images_settings VALUES ('max_thumb_width', '300');
INSERT INTO 4images_settings VALUES ('max_thumb_height', '300');
INSERT INTO 4images_settings VALUES ('max_thumb_size', '100');
INSERT INTO 4images_settings VALUES ('max_image_width', '1024');
INSERT INTO 4images_settings VALUES ('max_image_height', '1024');
INSERT INTO 4images_settings VALUES ('max_media_size', '2000');
INSERT INTO 4images_settings VALUES ('upload_notify', '0');
INSERT INTO 4images_settings VALUES ('upload_emails', '');
INSERT INTO 4images_settings VALUES ('auto_thumbnail', '1');
INSERT INTO 4images_settings VALUES ('auto_thumbnail_dimension', '100');
INSERT INTO 4images_settings VALUES ('auto_thumbnail_resize_type', '1');
INSERT INTO 4images_settings VALUES ('auto_thumbnail_quality', '75');
INSERT INTO 4images_settings VALUES ('badword_list', 'fuck {fuck}');
INSERT INTO 4images_settings VALUES ('badword_replace_char', '*');
INSERT INTO 4images_settings VALUES ('wordwrap_comments', '50');
INSERT INTO 4images_settings VALUES ('html_comments', '0');
INSERT INTO 4images_settings VALUES ('bb_comments', '1');
INSERT INTO 4images_settings VALUES ('bb_img_comments', '0');
INSERT INTO 4images_settings VALUES ('category_separator', '&nbsp;/&nbsp;');
INSERT INTO 4images_settings VALUES ('paging_range', '5');
INSERT INTO 4images_settings VALUES ('user_edit_image', '1');
INSERT INTO 4images_settings VALUES ('user_delete_image', '1');
INSERT INTO 4images_settings VALUES ('user_edit_comments', '1');
INSERT INTO 4images_settings VALUES ('user_delete_comments', '1');
INSERT INTO 4images_settings VALUES ('account_activation', '1');
INSERT INTO 4images_settings VALUES ('activation_time', '14');
INSERT INTO 4images_settings VALUES ('session_timeout', '15');
INSERT INTO 4images_settings VALUES ('display_whosonline', '1');
INSERT INTO 4images_settings VALUES ('highlight_admin', '1');

#
# Table structure for table 4images_users
#

#DROP TABLE IF EXISTS 4images_users;
CREATE TABLE 4images_users (
  user_id int(11) NOT NULL auto_increment,
  user_level int(11) NOT NULL default '1',
  user_name varchar(255) NOT NULL default '',
  user_password varchar(255) NOT NULL default '',
  user_email varchar(255) NOT NULL default '',
  user_showemail tinyint(1) NOT NULL default '0',
  user_allowemails tinyint(1) NOT NULL default '1',
  user_invisible tinyint(1) NOT NULL default '0',
  user_joindate int(11) unsigned NOT NULL default '0',
  user_activationkey varchar(32) NOT NULL default '',
  user_lastaction int(11) unsigned NOT NULL default '0',
  user_location varchar(255) NOT NULL default '',
  user_lastvisit int(11) unsigned NOT NULL default '0',
  user_comments int(10) unsigned NOT NULL default '0',
  user_homepage varchar(255) NOT NULL default '',
  user_icq varchar(20) NOT NULL default '',
  PRIMARY KEY  (user_id),
  KEY user_lastaction (user_lastaction),
  KEY user_name (user_name)
) ENGINE=MyISAM;

#
# Dumping data for table 4images_users
#

INSERT INTO 4images_users VALUES (-1, -1, 'Guest', '0493984f537120be0b8d96bc9b69cdd2', '', 0, 0, 0, 0, '', 0, '', 0, 0, '', '');
INSERT INTO 4images_users VALUES (1, 9, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@yourdomain.com', 1, 1, 0, 1016023608, '1e3457c0b2052a9633b886fd75ef91e0', 1016023608, '', 0, 0, '', '');


#
# Table structure for table 4images_wordlist
#

#DROP TABLE IF EXISTS 4images_wordlist;
CREATE TABLE 4images_wordlist (
  word_text varchar(50) NOT NULL default '',
  word_id int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (word_id),
  UNIQUE KEY word_text (word_text)
) ENGINE=MyISAM;

#
# Table structure for table 4images_wordmatch
#

#DROP TABLE IF EXISTS 4images_wordmatch;
CREATE TABLE 4images_wordmatch (
  image_id int(10) unsigned NOT NULL default '0',
  word_id int(10) unsigned NOT NULL default '0',
  name_match tinyint(1) NOT NULL default '0',
  desc_match tinyint(1) NOT NULL default '0',
  keys_match tinyint(1) NOT NULL default '0',
  UNIQUE KEY image_word_id (image_id,word_id)
) ENGINE=MyISAM;
