<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   게시판 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   14. 5. 15.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class cfg_board extends common {


    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('cfg_board_model'); $this->load->model('board_model');
    }


    /*  관리자 게시판 생성    */
    public function write() {
        $this->_print();
    }



    /*  관리자 게시판 통합리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['cfg_board']);
        $secParams["oKey"] = "bodRegDate DESC"; $secParams["oType"] = "";
        $result["cnt"] = $this->common_model->_select_cnt("board_cfg",$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->cfg_board_model->_select_list($secParams);

        $pager["CNT"] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));

        $this->_print($data);
    }


    /*  관리자 게시판 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('board_cfg',array('bodID'=>$params['bodID']));

        $this->_print($data);
    }
    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        // 관리자 게시판 생성
        if($params['mode'] == "write"){
            $params['bodModDate'] = 'NOW()';
            $params['bodRegDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_insert('board_cfg',$params);


            // 관리자 게시판 수정
        } else if($params['mode'] == "modify"){
            $params['bodModDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('board_cfg',$params,array('bodID'=>$params['bodID']));

            // 관리자 게시판 삭제
        } else if($params['mode'] == "delete"){
            $params['bodIsDelete'] = 'YES';
            unset($params['mode']);unset($params['checkAll_length']);
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $bodID[] = $params['chk'][$i];
                }
                unset($params['chk']);
                for($i=0; $i<count($bodID); $i++){
                    $params['bodModDate'] = 'NOW()';
                    $this->common_model->_update('board_cfg',$params,array('bodID'=>$bodID[$i]));
                }
            }else{
                $params['bodModDate'] = 'NOW()';
                $this->common_model->_update('board_cfg',$params,array('bodID'=>$params['bodID']));
            }

            // 게시판 일괄 삭제
        } else if($params['mode'] == "all_delete_cfg"){
            $this->cfg_board_model->all_delete_cfg($params);
        }

        redirect(base_url("/adm/cfg_board/index"));
    }


}

?>
