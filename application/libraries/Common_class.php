<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| @ PURPOSE
| @ AUTHOR  JoonCh
| @ SINCE   13. 9. 3.
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

class Common_class {

    function cut_str_han( $str, $n=5, $end_char = '' )
    {
        $CI =& get_instance();
        $charset = $CI->config->item('charset');

        if ( mb_strlen( $str , $charset) < $n ) {
            return $str ;
        }

        $str = preg_replace( "/\s+/iu", ' ', str_replace( array( "\r\n", "\r", "\n" ), ' ', $str ) );

        if ( mb_strlen( $str , $charset) <= $n ) {
            return $str;
        }
        return mb_substr(trim($str), 0, $n ,$charset) . $end_char ;
    }

    function upload_file( $fieldName, $uploadDir, $isSmall, $where )
    {
        $CI =& get_instance();
        $CI->load->model('common_model');
        $field = $fieldName;
        if($isSmall)
            $fieldS = $fieldName."S";
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  $field 값은 업로드 컬럼명.
         *  업로드한 선택한 파일이 있을 경우 CI의 Upload 헬퍼를 로드하여 업로드함
         *  (루트경로/uploads/program 디렉터리에 파일을 업로드 시키며 파일 명은 $field 값의 컬럼에 들어감)
         *  실패할 경우 에러메시지를 띄우고 쓰기 페이지로 이동
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($_FILES[$field]['name']) {
            $CI->load->helper(array('form', 'url'));
            $config['upload_path'] = './uploads/'.$uploadDir.'/';
            $config['allowed_types'] = 'gif|jpg|png|bmp';
            $config['max_size'] = '20000';
            $config['max_width'] = '5000';
            $config['max_height']  = '3000';
            $config['encrypt_name'] = true;

            $CI->load->library('upload', $config);
            if ( ! $CI->upload->do_upload($field))  $error = array('error' => $CI->upload->display_errors());
            else                                      $file_data = array('upload_data' => $CI->upload->data());
            if($error){
                echo '<script>alert("'.$error["error"].'");location.href="/adm/'.$uploadDir.'/write"</script>';exit;
            }
            if($where){
                $data = $CI->common_model->_select_row($uploadDir.'_data',array($where['key']=>$where['val']));

                if(is_file("./uploads/".$uploadDir."/".$data[$field]))
                    unlink("./uploads/".$uploadDir."/".$data[$field]);
            }
            $params[$field.'Origin'] = $_FILES[$field]['name'];
            $params[$field] = $file_data['upload_data']['file_name'];
        }
        if($_FILES[$fieldS]['name']) {
            $CI->load->helper(array('form', 'url'));
            $config['upload_path'] = './uploads/'.$uploadDir.'/';
            $config['allowed_types'] = 'gif|jpg|png|bmp';
            $config['max_size'] = '20000';
            $config['max_width'] = '5000';
            $config['max_height']  = '3000';
            $config['encrypt_name'] = true;

            $CI->load->library('upload', $config);
            if ( ! $CI->upload->do_upload($fieldS))  $error = array('error' => $CI->upload->display_errors());
            else                                      $file_data = array('upload_data' => $CI->upload->data());
            if($error){
                echo '<script>alert("'.$error["error"].'");location.href="/adm/'.$uploadDir.'/write"</script>';exit;
            }
            if($where){
                $data = $CI->common_model->_select_row($uploadDir.'_data',array($where['key']=>$where['val']));

                if(is_file("./uploads/".$uploadDir."/".$data[$fieldS]))
                    unlink("./uploads/".$uploadDir."/".$data[$fieldS]);
            }

            $params[$fieldS.'Origin'] = $_FILES[$fieldS]['name'];
            $params[$fieldS] = $file_data['upload_data']['file_name'];
        }
        return $params;
    }
}

/* End of file Someclass.php */