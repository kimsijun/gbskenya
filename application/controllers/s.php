<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE 공유 Redirect 전용 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   14. 2. 11.
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

class s extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model('content_model');
        $this->load->model('program_model');
    }


    public function p() {
        $data = $this->common_model->_select_row('program_data',array("prCode"=>$this->uri->segment(3)));
        $data["prName"] = str_replace(" ", "-", $data["prName"]);
        redirect(base_url("/program/view/prCode/".$data['prCode']."/prName/".$data['prName']));
    }


    public function c() {
        $data = $this->common_model->_select_row('content_data',array("ctNO"=>$this->uri->segment(3)));
        $data["ctName"] = str_replace(' ', '-', $this->common_class->cut_str_han($data["ctName"], 30,""));
        redirect(base_url("/content/view/ctNO/".$data['ctNO']."/ctName/".$data['ctName']));

    }

    public function s() {
        $data = $this->common_model->_select_row('program_data',array("prCode"=>$this->uri->segment(3)));
        $data["prName"] = str_replace(" ", "-", $data["prName"]);
        redirect(base_url("/sermon/view/prCode/".$data['prCode']."/prName/".$data['prName']));
    }

    public function sp() {
        $data["content"] = $this->common_model->_select_row('content_data',array("ctNO"=>$this->uri->segment(3)));
        $data["program"] = $this->common_model->_select_row('program_data',array("prCode"=>$data["content"]["prCode"]));
        $data["program"]["prName"] = str_replace(" ", "-", $data["program"]["prName"]);
        $secParams["oKey"] = "ctRegDate";
        $secParams["oType"] = "DESC";
        $secParams["prCode"] = $data['program']['prCode'];
        $data["contentList"] = $this->common_model->_select_list('content_data',$secParams);

        $idx = 0;
        for($i=0; $i<count($data["contentList"]); $i++){
            if($data["contentList"][$i]['ctNO'] == $data["content"]['ctNO'])    $idx = $i;
        }
        $ctPage = (int)($idx/5) + 1;
        redirect(base_url("/sermon/view/prCode/".$data["program"]['prCode']."/prName/".$data["program"]['prName']."/ctNO/".$data["content"]["ctNO"]."/ctPage/".$ctPage));

    }


    public function cp() {
        $data["content"] = $this->common_model->_select_row('content_data',array("ctNO"=>$this->uri->segment(3)));
        $data["program"] = $this->common_model->_select_row('program_data',array("prCode"=>$data["content"]["prCode"]));
        $data["program"]["prName"] = str_replace(" ", "-", $data["program"]["prName"]);
        $secParams["oKey"] = "ctRegDate";
        $secParams["oType"] = "DESC";
        $secParams["prCode"] = $data['program']['prCode'];
        $data["contentList"] = $this->common_model->_select_list('content_data',$secParams);

        $idx = 0;
        for($i=0; $i<count($data["contentList"]); $i++){
            if($data["contentList"][$i]['ctNO'] == $data["content"]['ctNO'])    $idx = $i;
        }
        $ctPage = (int)($idx/5) + 1;
        redirect(base_url("/program/view/prCode/".$data["program"]['prCode']."/prName/".$data["program"]['prName']."/ctNO/".$data["content"]["ctNO"]."/ctPage/".$ctPage));
    }

    public function sun() {
        $data["content"] = $this->common_model->_select_list('content_data',array("oKey"=>"ctRegDate","oType"=>"desc","prCode"=>"001001001"));
        redirect(base_url("/sermon/view/prCode/001001001/prName/".$data["content"][0]['prName']."/ctNO/".$data["content"][0]['ctNO']."/ctPage/1"));
    }

    public function sat() {
        $data["content"] = $this->common_model->_select_list('content_data',array("oKey"=>"ctRegDate","oType"=>"desc","prCode"=>"005001"));
        redirect(base_url("/program/view/prCode/005001/prName/".$data["content"][0]['prName']."/ctNO/".$data["content"][0]['ctNO']."/ctPage/1"));
    }
}