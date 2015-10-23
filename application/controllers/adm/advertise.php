<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class advertise extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model('advertise_model');
    }

    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['advertise']);

        $secParams["oKey"] = "adNO"; $secParams["oType"] = "DESC";
        $result["cnt"] = $data['listCnt'] = $this->common_model->_select_cnt('advertise_data',$secParams);
        $secParams["limit"] = 10;
        $secParams["offset"] = $secParams["page"]? $secParams["page"]:0;
        $result["list"] = $this->common_model->_select_list('advertise_data',$secParams);
        for($i=0; $i<$result["cnt"]; $i++){
            if($result["list"][$i]['adStatus'] =='APPLY') $result["list"][$i]['adStatus'] = '접수';
            elseif($result["list"][$i]['adStatus'] =='IN PROGRESS') $result["list"][$i]['adStatus'] = '진행중';
            elseif($result["list"][$i]['adStatus'] =='COMPLETE') $result["list"][$i]['adStatus'] = '완료';
        }
        $pager["CNT"] = $data['listCnt'] = $result["cnt"];
        $pager["PRPAGE"] = isset($secParams["limit"]) ? $secParams["limit"] : $this->cfg["perpage"];
        $pagerHtm = $this->_set_pager($pager);

        $data["list"] = $result["list"];
        $data["pager"] = $pagerHtm;
        $data["pagerIdx"] = $result["cnt"] - $secParams["offset"];
        $data["secParams"] = $secParams;
        $this->_print($data);
    }

    public function view() { 
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('advertise_data',array('adNO'=>$params['adNO']));
        if($data['adStatus'] =='APPLY') $data['adStatus'] = '접수';
        elseif($data['adStatus'] =='IN PROGRESS') $data['adStatus'] = '진행중';
        elseif($data['adStatus'] =='COMPLETE') $data['adStatus'] = '완료';
        $this->_print($data);
    }

    public function modify() { 
        $params = $this->input->post();
        $data = $this->common_model->_select_row('advertise_data',array('adNO'=>$params['adNO']));

        $this->_print($data);
    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();

        if($params['mode'] == "modify"){
            unset($params['mode']);
            $params['adModDate'] = 'NOW()';
            $this->common_model->_update('advertise_data',$params,array('adNO'=>$params['adNO']));

            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/advertise/view/adNO/".$params['adNO']));

            // 관리자 회원 선택삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $Code[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($Code); $i++){
                    $this->common_model->_delete('advertise_data',array('adNO'=>$Code[$i]));
                }
            }else{
                $this->common_model->_delete('advertise_data',array('adNO'=>$params['adNO']));
            }
            $path = "./_cache/%cache"; delete_files($path, true);
            redirect(base_url("/adm/advertise/index"));
        }
    }



}
?>
