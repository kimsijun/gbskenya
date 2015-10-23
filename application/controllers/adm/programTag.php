<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class programTag extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model('program_model');
        $this->load->model('member_model');
        $this->load->model('tag_model');
    }


    /*  관리자 컨텐츠 태그 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        $secParams['tgCodeType'] = 'prCode';

        if(!$secParams['oderkey'] || $secParams['oderkey']== 'recentTag'){
            $secParams['oKey'] = 'tgRegDate';
            $secParams['oType'] = 'DESC';
            $result["cnt"] = $this->tag_model->_select_cnt($secParams);
            $result["list"] = $this->tag_model->_select_list($secParams);
            for($i=0; $i<count($result["list"]); $i++){
                $result["list"][$i]["program"] = $this->program_model->_select_row(array('prCode'=>$result["list"][$i]['tgCode']));
            }
            $data["recentTag"] = $result["list"];

        }elseif($secParams['oderkey']=='countTag'){
            $result["cnt"] = $this->tag_model->_select_cnt($secParams);
            $result["list"] = $this->tag_model->_cnt_tag_list($secParams);
            for($i=0; $i<count($result["list"]); $i++){
                $paramTag["tgTag"] = $result["list"][$i]["tgTag"];
                $paramTag["tgCodeType"] = $secParams['tgCodeType'];
                //$paramTag['oKey'] = 'tgRegDate';
                //$paramTag['oType'] = 'DESC';
                $result["list"][$i]["program"] = $this->tag_model->_select_list($paramTag);
                for($k=0; $k<count($result["list"][$i]["program"]); $k++){
                    $tagProgram = $this->program_model->_select_row(array('prCode'=>$result["list"][$i]["program"][$k]["tgCode"]));
                    $result["list"][$i]["program"][$k] = array_merge($result["list"][$i]["program"][$k], $tagProgram);
                }
            }
            $data["countTag"] = $result["list"];
        }
        $data['listCnt'] = $result["cnt"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }

    public function process(){
        $params = $this->input->post();
        if($params['mode'] == "changeTagMode"){
            redirect(base_url("/adm/programTag/index/oderkey/".$params['oderkey']));
        }elseif($params['mode'] == "delete"){
            for($i=0; $i<count($params['chk']); $i++){
                $tgNO[] = $params['chk'][$i];
            }
            unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
            for($i=0; $i<count($tgNO); $i++){
                $this->tag_model->_delete(array('tgNO'=>$tgNO[$i]));
            }
            if($params['oderkey']){
                redirect(base_url("/adm/programTag/index/oderkey/".$params['oderkey']));
            }else
                redirect(base_url("/adm/programTag/index"));
        }
    }
}