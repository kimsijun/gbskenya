<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| @ TITLE   회원 관련 라이브러리
| @ AUTHOR  JoonCh
| @ SINCE   14. 5. 2.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class member_lib {

    /**
     * login Check
     * @param	none
     */
    function loginCheck()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->library('javascript_lib');
        if(!$CI->session->userdata('mbID')){
            $CI->javascript_lib->confirm(array("msg"=>"로그인하시겠습니까?", "nextUrl"=>"/member/login","prevUrl"=>$_SERVER['HTTP_REFERER']));
        }
    }

    /**
     * admin Check
     * @param	none
     */
    function adminCheck()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->library('javascript_lib');
        if(!$CI->session->userdata('mbID')){
            $CI->javascript_lib->confirm(array("msg"=>"로그인하시겠습니까?", "nextUrl"=>"/member/login","prevUrl"=>$_SERVER['HTTP_REFERER']));
        }else {
            $CI->load->model('common_model');
            $data = $CI->common_model->_select_row('member_data', array('mbID'=>$CI->session->userdata('mbID')));
            if($data['mbIsAdmin'] == "NO")     $CI->javascript_lib->msgBack(array("msg"=>"관리자 전용 페이지 입니다."));
        }
    }
}
