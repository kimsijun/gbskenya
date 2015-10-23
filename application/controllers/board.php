<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   게시판 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class board extends common {


    public function __construct(){
        parent::__construct();
        $this->load->model('board_model');
    }


    /*  관리자 게시판 생성    */
    public function write() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['bodID'] = $params['bodID'];
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["cfg_bodName"] = $data["boInfo"]["bodName"];
        $data['menu_rs'] = $this->common_model->_select_list('board_data');

        $this->_print($data);
    }



    /*  관리자 게시판 통합리스트    */
    public function index() {
        $this->_set_sec(array('x','y'));
        $secParams = $this->_get_sec();
        if(!$this->session->userdata('mbID') && $secParams['bodID']!='notice'){
            echo "<script>if(confirm('Would you like to sign-in?')){location.href='/member/login';}else{location.href='".$_SERVER['HTTP_REFERER']."';}</script>";
        }
        if($secParams['secTxt'])
            $secParams['secTxt'] = urldecode($secParams['secTxt']);
        $data = $secParams;
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$secParams['bodID']));
        $data["cfg_bodName"] = $data["boInfo"]["bodName"];

        $secParams["oKey"] = "boGroup DESC, boStep ASC"; $secParams["oType"] = "";
        $secParams["offset"] = isset($secParams["page"]) ? $secParams["page"] : "0";
        $secParams["limit"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $secParams["boIsDelete"] = 'NO';
        $secParams["boStep"] = 0;
        $totalParams = array('bodID'=>$secParams['bodID']);
        if($secParams['bodID'] == "qna")    $totalParams["mbID"] = $secParams["mbID"] = $this->session->userdata('mbID');

        unset($secParams['board']);
        $data["total"] = $this->common_model->_select_cnt('board_data',$totalParams);
        $result["cnt"] = $this->common_model->_select_cnt('board_data',$secParams);
        $result["list"] = $this->board_model->_select_list($secParams);
        $pager["CNT"] = $data['cnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);


        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $data['today7day'] = date("Ymd", strtotime(date('Ymd')." -7 day"));
        $data['today15day'] = date("Ymd", strtotime(date('Ymd')." -15 day"));
        $data['today1month'] = date("Ymd", strtotime(date('Ymd')." -1 month"));
        $data['today2month'] = date("Ymd", strtotime(date('Ymd')." -2 month"));
        $data['today'] = date('Ymd');

        if($secParams['bodID'] == "qna"){
            $qnaParams['bodID'] = $secParams['bodID'];
            for($i=0; $i<count($result["list"]); $i++){
                $qnaParams['boGroup'] = $result["list"][$i]['boNO'];
                $qnaParams["boIsDelete"] = 'NO';
                $result["tmpList"] = $this->board_model->_private_select_list($qnaParams);
                for($j=0; $j<count($result['tmpList']); $j++){
                    if($result["list"][$i]['boNO'] == $result['tmpList'][$j]['boParent']){
                        $result['list'][$i]['answer'] = 'YES';
                    }
                }

                //$result["qnaList"] = ($result["qnaList"]) ? array_merge($result["qnaList"], $result["tmpList"]) : $result["tmpList"];
            }
            //$data["list"] = $result["qnaList"];
        }
        $data["list"] = $result["list"];
        // 게시글 메뉴
        $data['menu_rs'] = $this->common_model->_select_list('board_cfg');
        unset($data["boInfo"]);
        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('board_data',array('boNO'=>$params['boNO']));
        unset($params['board']);
        $params['boHit'] = $data['boHit'] + 1;
        $this->common_model->_update('board_data',$params,array('boNO'=>$params['boNO']));
        $data['bo_mbID'] = $data['mbID'];
        $data['bodID'] = $params['bodID'];
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["cfg_bodName"] = $data["boInfo"]["bodName"];

        $secParams["oKey"] = "boGroup DESC, boStep ASC"; $secParams["oType"] = ""; $secParams['boGroup'] = $data['boGroup'];
        unset($params['board']);
        $data["cnt"] = $this->common_model->_select_cnt('board_data',$secParams);
        $data["list"] = $this->board_model->_select_list($secParams);
        $params["oKey"] = "bcoGroup ASC, bcoStep ASC";        $params["oType"] ="";
        $data['bco_rs'] = $this->common_model->_select_list('comment_data',array('boNO'=>$params['boNO']));
        $this->_print($data);
    }



    /*  관리자 게시판 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('board_data',array('boNO'=>$params['boNO']));
        $data['boMember'] = $this->common_model->_select_row('member_data',array('mbID'=>$data['mbID']));
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data["cfg_bodName"] = $data["boInfo"]["bodName"];
        $data['menu_rs'] = $this->common_model->_select_list('board_data');
        $this->_print($data);
    }



    /*  관리자 게시판의 게시글 답글    */
    public function reply() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['boInfo'] = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));
        $data['bodID'] = $params['bodID'];
        $data['boNO'] = $params['boNO'];
        $data["cfg_bodName"] = $data["boInfo"]["bodName"];
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
            redirect(base_url("/board/index/bodID/".$params['bodID']));


            // 관리자 게시글 수정
        } else if($params['mode'] == "modify"){
            $params['boModDate'] = 'NOW()';

            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('board_data',$params,array('bodID'=>$params['bodID'],'boNO'=>$params['boNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/board/index/bodID/".$params['bodID']));
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
            redirect(base_url("/board/view/bodID/".$params['bodID']."/boNO/".$data['boNO']));

         //게시글 삭제
        }else if($params['mode'] == 'delete'){
            $params['boIsDelete'] = "YES";
            $params['boModDate'] = 'NOW()';
            unset($params['mode']);

            $this->common_model->_update('board_data',$params,array('boNO'=>$params['boNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/board/index/bodID/".$params['bodID']));
        }
    }

}

?>
