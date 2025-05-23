<?php

namespace App\Traits;

/*

 * PHP Pagination Class

 * @author admin@catchmyfame.com - http://www.catchmyfame.com

 * @version 2.0.0

 * @date October 18, 2011

 * @copyright (c) admin@catchmyfame.com (www.catchmyfame.com)

 * @license CC Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) - http://creativecommons.org/licenses/by-sa/3.0/

 */

class Paginator {
    var $items_per_page;
    var $items_total;
    var $current_page;
    var $num_pages;
    var $mid_range;
    var $low;
    var $limit;
    var $return;
    var $default_ipp;
    var $querystring;
    var $ipp_array;
    var $url;

    public function __construct() {
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        // echo '<pre>',print_r($uri_segments);die;
        if(isset($uri_segments[1]) && $uri_segments[1] == 'search'){
            $PER_PAGE_ITEMS = 24;
        }else{
            $PER_PAGE_ITEMS = PER_PAGE_ITEMS;
        }
        // echo PER_PAGE_ITEMS;die;
        // echo CURR_EXT;die;
        $this->arrowLeft = 'fas fa-angle-double-left';
        $this->arrowRight = 'fas fa-angle-double-right';
        if(CURR_EXT == 'COM.AU'){
            $this->arrowLeft = 'fi-rs-arrow-small-left';
            $this->arrowRight = 'fi-rs-arrow-small-right';
        }

        $this->current_page = 1;

        $this->mid_range = 7;

        $this->ipp_array = array(10, 25, 50, 100, 150, 200);
        $this->url = url()->current();
        $this->items_per_page = (!empty($_GET['ipp'])) ? $PER_PAGE_ITEMS : $this->default_ipp;
    }

    function paginate() {

        if (!isset($this->default_ipp))
            $this->default_ipp =  $PER_PAGE_ITEMS;



        if (isset($_GET['ipp']) && $_GET['ipp'] == 'All') {

            $this->num_pages = 1;
        } else {

            if (!is_numeric($this->items_per_page) OR $this->items_per_page <= 0)
                $this->items_per_page = $this->default_ipp;

            $this->num_pages = ceil($this->items_total / $this->items_per_page);
        }



        $this->current_page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1; // must be numeric > 0

        $prev_page = $this->current_page - 1;

        $next_page = $this->current_page + 1;



        if ($_GET) {

            $args = explode("&", $_SERVER['QUERY_STRING']);

            foreach ($args as $arg) {

                $keyval = explode("=", $arg);

                if ($keyval[0] != "page" And $keyval[0] != "ipp")
                    $this->querystring .= "&" . $arg;
            }
        }



        if ($_POST) {

            foreach ($_POST as $key => $val) {

                if ($key != "page" And $key != "ipp")
                    $this->querystring .= "&$key=$val";
            }
        }



        if ($this->num_pages > 1) {

            $this->return = ($this->current_page > 1 And $this->items_total >= $this->items_per_page) ? "<div class='row'><div class='col-sm-12'><ul class='pagination'><li class='page-item'><a class=\"page-link\" href=\"$this->url?page=$prev_page&ipp=$this->items_per_page$this->querystring\"><i class='".$this->arrowLeft."'></i></a></li> " : "<div class='row'><div class='col-sm-12'><ul class='pagination'> ";



            $this->start_range = $this->current_page - floor($this->mid_range / 2);

            $this->end_range = $this->current_page + floor($this->mid_range / 2);



            if ($this->start_range <= 0) {

                $this->end_range += abs($this->start_range) + 1;

                $this->start_range = 1;
            }



            if ($this->end_range > $this->num_pages) {

                $this->start_range -= $this->end_range - $this->num_pages;

                $this->end_range = $this->num_pages;
            }



            $this->range = range($this->start_range, $this->end_range);



            for ($i = 1; $i <= $this->num_pages; $i++) {

                //if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " ... ";

                if ($this->range[0] > 2 And $i == $this->range[0])
                    $this->return .= "";

                // loop through all pages. if first, last, or in range, display

                if ($i == 1 Or $i == $this->num_pages Or in_array($i, $this->range)) {

                    $this->return .= ($i == $this->current_page And ( isset($_GET['page']) && $_GET['page'] != 'All')) ? "<li class='page-item active'><a title=\"Go to page $i of $this->num_pages\" class=\"page-link\" href=\"#\">$i</a></li> " : "<li class='page-item'><a class=\"page-link\" title=\"Go to page $i of $this->num_pages\" href=\"$this->url?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a></li> ";
                }

                //if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= " ... ";

                if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 And $i == $this->range[$this->mid_range - 1])
                    $this->return .= "";
            }
// print_r($this->current_page);die;

            $this->return .= (($this->current_page < $this->num_pages And $this->items_total > $this->items_per_page) || ( isset($_GET['page']) && $_GET['page'] != 'All') And $this->current_page > 0 And $this->current_page < $this->num_pages And $this->items_total > $this->items_per_page) ? "<li class='page-item'><a class=\"page-link\" href=\"$this->url?page=$next_page&ipp=$this->items_per_page$this->querystring\"><i class='".$this->arrowRight."'></i></a></li>\n" : "";

            // $this->return .= (isset($_GET['page']) && $_GET['page'] == 'All') ? "<li class='page-item active'><a class=\"page-link\" hidden href=\"javascript:;\">All</a></li> \n" : "<li class='page-item'><a class=\"page-link\" hidden href=\"$this->url?page=1&ipp=All$this->querystring\">All</a></li></ul></div> \n";
        }

