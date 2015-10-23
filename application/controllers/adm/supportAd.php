<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class supportAd extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('supportad_model');
    }

    public function write() {
        $this->_print();
    }

    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['supportAd']);

        $secParams["oKey"] = "sAOrder"; $secParams["oType"] = "DESC";
        $result["cnt"] = $this->common_model->_select_cnt('supportAd_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('supportAd_data',$secParams);

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
        $data = $this->common_model->_select_row('supportAd_data',array('sANO'=>$params['sANO']));
        $this->_print($data);
    }
    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('supportAd_data',array('sANO'=>$params['sANO']));
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        if($params['mode'] == "write"){
            if($_FILES['sAThumb']['name']){
                $upload = $this->common_class->upload_file('sAThumb', 'supportAd', false);
                $params = array_merge ($params, $upload);
            }

            unset($params['mode']);
            $params['sARegDate'] = 'NOW()';
            $params['sAModDate'] = 'NOW()';
            $this->supportad_model->_insert($params);
            $path = "./_cache/%cache";      delete_files($path, true);
            redirect(base_url("/adm/supportAd/index"));

            // 관리자 컨텐츠 수정
        } else if($params['mode'] == "modify"){
            if($_FILES['sAThumb']['name']){
                $upload = $this->common_class->upload_file('sAThumb', 'supportAd', false, array('key'=>'sANO', 'val'=>$params['sANO']));
                $params = array_merge ($params, $upload);
            }
            $paramView['sAModDate'] = 'NOW()';
            unset($params['mode']);

            $this->common_model->_update('supportAd_data',$params,array('sANO'=>$params['sANO']));
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/supportAd/view/sANO/".$params['sANO']));

            // 관리자 컨텐츠 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $sANO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($sANO); $i++){
                    $this->common_model->_delete('supportAd_data',array('sANO'=>$sANO[$i]));
                }
            }else{
                $this->common_model->_delete('supportAd_data',array('sANO'=>$params['sANO']));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/supportAd/index"));
        }

    }

}