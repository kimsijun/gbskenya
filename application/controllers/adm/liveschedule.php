<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   편성표 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 7. 27.
| @ PURPOSE 언제 어디서든 온라인으로 편성할 수 있도록 함
| 시간 계산을 일일이 안해도 됨
| -------------------------------------------------------------------
*/

class liveschedule extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('liveschedule_model');
        $this->load->model('popupschedule_dt_model');
    }


    /*  관리자 편성표 생성    */
    public function write() {
        $params = $this->_get_sec();
        $this->_print($params);
    }

    /*  관리자 편성표 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        if(!$secParams["lsDate"]) $secParams["lsDate"] = date("Y-m-d");
        if(!$secParams["chNO"]) $secParams["chNO"] = '1';

        $json = read_file('./assets/json/liveschedule/schedule.json');
        $data = json_decode($json,true);

        $data["list"] = $this->liveschedule_model->_select_list($secParams);
        $data["liveList"] = $this->popupschedule_dt_model->_select_list($secParams);

        $data['prList'] = $this->common_model->_select_list('program_data',array("LENGTH(prCode)"=>3,"oKey"=>"prCode", "oType"=>"asc"));
        $data['outProgram'] = $this->common_model->_select_list('livecontent_data',array("prCode"=>"","oKey"=>"lcName", "oType"=>"asc"));
        $data['channel'] = $this->common_model->_select_list('channel_data',array("oKey"=>"chNO", "oType"=>"asc"));
        for($i=0; $i<count($data['list']); $i++){
            if($data['list'][$i]['lcNO'])    $data['curSTime'] = $data['list'][$i]['lsETime'];
        }

        $data["secParams"] = $secParams;
        $this->_print($data);
    }

    /*  관리자 편성표 정보 수정    */
    public function modify() {
        $params = $this->input->post();

        $data = $this->common_model->_select_row('liveschedule_data',array('lsNO'=>$params['lsNO']));
        $data['livecontent'] = $this->common_model->_select_row('livecontent_data',array('lcNO'=>$data['lcNO']));
        $data['channel'] = $this->common_model->_select_list('channel_data',array("oKey"=>"chNO", "oType"=>"asc"));
        $data['prList'] = $this->common_model->_select_list('program_data',array("LENGTH(prCode)"=>3,"oKey"=>"prCode", "oType"=>"asc"));
        $data['outProgram'] = $this->common_model->_select_list('livecontent_data',array("prCode"=>"","oKey"=>"lcNO", "oType"=>"desc"));

        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        if($params['mode'] == "modify"){
            $data = $this->common_model->_select_row('liveschedule_data',array('lsNO'=>$params['lsNO']));

            if($params['lsIsProgram']!="YES") $params['lcNO'] = $params['out_lcNO'];
            else{
                if($params['prCode']) $params['lcNO'] = $params['in_lcNO'];
            }

            unset($params['mode']);
            unset($params['in_lcNO']);
            unset($params['out_lcNO']);
            unset($params['lsIsProgram']);
            unset($params['prCode']);

            if($params['lcNO']){
                $lcData = $this->common_model->_select_row('livecontent_data',array('lcNO'=>$params['lcNO']));
            }else{
                $lcData = $this->common_model->_select_row('livecontent_data',array('lcNO'=>$data['lcNO']));
            }

            if($params['lsSTime']){
                $zeroTime = strtotime('00:00:00') * 2;
                $diff = strtotime($params['lsSTime']) + strtotime($lcData['lcDuration']) - $zeroTime;
                $hour = (int)($diff / 3600);    $diff = $diff % 3600;
                $minute = (int)($diff / 60);    $diff = $diff % 60;
                $second = $diff;
                $params['lsETime'] = $hour.':'.$minute.':'.$second;
            }


            $arrETime = explode(':', $params['lsETime']);
            $lsETime = $arrETime[0];

            if($lsETime >= "24") $params['lsETime'] = "24:00:00";
            $params['lsModDate'] = 'NOW()';
            $this->common_model->_update('liveschedule_data',$params, array('lsNO'=>$params['lsNO']));

            $list = $this->common_model->_select_list('liveschedule_data',array('lsDate'=>$data['lsDate']));

            for($i=0;$i<count($list);$i++){
                $json_data['cfgSTime'] = $list[$i]['lsETime'];
            }
            $jsonData = json_encode($json_data);
            write_file('./assets/json/liveschedule/schedule.json', $jsonData);
            $path = "./_cache/%cache"; delete_files($path, true);

            redirect(base_url("/adm/liveschedule/index/chNO/".$data['chNO']."/lsDate/".$data['lsDate']));

        } else if($params['mode'] == "importLsData") {
            $paramDel['chNO'] = $params['chNO'];
            for($i=0; $i<7; $i++) {
                //현재일(오늘)부터 일주일 동안의 날짜 계산
                $delDate[$i] = date("Y-m-d",strtotime ("+".$i." days",  strtotime($params['importLsDate'])));
                //기존데이터 삭제
                $paramDel['lsDate'] = $delDate[$i];
                $this->common_model->_delete("liveschedule_data",$paramDel);

                //선택일부터 일주일 동안의 날짜 계산
                $tempDate[$i] = date("Y-m-d",strtotime ("+".$i." days",  strtotime($params['secLsDate'])));
                //해당 날짜 편성표 추가
                $secParams['lsDate'] = $tempDate[$i];
                $secParams['chNO'] = $params['chNO'];
                $secParams["oKey"] = "lsSTime";
                $secParams["oType"] = "ASC";

                $data = $this->common_model->_select_list("liveschedule_data",$secParams);

                for($j=0; $j<count($data); $j++) {
                    $data[$j]['lsDate'] = $delDate[$i];
                    $data[$j]['lsIsView'] = 'NO';
                    $data[$j]['lsIsSpecial'] = 'NO';
                    $data[$j]['lsModDate'] = 'NOW()';
                    $data[$j]['lsRegDate'] = 'NOW()';
                    unset($data[$j]['lsNO']);
                    $this->liveschedule_model->_insert($data[$j]);
                    $json_data['cfgSTime'] = $data[$j]['lsETime'];
                    $jsonData = json_encode($json_data);
                    write_file('./assets/json/liveschedule/schedule.json', $jsonData);
                }
            }
            redirect(base_url("/adm/liveschedule/index/chNO/".$params['chNO']."/lsDate/".$params['importLsDate']));
        }



    }

    /*  AJAX 처리 메소드    */
    public function ajax_process() {
        $params = $this->input->post();
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  프로그램 별 컨텐츠 불러오기
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($params['mode'] == "getContent"){

            $data = $this->common_model->_select_list('livecontent_data',array("prCode"=>$params['prCode']));
            $result['data'] = $data;
            $result['templateHtml'] = 'adm/liveschedule/selectliveContent.html';
            $html = $this->_print($result, TRUE);
            $params['html'] = $html;
            echo json_encode($params);


        } else if($params['mode'] == "add"){
            if($params['lsIsProgram']!="YES") $params['lcNO'] = $params['out_lcNO'];
            else $params['lcNO'] = $params['in_lcNO'];

            unset($params['mode']);
            unset($params['in_lcNO']);
            unset($params['out_lcNO']);
            unset($params['lsIsProgram']);
            unset($params['prCode']);

            $needParam = $params;
            // 편성표에 들어갈 컨텐츠의 시작시간을 구하기 위함(생중계를 제외한 데이터 구하기)
            $json = read_file('./assets/json/liveschedule/schedule.json');
            $json_data = json_decode($json,true);

            $data = $this->liveschedule_model->_select_list($params);
            if(!$data){
                $data['curSTime']='00:00:00';
                $json_data['cfgTime']='00:00:00';
            }
            for($i=0; $i<count($data); $i++){
                if($data['curSTime'] == $data[$i]['lsSTime']) $data['curSTime'] = $data[$i]['lsETime'];
            }
            if($data['curSTime'] == "24:00:00"){
                echo json_encode(array('success'=>'false', 'msg'=>'편성표 등록 초과'));
                exit;
            }

            $params['lsSTime'] = ($data['curSTime']==$json_data['cfgSTime']) ? $data['curSTime'] : $json_data['cfgSTime'];


            // 생중계 컨텐츠 정보 가져오기
            $lsData = $this->common_model->_select_row('livecontent_data',array('lcNO'=>$params['lcNO']));
            $zeroTime = strtotime('00:00:00') * 2;
            $diff = strtotime($params['lsSTime']) + strtotime($lsData['lcDuration']) - $zeroTime;
            $hour = (int)($diff / 3600);    $diff = $diff % 3600;
            $minute = (int)($diff / 60);    $diff = $diff % 60;
            $second = $diff;
            $params['lsETime'] = $hour.':'.$minute.':'.$second;

            $params['lcNO'] = $lsData['lcNO'];
            $params['lsModDate'] = 'NOW()';
            $params['lsRegDate'] = 'NOW()';
            $json_data['cfgSTime'] = $params['lsETime'];
            $jsonData = json_encode($json_data);
            write_file('./assets/json/liveschedule/schedule.json', $jsonData);

            // 추가하는 컨텐츠의 종료시간이 생중계의 시간보다 초과되면 강제로 줄임
            for($i=0; $i<count($data); $i++){
                if(strtotime($params['lsETime']) >= strtotime($data[$i]['lsSTime'])
                    && strtotime($params['lsETime']) <= strtotime($data[$i]['lsETime']))
                    $params['lsETime'] = $data[$i]['lsSTime'];
            }

            // 편성표가 24시간이 넘어갈 경우 강제로 자름
            $arrETime = explode(':', $params['lsETime']);
            $lsETime = $arrETime[0];

            if($lsETime >= "24")
                $params['lsETime'] = "24:00:00";

            $liveParams = $params;
            unset($liveParams['slc_program']);
            $this->liveschedule_model->_insert($liveParams);

            $data = $this->liveschedule_model->_select_list($needParam);
            $result['data'] = $data;
            $result['templateHtml'] = 'adm/liveschedule/livescheduleList.html';
            $html = $this->_print($result, TRUE);
            $result['success'] = "true";
            $result['source'] = $html;
            $path = "./_cache/%cache"; delete_files($path, true);
            echo json_encode($result);


            // 편성표 데이터 삭제
        } else if($params['mode'] == "delete"){
            $secParams['chNO'] = $params['chNO'];
            $secParams['lsDate'] = $params['lsDate'];
            $secParams['oKey'] = "lsSTime";
            $secParams['oType'] = "asc";
            $needParam = $secParams;

            $this->common_model->_delete('liveschedule_data',array('lsNO'=>$params['lsNO']));

            $data = $this->liveschedule_model->_select_list($needParam);

            $result['data'] = $data;
            $result['templateHtml'] = 'adm/liveschedule/livescheduleList.html';
            $html = $this->_print($result, TRUE);

            $result['success'] = "true";
            $result['source'] = $html;
            $path = "./_cache/%cache"; delete_files($path, true);
            echo json_encode($result);


        } else if($params['mode'] == "setSpecialLive"){
            $data = $this->common_model->_select_row('liveschedule_data',array('lsNO'=>$params['lsNO']));
            if($data['lsIsSpecial'] == 'YES')  $params['lsIsSpecial'] = 'NO';
            else                        $params['lsIsSpecial'] = 'YES';
            $params['lsModDate'] = 'NOW()';
            unset($params['mode']);
            $this->common_model->_update('liveschedule_data',$params, array('lsNO'=>$params['lsNO']));
            $result['success'] = "true";
            $path = "./_cache/%cache"; delete_files($path, true);
            echo json_encode($result);

        } else if($params['mode'] == "setView"){
            $data = $this->common_model->_select_row('liveschedule_data',array('lsNO'=>$params['lsNO']));
            if($data['lsIsView'] == 'YES')  $params['lsIsView'] = 'NO';
            else                        $params['lsIsView'] = 'YES';
            $params['lsModDate'] = 'NOW()';
            unset($params['mode']);
            $this->common_model->_update('liveschedule_data',$params, array('lsNO'=>$params['lsNO']));
            $result['success'] = "true";
            $path = "./_cache/%cache"; delete_files($path, true);
            echo json_encode($result);
        }
    }
}