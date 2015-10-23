<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE Site Configuration
| @ AUTHOR  JoonCh
| @ SINCE   13. 8. 31.
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

class site extends common {

    public function main_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/main.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function live_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/live.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function program_list_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/program_list.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function program_view_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/program_view.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function content_list_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/content_list.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function content_view_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/content_view.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function search_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/search.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function mypage_config(){
        $this->load->helper('file');
        $json = read_file('./assets/json/config/mypage.json');
        $data = json_decode($json,true);
        $this->_print($data);
    }

    public function process() {
        $this->load->helper('file');
        $params = $this->input->post();

        if($params['mode'] == "main_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/main.json', $jsonData);
            redirect(base_url("/adm/site/main_config"));

        } else if($params['mode'] == "live_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/live.json', $jsonData);
            redirect(base_url("/adm/site/live_config"));

        } else if($params['mode'] == "program_list_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/program_list.json', $jsonData);
            redirect(base_url("/adm/site/program_list_config"));

        } else if($params['mode'] == "program_view_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/program_view.json', $jsonData);
            redirect(base_url("/adm/site/program_view_config"));

        } else if($params['mode'] == "content_list_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/content_list.json', $jsonData);
            redirect(base_url("/adm/site/content_list_config"));

        } else if($params['mode'] == "content_view_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/content_view.json', $jsonData);
            redirect(base_url("/adm/site/content_view_config"));

        } else if($params['mode'] == "search_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/search.json', $jsonData);
            redirect(base_url("/adm/site/search_config"));

        } else if($params['mode'] == "mypage_write"){
            $data = $params;
            unset($data['mode']);
            $jsonData = json_encode($data);
            write_file('./assets/json/config/mypage.json', $jsonData);
            redirect(base_url("/adm/site/mypage_config"));

        } else if($params['mode'] == "write"){

            unset($params['mode']);
            $jsonData = json_encode($params['ctNO']);
            write_file('./assets/json/config/content/contentmain.js', $jsonData);
        }


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