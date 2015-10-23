<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose PopupSchedule Controller class
 * @author  JoonCh
 * @since   13. 7. 27.
 */

class popupschedule extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('popupschedule_model');
        $this->load->model('popupschedule_dt_model');
    }


    /*  관리자 행사 생중계 스케쥴 생성    */
    public function write() {
        $data = $this->_get_sec();
        $data["channel"] = $this->common_model->_select_list('channel_data');
        $this->_print($data);
    }



    /*  관리자 행사 생중계 스케쥴 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['popupschedule']);

        $secParams["oKey"] = "psNO"; $secParams["oType"] = "DESC";
        $result["cnt"] = $data['listCnt'] = $this->common_model->_select_cnt('popupschedule_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('popupschedule_data',$secParams);

        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /*  관리자 행사 생중계 스케쥴 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('popupschedule_data',array('psNO'=>$params['psNO']));
        $data['dt'] = $this->common_model->_select_list('popupschedule_dt_data',array('psNO'=>$params['psNO']));
        $this->_print($data);
    }


    /*  관리자 행사 생중계 스케쥴 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('popupschedule_data',array('psNO'=>$params['psNO']));
        $data['psSDate'] = substr($data['psSDate'],0,count($data['psSDate'])-4);
        $data['psEDate'] = substr($data['psEDate'],0,count($data['psEDate'])-4);
        $data['chNO'] = explode(',',$data['chNO']);
        if(strpos($data['psPopupSite'], "news"))    $data['psPopupSite_goodnews'] = "YES";
        if(strpos($data['psPopupSite'], "yf"))         $data['psPopupSite_iyf'] = "YES";
        $data['dt'] = $this->common_model->_select_list('popupschedule_dt_data',array('psNO'=>$params['psNO']));
        $dtCnt = count($data['dt']);
        for($i=0; $i<$dtCnt; $i++){
            $data['dt'][$i]['dtSTime'] = substr($data['dt'][$i]['dtSTime'],0,count($data['dt'][$i]['dtSTime'])-4);
            $data['dt'][$i]['dtETime'] = substr($data['dt'][$i]['dtETime'],0,count($data['dt'][$i]['dtETime'])-4);
        }
        $data["channel"] = $this->common_model->_select_list('channel_data');
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        // 관리자 프로그램 생성처리
        if($params['mode'] == "write"){
            if($_FILES['psTopImg']['name']){
                $upload = $this->common_class->upload_file('psTopImg', 'popupschedule', false);
                $params = array_merge ($params, $upload);
            }
            $params['psModDate'] = 'NOW()';
            $params['psRegDate'] = 'NOW()';
            unset($params['mode']);
			$popupSite = "";
            if($params['psPopupSite']) {
                foreach($params['psPopupSite'] as $k => $v)
                    $popupSite .= $v.",";

                unset($params['psPopupSite']);
                $paramPs['psPopupSite'] = substr($popupSite, 0, -1);
            }

            $chNO = "";
            foreach($params['chNO'] as $v)  $chNO .= $v.',';
            $paramPs['chNO'] = substr($chNO, 0, -1);
            $paramPs['psName'] = $params['psName'];
            $paramPs['psContent'] = $params['psContent'];
            $paramPs['psSDate'] = $params['psSDate'].':00';
            $paramPs['psEDate'] = $params['psEDate'].':00';
            $paramPs['psVodLink'] = $params['psVodLink'];
            $paramPs['psLiveTime'] = $params['psLiveTime'];
            $paramPs['psTopImg'] = $params['psTopImg'];
            $paramPs['psTopImgOrigin'] = $params['psTopImgOrigin'];
            $paramPs['psModDate'] = 'NOW()';
            $paramPs['psRegDate'] = 'NOW()';
            $this->popupschedule_model->_insert($paramPs);
            $psNO = $this->common_model->_select_row('popupschedule_data',array('chNO'=>$paramPs['chNO'],'psName'=>$paramPs['psName'],'psContent'=>$paramPs['psContent'],'psTopImg'=>$paramPs['psTopImg'],));
            $dtCnt = count($params['dtName']);
            if($dtCnt){
                for($i=0; $i<$dtCnt; $i++){
                    $paramDt[$i]['psNO'] = $psNO['psNO'];
                    $paramDt[$i]['dtName'] = $params['dtName'][$i];
                    $paramDt[$i]['dtSTime'] = $params['dtSTime'][$i].':00';
                    $paramDt[$i]['dtETime'] = $params['dtETime'][$i].':00';
                    $paramDt[$i]['dtVideo'] = $params['dtVideo'][$i];
                    $paramDt[$i]['dtMp3'] = $params['dtMp3'][$i];
                    $paramDt[$i]['dtModDate'] = 'NOW()';
                    $paramDt[$i]['dtRegDate'] = 'NOW()';
                    $this->popupschedule_dt_model->_insert($paramDt[$i]);
                }
            }

            
            $data = $this->common_model->_select_list('popupschedule_data');
            $idx = 0;
            for($i=0; $i<count($data); $i++) {
                if(substr($data[$i]['psEDate'], 0, 10) >= date("Y-m-d")){
	                $psData[$idx] = $data[$i];
                    $psDataDT = $this->common_model->_select_list('popupschedule_dt_data',array('psNO'=>$data[$i]['psNO']));
                    $psData[$idx++]['detail'] = $psDataDT;
                }
            }

            $jsonData = json_encode($psData);
            write_file('./assets/json/popupschedule/schedule.json', $jsonData);
            


            // 관리자 프로그램 수정처리
        } else if($params['mode'] == "modify"){
            if($_FILES['psTopImg']['name']){
                $upload = $this->common_class->upload_file('psTopImg', 'popupschedule', false, array('key'=>'psNO', 'val'=>$params['psNO']));
                $params = array_merge ($params, $upload);
            }

			$popupSite = "";
            if($params['psPopupSite']) {
                foreach($params['psPopupSite'] as $k => $v)
                    $popupSite .= $v.",";

                unset($params['psPopupSite']);
                $paramPs['psPopupSite'] = substr($popupSite, 0, -1);
            }

            $params['psModDate'] = 'NOW()';
            unset($params['mode']);

            $paramPs['psName'] = $params['psName'];
            $paramPs['psContent'] = $params['psContent'];
            $paramPs['psSDate'] = $params['psSDate'].':00';
            $paramPs['psEDate'] = $params['psEDate'].':00';
            $paramPs['psVodLink'] = $params['psVodLink'];
            $paramPs['psLiveTime'] = $params['psLiveTime'];
            $paramPs['psTopImg'] = $params['psTopImg'];
            $paramPs['psTopImgOrigin'] = $params['psTopImgOrigin'];
            $paramPs['psModDate'] = 'NOW()';
            $this->common_model->_update('popupschedule_data',$paramPs,array('psNO'=>$params['psNO']));

            $dtCnt = count($params['dtName']);

            if($dtCnt){
                $this->common_model->_delete('popupschedule_dt_data',array('psNO'=>$params['psNO']));
                for($i=0; $i<$dtCnt; $i++){
                    $paramDt[$i]['psNO'] = $params['psNO'];;
                    $paramDt[$i]['dtName'] = $params['dtName'][$i];
                    $paramDt[$i]['dtSTime'] = $params['dtSTime'][$i].':00';
                    $paramDt[$i]['dtETime'] = $params['dtETime'][$i].':00';
                    $paramDt[$i]['dtVideo'] = $params['dtVideo'][$i];
                    $paramDt[$i]['dtMp3'] = $params['dtMp3'][$i];
                    $paramDt[$i]['dtModDate'] = 'NOW()';
                    $paramDt[$i]['dtRegDate'] = 'NOW()';
                    $this->popupschedule_dt_model->_insert($paramDt[$i]);
                }
            }


            $data = $this->common_model->_select_list('popupschedule_data');
            $idx = 0;
            for($i=0; $i<count($data); $i++) {
                if(substr($data[$i]['psEDate'], 0, 10) >= date("Y-m-d")){
	                $psData[$idx] = $data[$i];
                    $psDataDT = $this->common_model->_select_list('popupschedule_dt_data',array('psNO'=>$data[$i]['psNO']));
                    $psData[$idx++]['detail'] = $psDataDT;
                }
            }

            $jsonData = json_encode($psData);
            write_file('./assets/json/popupschedule/schedule.json', $jsonData);


            // 관리자 프로그램 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $psNO[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($psNO); $i++){
                    $this->common_model->_delete('popupschedule_data',array('psNO'=>$psNO[$i]));
                    $this->common_model->_delete('popupschedule_dt_data',array('psNO'=>$psNO[$i]));
                }
            }else{
                $this->common_model->_delete('popupschedule_data',array('psNO'=>$params['psNO']));
                $this->common_model->_delete('popupschedule_dt_data',array('psNO'=>$params['psNO']));
            }
            redirect(base_url("/adm/popupschedule/index"));

        }

        redirect(base_url("/adm/popupschedule/index"));
    }

}