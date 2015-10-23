<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   관리자 메인 페이지 컨트롤러
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 11.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class main extends common {
    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
        $this->load->model("view_model");
        $this->load->model("program_model");
        $this->load->model("content_model");
        $this->load->model("board_model"); 
        $this->load->model("cfg_board_model");
        $this->load->model("content_comment_model");
        $this->load->model("program_comment_model");
    }

    public function index() {
        $data['contentTop10'] = $this->content_model->_select_top10();
        $data['programTop10'] = $this->program_model->_select_top10();

        $memberParam['oKey'] = 'mbRegDate';
        $memberParam['oType'] = 'DESC';
        $memberParam['limit'] = 10;
        $data['mbList'] = $this->common_model->_select_list('member_data',$memberParam);

        unset($param);
        $param['cbcoIsDelete'] = 'NO';
        $param['limit'] = 10;
        $param['oKey'] = 'cbcoGroup DESC, cbcoStep ASC';
        $param['oType'] = '';
        $data['contentComment'] = $this->common_model->_select_list('content_comment_data',$param);
        for($i=0; $i<count($data['contentComment']); $i++){
            $data['contentComment'][$i]['cbcoContent'] = $this->common_class->cut_str_han($data['contentComment'][$i]['cbcoContent'], 30,"");
        }

        $this->_print($data);
    }

    public function delete_cache() {
        $path = "./_cache/%cache";
        delete_files($path, true);
        echo "<script>this.close();</script>";
    }

    public function ajax_process(){
        $params = $this->input->post();
        $html ='';
        $param['limit'] = 10;

        if($params['mode'] == "Board"){
            $param['bodID'] = 'qna';
            $param['oKey'] = 'bcoGroup DESC, bcoStep ASC';
            $param['oType'] = '';
            $data = $this->comment_model->_select_list($param);
            for($i=0; $i<count($data); $i++){
                $data[$i]['boContent'] = $this->common_class->cut_str_han($data[$i]['boContent'], 30,"");
                $boInfo = $this->cfg_board_model->_select_row(array('bodID'=>$data[$i]['bodID']));
                $data[$i]['boName'] = $boInfo['boName'];
            }
            for($i=0; $i<count($data); $i++){
                $html .= '<li class="clearfix">';
                $html .= '<div class="txt pull-left">';
                $html .= '<span class="mrt10">['.$data[$i]['boName'].']</span>';
                if($data[$i]['bcoDepth']){
                    $html .= '<img src="/images/common/blank.png" style="width:'.($data[$i]['bcoDepth']-1)*20;
                    $html .= 'px;height:15px;"><img src="/images/godo_admin/board_re.gif">';
                }
                $html .= '<a href="/adm/board/view/bodID/'.$data[$i]['bodID'].'/boNO/'.$data[$i]['boNO'].'">'.$data[$i]['bcoContent'].'</a>';
                $html .= '</div>';
                $html .= '<div class="controls pull-right">';
                $html .= '<span class="mrt20">'.$data[$i]['mbID'].'</span>';
                $html .= '<span class="mrt10">'.$data[$i]['bcoRegDate'].'</span>';
                $html .= '</div></li>';
            }
            echo json_encode($html);

        }elseif($params['mode'] == "Content"){
            $param['cbcoIsDelete'] = 'NO';
            $param['oKey'] = 'cbcoGroup DESC, cbcoStep ASC';
            $param['oType'] = '';
            $data = $this->common_model->_select_list('content_comment_data',$param);
            for($i=0; $i<count($data); $i++){
                $data[$i]['cbcoContent'] = $this->common_class->cut_str_han($data[$i]['cbcoContent'], 30,"");
            }
            for($i=0; $i<count($data); $i++){
                $html .= '<li class="clearfix">';
                $html .= '<div class="txt pull-left">';
                if($data[$i]['cbcoDepth']){
                    $html .= '<img src="/images/common/blank.png" style="width:'.($data[$i]['cbcoDepth']-1)*20;
                    $html .= 'px;height:15px;"><img src="/images/godo_admin/board_re.gif">';
                }
                $html .= '<span style="display:inline-block;width:80px;">['.$data[$i]['mbAccountType'].']</span>';
                $html .= '<a href="/content/view/ctNO/'.$data[$i]['ctNO'].'">'.$data[$i]['cbcoContent'].'</a>';
                $html .= '</div>';
                $html .= '<div class="controls pull-right">';
                $html .= '<span class="mrt20"><b>'.$data[$i]['mbID'].'</b></span>';
                $html .= '<span class="mrt10">'.$data[$i]['cbcoRegDate'].'</span>';
                $html .= '</div></li>';
            }
            echo json_encode($html);
        }elseif($params['mode'] == "Program"){
            $param['oKey'] = 'pbcoNO';
            $param['oType'] = 'DESC';
            $param['pbcoIsDelete'] = 'NO';
            $data = $this->common_model->_select_list('program_comment_data',$param);
            for($i=0; $i<count($data); $i++){
                $data[$i]['pbcoContent'] = $this->common_class->cut_str_han($data[$i]['pbcoContent'], 30,"");
            }
            for($i=0; $i<count($data); $i++){
                $html .= '<li class="clearfix">';
                $html .= '<div class="txt pull-left">';
                //$html .= '<span class="mrt10">['.$data[$i]['boName'].']</span>';
                if($data[$i]['pbcoDepth']){
                    $html .= '<img src="/images/common/blank.png" style="width:'.($data[$i]['pbcoDepth']-1)*20;
                    $html .= 'px;height:15px;"><img src="/images/godo_admin/board_re.gif">';
                }
                $html .= '<a href="/program/view/prCode/'.$data[$i]['prCode'].'">'.$data[$i]['pbcoContent'].'</a>';
                $html .= '</div>';
                $html .= '<div class="controls pull-right">';
                $html .= '<span class="mrt20">'.$data[$i]['mbID'].'</span>';
                $html .= '<span class="mrt10">'.$data[$i]['pbcoRegDate'].'</span>';
                $html .= '</div></li>';
            }
            echo json_encode($html);

        }elseif($params['mode'] == "contentTop10"){
            $data = $this->content_model->_select_top10(array('date'=>$params['date']));
            $html = "";
            for($i=0; $i<count($data); $i++) { 
                $html .= '
                    <tr>
                        <td>'.($i+1).'</td>
                        <td>'.$data[$i]['prName'].'</td>
                        <td><a href="/content/view/ctNO/'.$data[$i]['ctNO'].'">'.$data[$i]['ctName'].'</a></td>
                        <td>'.$data[$i]['viewCount'].'</td>
                        <td>'.$data[$i]['ctRegDate'].'</td>
                    </tr>
            ';
            }


            echo json_encode($html);



        }elseif($params['mode'] == "programTop10"){
            $data = $this->program_model->_select_top10(array('date'=>$params['date']));
            for($i=0; $i<count($data); $i++) {
                $html .= '
                    <tr>
                        <td>'.($i+1).'</td>
                        <td><a href="/'.$data[$i]['prType'].'/view/prCode/'.$data[$i]['prCode'].'">'.$data[$i]['prName'].'</a></td>
                        <td>'.$data[$i]['viewCount'].'</td>
                        <td>'.$data[$i]['prRegDate'].'</td>
                    </tr>
            ';
            }


            echo json_encode($html);

        }
    }

}