<?php

abstract class Pagination{
    static function getPaging($page, $total_rows, $rows_per_page, $page_url){

        $paging_arr= array();

        $paging_arr['first'] = ($page > 1)?$page_url.'?page=1':"";

        $total_page = ceil($total_rows/$rows_per_page);

        $range = 2;

        $initial_num = $page -$range;
        $condition_limit_num = ($page + $range)+1;

        $paging_arr['pages']=array();
        $page_count=0;

        for($x = $initial_num; $x < $condition_limit_num; $x++){
            if(($x>0) && ($x <= $total_page)){
                $paging_arr['pages'][$page_count]['page']=$x;
                $paging_arr['pages'][$page_count]['url']=$page_url.'?page='.$x;
                $paging_arr['pages'][$page_count]['current_page']= $x==$page ? 'yes' : 'no';

                $page_count++;
            }
        }

        $paging_arr['last'] = $page < $total_page ? $page_url.'?page='.$total_page : '';

        return $paging_arr;
    }
}