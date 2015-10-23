<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   콘텐츠 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class content extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("content_model");
        $this->load->model("tag_model");
        $this->load->model("program_model");
        $this->load->model("view_model");

    }

    public function view() {
        $params = $this->_get_sec();
        // 콘텐츠 조회수 증가
        // 컨텐츠 정보
        $data= $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
        $viewCount['ctViewCount'] = $data['ctViewCount'] + 1;
        $viewCount['ctViewMonthCount'] = $data['ctViewMonthCount'] + 1;

        $this->common_model->_update('content_data',$viewCount,array('ctNO'=>$params['ctNO']));

        // 조회 카운트 조건
        $paramView["vType"] = "CONTENT";
        $paramView["ctNO"] = $params['ctNO'];


        // 조회 카운트 증가
        if($this->session->userdata('mbID'))
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        if($data["member"]) $paramView["mbID"] = $data["member"]["mbID"];
        else $paramView["mbID"] = $_SERVER['REMOTE_ADDR'];
        $paramView['vRemoteIP'] = $_SERVER['REMOTE_ADDR'];
        $paramView['vRegDate'] = 'NOW()';
        $this->view_model->_insert($paramView);

        // 내가 본 콘텐츠(프로그램) 등록
        if($this->session->userdata('mbID')){
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $data['mbLoginID'] = $this->session->userdata('mbID');

            $paramVew["mpSection"] = "VIEW";
            $paramVew["mpType"] = "CONTENT";
            $paramVew["ctNO"] = $params['ctNO'];
            $paramVew["mbID"] = $data["member"]["mbID"];
            $paramVew['mpRemoteIP'] = $_SERVER['REMOTE_ADDR'];

            // 뷰 로그
            $isView = $this->common_model->_select_cnt('mypage_log',$paramVew);
            if(!$isView){
                $paramVew['mpRegDate'] = 'NOW()';
                $this->mypage_model->_insert($paramVew);
            }
        }

        $data["ctName"] = $this->common_class->cut_str_han($data["ctName"], 38,"...");
        $data["program"] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
        $data['program']['prContent'] =  str_replace("\n","<br>", $data['program']['prContent']);
        $adContent = $this->common_model->_select_row('adcontent_data',array('aCType'=>'ctNO'));//,'aCKey'=>$params['ctNO']));
        if($adContent){
            $data['adContent'] = $adContent;
        }else{
            $data['adContent'] = $this->common_model->_select_row('adcontent_data',array('aCType'=>'prCode'));//,'aCKey'=>$data['prCode']));
        }

        $this->load->helper('cookie');
        $data['mbAccountType'] = $this->input->cookie('mbAccountType', TRUE);

        if(strlen($data["prCode"])==3){
            $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>$data["prCode"]));
            $data['prDepth1']= $prDepth1["prName"];
            $data['prDepth1Code']= $data["prCode"];
        }elseif(strlen($data["prCode"])==6){
            $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth1']= $prDepth1["prName"];
            $data['prDepth1Code']= substr($data['prCode'],0,3);
            $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>$data["prCode"]));
            $data['prDepth2']= $prDepth2["prName"];
            $data['prDepth2Code']= $data["prCode"];
        }elseif(strlen($data["prCode"])==9){
            $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth1']= $prDepth1["prName"];
            $data['prDepth1Code']= substr($data['prCode'],0,3);
            $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,6)));
            $data['prDepth2']= $prDepth2["prName"];
            $data['prDepth2Code']= substr($data['prCode'],0,6);
            $prDepth3 = $this->common_model->_select_row('program_data',array('prCode'=>$data["prCode"]));
            $data['prDepth3']= $prDepth3["prName"];
            $data['prDepth3Code']= $data["prCode"];
        }elseif(strlen($data["prCode"])==12){
            $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth1']= $prDepth1["prName"];
            $data['prDepth1Code']= substr($data['prCode'],0,3);
            $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,6)));
            $data['prDepth2']= $prDepth2["prName"];
            $data['prDepth2Code']= substr($data['prCode'],0,6);
            $prDepth3 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,9)));
            $data['prDepth3']= $prDepth3["prName"];
            $data['prDepth3Code']= substr($data['prCode'],0,9);
            $prDepth4 = $this->common_model->_select_row('program_data',array('prCode'=>$data["prCode"]));
            $data['prDepth4']= $prDepth4["prName"];
            $data['prDepth4Code']= $data["prCode"];
        }

        $secParams["oKey"] = "ctRegDate";
        $secParams["oType"] = "DESC";
        $secParams["prCode"] = $data['prCode'];
        $data['inProgramCnt'] = $this->common_model->_select_cnt('content_data',$secParams);

        $params['ctPage'] = ($params['ctPage'])? $params['ctPage'] : 1;
        $secParams["offset"] = ($params['ctPage']-1) * 5;
        $secParams["limit"] = 5;
        $data['inProgram'] = $this->common_model->_select_list('content_data',$secParams);

        $pageCnt = $data['inProgramCnt']/$secParams["limit"];

        for($i=0; $i<$pageCnt; $i++){
            $data['ajaxPage'][] = $i+1;
        }

        //태그
        if($this->session->userdata('mbID')){
            $data["tag"] = $this->common_model->_select_list('tag_data',array('ctNO'=>$params['ctNO'],'mbID'=>$data["member"]["mbID"] ));
        }
        //관련동영상
        $data["tagList"] = $this->common_model->_select_list('tag_data',array('ctNO'=>$params['ctNO'])); 
        if($data["ctRelativeMode"] == "AUTO"){
            for($i=0; $i<count($data["tagList"]); $i++){
                $data["tagList"][$i]["contents"] = $this->tag_model->_select_list(array('tgTag'=>$data["tagList"]["$i"]['tgTag']));
                for($j=0; $j<count($data["tagList"][$i]["contents"]); $j++){
                    if($data["ctNO"] != $data["tagList"][$i]["contents"][$j]["ctNO"]){
                        $data["relationList"][] = $this->common_model->_select_row('content_data',array('ctNO'=>$data["tagList"][$i]["contents"][$j]["ctNO"]));
                        for($z=0; $z<count($data["relationList"]); $z++){
                            $data["relationList"][$z]["ctNameShort"] = $this->common_class->cut_str_han($data["relationList"][$z]["ctName"], 15,"...");
                        }
                    }
                }
            }
        }else{
            $arrTemp = explode(',', $data['ctRelativeContents']);
            for($i=0; $i<count($arrTemp)-1; $i++){
                $data["relationList"][$i] = $this->common_model->_select_row('content_data',array('ctNO'=>$arrTemp[$i]));
                for($j=0; $j<count($data["relationList"]); $j++){
                    $data["relationList"][$j]["ctNameShort"] = $this->common_class->cut_str_han($data["relationList"][$j]["ctName"], 15,"...");
                }
            }
        }

        for($i=0; $i<count($data["relationList"])/5; $i++){
            for($j=0; $j<count($data["relationList"]); $j++){
                if(intval($j/5) == $i){
                    $data["relationContent"][$i][] = $data["relationList"][$j];
                }
            }
        }
        //위젯 config
        $json = read_file('./assets/json/config/content_view.json');
        $data['config'] = json_decode($json,true);

        //댓글
        $data["commentCnt"] = $this->common_model->_select_cnt('content_comment_data',array('ctNO'=>$params['ctNO']));
        $paramComment["oKey"] = "cbcoGroup DESC, cbcoStep ASC"; $paramComment["oType"] = "";
        $paramComment["ctNO"] = $params["ctNO"];

        $paramComment["cbcoIsNotice"] = "YES";
        $paramComment["cbcoIsDelete"] = "NO";
        $data["noticeComment"] = $this->common_model->_select_list('content_comment_data',$paramComment);
        for($i=0; $i<count($data["comment"]); $i++){
            $data["noticeComment"][$i]["cbcoRegDate"] = $this->common_class->cut_str_han($data["noticeComment"][$i]["cbcoRegDate"], 10,"");
            $data["noticeComment"][$i]["cbcoModDate"] = $this->common_class->cut_str_han($data["noticeComment"][$i]["cbcoModDate"], 10,"");
        }

        $paramComment["cbcoIsNotice"] = "NO";
        $data["comment"] = $this->common_model->_select_list('content_comment_data',$paramComment);
        for($i=0; $i<count($data["comment"]); $i++){
            $data["comment"][$i]["cbcoRegDate"] = $this->common_class->cut_str_han($data["comment"][$i]["cbcoRegDate"], 10,"");
            $data["comment"][$i]["cbcoModDate"] = $this->common_class->cut_str_han($data["comment"][$i]["cbcoModDate"], 10,"");
        }
        $data["ctNameShort"] = str_replace(' ', '-', $this->common_class->cut_str_han($data["ctName"], 15,""));

        //신고
        $reportParams["oKey"] = "boGroup DESC, boStep ASC, boRegDate DESC"; $reportParams["oType"] = "";
        $reportParams["bodID"] = "report";

        $data["total"] = $this->common_model->_select_cnt('board_data',array('bodID'=>$reportParams["bodID"]));
        $result["cnt"] = $this->common_model->_select_cnt('board_data',$reportParams);
        $result["list"] = $this->common_model->_select_list('board_data',$reportParams);
        $data["list"] = $result["list"];
        $data["ctPage"] = $params["ctPage"];

        /*  Facebook 인증    */
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
        $data['appId'] = $config['appId'];
        $userId = $this->facebook->getUser();

        if($userId == 0){
            $data['mbFBLoginUrl'] = $this->facebook->getLoginUrl(array('redirect_uri' => base_url().'content/view/ctNO/'.$params['ctNO'].'/fbLogin/true'));
        } else {
            $data['mbFBSession'] = "true";
            if($params['fbLogin'] == "true"){
                $user = $this->facebook->api('/me');
                if($data["member"]['mbFBSeq'] != $user['id']) {
                    $mbParams['mbFBSeq'] = $user['id'];
                    $mbParams['mbFBThumbOrigin'] = "https://graph.facebook.com/".$user['id']."/picture";
                    $mbParams['mbFBThumb'] = md5(date('YmdHis').$mbParams['mbFBThumbOrigin']).".jpg";
                    file_put_contents('./uploads/member/thumb/facebook/'.$mbParams['mbFBThumb'], file_get_contents($mbParams['mbFBThumbOrigin']));
                    $mbParams['mbFBID'] = $user['username'];
                    $mbParams['mbFBName'] = $user['name'];
                    $mbParams['mbFBLink'] = $user['link'];
                    $this->common_model->_update('member_data',$mbParams, array('mbID'=>$this->session->userdata('mbID')));
                }
                /*  Feed Post 인증    */
                redirect("/member/fb_feed_oauth/ctNO/".$data['ctNO']."/ctName/".$data['ctNameShort']."/mode/content/fbLogin/true/");
            }
        }


        $this->load->library('twconnect');
        if(!$this->session->userdata("tw_access_token"))
            $data['mbTWLoginUrl'] = $this->twconnect->twredirect("member/twitter_callback/ctNO/".$data['ctNO']."/mode/content");
        else
            $data['mbTWSession'] = "true";


        $tag = "";
        foreach($data['tag'] as $k => $v)
            $tag .= $v['tgTag']. " ";

        $data['title'] = $data['metaDescription'] = "GoodNewsTV Video ". $data['prName']. ", ". $data['ctName']. ", ". $tag;
        $data['ctTab'] = $params['ctTab'];

        $data['shortUrl'] = base_url("/s/c/".$params['ctNO']);
        $this->_print($data);
    }

    /*
    | -------------------------------------------------------------------
    | # 짧은 주소가 해당 메소드로 접근 하면 Redirect 시켜줌
    | -------------------------------------------------------------------
    */
    public function share() {
        redirect(base_url("/adm/content/index"));
        if($this->uri->segment(3) == "be"){
            $this->load->model("url_match_model");
            $umData = $this->url_match_model->_select_row(array('umShortUrl'=>$this->uri->segment(4)));
            redirect(base_url($umData['umUrl']));
        }else   redirect(base_url());
    }

    public function downview() {
        $params = $this->_get_sec();
        $data = $params;

        $secParams['prCode'] = $params['prCode'];
        $secParams['oKey'] = 'ctRegDate';
        $secParams['oType'] = 'desc';
        $data["contentList"]  = $this->common_model->_select_list('content_data',$secParams);

        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        $CI->config->load("soundcloud",TRUE);
        $config = $CI->config->item('soundcloud');
        $scClinetID = $config['scClinetID'];


        for($i=0; $i<count($data["contentList"]); $i++) {
            if($data["contentList"][$i]['ctMP3'] && !strpos($data["contentList"][$i]['ctMP3'], 'ttp:') && !strpos($data["contentList"][$i]['ctMP3'], 'ttps:')){
                $data["contentList"][$i]['ctMP3']     = 'http://api.soundcloud.com/tracks/'.$data["contentList"][$i]['ctMP3'] .'/download/?client_id='. $scClinetID;
            }
        }

        $prLen = strlen($params['prCode']);
        for($i=3; $i<=$prLen; $i+=3){
            $prParams['prCode'] = substr($params['prCode'], 0, $i);
            $program  = $this->common_model->_select_row('program_data',$prParams);
            $data["prDir"] .= "\\".$program['prDir'];
            $data["prArrDir"][] = $program['prDir'];

            if($params['prCode'] == "001001001" && $i == 12) $data['prArrTitleDir'][] = $program;
            else if($params['prCode'] == "001001001"){
                if($i == 9)     $data['prArrTitleDir'][] = $program;
            } else if($i == 6)    $data['prArrTitleDir'][] = $program;
        }

        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        $CI->config->load("vodServer",TRUE);
        $config = $CI->config->item('vodServer');
        $data['vodServer'] = $config['vodServerDownUrl'];

        $this->_print($data);
    }

    public function downlist() {
        $params = $this->_get_sec();
        $data = $params;


        if(!$params['prCode']){
            $today = date('Y-m-d');
            $date = date("Y-m-d", strtotime($today." -2 day")) ;
            $secParams['prIsSimpleDown'] = 'YES';
            $secParams['oKey'] = 'prSort';
            $secParams['oType'] = 'asc';
            $data["programAll"]  = $this->program_model->_select_list($secParams);
            $data["simpleProgram"]  = $this->common_model->_select_list('program_data',$secParams);


            $loopCnt = count($data["simpleProgram"]);
            for($i=0; $i<$loopCnt; $i++){
                $data["simpleProgram"][$i]['strlen'] = strlen($data["simpleProgram"][$i]['prCode']);
                if(substr($data["simpleProgram"][$i]['prCode'], 0, 3) == "002" && strlen($data["simpleProgram"][$i]['prCode']) != 6)
                    unset($data["simpleProgram"][$i]);

            }

            $loopCnt = count($data["simpleProgram"]);
            for($i=0; $i<$loopCnt; $i++){
                /*  하위 프로그램    */
                $loopSubCnt = count($data['programAll']);
                for($j=0; $j<$loopSubCnt; $j++){
                    if($data["simpleProgram"][$i]['prCode'] == substr($data['programAll'][$j]['prCode'],0,6) && strlen($data['programAll'][$j]['prCode']) == 9){
                        $data['simpleProgram'][$i]['prSub'][] = $data['programAll'][$j];
                    }
                }
            }

            /*  서브페이지    */
        } else {
            $secParams['oKey'] = 'prSort';
            $secParams['oType'] = 'desc';
            $secParams['prCodePrev'] = $params['prCode'];
            $secParams['NOTprCode'] = $params['prCode'];
            $data["simpleProgram"]  = $this->common_model->_select_list('program_data',$secParams);

        }

        $this->_print($data);
    }




    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();
        $html = "";
        if($params['mode'] == "setCookieSnsLogin"){
            $this->load->helper('cookie');
            $cookie = array(
                'name'   => 'mbAccountType',
                'value'  => $params['type'],
                'expire' => '86500',
                'path'   => '/',
            );

            $this->input->set_cookie($cookie);
        } else if(!$this->session->userdata('mbID') && $params['mode']!="playList"){
            echo json_encode('false');exit;
        }else{
            if($params['mode'] == "addTag"){

                $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                $paramTag["ctNO"] = $params["ctNO"];
                $paramTag["mbID"] = $data["member"]["mbID"];
                $paramTag['tgRemoteIP'] = $_SERVER['REMOTE_ADDR'];
                $paramTag["tgTag"] = urldecode($params["tagTxt"]);
                $paramTag['tgModDate'] = 'NOW()';
                $paramTag['tgRegDate'] = 'NOW()';

                $tagParam['ctNO'] = $params["ctNO"];
                $tagParam['mbID'] = $data["member"]["mbID"];
                $tagParam["oKey"] = "tgRegDate";
                $tagParam["oType"] = "ASC";
                $data['tagCnt'] = $this->common_model->_select_list('tag_data',$tagParam);

                if(count($data["tagCnt"])<3){
                    $tagParam["tgTag"] = $paramTag["tgTag"];
                    $data["tag"] = $this->common_model->_select_list('tag_data',$tagParam);
                    if(!$data["tag"]){
                        $this->tag_model->_insert($paramTag);
                        unset($data["tag"]);
                    }
                }
                $path = "./_cache/%cache"; delete_files($path, true);
                unset($tagParam["tgTag"]);

                $data['tagList'] = $this->common_model->_select_list('tag_data',$tagParam);
                $data['param'] = $params;
                echo json_encode($data);

            }elseif($params['mode'] == "modTag"){
                unset($params['tagTxt']);unset($params['mode']);
                $data = $this->common_model->_select_row('tag_data',array('tgNO'=>$params['tgNO']));
                $params['tgModDate'] = 'NOW()';
                $this->common_model->_update('tag_data',$params,array('tgNO'=>$params['tgNO']));

                $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                $tagParam['ctNO'] = $params['ctNO'];
                $tagParam['mbID'] = $data["member"]["mbID"];
                $tagParam["oKey"] = "tgRegDate";
                $tagParam["oType"] = "ASC";
                $path = "./_cache/%cache"; delete_files($path, true);
                $data['tagList'] = $this->common_model->_select_list('tag_data',$tagParam);
                $data['param'] = $params;
                echo json_encode($data);

            }elseif($params['mode'] == "delTag"){
                $this->common_model->_delete('tag_data',array('tgNO'=>$params['tgNO']));
                $path = "./_cache/%cache"; delete_files($path, true);
                
            }elseif($params['mode'] == "playList"){
                $secParams["oKey"] = "ctRegDate";
                $secParams["oType"] = "DESC";
                $secParams["prCode"] = $params['prCode'];
                $perPage = 5;
                $secParams["offset"] = $perPage * ($params['page']-1);
                $secParams["limit"] = 5;
                $data = $this->common_model->_select_list('content_data',$secParams);

                $result = $params;
                $result['data'] = $data;
                $result['offset'] = $secParams["offset"];
                echo json_encode($result);
                
            }elseif($params['mode'] == "snsShare"){
                if($params['ctNO']){
                    $secParams['ctNO'] = $params['ctNO'];
                    $data = $this->common_model->_select_row('content_data',$secParams);
                    $snsShare= json_decode($data['ctShareCount'],true);

                    if($params['type']=='facebook'){
                        $snsShare['facebook'] += 1;
                    }else $snsShare['twitter'] += 1;
                    $data['ctShareCount'] = '{"facebook":'.$snsShare['facebook'].',"twitter":'.$snsShare['twitter'].'}';
                    $this->common_model->_update('content_data',$data,array('ctNO'=>$params['ctNO']));

                }
                echo json_encode($data);
            }
        }
    }

    public function getOGMeta(){
        $params = $this->input->post();
        $data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
        $data['program'] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
        $data['program']['prContent'] =  str_replace("\n","<br>", $data['program']['prContent']);
        echo json_encode($this->_print($data,TRUE));
    }
}