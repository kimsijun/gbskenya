<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mypage extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model('member_model');
        $this->load->model('cfg_board_model'); $this->load->model('board_model'); $this->load->model('comment_model');
    }

    public function index() {
        $this->mb_check();
        $this->_print();
    }
    /*  로그인 체크    */
    public function mb_check(){
        if(!$this->session->userdata($this->cfg["session_key"])){
            echo "<script> alert('잘못된 접근 입니다.');</script>";
            redirect(base_url("/adm/member/login"));
        }
    }

    public function report() {
        $this->_set_sec(array('x','y'));
        $secParams = $this->_get_sec();
        if($secParams['secTxt'])
            $secParams['secTxt'] = urldecode($secParams['secTxt']);

        unset($secParams["member"]);
        $data = $secParams;
        $data["member"] = $this->member_model->_select_row(array('mbID'=>$this->session->userdata('mbID')));

        $secParams["oKey"] = "boGroup DESC, boStep ASC, boRegDate DESC"; $secParams["oType"] = "";
        $secParams["offset"] = isset($secParams["page"]) ? $secParams["page"] : "0";
        $secParams["limit"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $secParams["mbID"] = $data["member"]["mbID"];

        $data["total"] = $this->board_model->_select_cnt(array('boID'=>$secParams['boID']));
        $result["cnt"] = $this->board_model->_select_cnt($secParams);
        $result["list"] = $this->board_model->_select_list($secParams);

        $pager["CNT"] = $data['cnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $data['today7day'] = date("Ymd", strtotime(date('Ymd')." -7 day"));
        $data['today15day'] = date("Ymd", strtotime(date('Ymd')." -15 day"));
        $data['today1month'] = date("Ymd", strtotime(date('Ymd')." -1 month"));
        $data['today2month'] = date("Ymd", strtotime(date('Ymd')." -2 month"));
        $data['today'] = date('Ymd');

        // 게시글 메뉴
        $data['menu_rs'] = $this->cfg_board_model->_select_list();
        $this->_print($data);
    }

    public function report_view() {
        $params = $this->_get_sec();
        $this->db->set('boHit', 'boHit+1', FALSE);
        $this->board_model->_update(null,array('boNO'=>$params['boNO']));
        $data = $this->board_model->_select_row(array('boNO'=>$params['boNO']));
        $data["member"] = $this->member_model->_select_row(array('mbID'=>$this->session->userdata('mbID')));

        $secParams["oKey"] = "boGroup DESC, boStep ASC";
        $secParams["oType"] = "";
        $secParams['boGroup'] = $data['boGroup'];
        $secParams["mbID"] = $data["member"]["mbID"];

        $data["cnt"] = $this->board_model->_select_cnt($secParams);
        $data["list"] = $this->board_model->_select_list($secParams);
        $params["oKey"] = "bcoGroup ASC, bcoStep ASC";        $params["oType"] ="";
        $data['bco_rs'] = $this->comment_model->_select_list($params);
        $data['menu_rs'] = $this->cfg_board_model->_select_list();
        $this->_print($data);
    }

    public function report_modify() {
        $params = $this->_get_sec();
        $data = $this->board_model->_select_row(array('boNO'=>$params['boNO']));
        $data["member"] = $this->member_model->_select_row(array('mbID'=>$this->session->userdata('mbID')));

        // 게시글 메뉴
        $data['menu_rs'] = $this->cfg_board_model->_select_list();
        $this->_print($data);
    }

    public function report_process () {
        $params = $this->input->post();
        //echo'<pre>';print_r($params);echo"</pre>";exit;
        // 관리자 게시글 생성
        if($params['mode'] == "write"){
            $params['boID'] = "report";
            $params['codeType'] = $params['codeType'];
            $params['boID'] = "report";
            $params['boName'] = urldecode($params['ReportTxt']);
            $params['boContent'] = urldecode($params['ReportTxt']);
            $params['boRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $this->db->set('boModDate', 'NOW()', false);
            $this->db->set('boRegDate', 'NOW()', false);

            $this->load->model('member_model');
            $memberData = $this->member_model->_select_row(array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];
            $params['mbFirstName'] = $memberData['mbFirstName'];
            $params['mbLastName'] = $memberData['mbLastName'];
            $params['mbNick'] = $memberData['mbNick'];
            $params['boEmail'] = $memberData['mbEmail'];

            unset($params['mode']);
            unset($params['ReportTxt']);
            $this->board_model->_insert($params);
            if($params["codeType"] == "ctNO"){
                redirect(base_url("/content/view/ctNO/".$params['code']));
            }else{
                redirect(base_url("/program/view/prCode/".$params['code']));
            }

            // 관리자 게시글 수정
        } else if($params['mode'] == "modify"){
            $this->db->set('boModDate', 'NOW()', false);

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->board_model->_update($params,array('boID'=>$params['boID'],'boNO'=>$params['boNO']));
            redirect(base_url("/mypage/report/boID/report"));


            // 관리자 게시글 삭제
        } else if($params['mode'] == "delete"){
            $this->board_model->_delete(array('boNO'=>$params['boNO']));
            redirect(base_url("/mypage/report/boID/report"));


            // 관리자 답글 생성
        } else if($params['mode'] == "reply"){
            $this->load->model('member_model');
            $memberData = $this->member_model->_select_row(array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];
            $params['mbFirstName'] = $memberData['mbFirstName'];
            $params['mbLastName'] = $memberData['mbLastName'];
            $params['mbNick'] = $memberData['mbNick'];
            $params['boRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $this->db->set('boModDate', 'NOW()', false);
            $this->db->set('boRegDate', 'NOW()', false);

            $boardData = $this->board_model->_select_row(array('boNO'=>$params['boNO']));
            $params['boGroup'] = $boardData['boGroup'];
            $params['boDepth'] = $boardData['boDepth'];
            $params['boStep'] = $boardData['boStep'];
            $params['boParent'] = $boardData['boNO'];

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $boNO = $this->board_model->_reply($params);

            redirect(base_url("/mypage/report_view/boID/".$params['boID']."/boNO/".$boNO));
        }


    }
}