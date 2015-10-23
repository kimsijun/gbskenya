<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   생방송 콘텐츠 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 7. 27.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class livecontent extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('livecontent_model');
    }


    /*  관리자 생중계 컨텐츠 생성    */
    public function write() {
        $data['prList'] = $this->common_model->_select_list('program_data',array("LENGTH(prCode)"=>3,"oKey"=>"prCode", "oType"=>"asc"));

        for($i=1; $i<26; $i++)  $data['lcDurationH'][$i] = $i;
        for($i=0; $i<60; $i++)  $data['lcDurationM'][$i] = $i;
        $data['lcDurationS'] = $data['lcDurationM'];
        $this->_print($data);
    }



    /*  관리자 생중계 컨텐츠 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();

        unset($secParams['livecontent']);
        $secParams["oKey"] = "A.lcNO";
        $secParams["oType"] = "DESC";
        $result["cnt"] = $this->livecontent_model->_select_cnt($secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->livecontent_model->_select_list($secParams);

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $data["list"] = $result["list"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /*  관리자 생중계 컨텐츠 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->livecontent_model->_select_row($params['lcNO']);

        $this->_print($data);
    }


    /*  관리자 생중계 컨텐츠 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->livecontent_model->_select_row($params['lcNO']);

        $prData = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
        $data['prPreCode'] = $prData['prPreCode'];
        $secParams["oKey"] = "prCode";
        $secParams["oType"] = "ASC";
        $secParams["prPreCode"] = $prData['prPreCode'];
        $data["prList"] = $this->common_model->_select_list('program_data',$secParams);

        for($i=1; $i<26; $i++)  $data['lcDurationH'][$i] = $i;
        for($i=0; $i<60; $i++)  $data['lcDurationM'][$i] = $i;
        $data['lcDurationS'] = $data['lcDurationM'];

        $arrDuration = explode(':',$data['lcDuration']);
        $data['lcDuration_hour'] = $arrDuration[0];
        $data['lcDuration_minute'] = $arrDuration[1];
        $data['lcDuration_second'] = $arrDuration[2];
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        // 관리자 생중계 컨텐츠 생성처리
        if($params['mode'] == "write"){
            $params['lcDuration'] = $params['lcDurationH']. ':' .$params['lcDurationM']. ':' .$params['lcDurationS'];
            unset($params['lcDurationH']);unset($params['lcDurationM']);
            unset($params['lcDurationS']);unset($params['mode']);
            $params['lcRegDate'] = 'NOW()';
            $params['lcModDate'] = 'NOW()';
            $this->livecontent_model->_insert($params);
            redirect(base_url("/adm/livecontent/index"));

            // 관리자 생중계 컨텐츠 수정처리
        } else if($params['mode'] == "modify"){
            $params['lcDuration'] = $params['lcDurationH']. ':' .$params['lcDurationM']. ':' .$params['lcDurationS'];
            if(!$params['ctNO']) unset($params['prCode']);
            unset($params['lcDurationH']);unset($params['lcDurationM']);unset($params['lcDurationS']);unset($params['mode']);
            $params['lcModDate'] = 'NOW()';
            $this->common_model->_update('livecontent_data',$params,array('lcNO'=>$params['lcNO']));
            redirect(base_url("/adm/livecontent/view/lcNO/".$params['lcNO']));

            // 관리자 생중계 컨텐츠 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $lcNO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($lcNO); $i++){
                    $this->common_model->_delete('livecontent_data',array('lcNO'=>$lcNO[$i]));
                }
            }else{
                $this->common_model->_delete('livecontent_data',array('lcNO'=>$params['lcNO']));
            }
            redirect(base_url("/adm/livecontent/index"));
        }


    }


    /*  AJAX 처리 메소드    */
    public function ajax_process() {
        $params = $this->input->post();

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  프로그램 별 컨텐츠 불러오기
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($params['mode'] == "getContent"){
            $data = $this->common_model->_select_list('content_data',array("prCode"=>$params['prCode']));

            $result['data'] = $data;
            $result['templateHtml'] = $this->cfg["module_dir_name"].'/_widgets/selectContent.html';
            $html = $this->_print($result, TRUE);
            $params['html'] = $html;
            echo json_encode($params);


        }
    }

}