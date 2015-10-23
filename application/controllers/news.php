<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class news extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("news_model");
        $this->load->model("view_model");
        $this->load->model("mypage_model");
    }

    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('news_data',array('nwNO'=>$params['nwNO']));
        unset($params['news']);
        $viewCount['nwViewCount'] = $data['nwViewCount'] + 1;
        $viewCount['nwViewMonthCount'] = $data['nwViewMonthCount'] + 1;
        $this->common_model->_update('news_data',$viewCount,array('nwNO'=>$params['nwNO']));

        // 조회 카운트 조건
        $paramView["vType"] = "NEWS";
        $paramView["nwNO"] = $params['nwNO'];
        // 조회 카운트 증가
        if($this->session->userdata('mbID'))
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

        if($data["member"]) $paramView["mbID"] = $data["member"]["mbID"];
        else $paramView["mbID"] = $_SERVER['REMOTE_ADDR'];
        $paramView['vRemoteIP'] = $_SERVER['REMOTE_ADDR'];
        $paramView['vRegDate'] = 'NOW()';
        $this->view_model->_insert($paramView);
        if($this->session->userdata('mbID')){
            $data["member"] = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $data['mbLoginID'] = $this->session->userdata('mbID');

            $paramVew["mpSection"] = "VIEW";
            $paramVew["mpType"] = "NEWS";
            $paramVew["nwNO"] = $data["nwNO"];
            $paramVew["mbID"] = $data["member"]["mbID"];
            $paramVew['mpRemoteIP'] = $_SERVER['REMOTE_ADDR'];

            // 뷰 로그
            $isView = $this->common_model->_select_cnt('mypage_log',$paramVew);
            if(!$isView){
                $paramVew['mpRegDate'] = 'NOW()';
                $this->mypage_model->_insert($paramVew);
            }
        }
        if( strlen($data['ncCode']) > 3 ){
            $data['ncCodePrev'] = substr($data['ncCode'],0,3);
            $data['ncPreCodeData'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data['ncPreCode']));
        }else{
            $data['ncCodePrev'] = $data['ncCode'];
        }

        $data["params"] = $params;
        $data["newsCategory"] = $this->common_model->_select_list('news_category_data',array('LENGTH(ncCode)'=>'3'));
        $data["date"] = explode(' ',$data["nwModDate"]);

        $paramPopular["oKey"] = 'nwViewCount';
        $paramPopular["oType"] = 'DESC';
        $paramPopular["limit"] = '10';
        $data["mostPopular"] = $this-> news_model->_select_list($paramPopular);

        $paramTopNews["nwIsHeadline"] = 'YES';
        $paramPopular["oKey"] = 'nwRegDate';
        $paramTopNews["oType"] = 'DESC';
        $paramTopNews["limit"] = '5';
        $data["topNews"] = $this-> news_model->_select_list($paramTopNews);

        $this->_print($data);
    }

    public function youtubeDownload() {


        if (empty($_GET['mime']) OR empty($_GET['token']))
        {
            exit('Invalid download token 8{');
        }

// Set operation params
        $mime = filter_var($_GET['mime']);
        $ext  = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
        $url  = base64_decode(filter_var($_GET['token']));
        $name = urldecode($_GET['title']). '.' .$ext;

        // Fetch and serve
        if ($url)
        {
            // Generate the server headers
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
            {
                header('Content-Type: "' . $mime . '"');
                header('Content-Disposition: attachment; filename="' . $name . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Content-Transfer-Encoding: binary");
                header('Pragma: public');
            }
            else
            {
                header('Content-Type: "' . $mime . '"');
                header('Content-Disposition: attachment; filename="' . $name . '"');
                header("Content-Transfer-Encoding: binary");
                header('Expires: 0');
                header('Pragma: no-cache');
            }

            readfile($url);
            exit;
        }

        exit('File not found 8{');

    }


    public function curlGet($URL) {
        $ch = curl_init();
        $timeout = 3;
        curl_setopt( $ch , CURLOPT_URL , $URL );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt( $ch , CURLOPT_CONNECTTIMEOUT , $timeout );
        /* if you want to force to ipv6, uncomment the following line */
        //curl_setopt( $ch , CURLOPT_IPRESOLVE , 'CURLOPT_IPRESOLVE_V6');
        $tmp = curl_exec( $ch );
        curl_close( $ch );
        return $tmp;
    }

    /*
     * function to use cUrl to get the headers of the file
     */
    public function get_location($url) {
        $my_ch = curl_init();
        curl_setopt($my_ch, CURLOPT_URL,$url);
        curl_setopt($my_ch, CURLOPT_HEADER,         true);
        curl_setopt($my_ch, CURLOPT_NOBODY,         true);
        curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
        $r = curl_exec($my_ch);
        foreach(explode("\n", $r) as $header) {
            if(strpos($header, 'Location: ') === 0) {
                return trim(substr($header,10));
            }
        }
        return '';
    }

    public function get_size($url) {
        $my_ch = curl_init();
        curl_setopt($my_ch, CURLOPT_URL,$url);
        curl_setopt($my_ch, CURLOPT_HEADER,         true);
        curl_setopt($my_ch, CURLOPT_NOBODY,         true);
        curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
        $r = curl_exec($my_ch);
        foreach(explode("\n", $r) as $header) {
            if(strpos($header, 'Content-Length:') === 0) {
                return trim(substr($header,16));
            }
        }
        return '';
    }

    public function get_description($url) {
        $fullpage = curlGet($url);
        $dom = new DOMDocument();
        @$dom->loadHTML($fullpage);
        $xpath = new DOMXPath($dom);
        $tags = $xpath->query('//div[@class="info-description-body"]');
        $my_description = "";
        foreach ($tags as $tag) {
            $my_description .= (trim($tag->nodeValue));
        }

        return utf8_decode($my_description);
    }
}