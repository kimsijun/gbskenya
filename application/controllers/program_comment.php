<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE 프로그램 댓글관련 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

class program_comment extends common {


    public function __construct(){
        parent::__construct();
        $this->load->model('program_comment_model');
    }



    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();
        if(!$this->session->userdata('mbID')){
            echo json_encode('false');exit;
        }else{
            $memberData = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];

            // 생성
            if($params['mode'] == "write"){
                $params['pbcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];

                unset($params['mode']);
                $params['pbcoModDate'] = 'NOW()';
                $params['pbcoRegDate'] = 'NOW()';
                $this->program_comment_model->_insert($params);

                // 수정
            } else if($params['mode'] == "modify"){
                $params['pbcoModDate'] = 'NOW()';

                unset($params['mode']);
                unset($params['mbID']);
                $this->common_model->_update('program_comment_data',$params,array('pbcoNO'=>$params['pbcoNO'],"prCode"=>$params['prCode']));

                //답글 생성
            } else if($params['mode'] == "reply"){
                $pbco_info = $this->common_model->_select_row('program_comment_data',array("pbcoNO"=>$params['pbcoNO']));
                foreach($pbco_info as $k => $v) $bco_data[$k] = $v;
                $params['pbcoGroup'] = $bco_data['pbcoGroup'];$params['pbcoStep'] = $bco_data['pbcoStep'];
                $params['pbcoDepth'] = $bco_data['pbcoDepth'];$params['pbcoParent'] = $bco_data['pbcoNO'];

                $params['pbcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];
                $params['pbcoModDate'] = 'NOW()';
                $params['pbcoRegDate'] = 'NOW()';
                unset($params['mode']);
                $this->common_model->_reply('program_comment_data',$params);

              // 삭제
            } else if($params['mode'] == "delete"){
                $params['pbcoIsDelete'] = "YES";
                $params['pbcoModDate'] = 'NOW()';
                unset($params['mode']);
                $this->common_model->_update('program_comment_data',$params,array("pbcoNO"=>$params['pbcoNO'],"prCode"=>$params['prCode']));
            }
            $path = "./_cache/%cache"; delete_files($path, true);

            $paramComment["oKey"] = "pbcoGroup DESC, pbcoStep ASC"; $paramComment["oType"] = "";
            $paramComment["A.prCode"] = $params["prCode"];
            $paramComment["pbcoIsDelete"] = "NO";
            $paramComment["pbcoIsNotice"] = "NO";
            $data = $this->program_comment_model->_select_list($paramComment);
            for($i=0; $i<count($data); $i++) {
                $result[$i]["pbcoRegDate"] = $this->common_class->cut_str_han($data[$i]["pbcoRegDate"], 10,"");
                $result[$i]["pbcoModDate"] = $this->common_class->cut_str_han($data[$i]["pbcoModDate"], 10,"");
            }
            $result['data'] = $data;
            $result['prCode'] = $params["prCode"];
            $result['templateHtml'] = $this->cfg["module_dir_name"].'/widget/program/comment.html';
            $result['mbID'] = $this->session->userdata($this->cfg["session_key"]);
            $result['mbIsAdmin'] = $this->session->userdata('mbIsAdmin');
            $html = $this->_print($result, TRUE);
            $result['html'] = $html;
            echo json_encode($result);
            exit;
        }
    }
}

?>