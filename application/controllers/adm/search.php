<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   검색 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   14. 3. 7.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class search extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }


    /*  관리자 컨텐츠 태그 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['search']);
        $secParams['oKey'] = 'scGroup desc, scRegDate asc';
        $secParams['oType'] = '';

        $data["total"] = $this->common_model->_select_cnt('search_log');
        $result["cnt"] = $this->common_model->_select_cnt('search_log', $secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result['list']  = $this->common_model->_select_list('search_log',$secParams);

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;

        //echo '<pre>';print_r($data['list']);echo '</pre>';exit;

        $scGroup = "";
        for($i=0; $i<count($data['list']); $i++) {
            if($scGroup == $data['list'][$i]['scGroup'])  $data['list'][$i]['mbID'] = "";
            else                                         $scGroup = $data['list'][$i]['scGroup'];

            if($data['list'][$i]['scSecResult'])    $data['list'][$i]['scSecResult'] = (array)json_decode($data['list'][$i]['scSecResult']);
            if($data['list'][$i]['scSecResult'][1]){
                for($j=0; $j<count($data['list'][$i]['scSecResult']); $j++){
                    $data['list'][$i]['scSecResult'][$j] = (array)$data['list'][$i]['scSecResult'][$j];
                }
            } else if($data['list'][$i]['scSecResult']['scName']){
                $setData = $data['list'][$i]['scSecResult'];
                unset($data['list'][$i]['scSecResult']);
                $data['list'][$i]['scSecResult'][0] = $setData;
            }
        }
        $this->_print($data);
    }

}