<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   컨텐츠 관리자 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class content extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model("content_model");
    }


    /*  관리자 컨텐츠 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        $ctPage = $secParams['ctPage'];
        unset($secParams['content']);
        unset($secParams['ctPage']);

        $secParams["oKey"] = "ctEventDate DESC,ctRegDate DESC,ctName DESC";
        $secParams["oType"] = "";
        /*$result["totalCnt"] = $this->common_model->_select_cnt('content_data',$secParams);
        $result["totalCnt"] = (int)($result["totalCnt"]/100)+1;
        for($i=0; $i<$result["totalCnt"]; $i++) $result["totalPage"][$i] = " ";

        $secParams["limit"] = 100;
        if($ctPage) $secParams["offset"] = ($ctPage-1) * 100;
        else $secParams["offset"] = $ctPage * 100;*/
        $data["total"] = $this->common_model->_select_cnt('content_data');
        $result["cnt"] = $this->common_model->_select_cnt('content_data',$secParams);
        if($secParams['prCode']){
            $secParams['a.prCode'] = $secParams['prCode'];
            unset($secParams['prCode']);
        }
        $secParams["limit"] = 100;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->content_model->_select_admin_list($secParams);
        $data["program"] = $this->common_model->_select_list('program_data',array('oKey'=>'prCode','oType'=>'ASC'));

        for($i=0;$i<count($data["program"]);$i++){
            $data['program'][$i]['inContents'] = $this->common_model->_select_list('content_data',array("prCode"=>$data['program'][$i]['prCode']));
            if(strlen($data['program'][$i]['prCode']) == 3)
                $data['prlist'][] = $data['program'][$i];
        }
        $pager["CNT"] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["totalPage"] = $result["totalPage"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *  컨텐츠 생성의 경우 Vimeo, Youtube에 등록 되어 있는 컨텐츠를 선택 할 수 있도록 Json 파일 로드
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function write() {
        $data['program'] = $this->common_model->_select_list('program_data',array("oKey"=>"prCode", "oType"=>"asc"));
        $data['programCnt'] = $this->common_model->_select_cnt('program_data',array("oKey"=>"prCode", "oType"=>"asc"));
        $data['contents'] = $this->common_model->_select_list('content_data',array("oKey"=>"ctNO", "oType"=>"desc"));
        $data['contentsCnt'] = $this->common_model->_select_cnt('content_data',array("oKey"=>"ctNO", "oType"=>"desc"));

        for($i=0;$i<$data['programCnt'];$i++){
            $data['program'][$i]['inContents'] = $this->common_model->_select_list('content_data',array("prCode"=>$data['program'][$i]['prCode']));
            if(strlen($data['program'][$i]['prCode']) == 3)
                $data['prlist'][] = $data['program'][$i];
        }

        $url =  "./assets/json/vimeo/vimeo_cate.json";
        $contents = file_get_contents($url);
        $vimeoCate = json_decode($contents);
        $data['vimeo_cate'] = $vimeoCate;

        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;
        $this->_print($data);
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *  컨텐츠 수정의 경우 Vimeo, Youtube에 등록 되어 있는 컨텐츠를 선택 할 수 있도록 Json 파일 로드
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
        $data['prPreCode'] = substr($data['prCode'],0,strlen($data['prCode'])-3);

        $data['program'] = $this->common_model->_select_list('program_data',array("oKey"=>"prCode", "oType"=>"asc"));
        $data['programCnt'] = $this->common_model->_select_cnt('program_data',array("oKey"=>"prCode", "oType"=>"asc"));
        $data['contents'] = $this->common_model->_select_list('content_data',array("oKey"=>"ctNO", "oType"=>"desc"));
        $data['contentsCnt'] = $this->common_model->_select_cnt('content_data',array("oKey"=>"ctNO", "oType"=>"desc"));

        for($i=0;$i<$data['programCnt'];$i++){
            $data['program'][$i]['inContents'] = $this->common_model->_select_list('content_data',array("prCode"=>$data['program'][$i]['prCode']));
        }

        $secParams["oKey"] = "prCode";
        $secParams["oType"] = "ASC";
        $secParams["prPreCode"] = substr($data['prCode'],0,strlen($data['prCode'])-3);
        $secParams["LENGTH(prCode)"] = strlen($data['prCode']);

        $data["prlist"] = $this->common_model->_select_list('program_data',$secParams);

        $arrTemp = explode(',', $data['ctRelativeContents']);
        for($i=0; $i<$data['contentsCnt']; $i++){
            for($j=0; $j<count($arrTemp)-1; $j++){
                $data["ctRelative_ctNO"][$j]["ctNO"] = $arrTemp[$j];
                $tempContent = $this->common_model->_select_row('content_data',array("ctNO"=>$arrTemp[$j]));
                $data["ctRelative_ctNO"][$j]["ctName"] = $tempContent["ctName"];
                if($data["contents"][$i]["ctNO"] == $arrTemp[$j])
                    $data["contents"][$i]["ctRelative"] = YES;
            }
        }

        $url =  "./assets/json/vimeo/vimeo_cate.json";
        $contents = file_get_contents($url);
        $vimeoCate = json_decode($contents);
        $data['vimeo_cate'] = $vimeoCate;

        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;

        $this->_print($data);
    }


    /*  관리자 컨텐츠 리스트    */
    public function media_source() {
        $this->_set_sec();

        $url =  "./assets/json/config/media_source.json";
        $contents = file_get_contents($url);
        $youtubeID = json_decode($contents);
        $data['source'] = $youtubeID->youtube;
        $this->_print($data);
    }
    /*  홈쇼핑  */
    public function homeshopping() {
        $this->_set_sec();
        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;

        $url =  "./assets/json/config/homeshopping.json";
        $contents = file_get_contents($url);
        $homeshopping = json_decode($contents);
        $data['ctType'] = $homeshopping->ctType;
        $data['ctSource'] = $homeshopping->ctSource;
        $this->_print($data);
    }
    /*  UEFA CUP    */
    public function uefa() {
        $this->_set_sec();
        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;

        $url =  "./assets/json/config/uefa.json";
        $contents = file_get_contents($url);
        $uefa = json_decode($contents);
        $data['ctType'] = $uefa->ctType;
        $data['ctSource'] = $uefa->ctSource;

        $this->_print($data);
    }

    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        unset($params['content']);
        $data = $this->content_model->_select_row(array('ctNO'=>$params['ctNO']));
        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        unset($params['videoCheck']);
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  컨텐츠 등록 처리.
         *  뭔가 굉장히 많다. 소스를 줄일 수 있을것 같지만 그건 나중에(= _ =;;)
         *  Api를 통해 받은 썸네일의 URL 로 썸네일 파일을 서버에 내려받음
         *  URL 을 '/' 구분자로 자른 후 파일명을 추출하여 원본파일명 컬럼에 저장.
         *         '.' 구분자로 자른 후 파일확장자를 랜덤파일명에 붙여줌
         *         ( 랜덤파일명은 '년월일시분초+파일명' 을 m5로 변환한 값 )
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($params['mode'] == "write"){
            if($params['ctMP3'] && strpos($params['ctMP3'], "soundcloud.com") && !strpos($params['ctMP3'], "download")) {
                $lastStr = substr($params['ctMP3'], -1);
                $params['ctMP3'] = ($lastStr == "/")? $params['ctMP3']."download" : $params['ctMP3']."/download";
            }
            
            if($params['prCode']){
                $program = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                $strFileName = '';


                if($params['ctVideoNormal'] || $params['ctVideoLow']){

                /*  간편다운로드 체크되어있는 프로그램이면 mp4 일반화질, 저화질 자동등록하기    */
                } else if($program['prIsSimpleDown'] == "YES") {
                    $strLenPrCode = strlen($program['prCode']);
                    for($i=6; $i<$strLenPrCode; $i+=3) {
                        $prCode = substr($program['prCode'], 0, $i);
                        $tmpProgram = $this->common_model->_select_row('program_data',array('prCode'=>$prCode));
                        $strFileName .= $tmpProgram['prDir'].'-';
                    }
                    $strFileName .= $program['prDir'].'-'.str_replace('-','',$params['ctEventDate']);
                    $params['ctVideoNormal'] = $strFileName.'-normal.mp4';
                    $params['ctVideoLow'] = $strFileName.'-low.mp4';
                }
            }

            $arrThumb = explode('/', $params['ctThumbS']);
            $cntThumb = count($arrThumb)-1;
            $ThumbSOrigin = $arrThumb[$cntThumb];

            $arrThumb = explode('/', $params['ctThumbL']);
            $cntThumb = count($arrThumb)-1;
            $ThumbLOrigin = $arrThumb[$cntThumb];
            $params['ctThumbSOrigin'] = $ThumbSOrigin;
            $params['ctThumbLOrigin'] = $ThumbLOrigin;

            $arrFileType = explode('/', $ThumbSOrigin);
            $cntFileType = count($arrFileType)-1;
            $fileTypeS = $arrFileType[$cntFileType];

            $arrFileType = explode('/', $ThumbLOrigin);
            $cntFileType = count($arrFileType)-1;
            $fileTypeL = $arrFileType[$cntFileType];


            $ctThumbS = $params['ctThumbS'];
            $ctThumbL = $params['ctThumbL'];
            $params['ctThumbS'] = md5(date('YmdHis').$ThumbSOrigin).$fileTypeS;
            $params['ctThumbL'] = md5(date('YmdHis').$ThumbLOrigin).$fileTypeL;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ctThumbS);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contentsS = curl_exec($ch);
			curl_close($ch);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ctThumbL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contentsL = curl_exec($ch);
			curl_close($ch);
			
            file_put_contents('./uploads/content/thumbs/'.$params['ctThumbS'], $contentsS);
            file_put_contents('./uploads/content/thumbl/'.$params['ctThumbL'], $contentsL);

            if(count($params['ctRelative_ctNO'])>0){
                for($i = 0; $i<count($params['ctRelative_ctNO']); $i++){
                    $params['ctRelativeContents'] .= $params['ctRelative_ctNO'][$i].",";
                }
            }
            $params['ctRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['ctModDate'] = 'NOW()';
            $params['ctRegDate'] = 'NOW()';

            unset($params['ctRelative_ctNO']);
            unset($params['isChangeContent']);
            unset($params['mode']);
            $this->common_model->_insert('content_data',$params);
            $path = "./_cache/%cache";      delete_files($path, true);

            /*  XML, JSON 파일 만들기    */
            if($params['prCode'])
               // $this->makeExternalFiles($params['prCode']);

            echo "<script>location.href='/adm/content/index';</script>";exit;


            // 관리자 컨텐츠 수정
        } else if($params['mode'] == "modify"){
            if($params['prCode']){
                $program = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                $strFileName = '';

                if($params['ctVideoNormal'] || $params['ctVideoLow']){

                    /*  간편다운로드 체크되어있는 프로그램이면 mp4 일반화질, 저화질 자동등록하기    */
                } else if($program['prIsSimpleDown'] == "YES") {
                    $strLenPrCode = strlen($program['prCode']);
                    for($i=6; $i<$strLenPrCode; $i+=3) {
                        $prCode = substr($program['prCode'], 0, $i);
                        $tmpProgram = $this->common_model->_select_row('program_data',array('prCode'=>$prCode));
                        $strFileName .= $tmpProgram['prDir'].'-';
                    }
                    $strFileName .= $program['prDir'].'-'.str_replace('-','',$params['ctEventDate']);
                    $params['ctVideoNormal'] = $strFileName.'-normal.mp4';
                    $params['ctVideoLow'] = $strFileName.'-low.mp4';
                }
            }
            
            if($params['ctMP3'] && strpos($params['ctMP3'], "soundcloud.com") && !strpos($params['ctMP3'], "download")) {
                $lastStr = substr($params['ctMP3'], -1);
                $params['ctMP3'] = ($lastStr == "/")? $params['ctMP3']."download" : $params['ctMP3']."/download";
            }

            if($params['isChangeContent'] == 'Y') {
                $arrThumb = explode('/', $params['ctThumbS']);
                $cntThumb = count($arrThumb)-1;
                $ThumbSOrigin = $arrThumb[$cntThumb];

                $arrThumb = explode('/', $params['ctThumbL']);
                $cntThumb = count($arrThumb)-1;
                $ThumbLOrigin = $arrThumb[$cntThumb];
                $params['ctThumbSOrigin'] = $ThumbSOrigin;
                $params['ctThumbLOrigin'] = $ThumbLOrigin;


                $arrFileType = explode('/', $ThumbSOrigin);
                $cntFileType = count($arrFileType)-1;
                $fileTypeS = $arrFileType[$cntFileType];

                $arrFileType = explode('/', $ThumbLOrigin);
                $cntFileType = count($arrFileType)-1;
                $fileTypeL = $arrFileType[$cntFileType];


                $ctThumbS = $params['ctThumbS'];
                $ctThumbL = $params['ctThumbL'];
                $params['ctThumbS'] = md5(date('YmdHis').$ThumbSOrigin).$fileTypeS;
                $params['ctThumbL'] = md5(date('YmdHis').$ThumbLOrigin).$fileTypeL;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $ctThumbS);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$contentsS = curl_exec($ch);
				curl_close($ch);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $ctThumbL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$contentsL = curl_exec($ch);
				curl_close($ch);

                file_put_contents('./uploads/content/thumbs/'.$params['ctThumbS'], $contentsS);
                file_put_contents('./uploads/content/thumbl/'.$params['ctThumbL'], $contentsL);
            }

            $params['ctModDate'] = 'NOW()';
            foreach($params as $k => $v){
                if($v == "") unset($params[$k]);
            }

            if(count($params['ctRelative_ctNO'])>0){
                for($i = 0; $i<count($params['ctRelative_ctNO']); $i++){
                    $params['ctRelativeContents'] .= $params['ctRelative_ctNO'][$i].",";
                }
            }
            unset($params['ctRelative_ctNO']);
            unset($params['isChangeContent']);
            unset($params['mode']);

            //echo'<pre>';print_r($params);echo"</pre>";
            $this->common_model->_update('content_data',$params,array('ctNO'=>$params['ctNO']));

            //mainFocus 업데이트
            $paramMainFocus['mFName'] = $params['ctName'];
            $params['mFModDate'] = 'NOW()';
            $this->common_model->_update('mainFocus_data',$paramMainFocus,array('ctNO'=>$params['ctNO']));

            $path = "./_cache/%cache";      delete_files($path, true);

            /*  XML, JSON 파일 만들기    */
            if($params['prCode'])
             //   $this->makeExternalFiles($params['prCode']);

            echo "<script>location.href='/adm/content/view/ctNO/".$params['ctNO']."';</script>";exit;

            // 관리자 컨텐츠 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $Code[] = $params['chk'][$i];
                }
                for($i=0; $i<count($Code); $i++) {
                    $data = $this->common_model->_select_row('content_data',array('ctNO'=>$Code[$i]));

                    $this->common_model->_delete('livecontent_data',array('ctNO'=>$data['ctNO']));
                    $this->common_model->_delete('view_log',array('ctNO'=>$data['ctNO']));
                    $this->common_model->_delete('mypage_log',array('ctNO'=>$data['ctNO'],'mpSection'=>'VIEW'));
                    $this->common_model->_delete('content_data',array('ctNO'=>$data['ctNO'],'prCode'=>$data['prCode']));
                }
            }else{
                $this->common_model->_delete('livecontent_data',array('ctNO'=>$params['ctNO']));
                $this->common_model->_delete('view_log',array('ctNO'=>$params['ctNO']));
                $this->common_model->_delete('mypage_log',array('ctNO'=>$params['ctNO'],'mpSection'=>'VIEW'));
                $this->common_model->_delete('content_data',array('ctNO'=>$params['ctNO'],'prCode'=>$params['prCode']));

            }
            //$this->makeExternalFiles($params['prCode']);
            $path = "./_cache/%cache";      delete_files($path, true);
            redirect(base_url("/adm/content/index"));

        } else if($params['mode'] == "media_source"){
            $data = array('youtube'=>$params['source']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/media_source.json', $jsonData);
            redirect(base_url("/adm/content/media_source"));

        } else if($params['mode'] == "homeshopping"){
            $jsonData = json_encode($params);
            write_file('./assets/json/config/homeshopping.json', $jsonData);
            redirect(base_url("/adm/content/homeshopping"));

        } else if($params['mode'] == "uefa"){
            $jsonData = json_encode($params);
            write_file('./assets/json/config/uefa.json', $jsonData);
            redirect(base_url("/adm/content/uefa"));

        } else if($params['mode'] == "thumbUpdate"){
            $data = $this->common_model->_select_row('content_data',array("ctNO"=>$params['ctNO']));
            
            unlink("./uploads/content/thumbl/".$data['ctThumbL']);
            unlink("./uploads/content/thumbs/".$data['ctThumbS']);

            if($data['ctType'] == "VIMEO"){
                $this->load->model('vimeo');
                $vimeo = new vimeo();
                $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
                    '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
                $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);
                $videos = $vimeo->call('vimeo.videos.getInfo', array('video_id'=>$data['ctSource']));


                foreach($videos->video[0] as $k => $v)
                    $apiData[$k] = $v;

                if($apiData['thumbnails']->thumbnail[0]){

                    $data['ctThumbSOrigin'] = $apiData['thumbnails']->thumbnail[1]->_content;
                    $data['ctThumbLOrigin'] = $apiData['thumbnails']->thumbnail[2]->_content;
                    $arrFileType = explode('/', $data['ctThumbSOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeS = $arrFileType[$cntFileType];

                    $arrFileType = explode('/', $data['ctThumbLOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeL = $arrFileType[$cntFileType];

                    $data['ctThumbS'] = md5(date('YmdHis').$data['ctThumbSOrigin']).$fileTypeS;
                    $data['ctThumbL'] = md5(date('YmdHis').$data['ctThumbLOrigin']).$fileTypeL;

                    file_put_contents('./uploads/content/thumbs/'.$data['ctThumbS'], file_get_contents($data['ctThumbSOrigin']));
                    file_put_contents('./uploads/content/thumbl/'.$data['ctThumbL'], file_get_contents($data['ctThumbLOrigin']));

                }
                $data['ctModDate'] = 'NOW()';
                $this->common_model->_update('content_data',$data,array('ctNO'=>$data['ctNO']));

            } else if($data['ctType'] == "YOUTUBE"){


                $url = 'https://gdata.youtube.com/feeds/api/videos/'.$data['ctSource'].'?v=2&alt=json';
                $contents = file_get_contents($url);
                $video = json_decode($contents);

                foreach($video->entry as $k => $v)
                    $apiData[$k] = $v;


                if($apiData['media$group']->{'media$thumbnail'}[1]){

                    $data['ctThumbSOrigin'] = $apiData['media$group']->{'media$thumbnail'}[1]->{'url'};
                    $data['ctThumbLOrigin'] = $apiData['media$group']->{'media$thumbnail'}[2]->{'url'};

                    $arrFileType = explode('/', $data['ctThumbSOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeS = $arrFileType[$cntFileType];

                    $arrFileType = explode('/', $data['ctThumbLOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeL = $arrFileType[$cntFileType];

                    $data['ctThumbS'] = md5(date('YmdHis').$data['ctThumbSOrigin']).$fileTypeS;
                    $data['ctThumbL'] = md5(date('YmdHis').$data['ctThumbLOrigin']).$fileTypeL;

                    file_put_contents('./uploads/content/thumbs/'.$data['ctThumbS'], file_get_contents($data['ctThumbSOrigin']));
                    file_put_contents('./uploads/content/thumbl/'.$data['ctThumbL'], file_get_contents($data['ctThumbLOrigin']));
                }
                $data['ctModDate'] = 'NOW()';

                $this->common_model->_update('content_data',$data,array('ctNO'=>$data['ctNO']));
            }

            redirect(base_url("/adm/content/view/ctNO/".$data['ctNO']));

        }
    }

    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();

        if($params['mode'] == 'getProgram') {
            $secParams["oKey"] = "prCode";
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
                $result['data'] = $data;
            }
            $result['templateHtml'] = $this->cfg["module_dir_name"].'/_widgets/selectProgram.html';
            $html = $this->_print($result, TRUE);
            $result['html'] = $html;
            echo json_encode($result);

        }
    }


    public function xmlWrite($prCode, $xwType) {
        /*  XML 데이터 가져오기    */
        $arrBaseXml = $this->my_xml2array('./assets/podcast/base.xml');
        $arrBaseXmlFixData = $arrBaseXml[0];

        $arrItemXml = $this->my_xml2array('./assets/podcast/item.xml');
        $arrItemXml = $arrItemXml[0];

        /*  생성할 프로그램 데이터 가져오기    */
        $secParams['prCode'] = $prCode;
        $data = $this->common_model->_select_row('program_data',$secParams);

        $secParams['pcDataType'] = $xwType;
        $podcast = $this->common_model->_select_row('podcast_data',$secParams);


        /*  XML Base에 프로그램 데이터 넣기    */
        for($i=0; $i<count($arrBaseXmlFixData[0])-1; $i++){
            if($arrBaseXmlFixData[0][$i]['name'] == 'title'){
                if($xwType == "AUDIO")      $arrBaseXmlFixData[0][$i]['value'] = $data['prName']. ' Audio';
                else if($xwType == "VIDEO") $arrBaseXmlFixData[0][$i]['value'] = $data['prName']. ' Video';
            } else if($arrBaseXmlFixData[0][$i]['name'] == 'description')   $arrBaseXmlFixData[0][$i]['value'] = $data['prContent'];
            else if($arrBaseXmlFixData[0][$i]['name'] == 'image'){
                $arrBaseXmlFixData[0][$i][0]['value'] = $podcast['pcThumb'];
                if($xwType == "AUDIO")      $arrBaseXmlFixData[0][$i][1]['value'] = $data['prName']. ' Audio';
                else if($xwType == "VIDEO") $arrBaseXmlFixData[0][$i][1]['value'] = $data['prName']. ' Video';
            } else if($arrBaseXmlFixData[0][$i]['name'] == 'itunes:subtitle')   $arrBaseXmlFixData[0][$i]['value'] = $data['prSpeaker'];
            else if($arrBaseXmlFixData[0][$i]['name'] == 'itunes:summary'){
                if($xwType == "AUDIO")          $arrBaseXmlFixData[0][$i]['value'] = $data['prName']. ' Audio';
                else if($xwType == "VIDEO")    $arrBaseXmlFixData[0][$i]['value'] = $data['prName']. ' Video';
            } else if($arrBaseXmlFixData[0][$i]['name'] == 'itunes:image')
                $arrBaseXmlFixData[0][$i]['attributes']['href'] = $podcast['pcThumb'];
        }


        /*  생성할 컨텐츠 데이터 가져오기    */
        unset($data); unset($secParams);
        $secParams['prCode'] = $prCode;
        $secParams['oKey'] = 'ctRegDate';
        $secParams['oType'] = 'desc';

        $data = $this->common_model->_select_list('content_data',$secParams);

        /*   VodServer Url 가져오기   */
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        $CI->config->load("vodServer",TRUE);
        $config = $CI->config->item('vodServer');
        $vodServer = $config['vodServerUrl'];


        /*  mp3 저장 디렉토리 경로 가져오기    */
        $prLen = strlen($prCode);
        $prDir = '';
        for($z=3; $z<= strlen($prCode); $z+=3) {
            $prParams['prCode'] = substr($prCode, 0, $z);
            $program = $this->common_model->_select_row('program_data',array('prCode'=>$prParams['prCode']));
            $prDir .= $program['prDir'].'/';
        }

        /*  XML에 데이터 넣기    */
        for($i=0; $i<count($data); $i++) {
            for($j=0; $j<count($arrItemXml)-1; $j++){
                if($arrItemXml[$j]['name'] == 'title')  $arrItemXml[$j]['value'] = $data[$i]['ctName'];
                else if($arrItemXml[$j]['name'] == 'pubDate'){
                    $strDate = substr($data[$i]['ctRegDate'],0,10);
                    $arrItemXml[$j]['value'] = date('D, j M Y', strtotime($strDate)).' 10:00:00 -0500';
                } else if($arrItemXml[$j]['name'] == 'description') $arrItemXml[$j]['value'] = $data[$i]['ctPhrase'];
                else if($arrItemXml[$j]['name'] == 'enclosure'){
                    if($xwType == "AUDIO"){


                        $arrCtMP3 = explode('-normal', $data[$i]['ctVideoNormal']);
                        $ctMP3 = '';
                        if(count($arrCtMP3) > 0) {
                            $ctMP3 = $arrCtMP3[0].".mp3";
                        }
                        $arrItemXml[$j]['attributes']['url'] = $vodServer . $prDir. $ctMP3;

                        /*
                        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
                        $CI = & get_instance();
                        $CI->config->load("soundcloud",TRUE);
                        $config = $CI->config->item('soundcloud');
                        $scClinetID = $config['scClinetID'];


                        $arrItemXml[$j]['attributes']['url'] = 0;
                        /*
                        if(!strpos($data[$i]['ctMP3'], 'ttp'))
                            $arrItemXml[$j]['attributes']['url'] = "http://api.soundcloud.com/tracks/".$data[$i]['ctMP3']."/download?client_id=".$scClinetID;
                        else
                            $arrItemXml[$j]['attributes']['url'] = $data[$i]['ctMP3'];
                        */
                    } else if($xwType == "VIDEO"){

                        $arrItemXml[$j]['attributes']['url'] = $vodServer.$prDir.$data[$i]['ctVideoNormal'];
                    }

                } else if($arrItemXml[$j]['name'] == 'itunes:author')   $arrItemXml[$j]['value'] = $data[$i]['ctSpeaker'];
                else if($arrItemXml[$j]['name'] == 'itunes:subtitle')   $arrItemXml[$j]['value'] = $data[$i]['ctEventDate'];
                else if($arrItemXml[$j]['name'] == 'itunes:summary')   $arrItemXml[$j]['value'] = $data[$i]['ctPhrase'];
            }

            $arrItemXmlFixData[] = $arrItemXml;
        }

        $arrXml = array_merge($arrBaseXmlFixData[0],$arrItemXmlFixData);
        unset($arrBaseXmlFixData[0]);
        $arrBaseXmlFixData[0] = $arrXml;

        $xmlSource = '<?xml version="1.0" encoding="UTF-8"?>
        ';
        $xmlKey = '';
        $xmlVal = '';
        $closeIdx = -1;

        /*  rss만들기    */
        foreach($arrBaseXmlFixData as $key1 => $val1){
            if($key1 === 'name'){
                $xmlSource .= '<'.$val1.'>';
                $xmlKey = 'name';
                $xmlVal = $val1;
            } else if($key1 === 'attributes'){
                $xmlSource = substr($xmlSource,0,-1);
                foreach($val1 as $key2 => $val2) $xmlSource .= ' '.$key2.'="'.$val2.'"';
                $xmlSource .= '>
                ';


            } else if(is_array($val1)){
                /*  channel 만들기    */
                $closeName[++$closeIdx] = $xmlVal;
                foreach($val1 as $key2 => $val2){
                    if($key2 === 'name'){
                        $xmlKey = 'name';
                        $xmlVal = $val2;
                        $xmlSource .= '<'.$val2.'>';
                    } else if($key2 === 'attributes'){
                        $xmlSource = substr($xmlSource,0,-1);
                        foreach($val2 as $key3 => $val3) $xmlSource .= ' '.$key3.'="'.$val3.'"';
                        $xmlSource .= '>';


                    } else if(is_array($val2)){
                        /* 채널이하 팟캐스트 정보 만들기    */
                        if($val1['0']['name'] == $val2['name'])$closeName[++$closeIdx] = $xmlVal;
                        foreach($val2 as $key3 => $val3){
                            if($key3 === 'name'){
                                if($xmlKey === 'name') $xmlSource .= '
                                ';
                                $xmlKey = 'name';
                                $xmlVal = $val3;
                                $xmlSource .= '<'.$val3.'>';
                            } else if($key3 === 'attributes'){
                                $xmlKey = 'attributes';
                                $xmlSource = substr($xmlSource,0,-1);
                                foreach($val3 as $key4 => $val4) $xmlSource .= ' '.$key4.'="'.$val4.'"';
                                $xmlSource .= '/>
                                ';

                            } else if($key3 === 'value'){
                                $xmlKey = 'value';
                                $xmlSource .= $val3.'</'.$xmlVal.'>
                                ';


                            } else if(is_array($val3)){
                                /*   item 만들기   */
                                if($val2['0']['name'] == $val3['name'])$closeName[++$closeIdx] = $xmlVal;
                                foreach($val3 as $key4 => $val4){
                                    if($key4 === 'name'){
                                        if($xmlKey === 'name') $xmlSource .= '
                                ';
                                        $xmlKey = 'name';
                                        $xmlVal = $val4;
                                        $xmlSource .= '<'.$val4.'>';
                                    } else if($key4 === 'attributes'){
                                        $xmlKey = 'attributes';
                                        $xmlSource = substr($xmlSource,0,-1);
                                        foreach($val4 as $key5 => $val5) $xmlSource .= ' '.$key5.'="'.$val5.'"';
                                        $xmlSource .= '/>
                                        ';
                                    } else if($key4 === 'value'){
                                        $xmlKey = 'value';
                                        $xmlSource .= $val4.'</'.$xmlVal.'>
                                        ';

                                        $cntVal2 =count($val2)-2 . "";
                                        if($val2[$cntVal2]['name'] == $xmlVal){
                                            $xmlSource .= '</'.$closeName[$closeIdx--].'>
                                            ';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $xmlSource .= '</channel>
        </rss>';
        write_file('./assets/podcast/'.$prCode.$xwType.'.xml',$xmlSource,'w+');
        chmod('./assets/podcast/'.$prCode.$xwType.'.xml', 0777);
    }


    public function jsonListWrite() {
        $today = date('Y-m-d');
        $date = date("Y-m-d", strtotime($today." -2 day")) ;
        $params['oKey'] = 'prCode';
        $params['oType'] = 'asc';
        $params['pcFileType'] = 'JSON';

        $podData = $this->common_model->_select_list('podcast_data',$params);
        for($i=0; $i<count($podData); $i++) {
            $data[$i]['prCode'] = $podData[$i]['prCode'];
            $data[$i]['prTitle'] = $podData[$i]['pcTitle'];
            $data[$i]['prSubTitle'] = $podData[$i]['pcSubTitle'];
            $data[$i]['prThumb'] = $podData[$i]['pcThumb'];
            $data[$i]['prAuthor'] = $podData[$i]['pcAuthor'];
        }

        $result[] = $data[0];
        $idx = 1;
        $pcSubIdx = 0;
        $parentIdx = 0;
        $parentDepthCnt = 0;

        for($i=1; $i<count($data); $i++) {
            $newCnt = $this->content_model->_select_newItem_list(array('ctEventDate >='=>$date, 'prCode'=>$data[$i]['prCode']));
            if($newCnt > 0) $data[$i]['prNew'] = "YES";
            $curPrCode = $data[$i]['prCode'];
            $prevPrCode = $data[$i-1]['prCode'];
            $curPrCodeLen = strlen($curPrCode);
            $prevPrCodeLen = strlen($prevPrCode);

            // #5. prCode 길이가 3자리 짧을 경우 (자식임)
            if($parentIdx && $prevPrCodeLen == $curPrCodeLen+3 && $curPrCodeLen != 6){
                $result[$idx-1]['pcSub'][$pcSubIdx++] = $data[$i];
                $parentIdx = $idx-1;

                // #4. prCode 길이가 3자리 길 경우 (#2에서 자식으로 포함되어 있으면서 또 자식일 경우)
            } else if($parentIdx && $prevPrCodeLen == $curPrCodeLen-3){
                $result[$parentIdx]['pcSub'][$pcSubIdx-1]['pcSub'][] = $data[$i];
                $parentDepthCnt ++;

                // #3. prCode 길이가 같은경우 (#2에서 자식으로 포함 되었으면 이것도 자식)
            } else if($parentIdx && $prevPrCodeLen == $curPrCodeLen){
                if($parentDepthCnt>2) {
                    $result[$parentIdx]['pcSub'][$pcSubIdx-1]['pcSub'][] = $data[$i];
                }else if($parentDepthCnt==2) {
                    $result[$parentIdx]['pcSub'][$pcSubIdx-1]['pcSub'][] = $data[$i];
                }else if($parentDepthCnt==1) {
                    $result[$idx-1]['pcSub'][$pcSubIdx++] = $data[$i];
                }

                // #2. prCode 길이가 3자리 길 경우 (자식임)
            } else if($prevPrCodeLen == $curPrCodeLen-3){
                $result[$idx-1]['pcSub'][$pcSubIdx++] = $data[$i];
                $parentIdx = $idx-1;
                $parentDepthCnt ++;

                // #1. 새로운 팟캐스트 항목
            } else {
                $result[$idx++] = $data[$i];
                $parentIdx = 0;
                $parentDepthCnt = 0;
                $pcSubIdx = 0;
            }

        }

        write_file('./assets/podcast/list.json',json_encode($result),'w+');
        chmod('./assets/podcast/list.json', 0777);
    }

    public function jsonWrite($prCode) {
        $prDir = '';
        for($i=3; $i<= strlen($prCode); $i+=3) {
            $params['prCode'] = substr($prCode, 0, $i);
            $program = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
            $prDir .= $program['prDir'].'/';
        }

        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        $CI->config->load("vodServer",TRUE);
        $config = $CI->config->item('vodServer');
        $vodServerUrl = $config['vodServerUrl'].$prDir;

        $CI->config->load("soundcloud",TRUE);
        $config = $CI->config->item('soundcloud');
        $scClinetID = $config['scClinetID'];

        $params['oKey'] = 'ctEventDate';
        $params['oType'] = 'desc';

        $contentData = $this->common_model->_select_list('content_data',$params);
        for($i=0; $i<count($contentData); $i++) {
            $data[$i]['idx'] = $i+1;
            $data[$i]['ctName'] = $contentData[$i]['ctName'];
            $data[$i]['ctContent'] = $contentData[$i]['ctContent'];
            $data[$i]['ctSpeaker'] = $contentData[$i]['ctSpeaker'];
            $data[$i]['ctPhrase'] = $contentData[$i]['ctPhrase'];
            $data[$i]['ctThumb'] = 'http://goodnewstv.kr/uploads/content/thumbs/'.$contentData[$i]['ctThumbS'];
            $data[$i]['ctDuration'] = $contentData[$i]['ctDuration'];
            $data[$i]['ctMediaType'] = $contentData[$i]['ctType'];
            $data[$i]['ctSource'] = $contentData[$i]['ctSource'];
            $data[$i]['ctEventDate'] = $contentData[$i]['ctEventDate'];
            if($contentData[$i]['ctVideoNormal'])   $data[$i]['ctVideoNormal'] = $vodServerUrl.$contentData[$i]['ctVideoNormal'];
            if($contentData[$i]['ctVideoLow'])      $data[$i]['ctVideoLow'] = $vodServerUrl.$contentData[$i]['ctVideoLow'];
            if($contentData[$i]['ctMP3']){
                if(!strpos($contentData[$i]['ctMP3'], 'ttp:')){
                    $data[$i]['ctAudioStream']  = 'http://api.soundcloud.com/tracks/'.$contentData[$i]['ctMP3'] .'/stream?client_id='. $scClinetID;
                    $data[$i]['ctAudioDown']     = 'http://api.soundcloud.com/tracks/'.$contentData[$i]['ctMP3'] .'/download/?client_id='. $scClinetID;
                } else{
                    $data[$i]['ctAudioStream'] = $contentData[$i]['ctMP3'];
                    $data[$i]['ctAudioDown'] = $contentData[$i]['ctMP3'];
                }
            }
        }
        write_file('./assets/podcast/'.$prCode.'.json',json_encode($data),'w+');
        chmod('./assets/podcast/'.$prCode.'.json', 0777);
    }

    public function makeExternalFiles($prCode) {
        $data = $this->common_model->_select_list('podcast_data',array('prCode'=>$prCode));
        /*  팟캐스트 목록 만들기    */
        $this->jsonListWrite();

        for($i=0; $i<count($data); $i++) {
            if($data[$i]['pcFileType'] == "XML"){
                /*  XML 생성하기    */
                $this->xmlWrite($prCode, $data[$i]['pcDataType']);
                sleep(2);

            } else if($data[$i]['pcFileType'] == "JSON"){
                /*  해당 팟캐스트 json 파일 업데이트    */
                $this->jsonWrite($prCode);

            }
        }
    }

    public function my_xml2array($__url)
    {
        $xml_values = array();
        $contents = file_get_contents($__url);
        $parser = xml_parser_create('');
        if(!$parser)
            return false;

        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return array();

        $xml_array = array();
        $last_tag_ar =& $xml_array;
        $parents = array();
        $last_counter_in_tag = array(1=>0);
        foreach ($xml_values as $data)
        {
            switch($data['type'])
            {
                case 'open':
                    $last_counter_in_tag[$data['level']+1] = 0;
                    $new_tag = array('name' => $data['tag']);
                    if(isset($data['attributes']))
                        $new_tag['attributes'] = $data['attributes'];
                    if(isset($data['value']) && trim($data['value']))
                        $new_tag['value'] = trim($data['value']);
                    $last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
                    $parents[$data['level']] =& $last_tag_ar;
                    $last_tag_ar =& $last_tag_ar[$last_counter_in_tag[$data['level']]++];
                    break;
                case 'complete':
                    $new_tag = array('name' => $data['tag']);
                    if(isset($data['attributes']))
                        $new_tag['attributes'] = $data['attributes'];
                    if(isset($data['value']) && trim($data['value']))
                        $new_tag['value'] = trim($data['value']);

                    $last_count = count($last_tag_ar)-1;
                    $last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
                    break;
                case 'close':
                    $last_tag_ar =& $parents[$data['level']];
                    break;
                default:
                    break;
            };
        }
        return $xml_array;
    }
}










































