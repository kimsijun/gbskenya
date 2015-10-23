<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE 콘텐츠 댓글 관련 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

class content_comment extends common {


    public function __construct(){
        parent::__construct();
        $this->load->model('content_comment_model');
    }

	public function fb_feed() {
        $params = $this->input->post();
		$data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));

        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
		
		$link = base_url()."content/view/ctNO/".$params['ctNO']."/ctName/".str_replace(" ", "_",$data['ctName']);
				
	        try{
	            $this->facebook->api('/me/feed/', 'post', array('access_token' => $this->facebook->getAccessToken(), 'message' => $params['cbcoContent'], 'link' => $link, 'picture'=>base_url().'uploads/content/'.$data['prThumb']));
	        }catch(Exception $e) {
				echo "fail";
	        }
		
		
    }
    

	public function test_fb_feed() {
        $params = $this->input->post();

		$data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));


		parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
        $userId = $this->facebook->getUser();
        
		if($userId != 0){
			$link = base_url()."content/view/ctNO/".$params['ctNO']."/ctName/".str_replace(" ", "_",$data['ctName']);
			
	        try{
	            $this->facebook->api('/me/feed/', 'post', array('access_token' => $this->facebook->getAccessToken(), 'message' => $params['cbcoContent'], 'link' => $link, 'picture'=>base_url().'uploads/content/'.$data['prThumb']));
	        }catch(Exception $e) {
				echo "fail";
	        }
		
		}

		
    }

    public function tw_feed() {
        $params = $this->input->post();
		$data = $this->common_model->_select_row('content_data',array('ctNO'=>$params['ctNO']));
		
		$link = base_url()."content/view/ctNO/".$params['ctNO']."/ctName/".str_replace(" ", "_",$data['ctName']);

        $this->load->library('twconnect');
        $this->twconnect->tw_post('statuses/update', array('status' => $params['cbcoContent']." ".$link));
    }




    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();
        if(!$this->session->userdata('mbID')){
            echo json_encode('false');exit;
        }else{
            $memberData = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            $params['mbID'] = $memberData['mbID'];

            // 생성
            if($params['mode'] == "write"){
                $params['cbcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];

                if($params['mbAccountType'] == "FACEBOOK"){
                    $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                    $params['mbThumb'] = $data['mbFBThumb'];
                    $params['mbSnsID'] = $data['mbFBID'];
                    $params['mbLink'] = $data['mbFBLink'];

                } else if($params['mbAccountType'] == "TWITTER"){
                    $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                    $params['mbThumb'] = $data['mbTWThumb'];
                    $params['mbSnsID'] = $data['mbTWID'];
                    $params['mbLink'] = $data['mbTWLink'];
                }

                unset($params['mode']);
                $params['cbcoModDate'] = 'NOW()';
                $params['cbcoRegDate'] = 'NOW()';
                $this->content_comment_model->_insert($params);


                // 수정
            } else if($params['mode'] == "modify"){
                $params['cbcoModDate'] = 'NOW()';

                unset($params['mode']);
                $ctNO = $params['ctNO'];
                unset($params['ctNO']);
                $cbcoNO = $params['cbcoNO'];
                unset($params['cbcoNO']);
                $this->common_model->_update('content_comment_data',$params,array('cbcoNO'=>$cbcoNO,'ctNO'=>$ctNO));
                $params['ctNO'] = $ctNO;

                //답글 생성
            } else if($params['mode'] == "reply"){
                $cbco_info = $this->common_model->_select_row('content_comment_data',array("cbcoNO"=>$params['cbcoNO']));
                foreach($cbco_info as $k => $v) $cbco_data[$k] = $v;
                $params['cbcoGroup'] = $cbco_data['cbcoGroup'];$params['cbcoStep'] = $cbco_data['cbcoStep'];
                $params['cbcoDepth'] = $cbco_data['cbcoDepth'];$params['cbcoParent'] = $cbco_data['cbcoNO'];

                $params['cbcoRemoteIP'] = $_SERVER['REMOTE_ADDR'];
                $params['cbcoModDate'] = 'NOW()';
                $params['cbcoRegDate'] = 'NOW()';

                unset($params['mode']);
                $this->content_comment_model->_reply($params);

                // 삭제
            } else if($params['mode'] == "delete"){
                $params['cbcoIsDelete'] = "YES";
                $params['cbcoModDate'] = 'NOW()';
                unset($params['mode']);
                $this->common_model->_update('content_comment_data',$params,array('cbcoNO'=>$params['cbcoNO'],'ctNO'=>$params['ctNO']));
            }

            $path = "./_cache/%cache"; delete_files($path, true);

            $paramComment["oKey"] = "cbcoGroup DESC, cbcoStep ASC"; $paramComment["oType"] = "";
            $paramComment["ctNO"] = $params["ctNO"];
            $paramComment["cbcoIsNotice"] = "YES";
            $paramComment["cbcoIsDelete"] = "NO";

            $noticeData = $this->content_comment_model->_select_list($paramComment);
            for($i=0; $i<count($noticeData); $i++){
                $noticeData[$i]["cbcoRegDate"] = $this->common_class->cut_str_han($noticeData[$i]["cbcoRegDate"], 10,"");
                $noticeData[$i]["cbcoModDate"] = $this->common_class->cut_str_han($noticeData[$i]["cbcoModDate"], 10,"");
            }

            $paramComment["cbcoIsNotice"] = "NO";

            $data = $this->content_comment_model->_select_list($paramComment);
            for($i=0; $i<count($data); $i++){
                $data[$i]["cbcoRegDate"] = $this->common_class->cut_str_han($data[$i]["cbcoRegDate"], 10,"");
                $data[$i]["cbcoModDate"] = $this->common_class->cut_str_han($data[$i]["cbcoModDate"], 10,"");
            }

            $this->load->helper('cookie');
            $mbAccountType = get_cookie("mbAccountType");

            /*  Facebook 인증    */
            parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
            $CI = & get_instance();
            if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
            }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
            $this->load->library('Facebook', $config);
            $userId = $this->facebook->getUser();
            $this->load->library('twconnect');
            if(!$this->session->userdata("tw_access_token"))
                $mbTWLoginUrl = $this->twconnect->twredirect("member/twitter_callback/ctNO/".$data['ctNO']."/mode/content");

            if($userId == 0)
                $mbFBLoginUrl = $this->facebook->getLoginUrl(array('redirect_uri' => base_url().'content/view/ctNO/'.$params['ctNO'].'/fbLogin/true/'));


            $result['mbTWLoginUrl'] = $mbTWLoginUrl;
            $result['mbFBLoginUrl'] = $mbFBLoginUrl;
            $result['mbAccountType'] = $mbAccountType;
            $result['ctNO'] = $params['ctNO'];
            $result['data'] = $data;
            $result['noticeData'] = $noticeData;
            $result['templateHtml'] = $this->cfg["module_dir_name"].'/widget/content/comment.html';
            $result['mbID'] = $this->session->userdata($this->cfg["session_key"]);
            $result['mbIsAdmin'] = $this->session->userdata('mbIsAdmin');
            $html = $this->_print($result, TRUE);
            $params['html'] = $html;
            echo json_encode($params);
            exit;
        }
    }
}
?>