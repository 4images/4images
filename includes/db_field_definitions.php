<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: db_field_definitions.php                             *
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
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

/* ------------------------------------------------------------------------
If you want to add additional fields in order to store more info on
each image or user, set up these fields by inserting a column to the "4images_images"
or "4images_users" table in your database.

If you add an additional image field and allow upload from the gallery,
add the columns to "4images_images_temp" as well.

Add one line for each new column in the following format:

  $additional_image_fields['%column_name%'] = array("%field_description%", "%admin_field_type%", %is_required%);
or
  $additional_user_fields['%column_name%'] = array("%field_description%", "%admin_field_type%", %is_required%);

At the bottom of this file, you will find examples for adding a new field.

----------
%column_name% string

  Replace %column_name% with name of the table column.
  You can use the tag {%column_name%} in the templates to display the value of the
  database field.
  If you want to add a textfield to the templates, do this such like:

    <input type="text" name="%column_name%" value="{%column_name%}" />

----------
%field_description% string

  Replace %field_description% with a custom name. This name will be displayed in the Control Panel.
  The value can be displayed in the templates with the tag {lang_%column_name%}.
  It is also recommended to add this tag to the language files (main.php) and to replace "%field_description%"
  with $lang['%column_name%'].

----------
%admin_field_type% string

  Replace %admin_field_type% with the type of input field you would like to use in your
  Control Panel.

  You can use the following formats:

   "text"
     will display an input field type="text".

   "textarea"
     will display a textarea.

   "radio"
     will display radio buttons with Yes/No options.
     Please make sure that the database field type is an integer (for example: "tinyint(1)").
     You can use this field tag for conditional statements in your templates:

     {if %column_name%} Some text {endif %column_name%}

----------
%is_required% bool

  Sets up the field as required when adding data through the Control Panel or the user upload form.

------------------------------------------------------------------------ */

// Example for additional image fields:
//$additional_image_fields['image_photographer'] = array($lang['image_photographer'], "text", 1);

// Example for additional user fields
//$additional_user_fields['user_address'] = array($lang['user_address'], "text", 1);
?>
