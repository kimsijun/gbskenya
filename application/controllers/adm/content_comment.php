<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   콘텐츠 댓글 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/


class content_comment extends common {


    public function __construct(){
        parent::__construct();
    }

    /*  관리자 컨텐츠 댓글 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['content_comment']);
        $secParams["oKey"] = "cbcoGroup DESC, cbcoStep ASC"; $secParams["oType"] = "";
        $result["cnt"] = $this->common_model->_select_cnt('content_comment_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('content_comment_data',$secParams);
        for($i=0; $i<count($result['list']);$i++){
            $result["list"][$i]['content'] = $this->common_model->_select_row('content_data',array('ctNO'=>$result['list'][$i]['ctNO']));
            $result["list"][$i]['member'] = $this->common_model->_select_row('member_data',array('mbID'=>$result['list'][$i]['mbID']));
        }
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
                $cbcoContent = $this->common_model->_select_row('content_comment_data',array('cbcoNO'=>$params['chk'][$i]));
                $content[] = $cbcoContent['ctNO'];
                $cbcoNO[] = $params['chk'][$i];
            }
            unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
            for($i=0; $i<count($cbcoNO); $i++){
                $params['cbcoModDate'] = 'NOW()';
                $this->common_model->_delete('content_comment_data',array('cbcoNO'=>$cbcoNO[$i],'ctNO'=>$content[$i]));
            }

        }
        $path = "./_cache/%cache";      delete_files($path, true);
        redirect(base_url("/adm/content_comment/index"));
    }

}

?>