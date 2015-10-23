<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   회원 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class member extends common {

    public function __construct(){
        parent::__construct();
    }

    /*  관리자 회원정보 보기    */
    public function view() {
        $this->member_lib->loginCheck();
        
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
        $data['mbRegDate'] = $this->common_class->cut_str_han($data['mbRegDate'], 10,"");

        /*  Facebook 인증    */
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
        $data['appId'] = $config['appId'];
        $userId = $this->facebook->getUser();

        if($userId == 0){
            $data['mbFBLoginUrl'] = $this->facebook->getLoginUrl();
        } else {
            if($_GET){
                $user = $this->facebook->api('/me');
                if($data["member"]['mbFBSeq'] != $user['id']) {
                    $params['mbFBSeq'] = $user['id'];
                    $params['mbFBThumbOrigin'] = "https://graph.facebook.com/".$user['id']."/picture";
                    $params['mbFBThumb'] = md5(date('YmdHis').$params['mbFBThumbOrigin']).".jpg";
                    file_put_contents('./uploads/member/thumb/facebook/'.$params['mbFBThumb'], file_get_contents($params['mbFBThumbOrigin']));
                    $params['mbFBID'] = $user['username'];
                    $params['mbFBName'] = $user['name'];
                    $params['mbFBLink'] = $user['link'];
                    $params['mbModDate'] = 'NOW()';
                    $this->common_model->_update('member_data',$params, array('mbID'=>$this->session->userdata('mbID')));
                    $data['mbRegDate'] = $this->common_class->cut_str_han($data['mbRegDate'], 10,"");
                    $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
                }

                /*  Feed Post 인증    */
                redirect("/member/fb_feed_oauth/mode/member");
            }
        }

        $this->load->library('twconnect');
        if(!$this->session->userdata("tw_access_token"))
            $data['mbTWLoginUrl'] = $this->twconnect->twredirect("member/twitter_callback/mode/member");

        $data['mbFBLogoutUrl'] = $this->facebook->getLogoutUrl();

        $this->_print($data);
    }

    /*  관리자 회원 정보 수정    */
    public function modify() {
        $this->member_lib->loginCheck();

        $params = $this->input->post();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbPW'=>md5($params['mbPW'])));
        if(!$data){
            echo "<script>alert('Password is incorrect.'); location.href='/member/view';</script>";exit;
        }else{
            $data['arrPhone'] = explode('-', $data['mbPhone']);                     // 전화번호 자르기
            $data['arrCellPhone'] = explode('-', $data['mbCellPhone']);
        }
        $this->_print($data);

    }

    /*  회원 탈퇴    */
    public function withdraw() {
        $this->member_lib->loginCheck();

        $params = $this->input->post();
        $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbPW'=>md5($params['mbPW'])));
        if(!$data){
            echo "<script>alert('Password is incorrect.'); location.href='/member/view';</script>";exit;
        }else{
            $data['arrPhone'] = explode('-', $data['mbPhone']);                     // 전화번호 자르기
            $data['arrCellPhone'] = explode('-', $data['mbCellPhone']);
        }
        $this->_print($data);

    }

    public function remote_validate() {
        $params = $this->input->get();
        if($params['mbID']){
            if($this->session->userdata('mbID')){
                $secParams['mbID'] = $this->session->userdata('mbID');
                $secParams['mbIsWithdraw'] = 'NO';
                $myData = $this->common_model->_select_row('member_data',$secParams);

                if(!$myData) $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbIsWithdraw'=>'NO'));

            }else {
                $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbIsWithdraw'=>'NO'));
            }

        }elseif($params['mbNick']){
            if($this->session->userdata('mbID')){
                $secParams['mbID'] = $this->session->userdata('mbID');
                $secParams['mbNick'] = $params['mbNick'];
                $myData = $this->common_model->_select_row('member_data',$secParams);

                if(!$myData) $data = $this->common_model->_select_row('member_data',array('mbNick'=>$params['mbNick']));
            }else {
                $data = $this->common_model->_select_row('member_data',array('mbNick'=>$params['mbNick']));
            }
        }

        if($data)       echo json_encode(false);
        else            echo json_encode(true);
    }
    public function withdraw_validate() {
        $params = $this->input->get();
        if($params['mbID']){
            if($this->session->userdata('mbID')){
                $secParams['mbID'] = $this->session->userdata('mbID');
                $secParams['mbIsWithdraw'] = 'YES';
                $myData = $this->common_model->_select_row('member_data',$secParams);

                if(!$myData) $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbIsWithdraw'=>'YES'));

            }else {
                $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbIsWithdraw'=>'YES'));
            }

        }

        if($data)       echo json_encode(false);
        else            echo json_encode(true);
    }


    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        if($params['mode'] == "join"){

            $field = "mbThumb";

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  $field 값은 업로드 컬럼명.
             *  업로드한 선택한 파일이 있을 경우 CI의 Upload 헬퍼를 로드하여 업로드함
             *  (루트경로/uploads/program 디렉터리에 파일을 업로드 시키며 파일 명은 $field 값의 컬럼에 들어감)
             *  실패할 경우 에러메시지를 띄우고 쓰기 페이지로 이동
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($_FILES[$field]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/member/thumb/';
                $config['allowed_types'] = 'gif|jpg|png|bmp';
                $config['max_size'] = '10240';
                $config['max_width'] = '0';
                $config['max_height']  = '0';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($field))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/member/join"</script>';exit;
                }

                $params[$field.'Origin'] = $_FILES[$field]['name'];
                $params[$field] = $file_data['upload_data']['file_name'];


                if(is_file("./uploads/member/thumb/".$params[$field])){
                    $reConfig['image_library'] = 'gd2';
                    $reConfig['source_image']	= "./uploads/member/thumb/".$params[$field];
                    $reConfig['create_thumb'] = FALSE;
                    $reConfig['maintain_ratio'] = TRUE;
                    $reConfig['width']	 = 48;
                    $reConfig['height']	= 48;
                    $this->load->library('image_lib', $reConfig);
                    $this->image_lib->resize();
                }
            }

            $params['mbPW'] = md5($params['mbPW']);
            $params['mbVisitCount'] = '1';
            $params['mbLastLoginRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['mbRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['mbLastLoginDate'] = 'NOW()';
            $params['mbRegDate'] = 'NOW()';
            $params['mbModDate'] = 'NOW()';
            unset($params['mode']);unset($params['mbPWChk']);unset($params['ckPrivacy']);
            $this->common_model->_insert('member_data',$params);

            $sessionAry = array( $this->cfg["session_key"] => $params['mbID'] );
            $this->session->set_userdata($sessionAry);
            redirect(base_url());


        } else if($params['mode'] == "modify"){
            $this->member_lib->loginCheck();

            $field = "mbThumb";

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  $field 값은 업로드 컬럼명.
             *  업로드한 선택한 파일이 있을 경우 CI의 Upload 헬퍼를 로드하여 업로드함
             *  (루트경로/uploads/program 디렉터리에 파일을 업로드 시키며 파일 명은 $field 값의 컬럼에 들어감)
             *  실패할 경우 에러메시지를 띄우고 쓰기 페이지로 이동
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($_FILES[$field]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/member/thumb/';
                $config['allowed_types'] = 'gif|jpg|png|bmp';
                $config['max_size'] = '10240';
                $config['max_width'] = '0';
                $config['max_height']  = '0';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($field))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/member/modify"</script>';exit;
                }

                $params[$field.'Origin'] = $_FILES[$field]['name'];
                $params[$field] = $file_data['upload_data']['file_name'];

                $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID']));
                if(is_file('./uploads/member/thumb/'.$data['mbThumb']))
                    unlink('./uploads/member/thumb/'.$data['mbThumb']);

                if(is_file("./uploads/member/thumb/".$params[$field])){
                    $reConfig['image_library'] = 'gd2';
                    $reConfig['source_image']	= "./uploads/member/thumb/".$params[$field];
                    $reConfig['create_thumb'] = FALSE;
                    $reConfig['maintain_ratio'] = TRUE;
                    $reConfig['width']	 = 48;
                    $reConfig['height']	= 48;
                    $this->load->library('image_lib', $reConfig);
                    $this->image_lib->resize();
                }
            }

            $params['mbID'] = $this->session->userdata('mbID');
            if($params['mbPW']){
                $params['mbPW'] = md5($params['mbPW']);
            }else{
                unset($params['mbPW']);
            }
            $params['mbModDate'] = 'NOW()';
            unset($params['mode']);unset($params['mbPWChk']);

            $this->common_model->_update('member_data',$params,array('mbID'=>$params['mbID']));
          
            redirect(base_url("/member/view"));


        } else if($params['mode'] == "login"){
            $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['mbID'],'mbPW'=>md5($params['mbPW'])));

            if($data['mbID']){
            	if($data['mbIsWithdraw']=="NO"){
                    $sessionAry = array( $this->cfg["session_key"] => $data['mbID'] );
                    $this->session->set_userdata($sessionAry);

                    if($data['mbIsAdmin']){
                        $sessionAdmAry = array( 'mbIsAdmin' => $data['mbIsAdmin']);
                        $this->session->set_userdata($sessionAdmAry);
                    }

                    $params['mbLastLoginDate'] = 'NOW()';
                    $params['mbLastLoginRemoteIP'] = $_SERVER['REMOTE_ADDR'];
                    $params['mbVisitCount'] = $data['mbVisitCount']+1;
                    unset($params['mode']);unset($params['mbPW']);
                    $secParams = $params;
                    unset($secParams['returnUrl']);

                    $this->common_model->_update('member_data',$secParams,array('mbID'=>$params['mbID']));

					$this->load->helper('cookie');
	                $cookie = array(
	                    'name'   => 'mbAccountType',
	                    'value'  => 'GBS',
	                    'expire' => '86500',
	                    'path'   => '/',
	                );
	                set_cookie($cookie);

                }else {
                    echo "<script>alert('User account is withdrew already.'); location.href='/';</script>";exit;
                }
            }else{
                echo "<script>alert('Please, Check your ID,PW.'); history.back();</script>";exit;
            }

            if ($params['returnUrl'])   redirect($params['returnUrl']);
            else                        redirect(base_url());

        } else if($params['mode'] == "withdraw") {
            $params['mbIsWithdraw'] = "YES";
            $params['mbModDate'] = 'NOW()';
            unset($params['mode']);
            $this->common_model->_update('member_data',$params,array('mbID'=>$params['mbID']));
            $sessionAry = array( "mbID" => "", "mbIsAdmin" => "" );
            $this->session->unset_userdata($sessionAry);
            $this->common_model->_delete('mypage_log',array('mbID'=>$params['mbID']));
            $this->common_model->_delete('tag_data',array('mbID'=>$params['mbID']));

            echo "<script>alert('Success.'); location.href='/';</script>";exit;

        }  else if($params['mode'] == "removeFBInfo") {
            $params['mbFBSeq'] = $params['mbFBID'] = $params['mbFBName'] = $params['mbFBLink'] = $params['mbFBThumb'] = $params['mbFBThumbOrigin']= "";
            $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            if(is_file("./uploads/member/thumb/facebook/".$data['mbTWThumb']))
                unlink("./uploads/member/thumb/facebook/".$data['mbTWThumb']);
            unset($params['mode']);
            $params['mbModDate'] = 'NOW()';
            $this->common_model->_update('member_data',$params,array('mbID'=>$this->session->userdata('mbID')));

            $CI = & get_instance();
            if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
            }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
            $this->load->library('Facebook', $config);
            $this->facebook->destroySession();

            $this->load->helper('cookie');
            $cookie = array(
                'name'   => 'mbAccountType',
                'value'  => 'GNTV',
                'expire' => '86500',
                'path'   => '/',
            );
            set_cookie($cookie);

            echo json_encode('');
            exit;

        } else if($params['mode'] == "removeTWInfo") {
            $params['mbTWSeq'] = $params['mbTWID'] = $params['mbTWName'] = $params['mbTWLink'] = $params['mbTWThumb'] = $params['mbTWThumbOrigin']= "";
            $data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));
            if(is_file("./uploads/member/thumb/twitter/".$data['mbTWThumb']))
                unlink("./uploads/member/thumb/twitter/".$data['mbTWThumb']);
            unset($params['mode']);
            $params['mbModDate'] = 'NOW()';
            $this->common_model->_update('member_data',$params,array('mbID'=>$this->session->userdata('mbID')));
            $this->session->unset_userdata(array('tw_access_token' => '', 'tw_status' => ''));
            echo json_encode('');
            exit;
        }


    }
    
    public function ajax_process(){
        $params = $this->input->post();

        if($params['mode'] == "recovery"){
            if($params['preoption']=='1'){
                $data = $this->common_model->_select_row('member_data',array('mbID'=>$params['Email']));
                unset($params['Email2']);
                $result = $params;
                if($data)
                    $result['data'] = $data;

            }elseif($params['preoption']=='2'){
                $result = $params;
            }elseif($params['preoption']=='3'){
                unset($params['Email']);
                $result = $params;
            }

        }else if($params['mode'] == "sendEmailFrm"){
            $result = $params;

        }else if($params['mode'] == "recoveryUserName"){
            $data = $this->common_model->_select_row('member_data',array('mbFirstName'=>$params['mbFirstName'],'mbLastName'=>$params['mbLastName'], 'mbNick'=>$params['mbNick']));
            if($data){
                $accountArr = explode("@",$data['mbID']);
                 
                for($i=0;$i<count($accountArr); $i++){
                    if(strlen($accountArr[$i])>2){
                        $temp[$i] = substr($accountArr[$i],1,-1);
                        $accountArray[$i] = "*".$temp[$i]."*";
                        
                    }else{
                        $temp[$i] = substr($accountArr[$i],0,-1);
                        if($i!=0)
                            $accountArray[$i] = "*".$temp[$i]."*";
                        else
                            $accountArray[$i] = $temp[$i]."*";
                    }
                } 
                $data['mbAccount'] = $accountArray[0]."@".$accountArray[1];
                $result = $params;
                $result['data'] = $data;
            }else{
                $result = $params;
            }
        }else if($params['mode'] == "emailPassword"){
            $to = $params['mbID'];
            $subject = "Temporary password";
            $tempPW = $this->genPassword();
            $message = 'Temporary password : '.$tempPW;
            $from = "gbskenya@gbskenya.com";
            $header = "From:$from";
            $mail = mail($to, $subject, $message, $header);
//echo'<pre>';print_r($params);echo'</pre>';exit;
            $result = $params;
            if($mail){$result['result'] = 'Mail Sent.';
            }else{$result['result'] = 'Mail failed.';}
            $paramPW['mbPW'] = md5($tempPW);
            $params['mbModDate'] = 'NOW()';
            $this->common_model->_update('member_data',$paramPW,array('mbID'=>$params['mbID']));

        }else if($params['mode'] == "errorReport"){
            $paramEr['erType'] = 'login';
            $paramEr['erEmail'] = $params['Email'];
            $paramEr['erContent'] = $params['errorReport'];
            $paramEr['erRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $paramEr['erRegDate'] = 'NOW()';
            $paramEr['erModDate'] = 'NOW()';
            $this->common_model->_insert('login_error_log',$paramEr);

            $result = $params;
        }else if($params['mode'] == "mbMod"){
            $result = $params;

        }else if($params['mode'] == "mbWithdraw"){
            $result = $params;
        }else if($params['mode'] == "logincheck"){
            $key = $this->cfg["session_key"];
            $result = ($this->session->userdata($key)) ? "true":"false";
        }

        echo json_encode($result);

    }

    function genPassword ($length = 8)
    {
        // given a string length, returns a random password of that length
        $password = "";
        // define possible characters
        $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            // we don't want this character if it's already in the password
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public function login() {
        $params = $this->input->post();
        $key = $this->cfg["session_key"];
        if($this->session->userdata($key))
            redirect(base_url());
        $params['returnUrl']=$_SERVER['HTTP_REFERER'];
        $this->_print($params);
    }

    public function logout() {
        $CI = & get_instance();
        if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
        }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        $this->load->library('Facebook', $config);
        $this->facebook->destroySession();
        $this->session->unset_userdata(array('tw_access_token' => '', 'tw_status' => ''));
        $this->load->helper('cookie');
        $cookie = array(
            'name'   => 'mbAccountType',
            'value'  => '',
            'expire' => '1',
            'path'   => '/',
        );
        set_cookie($cookie);
        $sessionAry = array( "mbID" => "", "mbIsAdmin" => "" );
        $this->session->unset_userdata($sessionAry);
        redirect(base_url());
    }

    public function join() {
        $this->_print();
    }

    public function recovery() {
        $this->_print();
    }
    
    public function fb_feed_oauth() {
        $params = $this->_get_sec();

        if($_GET){
            if($params['mode'] == "content"){
                $this->load->helper('cookie');
                $cookie = array(
                    'name'   => 'mbAccountType',
                    'value'  => 'FACEBOOK',
                    'expire' => '86500',
                    'path'   => '/',
                );
                set_cookie($cookie);
                $link = "/content/view";
                $link .= "/ctNO/".$params['ctNO']."/ctName/".$params['ctName'];
                redirect($link);
            } else if($params['mode'] == "program"){
                $this->load->helper('cookie');
                $cookie = array(
                    'name'   => 'mbAccountType',
                    'value'  => 'FACEBOOK',
                    'expire' => '86500',
                    'path'   => '/',
                );
                set_cookie($cookie);
                $url = "/program/view/prCode/".$params['prCode']."/prName/".$params['prName']."/ctNO/".$params['ctNO']."/ctPage/".$params['ctPage'];
                redirect($url);
            } else if($params['mode'] == "member")
                redirect("/member/view");
        } else{
            parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
            $CI = & get_instance();
            if(base_url() == "http://lo.gbs.com/")            { $CI->config->load("facebook_local",TRUE); $config = $CI->config->item('facebook_local');
            }else if(base_url() == "http://gbskenya.com/") { $CI->config->load("facebook",TRUE);  $config = $CI->config->item('facebook');    }
        	$link = "<script>location.href='https://graph.facebook.com/oauth/authorize?client_id=".$config['appId']."&scope=publish_stream&redirect_uri=".base_url()."member/fb_feed_oauth/mode/".$params['mode'];

        	if($params['mode'] == "content"){
                $link .= "/ctNO/".$params['ctNO']."/ctName/".$params['ctName'];
            }else if($params['mode'] == "program"){
        		$link .= "/prCode/".$params['prCode']."/prName/".$params['prName']."/ctNO/".$params['ctNO']."/ctPage/".$params['ctPage'];
            }
            $link .= "';</script>";
        	
            echo $link;
        }
    }

    public function twitter_callback() {
        $params = $this->_get_sec();
        $this->load->library('twconnect');
        $ok = $this->twconnect->twprocess_callback();

        if ( $ok ) {
            if($params['mode'] == "content")
                redirect("member/twitter_success/mode/content/ctNO/".$params['ctNO']);
            else if($params['mode'] == "program")
                redirect("member/twitter_success/mode/program/prCode/".$params['prCode']."/ctNO/".$params['ctNO']."/ctPage/".$params['ctPage']);
            else if($params['mode'] == "member")
                redirect("member/twitter_success/mode/member");
        } else redirect ('member/twitter_failure');
    }

    public function twitter_success() {
        $this->load->library('twconnect');
        $this->twconnect->twaccount_verify_credentials();
		$data = $this->common_model->_select_row('member_data',array('mbID'=>$this->session->userdata('mbID')));

	    if($this->twconnect->tw_user_info) {
		    if($data['mbTWSeq'] != $this->twconnect->tw_user_info->id) {
	            $params['mbTWSeq'] = $this->twconnect->tw_user_info->id;
	            $params['mbTWThumbOrigin'] = $this->twconnect->tw_user_info->profile_image_url;
	            $params['mbTWThumb'] = md5(date('YmdHis').$params['mbTWThumbOrigin']).".jpg";
				file_put_contents('./uploads/member/thumb/twitter/'.$params['mbTWThumb'], file_get_contents($params['mbTWThumbOrigin']));
	            $params['mbTWID'] = $this->twconnect->tw_user_info->screen_name;
	            $params['mbTWName'] = $this->twconnect->tw_user_info->name;
	            $params['mbTWLink'] = "https://twitter.com/".$this->twconnect->tw_user_info->screen_name;
                $params['mbModDate'] = 'NOW()';
	            $this->common_model->_update('member_data',$params, array('mbID'=>$this->session->userdata('mbID')));
			}
        }
		

        $params = $this->_get_sec();
        if($params['mode'] == "content"){
            $this->load->helper('cookie');
            $cookie = array(
                'name'   => 'mbAccountType',
                'value'  => 'TWITTER',
                'expire' => '86500',
                'path'   => '/',
            );
            set_cookie($cookie);
            $ctData = $this->common_model->_select_list('content_data',array('ctNO'=>$params['ctNO']));
            $ctData['ctName'] = str_replace(' ', '_', $this->common_class->cut_str_han($ctData[0]['ctName'], 15,""));
            redirect("/content/view/ctNO/".$params['ctNO']."/ctName/".$ctData['ctName']);
        } else if($params['mode'] == "program"){
            $this->load->helper('cookie');
            $cookie = array(
                'name'   => 'mbAccountType',
                'value'  => 'TWITTER',
                'expire' => '86500',
                'path'   => '/',
            );
            set_cookie($cookie);
            $prData = $this->common_model->_select_list('program_data',array('prCode'=>$params['prCode']));
            $prData['prName'] = str_replace(' ', '_', $this->common_class->cut_str_han($prData[0]['prName'], 15,""));
            redirect("/program/view/prCode/".$params['prCode']."/prName/".$prData['prName']."/ctNO/".$params['ctNO']."/ctPage/".$params['ctPage']);
        } else if($params['mode'] == "member")
            redirect("/member/view");
    }

    public function twitter_failure() {
        redirect(base_url());
    }

}