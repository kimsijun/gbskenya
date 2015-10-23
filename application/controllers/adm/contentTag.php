<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class contentTag extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('content_model');
        $this->load->model('tag_model');
    }


    /*  관리자 컨텐츠 태그 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['contentTag']);
        if(!$secParams['oderkey'] || $secParams['oderkey']== 'recentTag'){
            unset($secParams['oderkey']);
            $secParams['oKey'] = 'tgRegDate';
            $secParams['oType'] = 'DESC';
            $result["cnt"] = $this->common_model->_select_cnt('tag_data',$secParams);
            $secParams["limit"] = 10;
            $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
            $result["list"] = $this->tag_model->_select_list($secParams);
            $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
            $baseParams['oType'] = '';
            for($i=0; $i<count($result["list"]); $i++){
                $baseParams["prCode"] = $result["list"][$i]["prCode"];
                $baseContent = $this->common_model->_select_list('content_data',$baseParams);
                for($j=0; $j<count($baseContent); $j++) {
                    if($result["list"][$i]["ctNO"] == $baseContent[$j]['ctNO']){
                        $result["list"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                        break;
                    }
                }
            }
            $data["recentTag"] = $result["list"];

        }elseif($secParams['oderkey']=='countTag'){
            unset($secParams['oderkey']);
            $result["cnt"] = $this->common_model->_select_cnt('tag_data');
            $secParams["limit"] = 10;
            $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
            $result["list"] = $this->tag_model->_select_list($secParams);
            $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
            $baseParams['oType'] = '';
            for($i=0; $i<count($result["list"]); $i++){
                $baseParams["prCode"] = $result["list"][$i]["prCode"];
                $baseContent = $this->common_model->_select_list('content_data',$baseParams);
                for($j=0; $j<count($baseContent); $j++) {
                    if($result["list"][$i]["ctNO"] == $baseContent[$j]['ctNO']){
                        $result["list"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                        break;
                    }
                }
            }
            $data["countTag"] = $result["list"];
        }
        $data['listCnt'] = $result["cnt"];
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $this->_get_sec();
        $this->_print($data);
    }

    public function process(){
        $params = $this->input->post();

        if($params['mode'] == "changeTagMode"){
            redirect(base_url("/adm/contentTag/index/oderkey/".$params['oderkey']));
        }elseif($params['mode'] == "delete"){
            for($i=0; $i<count($params['chk']); $i++){
                $tgNO[] = $params['chk'][$i];
            }
            unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
            for($i=0; $i<count($tgNO); $i++){
                $this->common_model->_delete('tag_data',array('tgNO'=>$tgNO[$i]));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            if($params['oderkey']){
                redirect(base_url("/adm/contentTag/index/oderkey/".$params['oderkey']));
            }else
                redirect(base_url("/adm/contentTag/index"));
        }
    }
}