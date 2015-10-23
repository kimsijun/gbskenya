<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   프로그램 관리자 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class program extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }


    /*  관리자 프로그램 생성    */
    public function write() {
        $params = $this->_get_sec();
        
        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $params['youtube_cate'] = $youtubeCate;
        
        $this->_print($params);
    }



    /*  관리자 프로그램 리스트    */
    public function index() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        unset($secParams['program']);
        $secParams["oKey"] = "prCode";
        $secParams["oType"] = "asc";

        $result["cnt"] = $this->common_model->_select_cnt('program_data',$secParams);
        $result["list"] = $this->common_model->_select_list('program_data',$secParams);

        $data["list"] = $result["list"];
        $data["secParams"] = $secParams;

        // Depth 생성
        for($i=0; $i<count($data['list']); $i++)
            if(strlen($data['list'][$i]['prCode']) > 3)
                $data['list'][$i]['prDepth'] = strlen($data['list'][$i]['prCode']) / 3;

        $this->_print($data);
    }


    /*  관리자 게시판 보기    */
    public function view() {
        $params = $this->_get_sec();
        $data = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        $this->_print($data);
    }


    /*  관리자 프로그램 정보 수정    */
    public function modify() {
        $params = $this->input->post();
        $data = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
        $url =  "./assets/json/youtube/youtube_cate.json";
        $contents = file_get_contents($url);
        $youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;
        $this->_print($data);
    }

    /*  관리자 프로그램 XML 관리    */
    public function manage_xml() {
        $this->_set_sec();
        $secParams = $this->_get_sec();
        $secParams["oKey"] = "prCode";
        $secParams["oType"] = "desc";

        $result["cnt"] = $this->common_model->_select_cnt('program_data',$secParams);
        $result["list"] = $this->common_model->_select_list('program_data',$secParams);

        $data["list"] = $result["list"];
        $data["secParams"] = $secParams;

        // Depth 생성
        for($i=0; $i<count($data['list']); $i++)
            if(strlen($data['list'][$i]['prCode']) > 3)
                $data['list'][$i]['prDepth'] = strlen($data['list'][$i]['prCode']) / 3;

        $this->_print($data);
    }

    /*  관리자 프로그램 정렬    */
    public function sort() {
        $this->_set_sec();
        $params = $this->_get_sec();

        if($params['prCode']){
            $secParams["LENGTH(prCode)"] = strlen($params['prCode']) + 3;
            $secParams["prPreCode"] = $params['prCode'];
            $secParams["oKey"] = "prCode";
            $secParams["oType"] = "asc";
            $data['prCode'] = $params['prCode'];
            $data["program"] = $this->common_model->_select_list('program_data',$secParams);

            $pcLen = strlen($params['prCode']);
            $prCnt = $pcLen / 3;
            for($i=0; $i<=$prCnt; $i++) {
                if($i == 0)     $secParams["LENGTH(prCode)"] = 3;
                else            $secParams["LENGTH(prCode)"] = ($i*3)+3;
                $secParams["prPreCode"] = substr($params["prCode"], 0, ($i*3));
                $data["secProgram"][$i]['prSub'] = $this->common_model->_select_list('program_data',$secParams);
            }
        } else {
            $secParams["LENGTH(prCode)"] = 3;
            $secParams["oKey"] = "prCode";
            $secParams["oType"] = "asc";

            $data["program"] = $this->common_model->_select_list('program_data',$secParams);
        }

        $this->_print($data);
    }

    /*  공통 Ajax 처리페이지    */
    public function ajax_process () {
        $params = $this->input->post();

        if($params['mode'] == "getPrList"){
            $pcLen = strlen($params['prCode']);
            $prCnt = $pcLen / 3;
            $secParams["oKey"] = "prSort";
            $secParams["oType"] = "desc";
            $html = '<form method="post" action="/adm/program/sort">';

            for($i=0; $i<=$prCnt; $i++) {
                if($i == 0) {
                    $secParams["LENGTH(prCode)"] = 3;
                    $data["program"][$i] =  $this->common_model->_select_list('program_data',$secParams);
                } else {
                    $secParams["LENGTH(prCode)"] = ($i*3)+3;
                    $secParams["prPreCode"] = substr($params["prCode"], 0, ($i*3));
                    $data["program"][$i] = $this->common_model->_select_list('program_data',$secParams);
                }

                if(!count($data["program"][$i]))    continue;

                $html .= '<div class="span2 marginB10">
                            <form method="post" action="/adm/program/sort">
                                <select name="prCode" class="secPrCode  form-control">
                                    <option value="">선택</option>';

                for($j=0; $j<count($data["program"][$i]); $j++) {
                    $html .= '<option value="'.$data["program"][$i][$j]['prCode'].'" ';
                    if($data["program"][$i][$j]['prCode'] == substr($params['prCode'], 0, ($i+1)*3))
                        $html .= 'selected=selected';
                    $html .= '>'.$data["program"][$i][$j]['prName'].'</option>';
                }

                $html .= '      </select>
                                <button type="submit" class="btn btn-default marginR10">Search</button>
                            </form>
                        </div><br>';
            }
            echo json_encode($html);

        } else if($params['mode'] == "sortWrite"){

            foreach($params['prSort'] as $k => $v) {
                if($v){
                    $secParams['prCode'] = $k;
                    $this->common_model->_update('program_data',array('prSort'=>$v), $secParams);
                } else {
                    $secParams['prCode'] = $k;
                    $this->common_model->_update('program_data',array('prSort'=>100), $secParams);
                }
            }

            $url = "/adm/program/sort";
            if($params['curPrCode'])    $url .= "/prCode/".$params['curPrCode'];

            redirect(base_url($url));

        } else if($params['mode'] == "modifySimpleDown"){
            $arrPrCode = explode(',', $params['prCode']);
            for($i=0; $i<count($arrPrCode); $i++) {
                $this->common_model->_update('program_data',array('prIsSimpleDown'=>$params['prIsSimpleDown']),array('prCode'=>$params['prCode']));
            }
            echo json_encode($arrPrCode);
            exit;

            echo json_encode('');
        }


    }

    /*  공통 처리페이지    */
    public function process () {
        $params = $this->input->post();
        // 관리자 프로그램 생성처리
        if($params['mode'] == "write"){
            $field = "prThumb";
            $fieldS = "prThumbS";

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  $field 값은 업로드 컬럼명.
             *  업로드한 선택한 파일이 있을 경우 CI의 Upload 헬퍼를 로드하여 업로드함
             *  (루트경로/uploads/program 디렉터리에 파일을 업로드 시키며 파일 명은 $field 값의 컬럼에 들어감)
             *  실패할 경우 에러메시지를 띄우고 쓰기 페이지로 이동
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($_FILES[$field]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/program/';  $config['allowed_types'] = 'gif|jpg|png|bmp';    $config['max_size'] = '20000'; $config['max_width'] = '5000';  $config['max_height']  = '3000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($field))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/adm/program/write"</script>';exit;
                }

                $params[$field.'Origin'] = $_FILES[$field]['name'];
                $params[$field] = $file_data['upload_data']['file_name'];
            }
            if($_FILES[$fieldS]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/program/';  $config['allowed_types'] = 'gif|jpg|png|bmp';    $config['max_size'] = '20000'; $config['max_width'] = '5000';  $config['max_height']  = '3000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($fieldS))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/adm/program/write"</script>';exit;
                }

                $params[$fieldS.'Origin'] = $_FILES[$fieldS]['name'];
                $params[$fieldS] = $file_data['upload_data']['file_name'];
            }

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  1. 하위 카테고리의 프로그램 생성의 경우 선택한 카테고리 보다 1 Depth 작은 데이터중 가장 큰 prCode의 + ~001
             *      - (1 Depth 작은 데이터가 없을 경우는 해당 prCode 뒤에 001 추가함)
             *  2. 최상위 카테고리의 프로그램 생성의 경우 DB에 1 Depth의 데이터중 가장 큰 prCode의 + ~001
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($params['prCode']){
                $prCodeLen = strlen($params['prCode']) + 3;
                $params['prPreCode'] = $params['prCode'];
                $data = $this->common_model->_select_list('program_data',array("oKey"=>"prCode", "oType"=>"desc", "prPreCode"=>$params['prCode'], "LENGTH(prCode)"=>$prCodeLen));
                if($data[0]['prCode']){
                    $max_prCode = "00".(float)($data[0]['prCode'] + 1);
                    $prCode = substr($max_prCode,(float)($prCodeLen * -1));
                } else
                    $prCode = $params['prCode']."001";

            } else {
                $data = $this->common_model->_select_list('program_data',array("oKey"=>"prCode", "oType"=>"desc"));
                $max_prCode = "00".(float)(substr($data[0]['prCode'],0,3) + 1);
                $prCode = substr($max_prCode,-3);
            }
            $params['prRemoteIP'] = $_SERVER['REMOTE_ADDR'];
            $params['prCode'] = $prCode;
            $params['prModDate'] = 'NOW()';
            $params['prRegDate'] = 'NOW()';
            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_insert('program_data',$params);
        // 관리자 프로그램 수정처리
        } else if($params['mode'] == "modify"){
            $field = "prThumb";
            $fieldS = "prThumbS";

            if($_FILES[$field]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/program/';  $config['allowed_types'] = 'gif|jpg|png|bmp';    $config['max_size'] = '20000'; $config['max_width'] = '5000';  $config['max_height']  = '3000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($field))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/adm/program/view/prCode/'.$params['prCode'].'"</script>';exit;
                }

                $data = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                if(is_file("./uploads/program/".$data[$field]))
                    unlink("./uploads/program/".$data[$field]);
                $params[$field.'Origin'] = $_FILES[$field]['name'];
                $params[$field] = $file_data['upload_data']['file_name'];
            }
            if($_FILES[$fieldS]['name']) {
                $this->load->helper(array('form', 'url'));
                $config['upload_path'] = './uploads/program/';  $config['allowed_types'] = 'gif|jpg|png|bmp';    $config['max_size'] = '20000'; $config['max_width'] = '5000';  $config['max_height']  = '3000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload($fieldS))  $error = array('error' => $this->upload->display_errors());
                else                                      $file_data = array('upload_data' => $this->upload->data());
                if($error){
                    echo '<script>alert("'.$error["error"].'");location.href="/adm/program/write"</script>';exit;
                }
                $data = $this->common_model->_select_row('program_data',array('prCode'=>$params['prCode']));
                if(is_file("./uploads/program/".$data[$fieldS]))
                    unlink("./uploads/program/".$data[$fieldS]);
                $params[$fieldS.'Origin'] = $_FILES[$fieldS]['name'];
                $params[$fieldS] = $file_data['upload_data']['file_name'];
            }
            $params['prModDate'] = 'NOW()';


            unset($params['mode']);unset($params['x']);unset($params['y']);
            $this->common_model->_update('program_data',$params,array('prCode'=>$params['prCode']));


        // 관리자 프로그램 삭제
        } else if($params['mode'] == "delete"){
            if($params['chk']){
                for($i=0; $i<count($params['chk']); $i++){
                    $pCode[] = $params['chk'][$i];
                }
                unset($params['mode']);unset($params['checkAll_length']);unset($params['chk']);
                for($i=0; $i<count($pCode); $i++){
                    $this->common_model->_delete('program_data',array('prCode'=>$pCode[$i]));
                    $this->common_model->_delete('mainFocus_data',array('mFType'=>'PROGRAM','prCode'=>$pCode[$i]));
                    $this->common_model->_delete('livecontent_data',array('prCode'=>$pCode[$i]));
                }
            }else{
                $this->common_model->_delete('program_data',array('prCode'=>$params['prCode']));
                $this->common_model->_delete('mainFocus_data',array('mFType'=>'PROGRAM','prCode'=>$params['prCode']));
                $this->common_model->_delete('livecontent_data',array('prCode'=>$params['prCode']));
            }
        }

        $path = "./_cache/%cache";      delete_files($path, true);

        redirect(base_url("/adm/program/index"));

    }

}