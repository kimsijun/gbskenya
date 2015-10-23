<?php
/*
| -------------------------------------------------------------------
| @ TITLE   공용 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 공용 컨트롤
| -------------------------------------------------------------------
*/
header('Content-Type: text/html; charset=UTF-8');
class common extends CI_Controller {

    protected $cfg = array(
        "module_dir_name" => "_gns",
        "title" => "GBS - Goodnews Broadcasting System",
        'metaDescription' => "GBS Kenya - The best broadcasting system in Kenya, HomeShopping, UEFA Champions League, Europe League, Gospel, Christian, Zoom in Africa, Womens View",
        "session_key" => "mbID",
        "perpage" => 10,
        "script_url" => "http://script.bahamutlabs.com",
    );
    public $scripts = array();
    public $manager = array();
    public $is_super_admin = FALSE;


    public function __construct(){
        parent::__construct();
        /*  GNSolution 공통상수 정의    */
        define('SITE_NO',		3);     //GBS 사이트 번호
        define('SITE_FULL_URL',		str_replace('index.php/','',site_url("/".$this->uri->uri_string()."/".$this->uri->assoc_to_uri(array()))));


        $this->config->load('cache_config');
        $cacheConfig = $this->config->item('cacheList');
        if(in_array($this->router->directory.$this->router->class.'/'.$this->router->method, $cacheConfig)){
            $this->template->define("tpl", $this->router->directory.$this->router->class.'/'.$this->router->method.".html");
            $this->template->setCache('tpl', 3600);
            if ($this->template->isCached('tpl')) {
                $this->_print();
                exit;
            }
        }
    }
    /**
     * 검색세팅 : POST -> URI Segment로 변경하여 Redirect
     * @param array $exceptParams array("edDcType","edIsOver");
     */
    public function _set_sec($exceptParams = NULL) {
        //제외항목 우선 제거
        if(is_array($exceptParams)) {
            foreach($exceptParams as $val) {
                if(isset($_POST[$val])) {
                    unset($_POST[$val]);
                }
            }
        }

        $params = $this->input->post();
        $paramsData = array();

        if(is_array($params)) {
            foreach($params as $key => $val) {
                if( is_array($this->input->post($key)) ) {
                    if(! $val[0] && ! $val[1]) {
                        continue;
                    }
                    $paramsData[$key] = "Array";
                    $paramsData[$key."_CNT"] = count($this->input->post($key));
                    foreach($this->input->post($key) as $key1 => $data) {
                        $paramsData[$key."_".$key1] = urlencode($data);
                    }
                    continue;
                }
                if(! $val) {
                    continue;
                }
                $paramsData[$key] = urlencode($val);
            }

            $url = site_url("/".$this->uri->uri_string()."/".$this->uri->assoc_to_uri($paramsData));
            redirect($url);
        }
    }
    /**
     * 검색세팅 : URI Segment를 Key-Value 배열로 생성
     * @param array $assoc (URI Segment No)
     */
    public function _get_sec($assoc=1) {
        $uriAry = $this->uri->ruri_to_assoc($assoc);
        $return = array();
        foreach ($uriAry as $key => $data) {
            if ($data === "Array") {
                $tmpAry = array();
                for ($i = 0; $i < $uriAry[$key . "_CNT"]; $i++) {
                    $tmpAry[] = $uriAry[$key . "_" . $i];
                }
                $return[$key] = $tmpAry;
                continue;
            }
            if (strrpos($key, "_CNT")) {
                continue;
            }
            $return[$key] = $data;
        }
        return $return;
    }




    /**
     * 페이징 : Codeigniter Pagination Library config & load
     * @param array $params
     */
    public function _set_pager($params) {
        $config["suffix"] = $this->config->item("url_suffix");

        $config["total_rows"] = $params["CNT"];
        $config["per_page"] = $params["PRPAGE"];

        $url = preg_replace("/\/page\/(.*)/", "", "/".$this->uri->uri_string());
        $config["first_url"] = $url.$config["suffix"];
        $config["base_url"] = $url."/page/";
        $config["uri_segment"] = count(explode("/", $url)) + 1;

        $config["num_links"] = 10;
        $config['full_tag_open'] = '<div class="text-center"><ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul></div>';
        $config["first_link"] = "처음";
        $config["first_tag_open"] = "";
        $config["first_tag_close"] = "";
        $config["prev_link"] = "&lt;";
        $config["prev_tag_open"] = "";
        $config["prev_tag_close"] = "";

        $config["next_link"] = "&gt;";
        $config["next_tag_open"] = "";
        $config["next_tag_close"] = "";
        $config["last_link"] = "끝";
        $config["last_tag_open"] = "";
        $config["last_tag_close"] = "";

        $config["cur_tag_open"] = "<li class='active'><a href=\"#\">";
        $config["cur_tag_close"] = "</a></li>";
        $this->load->library("pagination");
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }



