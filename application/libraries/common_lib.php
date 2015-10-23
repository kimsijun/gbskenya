<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| @ TITLE   공통 라이브러리
| @ AUTHOR  JoonCh
| @ SINCE   14. 6. 3.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class common_lib {

    /**
     * login Check
     * @param	none
     */
    function DBErrorCatch() {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->library('javascript_lib');
        $CI->DB1 = $CI->load->database('common', TRUE);
        $mbID = ($CI->session->userdata('mbID')) ? $CI->session->userdata('mbID') : $_SERVER['REMOTE_ADDR'];

        $sql = "INSERT INTO error values (".
            "'errorReport',".
            SITE_NO.",
            (select IFNULL(max(b.eNO),0)+1 from error as b),".
            "'".SITE_FULL_URL."',".
            "'".$mbID."',".
            $CI->db->_error_number().",".
            "'".addslashes($CI->db->_error_message())."',".
            "'',".
            "'".$_SERVER['REMOTE_ADDR']."',".
            "NOW())";
        $CI->DB1->query($sql);

        // eNO 넘기기
        $CI->DB1->select_max('eNO');
        $Q = $CI->DB1->get('error');
        $row = $Q->row_array();
        redirect(base_url("/main/errorReport/eNO/".$row['eNO']));
        return true;
    }

}

