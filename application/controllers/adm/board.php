<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   게시글 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   14. 5. 15.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class board extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('cfg_board_model'); $this->load->model('board_model');
    }


    /*  관리자 게시판 생성    */
    public function write() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['bodID'] = $params['bodID'];
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["bodName"] = $data["boInfo"]["bodName"];
        $this->_print($data);
    }



    /*  관리자 게시판 통합리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        if($secParams['secTxt'])
            $secParams['secTxt'] = urldecode($secParams['secTxt']);
        unset($secParams['board']);
        $data = $secParams;
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$secParams['bodID']));
        $data["bodName"] = $data["boInfo"]["bodName"];
        $data["total"] = $this->common_model->_select_cnt('board_data', array('bodID'=>$secParams['bodID']));
        $result["cnt"] = $this->common_model->_select_cnt('board_data', $secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->board_model->_select_list($secParams);
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
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
        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        unset($params['board']);

        $data = $this->common_model->_select_row('board_data',array('bodID'=>$params['bodID'],'boNO'=>$params['boNO']));
        $params['boHit']=$data['boHit']+1;
        $this->common_model->_update('board_data',$params,array('bodID'=>$params['bodID'],'boNO'=>$params['boNO']));
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["bodName"] = $data["boInfo"]["bodName"];
        $this->_print($data);
    }

    /*  관리자 게시판 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('board_data',array('boNO'=>$params['boNO']));
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$data['bodID']));
        $data["bodName"] = $data["boInfo"]["bodName"];
        $this->_print($data);
    }

    /*  관리자 게시판의 게시글 답글    */
    public function reply() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['bodID'] = $params['bodID'];
        $data['boNO'] = $params['boNO'];
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["bodName"] = $data["boInfo"]["bodName"];
        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        // 관리자 게시글 생성
        if($params['mode'] == "write"){
            $params['boRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['boModDate'] = 'NOW()';
            $params['boRegDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->board_model->_insert($params);
            $data = $this->common_model->_select_row('board_data',array('mbID'=>$params['mbID'], 'boName'=>$params['boName'], 'boContent'=>$params['boContent']));
            // boGroup 업데이트
            $this->common_model->_update('board_data',array('boGroup'=>$data['boNO']),array('boNO'=>$data['boNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$data['boNO']));


            // 관리자 게시글 수정
        } else if($params['mode'] == "modify"){
            $params['boModDate'] = 'NOW()';
            unset($params['mode']);
            $this->common_model->_update('board_data',$params,array('bodID'=>$params['bodID'],'boNO'=>$params['boNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$params['boNO']));


            // 관리자 게시글 삭제
        } else if($params['mode'] == "delete"){
            $params['boIsDelete'] = 'YES';
            unset($params['mode']);unset($params['checkAll_length']);
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $bNO[] = $params['chk'][$i];
                }
                unset($params['chk']);
                for($i=0; $i<count($bNO); $i++){
                    $params['boModDate'] = 'NOW()';
                    $this->common_model->_update('board_data',$params,array('boNO'=>$bNO[$i]));
                }
            }else{
                $params['boModDate'] = 'NOW()';
                $this->common_model->_update('board_data',$params,array('boNO'=>$params['boNO']));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/board/index/bodID/".$params['bodID']));

            // 관리자 답글 생성
        } else if($params['mode'] == "reply"){
            $params['boRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['boModDate'] = 'NOW()';
            $params['boRegDate'] = 'NOW()';

            $boardData = $this->common_model->_select_row('board_data',array('boNO'=>$params['boNO']));
            $params['boGroup'] = $boardData['boGroup'];
            $params['boDepth'] = $boardData['boDepth'];
            $params['boStep'] = $boardData['boStep'];
            $params['boParent'] = $boardData['boNO'];

            unset($params['mode']);unset($params['x']);unset($params['y']);unset($params['boNO']);
            $this->board_model->_insert($params);
            $data = $this->common_model->_select_row('board_data',array('mbID'=>$params['mbID'], 'boName'=>$params['boName'], 'boContent'=>$params['boContent']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/board/view/bodID/".$params['bodID']."/boNO/".$data['boNO']));


            // 게시판 일괄 삭제
        } else if($params['mode'] == "all_delete_cfg"){
            $path = "./_cache/%cache"; delete_files($path, true);
            $this->board_model->all_delete_cfg($params);
        }


    }

}

?>
