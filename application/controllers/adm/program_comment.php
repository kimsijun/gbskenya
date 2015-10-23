<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   프로그램 댓글 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class program_comment extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('program_comment_model');
    }

    /*  관리자 컨텐츠 댓글 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['program_comment']);

        $secParams["oKey"] = "pbcoGroup DESC,pbcoStep ASC"; $secParams["oType"] = "";
        $result["cnt"] = $this->common_model->_select_cnt('program_comment_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->program_comment_model->_select_list($secParams);

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        if($params['mode'] == "delete"){
            for($i=0; $i<count($params['chk']); $i++){
                $pbcoNO[] = $params['chk'][$i];
            }
            unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
            $params['pbcoIsDelete'] = 'YES';
            for($i=0; $i<count($pbcoNO); $i++){
                $this->db->set('pbcoModDate', 'NOW()', false);
                $this->common_model->_update('program_comment_data',$params,array('pbcoNO'=>$pbcoNO[$i]));
            }
        }
        $path = "./_cache/%cache";      delete_files($path, true);
        redirect(base_url("/adm/program_comment/index"));
    }

}

?>