        else {

            for ($i = 1; $i <= $this->num_pages; $i++) {

                $this->return .= ($i == $this->current_page) ? "<div class='row'><div class='col-sm-12'><ul class='pagination'><li class='page-item active'><a class=\"page-link\" href=\"#\">$i</a></li> " : "<li class='page-item'><a class=\"page-link\" href=\"$this->url?page=$i&ipp=$this->items_per_page$this->querystring\">$i</a></li> ";
            }

            // $this->return .= "<li class='page-item'><a class=\"page-link\" href=\"$this->url?page=1&ipp=All$this->querystring\">All</a></li></ul></div> \n";
        }
        if ($this->items_per_page != 'All') {
            $this->low = ($this->current_page <= 0) ? 0 : ($this->current_page - 1) * $this->items_per_page;
        }
        if ($this->current_page <= 0)
            $this->items_per_page = 0;

        $this->limit = (isset($_GET['ipp']) && $_GET['ipp'] == 'All') ? "" : " LIMIT $this->low,$this->items_per_page";
    }

    function display_items_per_page() {

        $items = '';

        if (!isset($_GET['ipp']))
            $this->items_per_page = $this->default_ipp;

        foreach ($this->ipp_array as $ipp_opt)
            $items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n" : "<option value=\"$ipp_opt\">$ipp_opt</option> \n";

        return "<div class='col-sm-5 float-sm-right'><div class='form-row mt-2 text-right'><div class='col'><span class=\"text-muted\">Rows:</span> <select class=\"border rounded text-muted\" onchange=\"window.location='$this->url?page=1&ipp='+this[this.selectedIndex].value+'$this->querystring';return false\">$items</select></div>\n";
    }

    function display_jump_menu() {

        $option = '';

        for ($i = 1; $i <= $this->num_pages; $i++) {

            $option .= ($i == $this->current_page) ? "<option value=\"$i\" selected>$i</option>\n" : "<option value=\"$i\">$i</option> \n";
        }

        return "<div class='col'><span class=\"text-muted\">Page:</span> <select class=\"border rounded text-muted\" onchange=\"window.location='$this->url?page='+this[this.selectedIndex].value+'&ipp=$this->items_per_page$this->querystring';return false\">$option</select></div><div class='col'><strong class='text-danger'>Total: " . $this->items_total . "</strong></div></div></div></div>\n";
    }

    function display_pages() {

        return $this->return;
    }

}
?>

