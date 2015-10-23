<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   생중계 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class live extends common {
    public function __construct(){
        parent::__construct();
        $this->load->model("content_model");
        $this->load->model("liveschedule_model");
        $this->load->model("popupschedule_model");
    }

    public function index() {
        $params = $this->_get_sec();

        $data['params'] = $params;
        $secParam['chNO'] = ($params['chNO']) ? $params['chNO'] : 1;
        $channel = $this->common_model->_select_list('channel_data',$secParam);
        $data['logo'] = base_url("/images/common/goodnewsTV.JPG");
        $data['channel'] = $channel[0];
        if($data['channel']['chLanguage'] != "--"){
            $chLanguage = explode('-',$data['channel']['chLanguage']);
            unset($data['channel']['chLanguage']);
            $data['channel']['chLanguage']['cn'] = $chLanguage[0];
            $data['channel']['chLanguage']['es'] = $chLanguage[1];
            $data['channel']['chLanguage']['en'] = $chLanguage[2];
            $data['channel']['chLanguageCheck'] = "YES";
        }

        $iphonecheck = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($iphonecheck, 'iPhone') == true)         $browser = "iPhone";
        else if (strpos($iphonecheck, 'iPad') == true)      $browser = "iPad";
        else if (strpos($iphonecheck, 'Android') == true)   $browser = "Android";
        if($browser)    $data['mobileUrl'] = "true";

        $data['channelNames'] = $this->liveschedule_model->_select_channel_list(array('lsDate'=>date('Y-m-d')));
        $secParam['lsDate'] = date('Y-m-d');
        $week = array("일","월","화","수","목","금","토");
        $data["today"] = $week[date('w',strtotime($secParam['lsDate']))];

        $secParam['lsIsView'] = 'YES';
        $liveParam = $secParam;
        $liveParam['oKey'] = "lsSTime";
        $liveParam['oType'] = "ASC";
        $data['liveSchedule'] = $this->liveschedule_model->_select_live_list($liveParam);
        for($i=0; $i<count($data['liveSchedule']); $i++){
            $data['liveSchedule'][$i]['lcName'] = $this->common_class->cut_str_han($data['liveSchedule'][$i]['lcName'], 25,"...");
        }

        $json = read_file('./assets/json/config/live.json');
        $data['config'] = json_decode($json,true);

        /*
        | -------------------------------------------------------------------
        | # 주요 행사 생중계
        | -------------------------------------------------------------------
        */
        $psEventList = $this->popupschedule_model->_select_list();
        $today = date('Y-m-d');
        $week = array("일","월","화","수","목","금","토");
        for($i=0; $i<count($psEventList);$i++){
            $psEventList[$i]['SDay'] = $week[date('w',strtotime($psEventList[$i]['psSDate']))];
            $psEventList[$i]['EDay'] = $week[date('w',strtotime($psEventList[$i]['psEDate']))];
            if($psEventList[$i]['psSDate']<= $today){
                if($today <= $psEventList[$i]['psEDate']){
                    $data['psEventList'][]=$psEventList[$i];
                }
            }
        }

        /*
        | -------------------------------------------------------------------
        | # 최신콘텐츠 START
        | -------------------------------------------------------------------
        */
        $recentContent = $this->content_model->_select_ctRecent_list(array('limit'=>15));
        $cntRecent = count($recentContent);

        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';

        for($i=0; $i<$cntRecent; $i++){
            $baseParams["prCode"] = $recentContent[$i]['prCode'];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            $cntBase = count($baseContent);
            for($j=0; $j<$cntBase; $j++) {
                if($recentContent[$i]['ctNO'] == $baseContent[$j]['ctNO']){
                    $recentContent[$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }
        }

        $lvfCnt = ((count($recentContent)%5) == 0)? count($recentContent) / 5 : (count($recentContent) / 5) + 1;
        $idx = 0;

        for($i=0; $i<$lvfCnt; $i++) {
            for($j=0; $j<5; $j++)   $data["recentContentSlide"][$i]["row01"][] = ($recentContent[$idx]) ? $recentContent[$idx++] : '';
        }

        /*
        | -------------------------------------------------------------------
        | # 인기콘텐츠 START
        | -------------------------------------------------------------------
        */
        $viewContent = $this->content_model->_select_top10();
        $cntView = count($viewContent);
        for($i=0; $i<$cntView; $i++){
            $baseParams["prCode"] = $viewContent[$i]['prCode'];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            $cntBase = count($baseContent);
            for($j=0; $j<$cntBase; $j++) {
                if($viewContent[$i]['ctNO'] == $baseContent[$j]['ctNO']){
                    $viewContent[$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }
        }
        $rcCnt = ((count($viewContent)%5) == 0)? count($viewContent) / 5 : (count($viewContent) / 5) + 1;
        $idx = 0;

        for($i=0; $i<$rcCnt; $i++) {
            for($j=0; $j<5; $j++)   $data["viewContentSlide"][$i]["row01"][] = ($viewContent[$idx]) ? $viewContent[$idx++] : '';
        }

        $this->_print($data);
    }

    public function popup() {
        $params = $this->_get_sec();

        $data['params'] = $params;
        $secParam['chNO'] = ($params['chNO']) ? $params['chNO'] : 1;
        $channel = $this->common_model->_select_list('channel_data',$secParam);
        $data['channel'] = $channel[0];

        if($data['channel']['chLanguage'] != "--"){
            $chLanguage = explode('-',$data['channel']['chLanguage']);
            unset($data['channel']['chLanguage']);
            $data['channel']['chLanguage']['cn'] = $chLanguage[0];
            $data['channel']['chLanguage']['es'] = $chLanguage[1];
            $data['channel']['chLanguage']['en'] = $chLanguage[2];
            $data['channel']['chLanguageCheck'] = "YES";
        }

        $iphonecheck = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($iphonecheck, 'iPhone') == true)         $browser = "iPhone";
        else if (strpos($iphonecheck, 'iPad') == true)      $browser = "iPad";
        else if (strpos($iphonecheck, 'Android') == true)   $browser = "Android";
        if($browser)    $data['mobileUrl'] = "true";

        $data['channelNames'] = $this->liveschedule_model->_select_channel_list(array('lsDate'=>date('Y-m-d')));
        $secParam['lsDate'] = date('Y-m-d');
        $week = array("일","월","화","수","목","금","토");
        $data["today"] = $week[date('w',strtotime($secParam['lsDate']))];

        $secParam['lsIsView'] = 'YES';
        $liveParam = $secParam;
        $liveParam['oKey'] = "lsSTime";
        $liveParam['oType'] = "ASC";
        $data['liveSchedule'] = $this->liveschedule_model->_select_live_list($liveParam);

        for($i=0; $i<count($data['liveSchedule']); $i++){
            $data['liveSchedule'][$i]['lcName'] = $this->common_class->cut_str_han($data['liveSchedule'][$i]['lcName'], 25,"...");
        }

        /*
        | -------------------------------------------------------------------
        | # 주요 행사 생중계
        | -------------------------------------------------------------------
        */
        $psEventList = $this->popupschedule_model->_select_list();
        $today = date('Y-m-d');
        $week = array("일","월","화","수","목","금","토");
        for($i=0; $i<count($psEventList);$i++){
            $psEventList[$i]['SDay'] = $week[date('w',strtotime($psEventList[$i]['psSDate']))];
            $psEventList[$i]['EDay'] = $week[date('w',strtotime($psEventList[$i]['psEDate']))];
            if($psEventList[$i]['psSDate']<= $today){
                if($today <= $psEventList[$i]['psEDate']){
                    $data['psEventList'][]=$psEventList[$i];
                }
            }
        }

        $json = read_file('./assets/json/config/live.json');
        $data['config'] = json_decode($json,true);

        $secParams['prCode !'] = '003001';
        $secParams['oKey'] = "ctRegDate";
        $secParams['oType'] = "DESC";
        $secParams['limit'] = '10';
		
		$data['recentContent'] = $this->common_model->_select_list('content_data',$secParams);

        for($i=0; $i<count($data['recentContent']); $i++)
            $data['recentContent'][$i]['ctName'] = $this->common_class->cut_str_han($data['recentContent'][$i]['ctName'], 40,"...");
         
        $secParams['oKey'] = "ctLikeMonthCount";
        $data['likeContent'] = $this->common_model->_select_list('content_data',$secParams);
        for($i=0; $i<count($data['likeContent']); $i++)
            $data['likeContent'][$i]['ctName'] = $this->common_class->cut_str_han($data['likeContent'][$i]['ctName'], 40,"...");
        
        $secParams['oKey'] = "ctViewMonthCount";
        $data['viewContent'] = $this->common_model->_select_list('content_data',$secParams);
        for($i=0; $i<count($data['viewContent']); $i++)
            $data['viewContent'][$i]['ctName'] = $this->common_class->cut_str_han($data['viewContent'][$i]['ctName'], 40,"...");

        $this->_print($data);
    }

    public function ajax_process() {
        $params = $this->input->post();

        if($params['mode'] == "getVideo") {
            $data = $this->common_model->_select_row('channel_data',array('chNO'=>$params['chNO']));
            $video = $params['video'];
            echo json_encode(stripslashes($data[$video]));


        } else if($params['mode'] == "getContent") {
            $data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
            $params['ctViewCount']=$data['ctViewCount']+1;
            $params['ctViewMonthCount']=$data['ctViewMonthCount']+1;
            unset($params['mode']);
            $this->common_model->_update('content_data',$params,array('ctNO'=>$params['ctNO']));


            if($data['ctType'] == 'YOUTUBE')
                $html = '<iframe width="720" height="480" src="//www.youtube.com/embed/'.$data['ctSource'].'?autoplay=1" frameborder="0" allowfullscreen></iframe>';
            else if($data['ctType'] == 'VIMEO')
                $html = '<iframe width="720" height="480" src="//player.vimeo.com/video/'.$data['ctSource'].'?autoplay=1" frameborder="0" allowfullscreen></iframe>';

            echo json_encode($html);


        } else if($params['mode'] == "getProgram") {
            unset($params['mode']);
            $secParams["oKey"] = "prSort";
            $secParams["oType"] = "ASC";
            if(strpos($params['prCode'],'back') == false){
                $secParams["prPreCode"] = $params['prCode'];
                $secParams["LENGTH(prCode)"] = strlen($params['prCode']) + 3;
            }else{
                $temp = explode('b',$params['prCode']);
                $prCode = $temp[0];
                $prCodeLen = strlen($prCode);
                if($prCodeLen != 3){
                    $secParams["prPreCode"] = substr($prCode,0,$prCodeLen-3);
                }
                $secParams["LENGTH(prCode)"] = $prCodeLen;
            }
            $data = $this->common_model->_select_list('program_data',$secParams);
            $result['param'] = $params;
            $result['secParams'] = $secParams;
            $result['temp'] = $temp;
            if($data){
                $result['templateHtml'] ='live/programList.html';
                $result['mode'] = "program";
            }else{
                $params['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
                $params['oType'] = '';
                $data = $this->common_model->_select_list('content_data',$params);
                $result['templateHtml'] = 'live/contentList.html';
                $result['mode'] = "content";
            }
            $result['data'] = $data;
            $html = $this->_print($result, TRUE);
            $result['html'] = $html;
            echo json_encode($result);

        } else if($params['mode'] == "getContent"){
            unset($params['mode']);
            $params['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
            $params['oType'] = '';
            $data = $this->common_model->_select_list('content_data',$params);
            $result['data'] = $data;
            $result['templateHtml'] = 'live/contentList.html';
            $html = $this->_print($result, TRUE);
            $result['html'] = $html;
            echo json_encode($result);


        } else if($params['mode'] == "playPopupList"){
                $secParams["oKey"] = "ctRegDate";
                $secParams["oType"] = "DESC";
                $perPage = 5;
                $secParams["offset"] = $perPage * ($params['page']-1);
                $secParams["limit"] = 5;

                $data = $this->common_model->_select_list('content_data',$secParams);
                $result = $params;
                $result['data'] = $data;
                $result['offset'] = $secParams["offset"];
                echo json_encode($result);
            }
    }

}