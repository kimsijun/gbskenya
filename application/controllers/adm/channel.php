<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   채널 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class channel extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }

    /*  관리자 채널 생성    */
    public function write() {
        $this->_print();
    }
    
    /*  관리자 채널 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['channel']);
        $secParams["oKey"] = "chNO"; $secParams["oType"] = "desc";
        $result["cnt"] = $this->common_model->_select_cnt("channel_data",$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('channel_data', $secParams);

        $pager["CNT"] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /*  관리자 채널 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('channel_data',array('chNO'=>$params['chNO']));
        $chLanguage = explode('-',$data['chLanguage']);
        unset($data['chLanguage']);
        $data['chLanguage']['cn'] = $chLanguage[0];
        $data['chLanguage']['es'] = $chLanguage[1]; $data['chLanguage']['en'] = $chLanguage[2];
        $this->_print($data);
    }


    /*  관리자 채널 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('channel_data',array('chNO'=>$params['chNO']));
        $chLanguage = explode('-',$data['chLanguage']);
        unset($data['chLanguage']); $data['chLanguage']['cn'] = $chLanguage[0];
        $data['chLanguage']['es'] = $chLanguage[1]; $data['chLanguage']['en'] = $chLanguage[2];
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        // 관리자 채널 생성처리
        if($params['mode'] == "write"){
            $params['chUstream'] = ($params['chUstream'])? addslashes($params['chUstream']) : "";
            $params['chYoutube'] = ($params['chYoutube'])? addslashes($params['chYoutube']) : "";
            $params['chTvpot'] = ($params['chTvpot'])? addslashes($params['chTvpot']) : "";
            $params['chWowza'] = ($params['chWowza'])? addslashes($params['chWowza']) : "";
            $params['chLangCN'] = ($params['chLangCN'])? addslashes($params['chLangCN']) : "";
            $params['chLangES'] = ($params['chLangES'])? addslashes($params['chLangES']) : "";
            $params['chLangEN'] = ($params['chLangEN'])? addslashes($params['chLangEN']) : "";

            $params['chLanguage'] = $params['chLanguage']['cn'].'-'.$params['chLanguage']['es'].'-'.$params['chLanguage']['en'];

            unset($params['mode']);
            $params['chModDate'] = 'NOW()';
            $params['chRegDate'] = 'NOW()';
            $this->common_model->_insert('channel_data',$params);
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/channel/index"));
            // 관리자 채널 수정처리
        } else if($params['mode'] == "modify"){
            $params['chUstream'] = ($params['chUstream'])? addslashes($params['chUstream']) : "";
            $params['chYoutube'] = ($params['chYoutube'])? addslashes($params['chYoutube']) : "";
            $params['chTvpot'] = ($params['chTvpot'])? addslashes($params['chTvpot']) : "";
            $params['chWowza'] = ($params['chWowza'])? addslashes($params['chWowza']) : "";
            $params['chLangCN'] = ($params['chLangCN'])? addslashes($params['chLangCN']) : "";
            $params['chLangES'] = ($params['chLangES'])? addslashes($params['chLangES']) : "";
            $params['chLangEN'] = ($params['chLangEN'])? addslashes($params['chLangEN']) : "";

            $params['chLanguage'] = $params['chLanguage']['cn'].'-'.$params['chLanguage']['es'].'-'.$params['chLanguage']['en'];

            unset($params['isModChYoutube']);unset($params['isModChUstream']);unset($params['isModChTvpot']);
            unset($params['mode']);
            $params['chModDate'] = 'NOW()';
            $this->common_model->_update('channel_data', $params, array('chNO'=>$params['chNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/channel/view/chNO/".$params['chNO']));
            // 관리자 채널 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $chNO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($chNO); $i++){
                    $this->common_model->_delete('channel_data',array('chNO'=>$chNO[$i]));
                }
            }else{
                $this->common_model->_delete('channel_data',array('chNO'=>$params['chNO']));
            }

            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/channel/index"));
        }


    }

}