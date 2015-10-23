<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class mypage_view extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->loginCheck();
        $this->load->model("mypage_model");
    }

    public function index() {

        if($this->session->userdata('mbID'))
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        $data["ctView"] = $this->mypage_model->_select_ctView_list(array('mbID'=>$data["member"]["mbID"]));

        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';

        for($i=0; $i<count($data["ctView"]); $i++){
            $baseParams["prCode"] = $data["ctView"][$i]["prCode"];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            for($j=0; $j<count($baseContent); $j++) {
                if($data["ctView"][$i]["ctNO"] == $baseContent[$j]['ctNO']){
                    $data["ctView"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }
            // 프로그램 네이게이션 만들기
            if(strlen($data["ctView"][$i]['prCode'])==3){
                $data["ctView"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prCode']));

            }elseif(strlen($data["ctView"][$i]['prCode'])==6){
                $data["ctView"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prPreCode']));
                $data["ctView"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prCode']));

            }elseif(strlen($data["ctView"][$i]['prCode'])==9){
                $data["ctView"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctView"][$i]['prCode'],0,3)));
                $data["ctView"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prPreCode']));
                $data["ctView"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prCode']));
            }elseif(strlen($data["ctView"][$i]['prCode'])==12){
                $data["ctView"][$i]['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctView"][$i]['prCode'],0,3)));
                $data["ctView"][$i]['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["ctView"][$i]['prCode'],0,6)));
                $data["ctView"][$i]['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prPreCode']));
                $data["ctView"][$i]['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data["ctView"][$i]['prCode']));
            }
        }
        $vcCnt = ((count($data['ctView'])%6) == 0)? count($data['ctView']) / 6 : (int)(count($data['ctView']) / 6) + 1;
        $idx = 0;
        for($i=0; $i<$vcCnt; $i++) {
            for($j=0; $j<6; $j++){
                if($data['ctView'][$idx]) $data["viewContent"][$i]["page"][] = $data['ctView'][$idx++];
            }
        }

        $data["nwView"] = $this->mypage_model->_select_nwView_list(array('mbID'=>$data["member"]["mbID"]));

        $baseParams['oKey'] = 'nwRegDate';
        $baseParams['oType'] = 'DESC';

        for($i=0; $i<count($data["nwView"]); $i++){
            // 프로그램 네이게이션 만들기
            if(strlen($data["nwView"][$i]['ncCode'])==3){
                $data["nwView"][$i]['nwDepth1'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncCode']));

            }elseif(strlen($data["nwView"][$i]['ncCode'])==6){
                $data["nwView"][$i]['nwDepth1'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncPreCode']));
                $data["nwView"][$i]['nwDepth2'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncCode']));

            }elseif(strlen($data["nwView"][$i]['ncCode'])==9){
                $data["nwView"][$i]['nwDepth1'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>substr($data["nwView"][$i]['ncCode'],0,3)));
                $data["nwView"][$i]['nwDepth2'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncPreCode']));
                $data["nwView"][$i]['nwDepth3'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncCode']));
            }elseif(strlen($data["nwView"][$i]['ncCode'])==12){
                $data["nwView"][$i]['nwDepth1'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>substr($data["nwView"][$i]['ncCode'],0,3)));
                $data["nwView"][$i]['nwDepth2'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>substr($data["nwView"][$i]['ncCode'],0,6)));
                $data["nwView"][$i]['nwDepth3'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncPreCode']));
                $data["nwView"][$i]['nwDepth4'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data["nwView"][$i]['ncCode']));
            }
        }
        $vnCnt = ((count($data['nwView'])%6) == 0)? count($data['nwView']) / 6 : (count($data['nwView']) / 6) + 1;
        $idx = 0;

        for($i=0; $i<$vnCnt; $i++) {
            for($j=0; $j<6; $j++){
                if($data['nwView'][$idx]) $data["viewNews"][$i]["page"][] = $data['nwView'][$idx++];
            }
        }
        $this->_print($data);
    }

    public function delete() {
        $params = $this->input->post();

        $ctParams = $params["ctNO"];
        $nwParams = $params["nwNO"];
        $prParams = $params["prCode"];

        $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        for($i=0; $i<count($ctParams); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["mpSection"] = "VIEW";
            $secParams[$i]["mpType"] = "CONTENT";
            $secParams[$i]["ctNO"] = $params["ctNO"][$i];
            $this->common_model->_delete('mypage_log',$secParams[$i]) ;
        }

        for($i=0; $i<count($prParams); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["mpSection"] = "VIEW";
            $secParams[$i]["mpType"] = "PROGRAM";
            $secParams[$i]["prCode"] = $params["prCode"][$i];
            $this->common_model->_delete('mypage_log',$secParams[$i]) ;
        }

        for($i=0; $i<count($nwParams); $i++){
            $secParams[$i]["mbID"] = $data["member"]["mbID"];
            $secParams[$i]["mpSection"] = "VIEW";
            $secParams[$i]["mpType"] = "NEWS";
            $secParams[$i]["nwNO"] = $params["nwNO"][$i];
            $this->common_model->_delete('mypage_log',$secParams[$i]) ;
        }
        redirect(base_url("/mypage_view/index"));
        echo '<pre>'; print_r($params );echo '</pre>';
        //$this->_print($data);
    }

}