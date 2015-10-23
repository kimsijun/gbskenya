<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   검색 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class search extends common {
    public function __construct(){
        parent::__construct();
        $this->load->model("search_model");
    }

    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        $searchUrl = base_url();
        foreach($secParams as $k=>$v)
            if($k != "prCode") {
                if($k == 'page'){
                    unset($k);
                    unset($v);
                }else   $searchUrl .= $k.'/'.$v.'/';

            }
            else $prCode = $v;
        $searchUrl = substr($searchUrl, 0, -1);

        /*
        | -------------------------------------------------------------------
        | # 검색 시 로그 저장 (검색번호그룹,아이디,키워드)
        | -------------------------------------------------------------------
        */
        $scLogParams['scKeyword'] = urldecode($secParams['secTxt']);
        $scLogParams['mbID'] = ($this->session->userdata('mbID')) ? $this->session->userdata('mbID') : $_SERVER['REMOTE_ADDR'];
        if(strpos($this->input->server("HTTP_REFERER"), 'search/index')){
            $mbID = ($this->session->userdata('mbID')) ? $this->session->userdata('mbID') : $_SERVER['REMOTE_ADDR'];
            $scLogData  = $this->common_model->_select_list('search_log',array('mbID'=>$mbID,'oKey'=>'scRegDate','oType'=>'DESC'));
            $scLogParams['scGroup'] = $scLogData[0]['scGroup'];
        }
        $scLogParams['scRemoteIP'] = $_SERVER['REMOTE_ADDR'];
        $scLogParams['scRegDate'] = 'NOW()';
        $scLogParams['scModDate'] = 'NOW()';
        $this->common_model->_insert('search_log',$scLogParams);
        if(!strpos($this->input->server("HTTP_REFERER"), 'search/index')){
            $scNO = $this->db->insert_id();
            $this->common_model->_update('search_log',array('scGroup'=>$scNO), array('scNO'=>$scNO));
        }

        //위젯config
        $json = read_file('./assets/json/config/search.json');
        $data['config'] = json_decode($json,true);
        $secParams['secText'] = $secParams['secTxt'];
        if($secParams['secTxt']){
            $secParams['secTxt'] = urldecode($secParams['secTxt']);
            if(!$secParams['secKey']) $secParams['secKey'] = "all";
        }
        $secParams['secTxt'] = explode(" ",$secParams['secTxt']);

        $data['secParams'] = $secParams;
        $secParams["oKey"] = "ctLikeCount";
        $secParams["oType"] = "DESC";
        $secParams["offset"] = isset($secParams["page"]) ? $secParams["page"] : "0";
        $secParams["limit"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];

        $data["total"] = $this->search_model->_select_cnt(array(), 0);
        $result["cnt"] = $this->search_model->_select_cnt($secParams,0);
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $baseParams['oKey'] = 'ctEventDate DESC,ctRegDate DESC,ctName DESC';
        $baseParams['oType'] = '';

        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];

        $secParams["oKey"] = "ctModDate";
        if(count($secParams['secTxt'])>1){
            $data["contentRecent"] = $this->search_model->_select_and_list($secParams, 0);
            $data["contentRecent"] =array_merge($data["contentRecent"],$this->search_model->_select_list($secParams, 0));
        }
        else $data["contentRecent"] = $this->search_model->_select_list($secParams, 0);
        for($i=0; $i<count($data["contentRecent"]); $i++) {
            $data["contentRecent"][$i]['ctModDate'] = $this->common_class->cut_str_han($data["contentRecent"][$i]['ctModDate'], 10,"");

            $baseParams["prCode"] = $data["contentRecent"][$i]['prCode'];
            $baseContent = $this->common_model->_select_list('content_data',$baseParams);
            $cntBase = count($baseContent);
            for($j=0; $j<$cntBase; $j++) {
                if($data["contentRecent"][$i]['ctNO'] == $baseContent[$j]['ctNO']){
                    $data["contentRecent"][$i]['ctPlayListPage'] = floor($j / 5) + 1;
                    break;
                }
            }

            for($j=0;$j<count($secParams['secTxt']);$j++){
                $data["contentRecent"][$i]['ctName'] = str_replace($secParams['secTxt'][$j], '<strong>'.$secParams['secTxt'][$j].'</strong>',$data["contentRecent"][$i]['ctName']);
                $data["contentRecent"][$i]['ctSpeaker'] = str_replace($secParams['secTxt'][$j], '<strong>'.$secParams['secTxt'][$j].'</strong>',$data["contentRecent"][$i]['ctSpeaker']);
                $data["contentRecent"][$i]['ctContent'] = str_replace($secParams['secTxt'][$j], '<strong>'.$secParams['secTxt'][$j].'</strong>',$data["contentRecent"][$i]['ctContent']);

                if(strpos($data["contentRecent"][$i]['ctName'],$secParams['secTxt'][$j])) $data["contentRecent"][$i]['ctNameSec'] = 'YES';
                if(strpos($data["contentRecent"][$i]['ctSpeaker'],$secParams['secTxt'][$j])) $data["contentRecent"][$i]['ctSpeakerSec'] = 'YES';
                if(strpos($data["contentRecent"][$i]['ctContent'],$secParams['secTxt'][$j])) $data["contentRecent"][$i]['ctContentSec'] = 'YES';
            }

            if(strlen($data["contentRecent"][$i]["prCode"])==3){
                $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>$data["contentRecent"][$i]["prCode"]));
                $data["contentRecent"][$i]['prDepth1']= $prDepth1["prName"];
            }elseif(strlen($data["contentRecent"][$i]["prCode"])==6){
                $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,3)));
                $data["contentRecent"][$i]['prDepth1']= $prDepth1["prName"];
                $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>$data["contentRecent"][$i]["prCode"]));
                $data["contentRecent"][$i]['prDepth2']= $prDepth2["prName"];
            }elseif(strlen($data["contentRecent"][$i]["prCode"])==9){
                $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,3)));
                $data["contentRecent"][$i]['prDepth1']= $prDepth1["prName"];
                $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,6)));
                $data["contentRecent"][$i]['prDepth2']= $prDepth2["prName"];
                $prDepth3 = $this->common_model->_select_row('program_data',array('prCode'=>$data["contentRecent"][$i]["prCode"]));
                $data["contentRecent"][$i]['prDepth3']= $prDepth3["prName"];
            }elseif(strlen($data["contentRecent"][$i]["prCode"])==12){
                $prDepth1 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,3)));
                $data["contentRecent"][$i]['prDepth1']= $prDepth1["prName"];
                $prDepth2 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,6)));
                $data["contentRecent"][$i]['prDepth2']= $prDepth2["prName"];
                $prDepth3 = $this->common_model->_select_row('program_data',array('prCode'=>substr($data["contentRecent"][$i]['prCode'],0,9)));
                $data["contentRecent"][$i]['prDepth3']= $prDepth3["prName"];
                $prDepth4 = $this->common_model->_select_row('program_data',array('prCode'=>$data["contentRecent"][$i]["prCode"]));
                $data["contentRecent"][$i]['prDepth4']= $prDepth4["prName"];
            }
        }

        if(!$prCode) {
            $secParams["oKey"] = "prRegDate";
            $data["programRecent"]  = $this->search_model->_select_list($secParams, 1);

            for($i=0; $i<count($data['programRecent']); $i++){
                for($j=0;$j<count($secParams['secTxt']);$j++){
                    $data["programRecent"][$i]['prName'] = str_replace($secParams['secTxt'][$j], '<strong>'.$secParams['secTxt'][$j].'</strong>',$data["programRecent"][$i]['prName']);
                }
                $data["programRecent"][$i]["programSub"] = $this->common_model->_select_list('program_data',array('prPreCode'=>$data["programRecent"][$i]["prCode"]));
                if(count($data["programRecent"][$i]["programSub"])>1){
                    $data["programRecent"][$i]["prIsSub"] = "YES";
                }
            }
        }

        $data['today7day'] = date("Ymd", strtotime(date('Ymd')." -7 day"));
        $data['today15day'] = date("Ymd", strtotime(date('Ymd')." -15 day"));
        $data['today1month'] = date("Ymd", strtotime(date('Ymd')." -1 month"));
        $data['today2month'] = date("Ymd", strtotime(date('Ymd')." -2 month"));
        $data['today'] = date('Ymd');
        $data['searchUrl'] = $searchUrl;
        $data['prCode'] = $prCode;
        $this->_print($data);
    }


    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();
        //위젯config
        $json = read_file('./assets/json/config/search.json');
        $data['config'] = json_decode($json,true);

        $html="";

        if($params['mode'] == "ctMoreLike"){

        }elseif($params['mode'] == "ctMoreRecent"){

        }elseif($params['mode'] == "prMore"){
            unset($params['mode']);
            $data["programRecent"]  = $this->common_model->_select_list('program_data',$params);

            for($i=0; $i<count($data['programRecent']); $i++){
                $data["programRecent"][$i]["programSub"] = $this->common_model->_select_list('program_data',array('prPreCode'=>$data["programRecent"][$i]["prCode"]));
                if(count($data["programRecent"][$i]["programSub"])>1){
                    $data["programRecent"][$i]["prIsSub"] = "YES";
                }
            }

            for($i=0; $i<count($data["programRecent"]); $i++){
                $html .= '<dl class="livecont"><a href="/program/';
                if($data["programRecent"][$i]['prIsSub']=="YES"){
                    $html .= 'index';
                }else
                    $html .= 'view';
                $html .= '/prCode/'.$data["programRecent"][$i]["prCode"].'">';
                $html .= '<dt>'.$data["programRecent"][$i]['prName'].' ';
                if($data["programRecent"][$i]['prIsSub']=="YES") $html .='+';
                $html .= '</dt>
                <dd class="img">
                    <img src="/uploads/program/'.$data["programRecent"][$i]['prThumb'].'" width="'.$data['config']['snd']['width'].'" height="'.$data['config']['snd']['height'].'">
                </dd>
                </a>
                </dl>';
            }
            $html .='<div style="clear: both;">
            <form class="frmMore" method="post">
                <input type="hidden" name="mode" value="prMore">
                <input type="hidden" class="prSearchCnt" name="prSearchCnt" value="">
                <input type="hidden" name="secTxt" value="{secParam.secTxt}">
                <span class="prMore">결과 더보기</span>
            </form>
        </div>';
            echo json_encode($html);

        /*
        | -------------------------------------------------------------------
        | # 검색결과 사용 로그저장 (콘탠츠타입[프로그램/콘텐츠],콘텐츠아이디,콘텐츠제목,콘텐츠링크)
        | -------------------------------------------------------------------
        */
        }elseif($params['mode'] == "searchResultSetLog"){
            unset($params['mode']);
            $mbID = ($this->session->userdata('mbID')) ? $this->session->userdata('mbID') : $_SERVER['REMOTE_ADDR'];
            $scLogData  = $this->common_model->_select_list('search_log',array('mbID'=>$mbID,'oKey'=>'scRegDate','oType'=>'DESC'));
            if($scLogData[0]['scSecResult']){
                $oldResult = (array)json_decode($scLogData[0]['scSecResult']);
                if($oldResult[1])   $setParams = $oldResult;
                else                $setParams[] = $oldResult;
                $setParams[] = $params;
                $scSecResult = json_encode($setParams);
            }else
                $scSecResult = json_encode($params);

            $this->common_model->_update('search_log',array('scSecResult'=>$scSecResult), array('scNO'=>$scLogData[0]['scNO']));

        }

    }
}













