<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   댓글 관련 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class comment extends common {


    public function __construct(){
        parent::__construct();
        $this->load->model('cfg_board_model'); $this->load->model('board_model'); $this->load->model('comment_model');
    }



    /*  관리자 게시판 통합리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        $data = $secParams;
        $secParams["oKey"] = "bcoGroup DESC, bcoStep ASC"; $secParams["oType"] = "";
        if($secParams['bodID']) {
            $bo_Info = $this->cfg_board_model->_select_row(array('bodID'=>$secParams['bodID']));
            $data['boName'] = $bo_Info['boName'];
        }
        else $data['boName'] = '전체보기';

        $result["cnt"] = $this->comment_model->_select_cnt($secParams);
        $result["list"] = $this->comment_model->_select_list($secParams);
        for($i=0; $i<$result["cnt"]; $i++){
            $boInfo = $this->cfg_board_model->_select_row(array('bodID'=>$result["list"][$i]['bodID']));
            $result["list"][$i]['boName'] = $boInfo['boName'];
        }

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;

        // 게시글 메뉴
        $data['menu_rs'] = $this->cfg_board_model->_select_list();
        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        // 관리자 댓글 생성
        if($params['mode'] == "write"){
            $params['bcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['bcoModDate'] = 'NOW()';
            $params['bcoRegDate'] = 'NOW()';
            $memberData = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_insert('comment_data',$params);
            // boGroup 업데이트
            $bcoNO  = mysql_insert_id();
            $this->common_model->_update('comment_data',array('bcoGroup'=>$bcoNO),array('bcoNO'=>$bcoNO));

            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));

            // 관리자 댓글 수정
        } else if($params['mode'] == "modify"){
            $params['bcoModDate'] = 'NOW()';

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('comment_data',array('bcoNO'=>$params['bcoNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));

            // 관리자 댓글 삭제
        }else if($params['mode'] == "delete"){
            $this->comment_model->_delete(array('bcoNO'=>$params['bcoNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));

            // 관리자 댓글 생성
        } else if($params['mode'] == "reply"){
            $memberData = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];
            $params['bcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['bcoModDate'] = 'NOW()';
            $params['bcoRegDate'] = 'NOW()';

            $commentData = $this->common_model->_select_row('comment_data',array('bcoNO'=>$params['bcoNO']));
            $params['bcoGroup'] = $commentData['bcoGroup'];
            $params['bcoDepth'] = $commentData['bcoDepth'];
            $params['bcoStep'] = $commentData['bcoStep'];
            $params['bcoParent'] = $commentData['bcoNO'];

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $boNO = $this->comment_model->_reply($params);

            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));

        }
    }


    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();

        if($params['mode'] == "selectRow"){
            $data = $this->comment_model->_select_row(array("bcoNO"=>$params['bcoNO']));
            echo json_encode($data);
        }
    }


}

?>