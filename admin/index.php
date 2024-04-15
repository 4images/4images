<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: index.php                                            *
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

define('IN_CP', 1);
define('ROOT_PATH', './../');
require('admin_global.php');

if ($redirect != "") {
    if (strpos($redirect, '://') === false) {
        show_admin_header("<meta http-equiv=\"Refresh\" content=\"0; URL=".$site_sess->url($redirect)."\">");
        echo "<p><a href=\"".$site_sess->url($redirect)."\">".$lang['admin_login_redirect']."</a></p>";
        show_admin_footer();
    } else {
        redirect('home.php');
    }
    exit;
}

if ($action == "") {
    $action = "frames";
}

if ($action == "frames") {
    if ($goto != "" && strpos($goto, '://') === false) {
        $framesrc = $site_sess->url($goto);
    } else {
        $framesrc = $site_sess->url("home.php");
    } ?>
<html dir="<?php echo $lang['direction']; ?>">
  <head>
    <title><?php echo $config['site_name']; ?> Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['charset']; ?>">
  </head>
  <frameset rows="70,*" framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
    <frame src="<?php echo $site_sess->url("index.php?action=head"); ?>" name="head" scrolling="NO" NORESIZE frameborder="0" marginwidth="0" marginheight="0" border="no">
    <frameset cols="216,*"  framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
      <frame src="<?php echo $site_sess->url("index.php?action=nav"); ?>" name="nav" scrolling="auto" NORESIZE frameborder="0" marginwidth="0" marginheight="0" border="no">
      <frame src="<?php echo str_replace("javascript:", "", (strip_tags($framesrc))) ?>" name="main" scrolling="auto" NORESIZE frameborder="0" marginwidth="20" marginheight="20" border="no">
    </frameset>
  </frameset>
</html>
<?php
}

if ($action == "head") {
    ?>
<html dir="<?php echo $lang['direction']; ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['charset']; ?>">
    <link rel="stylesheet" href="./cpstyle.css">
  </head>
  <body leftmargin="0" background="images/bg_header.gif" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#FCDC43">
    <table border="0" cellpadding=0 cellspacing=0 width="100%">
      <tr>
        <td><img src="images/logo.gif"></td>
        <td align="right"><b><a href="<?php echo $site_sess->url(ROOT_PATH."index.php"); ?>" target="_blank"><?php echo $lang['goto_homepage']; ?></a>&nbsp;|&nbsp;<a href="<?php echo $site_sess->url(ROOT_PATH."admin/index.php?logout=1"); ?>" target="_top"><?php echo $lang['logout']; ?></a>&nbsp;&nbsp;</b></TD>
      </tr>
    </table>
    <table border="0" cellpadding=4 cellspacing=0 width="100%">
      <tr>
        <td valign=top>
        <b><a href=<?php echo $site_sess->url("home.php?action=home"); ?> target=main>Control Panel Home</a></b>
        </td>
        <td align="center">
        <script language="JavaScript" type="text/javascript" src="http://www.4homepages.de/version/version.php"></script>
        <script language="JavaScript" type="text/javascript">
        <!--
        if ('<?php echo SCRIPT_VERSION; ?>' != latestversion) {
          document.write(latestversioninfo);
        }
        // -->
        </script>
        </td>
        <td align="right">
        Version: <b><?php echo SCRIPT_VERSION; ?></b>
        </td>
      </tr>
    </table>
  </body>
</html>
<?php
}

if ($action == "nav") {
    ?>
<html dir="<?php echo $lang['direction']; ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['charset']; ?>">
    <link rel="stylesheet" href="./cpstyle.css">
    <base target="main">
  </head>
  <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table width="200" height="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" height="1"><img src="images/spacer.gif" height="1"></td>
      </tr>
      <tr>
        <td bgcolor="#EEEEEE" width="199" height="100%" valign="top">
          <table width="199" border="0" cellpadding="0" cellspacing="0" bgcolor="#EEEEEE">
          <?php
          show_nav_header($lang['nav_categories_main']);
    show_nav_option($lang['nav_categories_edit'], "categories.php?action=modifycats");
    show_nav_option($lang['nav_categories_add'], "categories.php?action=addcat");

    show_nav_header($lang['nav_images_main']);
    show_nav_option($lang['nav_images_edit'], "images.php?action=modifyimages");
    show_nav_option($lang['nav_images_add'], "images.php?action=addimages");
    show_nav_option($lang['nav_images_validate'], "validateimages.php?action=validateimages");
    show_nav_option($lang['nav_images_check'], "checkimages.php?action=checkimages");
    show_nav_option($lang['nav_images_thumbnailer'], "thumbnailer.php?action=checkthumbnails");
    show_nav_option($lang['nav_images_resizer'], "resizer.php?action=selectoptions");

    show_nav_header($lang['nav_comments_main']);
    show_nav_option($lang['nav_comments_edit'], "comments.php?action=modifycomments");

    show_nav_header($lang['nav_users_main']);
    show_nav_option($lang['nav_users_edit'], "users.php?action=modifyusers");
    if (!defined('USER_INTEGRATION')) {
        show_nav_option($lang['nav_users_add'], "users.php?action=addusers");
    }
    show_nav_option($lang['nav_usergroups'], "usergroups.php?action=modifygroups");
    if (!defined('USER_INTEGRATION')) {
        show_nav_option($lang['nav_users_email'], "email.php?action=emailusers");
    }

    show_nav_header($lang['nav_general_main']);
    show_nav_option($lang['nav_general_settings'], "settings.php?action=modifysettings");
    show_nav_option($lang['nav_general_templates'], "templates.php?action=modifytemplates");
    show_nav_option($lang['nav_general_backup'], "backup.php?action=modifybackups");
    show_nav_option($lang['nav_general_stats'], "stats.php?action=resetstats");
    show_nav_option("phpinfo()", "phpinfo.php");

    if (@is_dir("plugins")) {
        show_nav_header("PlugIns");
        $handle = @opendir("plugins/");
        while ($file = @readdir($handle)) {
            if (get_file_extension($file) != "php") {
                continue;
            }
            $plugin_file = file("./plugins/".$file);
            $plugin_file[0] = trim($plugin_file[0]);
            if (preg_match("/PLUGIN_TITLE:(.+)/", $plugin_file[0], $regs)) {
                show_nav_option(trim($regs[1]), "./plugins/".$file);
            } else {
                show_nav_option($file, "./plugins/".$file);
            }
        }
        @closedir($handle);
    } ?>
          </table>
        </td>
        <td bgcolor="#004C75" width="1"><img src="images/spacer.gif"></td>
      </tr>
    </table>
  </body>
</html>
<?php
}
?>
