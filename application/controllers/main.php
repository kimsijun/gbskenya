<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   메인 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class main extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("content_model");
        $this->load->model('mainfocus_model');
        $this->load->model('advertise_model');
        $this->load->model('popupschedule_model');
        $this->load->model('news_model');
        $this->load->model('global_model');
    }

   public function index() {
        $json = read_file('./assets/json/config/main.json');
        $data['config'] = json_decode($json,true);
        $data['config']['fst']['totalWidth'] = ($data['config']['fst']['width'] + ($data['config']['fst']['margin'] * 2) ) * $data['config']['fst']['total'];
        $data['config']['fst']['perPageWidth'] = ($data['config']['fst']['width'] + ($data['config']['fst']['margin'] * 2) ) * $data['config']['fst']['perPage'];
        $data['config']['fst']['slideCount'] = ($data['config']['fst']['total'] / $data['config']['fst']['perPage']) - 1;
        for($i=0; $i<$data['config']['fst']['slideCount']; $i++)     $data['config']['fst']['pageControlCount'][] = 1;

        /*
        | -------------------------------------------------------------------
        | # 메인포커스 START
        | -------------------------------------------------------------------
        */
        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';
        $data['mainFocus'] = $this->common_model->_select_list('mainFocus_data', array('oKey'=>'mFOrder','oType'=>'asc'));

       /*
       | -------------------------------------------------------------------
       | # NEWS START
       | -------------------------------------------------------------------
       */
       $secParams['oKey'] = "nwRegDate";
       $secParams['oType'] = "DESC";
       $secParams['limit'] = 18;
       $data['goodnewsToday'] = $this->news_model->_select_list($secParams);
       for($i=0; $i<count($data['goodnewsToday']); $i++){
           if(intval(($i/3)%2)==0){
               $data["today1"][] = $data["goodnewsToday"][$i];
           }else{
               $data["today2"][] = $data["goodnewsToday"][$i];
           }
       }
       for($j=0; $j<count($data["today1"]); $j++){
           $data["today1"][$j]['nwName'] = $this->common_class->cut_str_han($data['today1'][$j]['nwName'], 15,"...");
           if(strpos($data["today1"][$j]['nwContent'],'<img')!=false){
               $specialNewsArr = explode('<',$data["today1"][$j]['nwContent']);
               $data["today1"][$j]["photo"] = '<'.$specialNewsArr[2];
           }
       }
       for($j=0; $j<count($data["today2"]); $j++){
           $data["today2"][$j]['nwName'] = $this->common_class->cut_str_han($data['today2'][$j]['nwName'], 15,"...");
           if(strpos($data["today2"][$j]['nwContent'],'<img')!=false){
               $specialNewsArr = explode('<',$data["today2"][$j]['nwContent']);
               $data["today2"][$j]["photo"] = '<'.$specialNewsArr[2];
           }
       }

       $paramSP['nwIsHeadline'] = "YES";
       $paramSP['oKey'] = "nwRegDate";
       $paramSP['oType'] = "desc";
       $data['spNews'] = $this->news_model->_select_row($paramSP);

       unset($secParams);

       $secParams['oKey'] = "ctRegDate";
       $secParams['oType'] = "DESC";
       $secParams['limit'] = $data['config']['trd']['total'];

       /*
        | -------------------------------------------------------------------
        | # programs
        | -------------------------------------------------------------------
        */
       $programList = $this->common_model->_select_list('program_data');
       $program = $this->common_model->_select_list('program_data',array('limit'=>$data['config']['fth']['total'], 'LENGTH(prCode)'=>6));
       for($i=0; $i<count($programList);$i++){
           for($j=0;$j<count($program);$j++){
               if(strlen($programList[$i]['prCode'])>strlen($program[$j]['prCode']) && $program[$j]['prCode']==substr($programList[$i]['prCode'],0,strlen($program[$j]['prCode']))){
                   $program[$j]['prIsSub'] = 'YES';
               }
           }
       }

       for($i=0; $i<count($program); $i++) {
           if($i < 4)          $data['programRow1'][] = $program[$i];
           else                $data['programRow2'][] = $program[$i];
       }

       /*
       | -------------------------------------------------------------------
       | # 게시판(공지사항)
       | -------------------------------------------------------------------
       */
        $secParams["oKey"] = "boGroup DESC, boStep ASC"; $secParams["oType"] = "";
        $secParams["bodID"] = "notice";
        $secParams["boIsDelete"] = "NO";
        $secParams["limit"] = 5;
        $data['notice'] = $this->common_model->_select_list('board_data',$secParams);
        $this->_print($data);
    }
    
    public function advertise() {
        $this->_print();
    }

    public function contact() {
        $this->_print();
    }

    public function companyInfo() {
        $this->_print();
    }

    public function terms() {
        $this->_print();
    }

    public function privacy() {
        $this->_print();
    }

    public function download() {
        $this->_print();
    }

    public function errorReport() {
        $params = $this->_get_sec();
        $this->_print($params);
    }

    public function process(){
        $params = $this->input->post();

        if($params['mode']=="advertise"){

            $params['adEmail'] = $params['adEmail1']."@".$params['adEmail2'];
            $params['adRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['adRegDate'] = 'NOW()';
            $params['adModDate'] = 'NOW()';
            unset($params['mode']);unset($params['adEmail1']);unset($params['adEmail2']);

            $this->advertise_model->_insert($params);
            $path = "./_cache/%cache"; delete_files($path, true);
            echo "<script>alert('Registered. We will contact you within the next few days.');location.href='/main/advertise';</script>";

        } else if($params['mode']=="errorReport"){
            $params['codeSeq'] = SITE_NO;
            $this->global_model->_insert($params);
            $path = "./_cache/%cache"; delete_files($path, true);
            echo "<script>alert('Registered. Thanks.');location.href='/main/index';</script>";
        }
    }

}