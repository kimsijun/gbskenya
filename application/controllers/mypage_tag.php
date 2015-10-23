<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mypage_tag extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->loginCheck();
        $this->load->model("tag_model");
    }

    public function index() {
        if($this->session->userdata('mbID'))
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        $tagParam['mbID'] = $data["member"]["mbID"];
        $data['tagList'] = $this->tag_model->_cnt_tag_list($tagParam);
        $this->_print($data);
    }

    public function view() {
        $params = $this->input->post();

        $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['tgTag'] = urldecode($params['tgTag']);

        $tagParam['mbID'] = $data["member"]["mbID"];
        $tagParam['tgTag'] = $data['tgTag'];
        $tagParam['oKey'] = "tgRegDate";
        $tagParam['oType'] = "DESC";
        $data['tagList'] = $this->tag_model->_select_list($tagParam);

        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';
        for($i=0; $i<count($data["tagList"]); $i++){
            $baseParams["prCode"] = $data["tagList"][$i]["prCode"];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            for($j=0; $j<count($baseContent); $j++) {
                if($data["tagList"][$i]["ctNO"] == $baseContent[$j]['ctNO']){
                    $data["tagList"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }

            // 프로그램 네이게이션 만들기
            if(strlen($data["tagList"][$i]['prCode'])==3){
                $data["tagList"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prCode']));

            }elseif(strlen($data["tagList"][$i]['prCode'])==6){
                $data["tagList"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prPreCode']));
                $data["tagList"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prCode']));

            }elseif(strlen($data["tagList"][$i]['prCode'])==9){
                $data["tagList"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["tagList"][$i]['prCode'],0,3)));
                $data["tagList"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prPreCode']));
                $data["tagList"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prCode']));
            }elseif(strlen($data["tagList"][$i]['prCode'])==12){
                $data["tagList"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["tagList"][$i]['prCode'],0,3)));
                $data["tagList"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["tagList"][$i]['prCode'],0,6)));
                $data["tagList"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prPreCode']));
                $data["tagList"][$i]['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["tagList"][$i]['prCode']));
            }
        }
        $this->_print($data);
    }

    public function delete() {
        $params = $this->input->post();

        $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        for($i=0; $i<count($params["tgNO"]); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["tgNO"] = $params["tgNO"][$i];
            $this->common_model->_delete('tag_data',$secParams[$i]) ;
        }

        redirect(base_url("/mypage_tag/index"));
        //$this->_print($data);
    }

}