<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 컨텐츠 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class news_category extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }


    /*  관리자 프로그램 생성    */
    public function write() {
        $params = $this->_get_sec();
        $this->_print($params);
    }



    /*  관리자 프로그램 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['news_category']);
        $secParams["oKey"] = "ncCode";
        $secParams["oType"] = "asc";

        $result["cnt"] = $this->common_model->_select_cnt('news_category_data',$secParams);
        $result["list"] = $this->common_model->_select_list('news_category_data',$secParams);

        $data["list"] = $result["list"];
        $data["secParams"] = $secParams;

        // Depth 생성
        for($i=0; $i<count($data['list']); $i++)
            if(strlen($data['list'][$i]['ncCode']) > 3)
                $data['list'][$i]['ncDepth'] = strlen($data['list'][$i]['ncCode']) / 3;

        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('news_category_data',array('ncCode'=>$params['ncCode']));
        $this->_print($data);
    }


    /*  관리자 게시판 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('news_category_data',array('ncCode'=>$params['ncCode']));
        $this->_print($data);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        // 관리자 프로그램 생성처리
        if($params['mode'] == "write"){

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  1. 하위 카테고리의 프로그램 생성의 경우 선택한 카테고리 보다 1 Depth 작은 데이터중 가장 큰 ncCode의 + ~001
             *      - (1 Depth 작은 데이터가 없을 경우는 해당 ncCode 뒤에 001 추가함)
             *  2. 최상위 카테고리의 프로그램 생성의 경우 DB에 1 Depth의 데이터중 가장 큰 ncCode의 + ~001
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($params['ncCode']){
                $ncCodeLen = strlen($params['ncCode']) + 3;
                $params['ncPreCode'] = $params['ncCode'];
                $data = $this->common_model->_select_list('news_category_data',array("oKey"=>"ncCode", "oType"=>"desc", "ncPreCode"=>$params['ncCode'], "LENGTH(ncCode)"=>$ncCodeLen));
                if($data[0]['ncCode']){
                    $max_ncCode = "00".(int)($data[0]['ncCode'] + 1);
                    $ncCode = substr($max_ncCode,(int)($ncCodeLen * -1));
                } else
                    $ncCode = $params['ncCode']."001";

            } else {
                $data = $this->common_model->_select_list('news_category_data',array("oKey"=>"ncCode", "oType"=>"desc"));
                $max_ncCode = "00".(int)(substr($data[0]['ncCode'],0,3) + 1);
                $ncCode = substr($max_ncCode,-3);
            }

            $params['ncRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['ncCode'] = $ncCode;
            $params['ncModDate'] = 'NOW()';
            $params['ncRegDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_insert('news_category_data',$params);
        // 관리자 프로그램 수정처리
        } else if($params['mode'] == "modify"){
            $params['ncModDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('news_category_data',$params,array('ncCode'=>$params['ncCode']));

        // 관리자 프로그램 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $Code[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($Code); $i++){
                    $this->common_model->_delete('news_category_data',array('ncCode'=>$Code[$i]));
                }
            }else{
                $this->common_model->_delete('news_category_data',array('ncCode'=>$params['ncCode']));
            }
        }

        redirect(base_url("/adm/news_category/index"));

    }

}