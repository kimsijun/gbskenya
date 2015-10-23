<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   댓글 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class comment extends common {


    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('board_model');
        $this->load->model('comment_model');
    }



    /*  관리자 게시판 통합리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['comment']);
        $data = $secParams;
        $secParams["oKey"] = "bcoGroup DESC, bcoStep ASC"; $secParams["oType"] = "";
        if($secParams['bodID']) {
            $bo_Info = $this->common_model->_select_row('board_cfg',array('bodID'=>$secParams['bodID']));
            $data['bodName'] = $bo_Info['bodName'];
        } else $data['bodName'] = '전체';
        $result["cnt"] = $this->comment_model->_select_cnt($secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->comment_model->_select_list($secParams);
        for($i=0; $i<$result["cnt"]; $i++){
            $boInfo = $this->common_model->_select_row('board_cfg',array('bodID'=>$result["list"][$i]['bodID']));
            $result["list"][$i]['bodName'] = $boInfo['bodName'];
        }

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;

        // 게시글 메뉴
        $data['menu_rs'] = $this->common_model->_select_list('board_cfg',array('bodIsDelete'=>"NO"));
        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        // 게시판 선택
        if($params['mode'] == "selectBoard"){
            redirect(base_url("/adm/comment/index/bodID/".$params['bodID']));

        }// 관리자 댓글 생성
        elseif($params['mode'] == "write"){
            $params['bcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['bcoModDate'] = 'NOW()';
            $params['bcoRegDate'] = 'NOW()';
            $memberData = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];
            $params['mbName'] = $memberData['mbName'];
            $params['mbNick'] = $memberData['mbNick'];

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_insert('comment_data',$params);
            // boGroup 업데이트
            $bcoNO  = mysql_insert_id();
            $this->common_model->_update('comment_data',array('bcoGroup'=>$bcoNO),array('bcoNO'=>$bcoNO));

            $path = "./_cache/%cache";      delete_files($path, true);
            redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));

            // 관리자 댓글 수정
        } else if($params['mode'] == "modify"){
            $params['bcoModDate'] = 'NOW()';

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('comment_data',$params,array('bcoNO'=>$params['bcoNO']));
            $path = "./_cache/%cache";      delete_files($path, true);
            redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));



            // 관리자 댓글 삭제
        }else if($params['mode'] == "delete"){
            //선택삭제
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $bcoNO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($bcoNO); $i++){
                    $this->common_model->_delete('comment_data',array('bcoNO'=>$bcoNO[$i]));
                }
                $path = "./_cache/%cache";      delete_files($path, true);
                redirect(base_url("/adm/comment/index"));
            }
            //board view 에서 삭제
            else{
                $this->common_model->_delete('comment_data',array('bcoNO'=>$params['bcoNO']));
                $path = "./_cache/%cache";      delete_files($path, true);
                redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));
            }


        }  else if($params['mode'] == "all_delete_cfg"){
            $this->board_model->all_delete_cfg($params);
        }
    }


    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();

        if($params['mode'] == "selectRow"){
            $data = $this->common_model->_select_row('comment_data',array("bcoNO"=>$params['bcoNO']));
            echo json_encode($data);
        }
    }


}

?>