<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mainFocus extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('mainfocus_model');
        $this->load->model('content_model');
        $this->load->model('program_model');
    }

    public function write() {
        $data['prList'] = $this->common_model->_select_list('program_data',array("LENGTH(prCode)"=>3,"oKey"=>"prCode", "oType"=>"asc"));

        $this->_print($data);
    }

    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['mainFocus']);

        $secParams["oKey"] = "mFOrder"; $secParams["oType"] = "ASC";
        $result["cnt"] = $this->common_model->_select_cnt('mainFocus_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('mainFocus_data',$secParams);

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }

    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('mainFocus_data',array('mFNO'=>$params['mFNO']));
        if($data['mFType']=='CONTENT'){
            $content = $this->common_model->_select_row('content_data',array('ctNO'=>$data['ctNO']));
            $data['prCode'] = $content['prCode'];
            $data['ctNO_cate'] = $this->common_model->_select_list('content_data',array("prCode"=>$content['prCode']));
            $prData = $this->common_model->_select_row('program_data',array('prCode'=>$content['prCode']));
            $data['prPreCode'] = $prData['prPreCode'];
            $secParams["oKey"] = "prCode";
            $secParams["oType"] = "ASC";
            $secParams["prPreCode"] = $prData['prPreCode'];
            $data["prList"] = $this->common_model->_select_list('program_data',$secParams);
        }
        elseif($data['mFType']=='PROGRAM'){
            $prData = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
            $data['prPreCode'] = $prData['prPreCode'];
            $secParams["oKey"] = "prCode";
            $secParams["oType"] = "ASC";
            $secParams["prPreCode"] = $prData['prPreCode'];
            $data["prList"] = $this->common_model->_select_list('program_data',$secParams);
        }

        $this->_print($data);
    }
    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('mainFocus_data',array('mFNO'=>$params['mFNO']));
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        if($params['mode'] == "write"){
            if($_FILES['mFThumb']['name']){
                $upload = $this->common_class->upload_file('mFThumb', 'mainFocus', false);
                $params = array_merge ($params, $upload);
            }

            if($params['mFType']=='PROGRAM') unset($params['ctNO']);
            elseif($params['mFType']=='CONTENT') unset($params['prCode']);
            elseif($params['mFType']=='BANNER'){
                unset($params['prCode']);  unset($params['ctNO']);
            }
            if(!$params['mFEDate']) $params['mFEDate'] = '9999-12-31';
            unset($params['mode']);
            $params['mFRegDate'] = 'NOW()';
            $params['mFModDate'] = 'NOW()';
            $this->mainfocus_model->_insert($params);
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/mainFocus/index"));

            // 관리자 컨텐츠 수정
        } else if($params['mode'] == "modify"){
            if($_FILES['mFThumb']['name']){
                $upload = $this->common_class->upload_file('mFThumb', 'mainFocus', false, array('key'=>'mFNO', 'val'=>$params['mFNO']));
                $params = array_merge ($params, $upload);
            }
            if($params['mFType']=='PROGRAM') unset($params['ctNO']);
            elseif($params['mFType']=='CONTENT') unset($params['prCode']);
            elseif($params['mFType']=='BANNER'){
                unset($params['prCode']);  unset($params['ctNO']);
            }
            if(!$params['mFEDate']) $params['mFEDate'] = '9999-12-31';
            $params['mFModDate'] = 'NOW()';
            unset($params['mode']);

            $this->common_model->_update('mainFocus_data',$params,array('mFNO'=>$params['mFNO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/mainFocus/view/mFNO/".$params['mFNO']));
            // 관리자 컨텐츠 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $mFNO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($mFNO); $i++){
                    $this->common_model->_delete('mainFocus_data',array('mFNO'=>$mFNO[$i]));
                }
            }else{
                $this->common_model->_delete('mainFocus_data',array('mFNO'=>$params['mFNO']));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/mainFocus/index"));
        }

    }

    /* ajax_process */
    public function ajax_process(){
        $params = $this->input->post();
        if($params['mode']=="getContent"){
            $data = $this->common_model->_select_list('content_data',array("oKey"=>"ctNO", "oType"=>"desc","prCode"=>$params['prCode']));
            echo json_encode($data);
        }
    }
}