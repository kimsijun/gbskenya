<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   프로그램 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class program extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("program_model");
        $this->load->model("view_model");
        $this->load->model("mypage_model");
        $this->load->model("content_comment_model");
        $this->load->model("program_comment_model");
    }


    /**
     * 프로그램 리스트
     * @param none
     * @return none
     */
    public function index() {
        $params = $this->_get_sec();
        $data["params"] = $params;

        // 프로그램 네이게이션 만들기
        $prName = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        $data["params"]["prName"] = $prName["prName"];
        if(strlen($params['prCode'])==3){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));

        }elseif(strlen($params['prCode'])==6){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));

        }elseif(strlen($params['prCode'])==9){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,6)));
            $data['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        }elseif(strlen($params['prCode'])==12){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,6)));
            $data['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($params['prCode'],0,9)));
            $data['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        }

        if(!$params['prCode'])  $secParams['LENGTH(prCode)'] = '3';
        else $secParams['prCode'] = $params['prCode'];
        $secParams['prType'] = 'PROGRAM';
        $secParams['oKey'] = 'prSort';
        $secParams['oType'] = 'asc';
        $data['programPrList'] = $this->common_model->_select_list('program_data',$secParams);
        $data['program2depthList'] = $this->program_model->_select_list($secParams);
        for($i=0; $i<count($data['programPrList']); $i++) {
            for($j=0; $j<count($data['program2depthList']); $j++) {
                if($data['programPrList'][$i]['prCode'] == $data['program2depthList'][$j]['prPreCode'])
                    $data['programPrList'][$i]['prSub'][] = $data['program2depthList'][$j];
            }
        }
        $this->_print($data);
    }



    /**
     * 프로그램 뷰
     * @param none
     * @return none
     */
    public function view() {
        $params = $this->_get_sec();

        // 프로그램 정보
        $data = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        if(strlen($data['prCode'])==3){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));

        }elseif(strlen($params['prCode'])==6){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));

        }elseif(strlen($params['prCode'])==9){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,6)));
            $data['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
        }elseif(strlen($params['prCode'])==12){
            $data['prDepth1'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,3)));
            $data['prDepth2'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,6)));
            $data['prDepth3'] = $this->common_model->_select_row('program_data',array('prCode'=>substr($data['prCode'],0,9)));
            $data['prDepth4'] = $this->common_model->_select_row('program_data',array('prCode'=>$data['prCode']));
        }
        // 콘텐츠 조회수 증가
        if($params['ctNO']){
            // 컨텐츠 정보
            $data["content"] = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
            $viewCount['ctViewCount'] = $data["content"]['ctViewCount'] + 1;
            $viewCount['ctViewMonthCount'] = $data["content"]['ctViewMonthCount'] + 1;

            $this->common_model->_update('content_data',$viewCount,array('ctNO'=>$params['ctNO']));

            // 조회 카운트 조건
            $paramView["vType"] = "CONTENT";
            $paramView["ctNO"] = $params['ctNO'];
        } else {
            // 조회 카운트 조건
            $paramView["vType"] = "PROGRAM";
            $paramView["prCode"] = $data["prCode"];
        }

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
            if($params['ctNO']){
                $paramVew["mpType"] = "CONTENT";
                $paramVew["ctNO"] = $params['ctNO'];
            }else{
                $paramVew["mpType"] = "PROGRAM";
                $paramVew["prCode"] = $data["prCode"];
            }
            $paramVew["mbID"] = $data["member"]["mbID"];
            $paramVew['mpRemoteIP'] = $_SERVER['REMOTE_ADDR'];

            // 뷰 로그
            $isView = $this->common_model->_select_cnt('mypage_log',$paramVew);
            if(!$isView){
                $paramVew['mpRegDate'] = 'NOW()';
                $this->mypage_model->_insert($paramVew);
            }
        }

        // 광고영상 처리
        /*if($params['ctNO']){
            $adContent = $this->common_model->_select_row('adcontent_data',array('aCType'=>'ctNO','aCKey'=>$params['ctNO']));
        }
        if($adContent){
            $data["content"]['adContent'] = $adContent;
        }else{
            $data['adContent'] = $this->common_model->_select_row('adcontent_data',array('aCType'=>'prCode','aCKey'=>$data['prCode']));
        } */


        // 기기별 프로그램 재생목록 생성
        $uagent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($uagent, 'iPhone') == true || strpos($uagent, 'iPad') == true || strpos($uagent, 'Android') == true)     $environment = "mobile";
        else if(strpos($uagent, 'Mac') == true)         $environment = "mac";
        else                                            $environment = "pc";
        $secParams["oKey"] = "ctEventDate DESC,ctRegDate DESC,ctName DESC";
        $secParams["oType"] = "";
        $secParams["prCode"] = $data['prCode'];
        $data['playlist'] = $this->common_model->_select_list('content_data',$secParams);
        for($i=0; $i<count($data['playlist']);$i++){
            if($data['playlist'][$i]['ctNO'] == $params['ctNO']){
                $data['nextCtPage'] = ($i<4)? 1 : floor(($i+1)/5) + 1;
                $data['nextCtNO'] = $data['playlist'][$i+1]['ctNO'];
                $data['nextSource'] = $data['playlist'][$i+1]['ctSource'];
            }
        }


        if($environment == "mac" || $environment == "pc") {
            $data['inProgramCnt'] = $this->common_model->_select_cnt('content_data',$secParams);

            $params['ctPage'] = ($params['ctPage'])? $params['ctPage'] : 1;
            $secParams["offset"] = ($params['ctPage']-1) * 5;
            $secParams["limit"] = 5;
            $data['inProgram'] = $this->common_model->_select_list('content_data',$secParams);

            $pageCnt = ceil($data['inProgramCnt']/$secParams["limit"]);
            $data['pageCnt'] = $pageCnt;

            $ctPage = $params['ctPage'] ? $params['ctPage'] : 0;
            if($ctPage < 4){
                $ctPage = 0;
                if( $pageCnt > 6 ){
                    $ctPageCnt = 7;
                    $data['pageNext'] = "true";
                } else $ctPageCnt = $pageCnt;
            } else{
                $ctPage -= 4;
                if($ctPage != 4) $data['pagePrev'] = "true";
                if(($ctPage + 7) < $pageCnt){
                    $ctPageCnt = $ctPage + 7;
                    $data['pageNext'] = "true";
                }else $ctPageCnt = $pageCnt;
            }

            for($i=$ctPage; $i<$ctPageCnt; $i++){
                $data['ajaxPage'][] = $i+1;
            }
            $data['ajaxPageCnt'] = count($data['ajaxPage']) * 25;
            if($data['pagePrev'] == "true") $data['ajaxPageCnt'] += 20;
            if($data['pageNext'] == "true") $data['ajaxPageCnt'] += 20;

        } else {

            $secParams["oKey"] = "ctEventDate DESC,ctRegDate DESC,ctName DESC";
            $secParams["oType"] = "";
            $secParams["prCode"] = $data['prCode'];
            $data['inProgram'] = $this->common_model->_select_list('content_data',$secParams);
            $data['inProgramCnt'] = $this->common_model->_select_cnt('content_data',$secParams);

            $pageCnt = $data['inProgramCnt']/5;
            $data['pageCnt'] = $pageCnt;

            $ctPage = $params['ctPage'] ? $params['ctPage'] : 0;
            if($ctPage < 4){
                $ctPage = 0;
                if( $pageCnt > 6 ){
                    $ctPageCnt = 7;
                    $data['pageNext'] = "true";
                } else $ctPageCnt = $pageCnt;
            } else{
                $ctPage -= 4;
                if($ctPage != 4) $data['pagePrev'] = "true";
                if(($ctPage + 7) < $pageCnt){
                    $ctPageCnt = $ctPage + 7;
                    $data['pageNext'] = "true";
                }else $ctPageCnt = $pageCnt;
            }

            $cntInProgram = count($data['inProgram']);
            $idx=0;

            $prCnt = (($cntInProgram%5) == 0)? $cntInProgram / 5 : $cntInProgram / 5;
            for($i=0; $i<$prCnt; $i++) {
                for($j=0; $j<5; $j++){
                    $data['inProgram'][$idx]['prIdx'] = $idx+1;
                    $data["inProgramSlide"][$i]["row01"][] = ($data['inProgram'][$idx]) ? $data['inProgram'][$idx++] : '';
                }
            }
        }
        $data['environment'] = $environment;
        $data["ctPage"] = $params["ctPage"];


        // 회원 로그인 타입
        $this->load->helper('cookie');
        $data['mbAccountType'] = get_cookie("mbAccountType");

        $data['ctNO'] = $params['ctNO'];
        if($this->session->userdata('mbID')){
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $data['mbLoginID'] = $this->session->userdata('mbID');
        }
        // Depth 생성
        for($i=0; $i<count($data['program']); $i++){
            if(strlen($data['program'][$i]['prCode']) > 3)
                $data['program'][$i]['prDepth'] = strlen($data['program'][$i]['prCode']) / 3;
            else
                $data['program'][$i]['prDepth'] = 1;
        }
        //태그
        $paramTag["oKey"] = "tgRegDate";
        $paramTag["oType"] = "ASC";
        $paramTag["ctNO"] = $params['ctNO'];
        $paramTag["mbID"] = $data["member"]["mbID"];
        $data["tag"] = $this->common_model->_select_list('tag_data',$paramTag);

        //위젯 config
        $json = read_file('./assets/json/config/program_view.json');
        $data['config'] = json_decode($json,true);

        //프로그램 응원
        $paramComment["oKey"] = "pbcoGroup DESC, pbcoStep ASC"; $paramComment["oType"] = "";
        unset($paramComment['prCode']);
        $paramComment["A.prCode"] = $params["prCode"];
        $paramComment["pbcoIsNotice"] = "NO";
        $paramComment["pbcoIsDelete"] = "NO";
        $data["comment"] = $this->program_comment_model->_select_list($paramComment);
        unset($paramComment['A.prCode']);
        for($i=0; $i<count($data["comment"]); $i++){
            $data["comment"][$i]["pbcoRegDate"] = $this->common_class->cut_str_han($data["comment"][$i]["pbcoRegDate"], 10,"");
            $data["comment"][$i]["pbcoModDate"] = $this->common_class->cut_str_han($data["comment"][$i]["pbcoModDate"], 10,"");
        }
        if($params['ctNO']){
            //댓글(코멘트)
            $data["ctCommentCnt"] = $this->common_model->_select_cnt('content_comment_data',array('ctNO'=>$params['ctNO']));
            $paramComment["oKey"] = "cbcoGroup DESC, cbcoStep ASC"; $paramComment["oType"] = "";
            $paramComment["ctNO"] = $params["ctNO"];
            $paramComment["cbcoIsNotice"] = "YES";
            $paramComment["cbcoIsDelete"] = "NO";
            unset($paramComment["A.prCode"]);
            unset($paramComment["pbcoIsNotice"]);
            unset($paramComment["pbcoIsDelete"]);
            $data["noticeComment"] = $this->content_comment_model->_select_list($paramComment);
            for($i=0; $i<count($data["noticeComment"]); $i++){
                $data["noticeComment"][$i]["cbcoRegDate"] = $this->common_class->cut_str_han($data["noticeComment"][$i]["cbcoRegDate"], 10,"");
                $data["noticeComment"][$i]["cbcoModDate"] = $this->common_class->cut_str_han($data["noticeComment"][$i]["cbcoModDate"], 10,"");
            }
            $paramComment["cbcoIsNotice"] = "NO";
            $data["ctComment"] = $this->content_comment_model->_select_list($paramComment);
            if($data["ctComment"]){
                for($i=0; $i<count($data["ctComment"]); $i++){
                    $data["ctComment"][$i]["cbcoRegDate"] = $this->common_class->cut_str_han($data["ctComment"][$i]["cbcoRegDate"], 10,"");
                    $data["ctComment"][$i]["cbcoModDate"] = $this->common_class->cut_str_han($data["ctComment"][$i]["cbcoModDate"], 10,"");
                }
            }

            $ctData = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));

            $data['ctMP3'] = $ctData['ctMP3'];
            if($data['content']['ctVideoNormal']){

                //  vod Server 다운로드 경로 만들기
                $prLen = strlen($data['content']['prCode']);
                for($i=3; $i<=$prLen; $i+=3){
                    $prParams['prCode'] = substr($data['content']['prCode'], 0, $i);
                    $program  = $this->common_model->_select_row('program_data',$prParams);
                    $data["content"]["prDir"] .= "/".$program['prDir'];
                    $data["content"]["prArrDir"][] = $program['prDir'];
                }

                parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
                $CI = & get_instance();
                $CI->config->load("vodServer",TRUE);
                $config = $CI->config->item('vodServer');
                $vodServer = $config['vodServerUrl'];
                $data['content']['vodServerUrl'] = $config['vodServerDownUrl'];
                $data['content']['vodServer'] =  $vodServer.$data["content"]["prDir"].'/'.$data['content']['ctVideoNormal'];
            }
            if($data['content']['ctMP3']){
                parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
                $CI = & get_instance();
                $CI->config->load("soundcloud",TRUE);
                $config = $CI->config->item('soundcloud');
                $scClinetID = $config['scClinetID'];

                if(!strpos($data['content']['ctMP3'], 'ttp'))
                    $data['content']['ctMP3'] = "http://api.soundcloud.com/tracks/".$data['content']['ctMP3']."/download?client_id=".$scClinetID;
            }
        }

        //  Facebook 인증
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
        $data['appId'] = $config['appId'];
        $userId = $this->facebook->getUser();

        $url = base_url().'program/view/ctNO/'.$params['ctNO'].'/prCode/'.$params['prCode'].'/fbLogin/true/';
        if($params['ctPage'])   $url .= "/ctPage/".$params['ctPage'];
        else                    $url .= "/ctPage/1";
        $url .= "/prName/".$params['prName'];

        if($userId == 0){
            $data['mbFBLoginUrl'] = $this->facebook->getLoginUrl(array('redirect_uri' => $url));
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

                //  Feed Post 인증
                $url = base_url().'member/fb_feed_oauth/mode/program/ctNO/'.$params['ctNO'].'/prCode/'.$params['prCode'].'/fbLogin/true/';
                if($params['ctPage'])   $url .= "/ctPage/".$params['ctPage'];
                else                    $url .= "/ctPage/1";
                $url .= "/prName/".$params['prName'];
                redirect($url);
            }
        }
        $this->load->library('twconnect');
        if(!$this->session->userdata("tw_access_token")){
            $url = 'member/twitter_callback/mode/program/ctNO/'.$params['ctNO'].'/prCode/'.$params['prCode'];
            if($params['ctPage'])   $url .= "/ctPage/".$params['ctPage'];
            else                    $url .= "/ctPage/1";

            $data['mbTWLoginUrl'] = $this->twconnect->twredirect($url);
        }else
            $data['mbTWSession'] = "true";


        $tag = "";
        foreach($data['tag'] as $k => $v)
            $tag .= $v['tgTag']. " ";
        if($params['ctNO']){
            if($tag !="") $data['title'] = $data['metaDescription'] = "GoodNewsTV Program ". $data['prName']." ".$ctData['ctName']. "-". $tag;
            else $data['title'] = $data['metaDescription'] = "GoodNewsTV Program ". $data['prName']." ".$ctData['ctName'];
        }else{
            if($tag !="") $data['title'] = $data['metaDescription'] = "GoodNewsTV Program ". $data['prName']. "-". $tag;
            else $data['title'] = $data['metaDescription'] = "GoodNewsTV Program ". $data['prName'];
        }
        $data['prTab'] = $params['prTab'];

        if($params['ctNO']) $data['shortUrl'] = base_url("/s/cp/".$params['ctNO']);
        else                $data['shortUrl'] = base_url("/s/p/".$params['prCode']);


        // 리모컨
        $recParams['LENGTH(prCode)'] = '3';
        $recParams['oKey'] = 'prSort';
        $recParams['oType'] = 'asc';
        $data['programSortList'] = $this->common_model->_select_list('program_data',$recParams);
       // $data['program2depthList'] = $this->program_model->_select_list($recParams);echo '<pre>';print_r($data);echo'</pre>';exit;
        for($i=0; $i<count($data['programSortList']); $i++) {
            for($j=0; $j<count($data['program2depthList']); $j++) {
                if($data['programSortList'][$i]['prCode'] == $data['program2depthList'][$j]['prPreCode'])
                    $data['programSortList'][$i]['prSub'][] = $data['program2depthList'][$j];
            }
        }
        $this->_print($data);
    }



    /**
     * 비메오 transcoding 여부 체크
     * @param none
     * @return none
     */
    public function isTranscoding() {
        $params = $this->input->post();

        $this->load->model('vimeo');
        $vimeo = new vimeo();
        $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
            '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
        $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);
        $videos = $vimeo->call('vimeo.videos.getInfo', array('video_id'=>$params['ctSource']));

        echo json_encode(array('isTranscoding'=>$videos->video[0]->is_transcoding));
    }




    public function ajax_process () {
        $params = $this->input->post();
        $html = "";
        if(!$this->session->userdata('mbID') && $params['mode']!="playList" && $params['mode']!="getContent" && $params['mode'] != "playListMobile"){
            echo json_encode('false');exit;
        }else{

            if($params['mode'] == "addTag"){

                $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                $paramTag["tgCodeType"] = "prCode";
                $paramTag["tgCode"] = $params["prCode"];
                $paramTag["mbID"] = $data["member"]["mbID"];
                $paramTag['tgRemoteIP'] = $_SERVER['REMOTE_ADDR'];
                $paramTag["tgTag"] = urldecode($params["tagTxt"]);
                $paramTag['tgModDate'] = 'NOW()';
                $paramTag['tgRegDate'] = 'NOW()';

                if($data["member"]){
                    $this->common_model->_insert('tag_data',$paramTag);
                }else{
                    //echo "<script>alert('로그인을 먼저 하세요.'); location.href='/member/login';</script>";
                    echo "<script>if(confirm('Would you like to log in??')){location.href='/member/login';}else{location.href='".$_SERVER['HTTP_REFERER']."';}</script>";
                }
                $path = "./_cache/%cache"; delete_files($path, true);
                $tagParam['tgCode'] = $params["prCode"];
                $tagParam['tgCodeType']= "prCode";
                $tagParam['mbID'] = $data["member"]["mbID"];
                $tagParam["oKey"] = "tgRegDate";
                $tagParam["oType"] = "ASC";
                $data['tagList'] = $this->common_model->_select_list('tag_data',$tagParam);
                $data['param'] = $params;
                echo json_encode($data);

            }elseif($params['mode'] == "delTag"){
                $this->common_model->_delete('tag_data',array('tgNO'=>$params['tgNO']));
                $path = "./_cache/%cache"; delete_files($path, true);

                /**
             * 공통 프로그램 재생목록 (PC)
             * @param none
             * @return none
             */
            } else if($params['mode'] == "playList"){
                $secParams["oKey"] = "ctEventDate DESC,ctRegDate DESC,ctName DESC";
                $secParams["oType"] = "";
                $secParams["prCode"] = $params['prCode'];
                $perPage = 5;
                $secParams["offset"] = $perPage * ($params['page']-1);
                $secParams["limit"] = 5;
                $data = $this->common_model->_select_list('content_data',$secParams);

                $prData = $this->common_model->_select_row('program_data',array('prCode'=>$params["prCode"]));
                $result = $params;
                $result['prName'] = $prData['prName'];
                $result['page'] = $params["page"];
                $result['data'] = $data;
                $result['prThumb'] = $prData['prThumb'];
                $result['offset'] = $secParams["offset"];

                $pageCnt = $result['total'];
                $ctPage = $result['page'];

                if($ctPage < 4){
                    $ctPage = 0;
                    if( $pageCnt > 6 ){
                        $ctPageCnt = 7;
                        $result['pageNext'] = "true";
                    } else $ctPageCnt = $pageCnt;
                } else{
                    $ctPage -= 4;
                    if($ctPage != 0) $result['pagePrev'] = "true";
                    if(($ctPage + 7) < $pageCnt){
                        $ctPageCnt = $ctPage + 7;
                        $result['pageNext'] = "true";
                    }else $ctPageCnt = $pageCnt;
                }


                for($i=$ctPage; $i<$ctPageCnt; $i++){
                    $result['ajaxPage'][] = $i+1;
                }
                $result['ajaxPageCnt'] = count($data['ajaxPage']) * 25;
                if($data['pagePrev'] == "true") $data['ajaxPageCnt'] += 20;
                if($data['pageNext'] == "true") $data['ajaxPageCnt'] += 20;



                $result['ctPage'] = $ctPage;
                $result['ctPageCnt'] = $ctPageCnt;
                $result['ctPageWidth'] = ($ctPageCnt-$ctPage) * 25;
                if($result['pagePrev'] == "true") $result['ctPageWidth'] += 30;
                if($result['pageNext'] == "true") $result['ctPageWidth'] += 30;

                $result['templateHtml'] = 'program/playlist.html';
                $html = $this->_print($result, TRUE);
                $params['html'] = $html;
                echo json_encode($params);


            /**
             * 프로그램 뷰페이지 리모컨
             * (prSub가 있으면 프로그램 리스트, 없으면 컨텐츠 리스트)
             * @param none
             * @return none
             */
            }elseif($params['mode'] == "getContent"){
                $secParams["oKey"] = "prSort";
                $secParams["oType"] = "ASC";
                $secParams["prPreCode"] = $params['prCode'];
                //$secParams["LENGTH(prCode)"] = strlen($params['prCode']) + 3;

                $data = $this->common_model->_select_list('program_data',$secParams);
                $program = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));

                if(count($data)>0){
                    for($i=0; $i<count($data); $i++) {
                        $perParams["prPreCode"] = $data[$i]['prCode'];
                        $perParams["LENGTH(prCode)"] = strlen($data[$i]['prCode']) + 3;
                        $perData = $this->common_model->_select_list('program_data',$perParams);
                        if(count($perData) > 0) $data[$i]['prSub'] = "YES";
                        if(strlen($data[$i]['prCode']) == 9 && (substr($data[$i]['prCode'],0,6))=="001001"){
                            // 주일예배 한밭, 대연교회 제거
                            if($data[$i]['prName'] == "기쁜소식한밭교회" || $data[$i]['prName'] == "부산대연교회") $ignoreSermon = "";
                            else $data2[] = $data[$i];
                        }
                    }
                } else {
                    $secParams["prCode"] = $secParams["prPreCode"];
                    unset($secParams["prPreCode"]);
                    $secParams["oKey"] = "ctEventDate DESC,ctRegDate DESC,ctName DESC";
                    $secParams["oType"] = "";
                    $secParams["prCode"] = $params['prCode'];
                    $data = $this->common_model->_select_list('content_data',$secParams);
                    if(substr($params['prCode'], 0, 3) == "001"){
                        for($i=0; $i<count($data); $i++) {
                            if(strpos($data[$i]['ctName'],'일')){
                                $arrCtName = explode('일', $data[$i]['ctName']);
                                $data[$i]['ctName'] = $arrCtName[0].'일';
                            }
                        }
                    }
                    for($i=0; $i<count($data); $i++) {
                        if($program['prType']==='SERMON'){
                            $data[$i]['prType'] = 'SERMON';
                        }else{
                            $data[$i]['prType'] = 'PROGRAM';
                        }
                    }

                }
                if($data2) $result['data'] = $data2;
                else $result['data'] = $data;
                echo json_encode($result);

            }elseif($params['mode'] == "snsShare"){
                $secParams['prCode'] = $params['prCode'];
                $data = $this->common_model->_select_row('program_data',$secParams);
                $snsShare= json_decode($data['prShareCount'],true);

                if($params['type']=='facebook'){
                    $snsShare['facebook'] += 1;
                }else $snsShare['twitter'] += 1;
                $data['prShareCount'] = '{"facebook":'.$snsShare['facebook'].',"twitter":'.$snsShare['twitter'].'}';
                $data['prModDate'] ='NOW()';
                $this->common_model->_update('program_data',$data,array('prCode'=>$params['prCode']));
                if($params['ctNO']){
                    $secParams['ctNO'] = $params['ctNO'];
                    $data['content'] = $this->common_model->_select_row('content_data',$secParams);
                    $snsShare= json_decode($data['content']['ctShareCount'],true);
                    if($params['type']=='facebook'){
                        $snsShare['facebook'] += 1;
                    }else $snsShare['twitter'] += 1;
                    $data['content']['ctShareCount'] = '{"facebook":'.$snsShare['facebook'].',"twitter":'.$snsShare['twitter'].'}';
                    $data['content']['ctModDate'] ='NOW()';
                    $this->common_model->_update('content_data',$data['content'],array('ctNO'=>$params['ctNO']));

                }
                echo json_encode($data);
            }
        }
    }



    public function getOGMeta(){
        $params = $this->input->post();
        $data = $this->program_model->_select_row(array('prCode'=>$params['prCode']));
        if($params['ctNO']) $data['content'] = $this->content_model->_select_row(array('ctNO'=>$params['ctNO']));
        $data['prContent'] =  str_replace("\n","<br>", $data['prContent']);
        echo json_encode($this->_print($data,TRUE));
    }
}