    /**
     *  템플릿언더바 사용
     * @param array $params
     */
    public function _print($data=array(), $result=FALSE) {
        $this->_set_value($data);
        $this->_set_modules($data);
        $this->_program_all($data);

        $this->template->assign($data);
        if($result === TRUE) {
            $this->template->define("tpl", $data['templateHtml']);
        } else {
            $this->template->define("tpl", $this->router->directory.$this->router->class.'/'.$this->router->method.".html");
        }


        $this->config->load('cache_config');
        $cacheConfig = $this->config->item('cacheList');

        if(in_array($this->router->directory.$this->router->class.'/'.$this->router->method, $cacheConfig)){
            $this->template->define("header", "_gns/layout/header.html");
            $this->template->setCache('tpl', 3600);
            $this->template->print_("header");
        }

        if($result === TRUE) {
            return $this->template->fetch("tpl");
        } else {
            $this->template->print_("tpl");
        }
    }



    /**
     *  로그인 여부 및 설정 정보 세팅
     * @param array $params
     */
    private function _set_value(&$data) {
        $uagent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($uagent, 'iPhone') == true){
            $data["environment"] = "mobile";
            $data["device"] = "iphone";

        } else if (strpos($uagent, 'iPad') == true) {
            $data["environment"] = "mobile";
            $data["device"] = "ipad";

        }  else if (strpos($uagent, 'Android') == true) {
            $data["environment"] = "mobile";
            $data["device"] = "android";

        } else if(strpos($uagent, 'Mac') == true){
            $data["environment"] = "mac";
            $data["device"] = "mac";

        } else {
            $data["environment"] = "pc";
        }

        $data["SITE_FULL_URL"] = SITE_FULL_URL;
        $data["url_suffix"] = $this->config->item("url_suffix");
        $data["uri_string"] = urldecode($this->uri->uri_string());
        $data["rsegment_1"] = urldecode($this->uri->rsegment(1));
        $data["rsegment_2"] = urldecode($this->uri->rsegment(2));
        $data['controller'] = $this->router->class;
        $data['method']     = $this->router->method;
        $data['dir']     = $this->router->directory;
        $data["referer"] = $this->input->server("HTTP_REFERER");
        $data["cfg"] = $this->cfg;

        if(! isset($data["title"])) {
            $data["title"] = $this->cfg["title"];
        }
        
        if(! isset($data["metaDescription"])) {
            $data["metaDescription"] = $this->cfg["metaDescription"];
        }

        if($this->session->userdata($this->cfg["session_key"])) {
            $data['mbID'] = $this->session->userdata($this->cfg["session_key"]);
            $data['member'] = $this->common_model->_select_row('member_data',array('mbID'=>$data['mbID']));
            $data["is_login"] = TRUE;
        } else {
            $data["is_login"] = FALSE;
        }

        if($this->session->userdata('mbIsAdmin')=="YES") {
            $data["is_Admlogin"] = TRUE;
        } else {
            $data["is_Admlogin"] = FALSE;
        }
    }



    /**
     *  header/footer/menu 모듈화 하기
     * @param array $params
     */
    private function _set_modules(&$data) {
        $filePath = APPPATH."views/".$this->cfg["module_dir_name"];
        $map = directory_map($filePath);
        if(is_array($map)) {
            $modulesList = array();
            foreach($map as $dir => $dirRow) {
                if(is_array($dirRow)) {
                    foreach($dirRow as $k => $modulePath) {
                        if(is_array($modulePath)) {
                            foreach($modulePath as $v) {
                                $htmlPath = $this->cfg["module_dir_name"]."/".$dir."/".$k."/".$v;
                                $modulesList[$dir."_".$k."_".substr($v,0,-5)] = $htmlPath;
                            }
                        }else {
                            $htmlPath = $this->cfg["module_dir_name"]."/".$dir."/".$modulePath;
                            $modulesList[$dir."_".substr($modulePath,0,-5)] = $htmlPath;
                        }
                    }
                }
            }
            $this->template->define($modulesList);
        }
        return TRUE;
    }

    //프로그램 전체보기
    public function _program_all(&$data) {
        $secParams['LENGTH(prCode)'] = '3';
        $secParams['oKey'] = 'prSort';
        $secParams['oType'] = 'asc';
        $data['programList'] = $this->common_model->_select_list('program_data',$secParams);
        $this->load->model('program_model');
        $data['program2depthList'] = $this->program_model->_select_list_all($secParams);
        for($i=0; $i<count($data['programList']); $i++) {
            for($j=0; $j<count($data['program2depthList']); $j++) {
                if($data['programList'][$i]['prCode'] == $data['program2depthList'][$j]['prPreCode'])
                    $data['programList'][$i]['prSub'][] = $data['program2depthList'][$j];
            }
        }
        $data['board_cfg'] = $this->common_model->_select_list('board_cfg');
    }


}
?>