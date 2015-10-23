<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mypage_favor extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->loginCheck();
        $this->load->model("mypage_model");
    }

    public function index() { 
        if($this->session->userdata('mbID'))
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        $data["ctFavor"] = $this->mypage_model->_select_ctFavor_list(array('mbID'=>$data["member"]["mbID"]));
        $data["prFavor"] = $this->mypage_model->_select_prFavor_list(array('mbID'=>$data["member"]["mbID"]));

        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';

        for($i=0; $i<count($data["ctFavor"]); $i++){
            $baseParams["prCode"] = $data["ctFavor"][$i]["prCode"];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            for($j=0; $j<count($baseContent); $j++) {
                if($data["ctFavor"][$i]["ctNO"] == $baseContent[$j]['ctNO']){
                    $data["ctFavor"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }

            // 프로그램 네이게이션 만들기
            if(strlen($data["ctFavor"][$i]['prCode'])==3){
                $data["ctFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prCode']));

            }elseif(strlen($data["ctFavor"][$i]['prCode'])==6){
                $data["ctFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prPreCode']));
                $data["ctFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prCode']));

            }elseif(strlen($data["ctFavor"][$i]['prCode'])==9){
                $data["ctFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctFavor"][$i]['prCode'],0,3)));
                $data["ctFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prPreCode']));
                $data["ctFavor"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prCode']));
            }elseif(strlen($data["ctFavor"][$i]['prCode'])==12){
                $data["ctFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctFavor"][$i]['prCode'],0,3)));
                $data["ctFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctFavor"][$i]['prCode'],0,6)));
                $data["ctFavor"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prPreCode']));
                $data["ctFavor"][$i]['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctFavor"][$i]['prCode']));
            }
        }

        for($i=0; $i<count($data["prFavor"]); $i++){
            $data["prFavor"][$i]["prName"] = $this->common_class->cut_str_han($data["prFavor"][$i]["prName"], 18,"...");
            // 프로그램 네이게이션 만들기
            if(strlen($data["prFavor"][$i]['prCode'])==3){
                $data["prFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prCode']));

            }elseif(strlen($data["prFavor"][$i]['prCode'])==6){
                $data["prFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prPreCode']));
                $data["prFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prCode']));

            }elseif(strlen($data["prFavor"][$i]['prCode'])==9){
                $data["prFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["prFavor"][$i]['prCode'],0,3)));
                $data["prFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prPreCode']));
                $data["prFavor"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prCode']));
            }elseif(strlen($data["prFavor"][$i]['prCode'])==12){
                $data["prFavor"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["prFavor"][$i]['prCode'],0,3)));
                $data["prFavor"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["prFavor"][$i]['prCode'],0,6)));
                $data["prFavor"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prPreCode']));
                $data["prFavor"][$i]['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["prFavor"][$i]['prCode']));
            }
        }
        $ctFCnt = ((count($data["ctFavor"])%6) == 0)? count($data["ctFavor"]) / 6 : (int)(count($data["ctFavor"]) / 6) + 1;
        $idx = 0;

        for($i=0; $i<$ctFCnt; $i++) {
            for($j=0; $j<6; $j++){
                if($data["ctFavor"][$idx]) $data["FavorContent"][$i]["page"][] = $data["ctFavor"][$idx++];
            }
        }
        $prFCnt = ((count($data["prFavor"])%6) == 0)? count($data["prFavor"]) / 6 : (int)(count($data["prFavor"]) / 6) + 1;
        $idx = 0;

        for($i=0; $i<$prFCnt; $i++) {
            for($j=0; $j<6; $j++){
                if($data["prFavor"][$idx]) $data["FavorProgram"][$i]["page"][] = $data["prFavor"][$idx++];
            }
        }
        $this->_print($data);
    }

    public function delete() {
        $params = $this->input->post();
        $ctParams = $params["ctNO"];
        $prParams = $params["prCode"];

        $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        for($i=0; $i<count($ctParams); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["mpSection"] = "FAVOR";
            $secParams[$i]["mpType"] = "CONTENT";
            $secParams[$i]["ctNO"] = $params["ctNO"][$i];
            $this->common_model->_delete('mypage_log',$secParams[$i]) ;
        }
        for($i=0; $i<count($prParams); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["mpSection"] = "FAVOR";
            $secParams[$i]["mpType"] = "PROGRAM";
            $secParams[$i]["prCode"] = $params["prCode"][$i];
            $this->common_model->_delete('mypage_log',$secParams[$i]) ;
        }

        redirect(base_url("/mypage_favor/index"));
        echo '<pre>'; print_r($secParams );echo '</pre>';
        //$this->_print($data);
    }
    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();

        if($params['mode'] == "write"){
            if($this->session->userdata('mbID')){
                $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

                $paramFavor["mpSection"] = 'FAVOR';
                $paramFavor["mpType"] = $params["mpType"];
                if($params["mpType"]=='CONTENT') $paramFavor["ctNO"] = $params["ctNO"];
                else $paramFavor["prCode"] = $params["prCode"];
                $paramFavor["mbID"] = $data["member"]["mbID"];
                $paramFavor['mpRemoteIP'] = $_SERVER['REMOTE_ADDR'];

                $isFavor = $this->common_model->_select_cnt('mypage_log',$paramFavor);
                $result["success"] = ($isFavor == 0) ? "true":"false";
                if(!$isFavor){
                    $paramFavor['mpRegDate'] = 'NOW()';
                    $this->mypage_model->_insert($paramFavor);
                }
            }else{
                $result['login']='Would you like to log in?';
            }
            echo json_encode($result);
        }
    }
}