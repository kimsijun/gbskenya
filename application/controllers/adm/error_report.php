<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   회원 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class error_report extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }

    /**
     * 관리자 회원정보 리스트 페이지
     * @param none
     * @return none
     */
    public function index() {
        $secParams = $this->_get_sec();
        $this->_print( );
    }

    /**
     * 관리자 회원정보 뷰 페이지
     * @param none
     * @return none
     */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('member_data', array('mbID'=>$params['mbID']));
        $data['memberID'] = $params['mbID'];
        $arrBirth = explode('-',$data['mbBirth']);
        $data['age'] =  date("Y") - $arrBirth[0] + 1;
        //$data['mbBirth'] = $arrBirth[1].'월 '.$arrBirth[2].'일';
        switch($data['mbWdReason']){
            case '1': $data['mbWdReason'] = 'Not often used';  break;
            case '2': $data['mbWdReason'] = 'In order to sign up again';break;
            case '3': $data['mbWdReason'] = 'Unsatisfactory use';break;
            case '4': $data['mbWdReason'] = 'Etc.';break;
        }
        $this->_print($data);
    }

    /**
     * 관리자 회원정보 수정페이지
     * @param none
     * @return none
     */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('member_data', array('mbID'=>$params['mbID']));
        $data['memberID'] = $params['mbID'];

        $this->_print($data);
    }

    /**
     * 관리자 탈퇴 회원정보 리스트
     * @param none
     * @return none
     */
    public function withdraw() {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  회원 리스트와 다른 부분은 mbIsWithdraw 조건이 "YES"
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

        $this->_set_sec(array('x','y'));
        $secParams = $this->_get_sec();
        unset($secParams['member']);
        if($secParams['secTxt'])
            $secParams['secTxt'] = urldecode($secParams['secTxt']);
        $secParams["oKey"] = "mbRegDate";
        $secParams["oType"] = "DESC";
        $secParams['mbIsWithdraw'] = "YES";
        $result["cnt"] = $this->common_model->_select_cnt('member_data', $secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"];
        $result["list"] = $this->common_model->_select_list('member_data', $secParams);
        for($i=0;$i<count($result['list']);$i++){
            switch($result['list'][$i]['mbWdReason']){
                case '1': $result['list'][$i]['mbWdReason'] = 'Not often used';  break;
                case '2': $result['list'][$i]['mbWdReason'] = 'In order to sign up again';break;
                case '3': $result['list'][$i]['mbWdReason'] = 'Unsatisfactory use';break;
                case '4': $result['list'][$i]['mbWdReason'] = 'Etc.';break;
            }
        }
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);
        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;

        $data['today7day'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['today15day'] = date("Y-m-d", strtotime(date('Y-m-d')." -15 day"));
        $data['today1month'] = date("Y-m-d", strtotime(date('Y-m-d')." -1 month"));
        $data['today2month'] = date("Y-m-d", strtotime(date('Y-m-d')." -2 month"));
        $data['today'] = date('Y-m-d');
        $data['listNO'] = 1;
        $this->_print($data);

    }

    
    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        /**
         * 관리자 회원정보 수정 처리
         * 패스워드 수정 시 md5로 암호화하여 디비에 넣고, 수정이 아니면 unset.
         * 생일, 전화번호, 휴대폰의 경우 유효성 체크를 한 후 합쳐서 넣음.
         * @param array $params array("mode","mbPW", "mbBirth", 회원정보,,,);
         * @return none
         */
        if($params['mode'] == "modify"){

            if($params['mbPW'][0] == ""){
                unset($params['mbPW']);
            } else if($params['mbPW'][0] == $params['mbPW'][1])     $params['mbPW'] = md5($params['mbPW'][0]);

            if($params['mbPhone'][0] && $params['mbPhone'][1] && $params['mbPhone'][2]) $params['mbPhone'] = implode('-', $params['mbPhone']);
            if($params['mbCellPhone'][0] && $params['mbCellPhone'][1] && $params['mbCellPhone'][2]) $params['mbCellPhone'] = implode('-', $params['mbCellPhone']);
            unset($params['mode']);unset($params['x']);unset($params['y']);
            unset($params['mod_pass']);
            $params['mbModDate'] = 'NOW()';
            $this->common_model->_update('member_data', $params,array('mbID'=>$params['mbID']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/member/index"));

            /**
             * 관리자 회원정보 선택 탈퇴처리
             * @param array $params array("mode","mbID check list");
             * @return none
             */
        } else if($params['mode'] == "delete"){
            for($i=0; $i<count($params['chk']); $i++){
                $mbID[] = $params['chk'][$i];
            }
            unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
            for($i=0; $i<count($mbID); $i++){
                $this->common_model->_delete('member_data', array('mbID'=>$mbID[$i]));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/member/index"));

            /**
             * 관리자 회원정보 일괄 탈퇴처리
             * @param array $params array("mode");
             * @return none
             */
        } else if($params['mode'] == "all_delete"){
            $this->common_model->adm_all_delete('member_data', $params);
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/member/index"));


        }
    }

    /**
     * 관리자 Ajax 로그인 체크
     * @param array $params array("mode");
     * @return json_encode( $result true:false )
     */
    public function ajax_process(){
        $params = $this->input->post();
        if($params['mode'] == "loginCheck"){
            $key = $this->cfg["session_key"];
            $result = ($this->session->userdata($key)) ? "true":"false";
        }

        echo json_encode($result);
    }

}
?>
