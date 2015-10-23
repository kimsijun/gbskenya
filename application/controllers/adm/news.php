<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class news extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('news_model');
        $this->load->model('news_category_model');
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *  컨텐츠 생성의 경우 Vimeo, Youtube에 등록 되어 있는 컨텐츠를 선택 할 수 있도록 Json 파일 로드
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function write() {
        $data['newsCategory'] = $this->common_model->_select_list('news_category_data',array("oKey"=>"ncCode", "oType"=>"asc"));
        $data['newsCategoryCnt'] = $this->common_model->_select_cnt('news_category_data',array("oKey"=>"ncCode", "oType"=>"asc"));
        $data['newsList'] = $this->news_model->_select_list(array("oKey"=>"A.ncCode", "oType"=>"desc"));
        $data['newsListCnt'] = $this->news_model->_select_cnt(array("oKey"=>"A.ncCode", "oType"=>"desc"));
        for($i=0;$i<$data['newsCategoryCnt'];$i++){
            $data['newsCategory'][$i]['inNews'] = $this->news_model->_select_list(array("A.ncCode"=>$data['newsCategory'][$i]['ncCode']));
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
        $data = $this->news_model->_select_row(array('nwNO'=>$params['nwNO']));

        $data['newsCategory'] = $this->common_model->_select_list('news_category_data',array("oKey"=>"ncCode", "oType"=>"asc"));
        $data['newsCategoryCnt'] = $this->common_model->_select_cnt('news_category_data',array("oKey"=>"ncCode", "oType"=>"asc"));
        $data['newsList'] = $this->news_model->_select_list(array("oKey"=>"A.ncCode", "oType"=>"desc"));
        $data['newsListCnt'] = $this->news_model->_select_cnt(array("oKey"=>"A.ncCode", "oType"=>"desc"));
        for($i=0;$i<$data['newsCategoryCnt'];$i++){
            $data['newsCategory'][$i]['inNews'] = $this->news_model->_select_list(array("A.ncCode"=>$data['newsCategory'][$i]['ncCode']));
        }
        $url =  "./assets/json/vimeo/vimeo_cate.json";
        $newss = file_get_contents($url);
        $vimeoCate = json_decode($newss);
        $data['vimeo_cate'] = $vimeoCate;

        $url =  "./assets/json/youtube/youtube_cate.json";
        $newss = file_get_contents($url);
        $youtubeCate = json_decode($newss);
        $data['youtube_cate'] = $youtubeCate;

        //echo'<pre>';print_r($data);echo"</pre>";
        $this->_print($data);
    }


    /*  관리자 컨텐츠 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['news']);
        $secParams["oKey"]  = 'nwNO';
        $secParams["oType"] = 'desc';
        $result["cnt"] = $this->common_model->_select_cnt('news_data',$secParams);
        $data["total"] = $this->common_model->_select_cnt('news_data');
        $secParams["limit"] = 20;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->news_model->_select_list($secParams);

        $pager["CNT"] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->news_model->_select_row(array('nwNO'=>$params['nwNO']));
        $arrTemp = explode(',', $data['nwRelativeNews']);
        for($i=0; $i<count($arrTemp)-1; $i++){
            $data["nwRelative"][$i] = $this->news_model->_select_row(array('nwNO'=>$arrTemp[$i]));
        }
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
//echo "<pre>";print_r($params); echo "</pre>";exit;
        if($params['mode'] == "write"){

            $arrThumb = explode('/', $params['nwThumbS']);
            $cntThumb = count($arrThumb)-1;
            $ThumbSOrigin = $arrThumb[$cntThumb];

            $arrThumb = explode('/', $params['nwThumbL']);
            $cntThumb = count($arrThumb)-1;
            $ThumbLOrigin = $arrThumb[$cntThumb];
            $params['nwThumbSOrigin'] = $ThumbSOrigin;
            $params['nwThumbLOrigin'] = $ThumbLOrigin;

            $arrFileType = explode('/', $ThumbSOrigin);
            $cntFileType = count($arrFileType)-1;
            $fileTypeS = $arrFileType[$cntFileType];

            $arrFileType = explode('/', $ThumbLOrigin);
            $cntFileType = count($arrFileType)-1;
            $fileTypeL = $arrFileType[$cntFileType];


            $nwThumbS = $params['nwThumbS'];
            $nwThumbL = $params['nwThumbL'];
            $params['nwThumbS'] = md5(date('YmdHis').$ThumbSOrigin).$fileTypeS;
            $params['nwThumbL'] = md5(date('YmdHis').$ThumbLOrigin).$fileTypeL;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $nwThumbS);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contentsS = curl_exec($ch);
			curl_close($ch);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $nwThumbL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contentsL = curl_exec($ch);
			curl_close($ch);

            file_put_contents('./uploads/news/thumbs/'.$params['nwThumbS'], $contentsS);
            file_put_contents('./uploads/news/thumbl/'.$params['nwThumbL'], $contentsL);

            if(count($params['nwRelative_nwNO'])>0){
                for($i = 0; $i<count($params['nwRelative_nwNO']); $i++){
                    $params['nwRelativeNews'] .= $params['nwRelative_nwNO'][$i].",";
                }
            }
            $params['nwRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['nwModDate'] = 'NOW()';
            $params['nwRegDate'] = 'NOW()';
            unset($params['nwRelative_nwNO']);
            unset($params['isChangeContent']);
            unset($params['mode']);
            $this->common_model->_insert('news_data',$params);
            echo "<script>location.href='/adm/news/index';</script>";exit;


            // 관리자 컨텐츠 수정
        } else if($params['mode'] == "modify"){
            if($params['isChangeContent'] == 'Y') {
                $arrThumb = explode('/', $params['nwThumbS']);
                $cntThumb = count($arrThumb)-1;
                $ThumbSOrigin = $arrThumb[$cntThumb];

                $arrThumb = explode('/', $params['nwThumbL']);
                $cntThumb = count($arrThumb)-1;
                $ThumbLOrigin = $arrThumb[$cntThumb];
                $params['nwThumbSOrigin'] = $ThumbSOrigin;
                $params['nwThumbLOrigin'] = $ThumbLOrigin;


                $arrFileType = explode('/', $ThumbSOrigin);
                $cntFileType = count($arrFileType)-1;
                $fileTypeS = $arrFileType[$cntFileType];

                $arrFileType = explode('/', $ThumbLOrigin);
                $cntFileType = count($arrFileType)-1;
                $fileTypeL = $arrFileType[$cntFileType];


                $nwThumbS = $params['nwThumbS'];
                $nwThumbL = $params['nwThumbL'];
                $params['nwThumbS'] = md5(date('YmdHis').$ThumbSOrigin).$fileTypeS;
                $params['nwThumbL'] = md5(date('YmdHis').$ThumbLOrigin).$fileTypeL;
                
                $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $nwThumbS);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$contentsS = curl_exec($ch);
				curl_close($ch);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $nwThumbL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$contentsL = curl_exec($ch);
				curl_close($ch);
			

                file_put_contents('./uploads/news/thumbs/'.$params['nwThumbS'], $contentsS);
                file_put_contents('./uploads/news/thumbl/'.$params['nwThumbL'], $contentsL);
            }

            $params['nwModDate'] = 'NOW()';
            foreach($params as $k => $v){
                if($v == "") unset($params[$k]);
            }

            if(count($params['nwRelative_nwNO'])>0){
                for($i = 0; $i<count($params['nwRelative_nwNO']); $i++){
                    $params['nwRelativeNews'] .= $params['nwRelative_nwNO'][$i].",";
                }
            }
            unset($params['nwRelative_nwNO']);
            unset($params['isChangeContent']);
            unset($params['mode']);

            //echo'<pre>';print_r($params);echo"</pre>";
            $this->common_model->_update('news_data',$params,array('nwNO'=>$params['nwNO']));
            echo "<script>location.href='/adm/news/view/nwNO/".$params['nwNO']."';</script>";exit;

            // 관리자 컨텐츠 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $Code[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($Code); $i++){
                    $this->common_model->_delete('news_data',array('nwNO'=>$Code[$i]));
                }
            }else{
                $this->common_model->_delete('news_data',array('nwNO'=>$params['nwNO']));
            }
            redirect(base_url("/adm/news/index"));
        }

    }
}