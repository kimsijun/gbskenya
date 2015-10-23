<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mypage_like extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->loginCheck();
        $this->load->model("mypage_model");
    }

    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();
        if($params['mode'] == "write"){
            if($this->session->userdata('mbID')){
                $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

                $paramLike["mpSection"] = 'LIKE';
                $paramLike["mpType"] = $params["mpType"];
                if($params["mpType"]=='CONTENT') $paramLike["ctNO"] = $params["ctNO"];
                else $paramLike["prCode"] = $params["prCode"];
                $paramLike["mbID"] = $data["member"]["mbID"];
                $paramLike['mpRemoteIP'] = $_SERVER['REMOTE_ADDR'];

                $isLike = $this->common_model->_select_cnt('mypage_log',$paramLike);
                $result["success"] = ($isLike == 0) ? "true":"false";
                $result["mpType"] = $params["mpType"];
                if(!$isLike){
                    $paramLike['mpRegDate'] = 'NOW()';
                    $this->mypage_model->_insert($paramLike);
                    if($params["mpType"]=="CONTENT"){
                        $content = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
                        $this->common_model->_update('content_data',array('ctLikeCount'=>$content['ctLikeCount']+1),array('ctNO'=>$params['ctNO']));
                        $result['content'] = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
                    }else{
                        $program = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                        $this->common_model->_update('program_data',array('prLikeCount'=>$program['prLikeCount']+1),array('prCode'=>$params['prCode']));
                        $result['program'] = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                    }
                }
            }else{
                $result['login']='Would you like to log in?';
            }
            echo json_encode($result);
        }
    }
}