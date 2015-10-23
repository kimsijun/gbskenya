<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE Content Main Controller Class
| @ AUTHOR  JoonCh
| @ SINCE   13. 8. 28.
| -------------------------------------------------------------------
| 컨텐츠 메인노출 설정파일
*/

class contentmain extends common {

    public function index(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/contentmain.json');
        $data = json_decode($json);
        foreach($data as $k =>$v)   $contentMain[$k] = $v;
        unset($data);
        $this->load->model('program_model');
        $data['program'] = $this->program_model->_select_list(array("oKey"=>"prCode", "oType"=>"asc"));

        $this->load->model("content_model");
        $data['content'] = $this->content_model->_select_list(array('ctNOs'=>$contentMain));
        $this->_print($data);
    }

    public function process() {
        $this->load->helper('file');
        $params = $this->input->post();

        if($params['mode'] == "write"){
            unset($params['mode']);
            $jsonData = json_encode($params['ctNO']);
            write_file('./assets/json/config/contentmain.json', $jsonData);
        }
        redirect(base_url("/adm/contentmain/index"));

    }

    /*  AJAX 처리 메소드    */
    public function ajax_process() {
        $params = $this->input->post();

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  프로그램 별 컨텐츠 불러오기
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($params['mode'] == "getContent"){
            $this->load->model('content_model');
            $data = $this->content_model->_select_list(array("prCode"=>$params['prCode']));
            $html = '<select class="slc_content">';
            $html.= '<option value="">선택</option>';
            foreach($data as $v)
                $html .= '<option value="'.$v['ctNO'].'" class="ctNO'.$v['ctNO'].'" prName="'.$v['prName'].'" ctName="'.$v['ctName'].'">'.$v['ctName'].'</option>';
            $html .= '</select>';

            echo json_encode($html);


        }
    }
}