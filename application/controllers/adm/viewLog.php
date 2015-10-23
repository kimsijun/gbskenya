<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class viewLog extends common {
    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model("view_model");
    }

    public function ctViewLog(){
        $this->_set_sec();
        $params = $this->_get_sec();
        unset($params['viewLog']);
        if(!$params['vCSection']) $params['vCSection']='MONTH';
        $params["vCType"]="CONTENT";
        $result["cnt"] = $data['listCnt'] = count($this->view_model->_select_ctlist($params));
        $params["limit"] = 10;
        $params["offset"] = $params["page"]? $params["page"]:0;
        $result["list"] = $this->view_model->_select_ctlist($params);
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($params["limit"]) ? $params["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $params["offset"];
        $data["secParams"] = $params;
        $this->_print($data);


    }

    public function prViewLog(){
        $this->_set_sec();
        $params = $this->_get_sec();
        unset($params['viewLog']);
        if(!$params['vCSection']) $params['vCSection']='MONTH';
        $params["vCType"]="PROGRAM";
        $result["cnt"] = $data['listCnt'] = count($this->view_model->_select_prlist($params));
        $params["limit"] = 10;
        $params["offset"] = $params["page"]? $params["page"]:0;
        $result["list"] = $this->view_model->_select_prlist($params);
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($params["limit"]) ? $params["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $params["offset"];
        $data["secParams"] = $params; 

        for($i=0; $i<count($data["list"]); $i++){
            // 프로그램 네이게이션 만들기
            if(strlen($data["list"][$i]['prCode'])==3){
                $data["list"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prCode']));

            }elseif(strlen($data["list"][$i]['prCode'])==6){
                $data["list"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prPreCode']));
                $data["list"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prCode']));

            }elseif(strlen($data["list"][$i]['prCode'])==9){
                $data["list"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["list"][$i]['prCode'],0,3)));
                $data["list"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prPreCode']));
                $data["list"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prCode']));
            }elseif(strlen($data["list"][$i]['prCode'])==12){
                $data["list"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["list"][$i]['prCode'],0,3)));
                $data["list"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["list"][$i]['prCode'],0,6)));
                $data["list"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prPreCode']));
                $data["list"][$i]['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["list"][$i]['prCode']));
            }
        }

        $this->_print($data);
    }


}