<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: paging.php                                           *
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

class Paging {
  var $page,$offset,$perpage,$num_rows_all,$link_args;
  var $first,$last,$total_pages,$config,$desc,$next,$back;

  function Paging($page = 1, $perpage = 0, $num_rows_all = 0, $link_args = "") {
    global $lang, $config;

    $this->page = intval($page);
    $this->perpage = intval($perpage);
    $this->num_rows_all = intval($num_rows_all);

    if ($this->page <= 0) {
      $this->page = 1;
    }
    if ($this->perpage <= 0) {
      $this->perpage = 1;
    }
    if ($this->num_rows_all <= 0) {
      $this->total_pages = 0;
    }
    elseif ($this->num_rows_all <= $this->perpage) {
      $this->total_pages = 1;
    }
    elseif ($this->num_rows_all % $this->perpage == 0) {
      $this->total_pages = $this->num_rows_all / $this->perpage;
    }
    else {
      $this->total_pages = ceil($this->num_rows_all / $this->perpage);
    }
    if ($this->page > $this->total_pages) {
      $this->page = 1;
    }
    if (!$this->num_rows_all) {
      $this->first = 0;
    }
    else {
      $this->first = $this->perpage * $this->page - $this->perpage + 1;
    }
    if (!$this->num_rows_all) {
      $this->last = 0;
    }
    elseif ($this->page == $this->total_pages) {
      $this->last = $this->num_rows_all;
    }
    else {
      $this->last = $this->perpage * $this->page;
    }

    $this->offset = $this->perpage * $this->page - $this->perpage;

    $link_args = preg_replace("/&page=[0-9]*/", "", $link_args);
    $link_args = preg_replace("/page=[0-9]*&/", "", $link_args);
    $this->link_args = basename($link_args);
    $this->link_args .= preg_match("/\?/",$this->link_args) ? "&amp;" : "?";

    $this->desc = $lang['paging_stats'];
    $this->paging_next = $lang['paging_next'];
    $this->paging_back = $lang['paging_previous'];
    $this->paging_lastpage = $lang['paging_lastpage'];
    $this->paging_firstpage = $lang['paging_firstpage'];
    $this->range = $config['paging_range'];
  }

  function get_paging() {
    $html = "";
    if ($this->total_pages > 1) {
      $page_back = $this->page - 1;
      $page_next = $this->page + 1;

      if ($page_back > 0) {
        $html .= "<a href=\"".$this->link_args."page=1\" class=\"paging\">".$this->paging_firstpage."</a>&nbsp;&nbsp;";
        $html .= "<a href=\"".$this->link_args."page=$page_back\" class=\"paging\">".$this->paging_back."</a>&nbsp;&nbsp;";
      }
      for ($page_num = 1; $page_num <= $this->total_pages; $page_num++) {
        if ($page_num >= ($this->page-$this->range) && $page_num <= ($this->page+$this->range)) {
          if ($this->page == $page_num) {
            $html .= "<b class=\"pagingon\">$page_num</b>&nbsp;&nbsp;";
          }
          else {
            $html .= "<a href=\"".$this->link_args."page=$page_num\" class=\"paging\">$page_num</a>&nbsp;&nbsp;";
          }
        }
      }
      if ($page_next <= $this->total_pages) {
        $html .= "<a href=\"".$this->link_args."page=$page_next\" class=\"paging\">".$this->paging_next."</a>&nbsp;&nbsp;";
        $html .= "<a href=\"".$this->link_args."page=$this->total_pages\" class=\"paging\">".$this->paging_lastpage."</a>";
      }
    }
    return $html;
  }

  function get_offset() {
    return $this->offset;
  }

  function get_paging_stats() {
    global $site_template;
    $search_array = array(
      "/".$site_template->start."total_cat_images".$site_template->end."/iU",
      "/".$site_template->start."total_pages".$site_template->end."/iU",
      "/".$site_template->start."first_page".$site_template->end."/iU",
      "/".$site_template->start."last_page".$site_template->end."/iU"
    );
    $replace_array = array(
      $this->num_rows_all,
      $this->total_pages,
      $this->first,
      $this->last
    );
    $this->desc = preg_replace($search_array, $replace_array, $this->desc);
    return $this->desc;
  }
} //end of class
?>
