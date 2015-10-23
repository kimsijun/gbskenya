<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ PURPOSE 데이터 이전
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 19.
| -------------------------------------------------------------------
|
*/

class migration extends common {

    public function __construct(){
        parent::__construct();
        $this->member_lib->adminCheck();
    }

    public function content() {
        $this->GNTV = $this->load->database('gbs', TRUE);

        $query = "  SELECT *
                    FROM gn_content_data
                    ORDER BY ctNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();

        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbID']);
            unset($data[$i]['mbFirstName']);
            unset($data[$i]['mbLastName']);
            unset($data[$i]['mbNick']);
            unset($data[$i]['prName']);
        }

        $query = "";
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_content_data SET ";
            foreach($v as $key => $val){
                if($key == "ctName" || $key == "ctContent")    $query .= $key. " = '". addslashes($val)."',";
                else if($key == "ctAuthor")    $query .= "ctSpeaker = '". addslashes($val)."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }



    public function board_cfg() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_board_cfg
                    ORDER BY bodID";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['boRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_board_cfg SET ";
            foreach($v as $key => $val){
                if($key == "bodID")    $query .= "bodID = '". $val."',";
                else if($key == "boName")    $query .= "bodName = '". $val."',";
                else if($key == "boIsDelete")    $query .= "bodIsDelete = '". $val."',";
                else if($key == "boModDate")    $query .= "bodModDate = '". $val."',";
                else if($key == "boRegDate")    $query .= "bodRegDate = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function board() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_board_data
                    ORDER BY boNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['boCodeType']);
            unset($data[$i]['boCode']);
            unset($data[$i]['mbFirstName']);
            unset($data[$i]['mbLastName']);
            unset($data[$i]['mbNick']);
            unset($data[$i]['boEmail']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_board_data SET ";
            foreach($v as $key => $val){
                if($key == "bodID")    $query .= "bodID = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function member() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_member_data
                    ORDER BY mbID";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbPhone']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_member_data SET ";
            foreach($v as $key => $val){  $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function channel() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_channel_data
                    ORDER BY chNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['chRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_channel_data SET ";
            foreach($v as $key => $val){   $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function content_comment() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_content_comment_data
                    ORDER BY cbcoNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbFirstName']);
            unset($data[$i]['mbLastName']);
            unset($data[$i]['mbNick']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_content_comment_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function program() {
        $this->GNTV = $this->load->database('gbs', TRUE);

        $query = "  SELECT *
                    FROM gn_program_data
                    ORDER BY prCode";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();

        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['prProducer']);
            unset($data[$i]['prEmcee']);
            unset($data[$i]['prMainViewSort']);
            unset($data[$i]['prIsMainView']);
        }

        $query = "";
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_program_data SET ";
            foreach($v as $key => $val){
                $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function tag() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_tag_data
                    ORDER BY tgNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['tgCodeType']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_tag_data SET ";
            foreach($v as $key => $val){
                if($key == "tgCode")    $query .= "ctNO = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function supportAd() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_supportAd_data
                    ORDER BY sANO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbID']);
            unset($data[$i]['sARemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_supportAd_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function search() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_search_log
                    ORDER BY scNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();


        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_search_log SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function news() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_news_data
                    ORDER BY nwNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();

        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbID']);
            unset($data[$i]['mbFirstName']);
            unset($data[$i]['mbLastName']);
            unset($data[$i]['mbNick']);
            unset($data[$i]['ncName']);
        }
        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_news_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function popupschedule() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_popupschedule_data
                    ORDER BY psNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['psRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_popupschedule_data SET ";
            foreach($v as $key => $val){
                if($key == "bodID")    $query .= "bodID = '". $val."',";
                else if($key == "boName")    $query .= "bodName = '". $val."',";
                else if($key == "boIsDelete")    $query .= "bodIsDelete = '". $val."',";
                else if($key == "boModDate")    $query .= "bodModDate = '". $val."',";
                else if($key == "boRegDate")    $query .= "bodRegDate = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query .= "psLiveTime='',";
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function popupscheduleDt() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_popupschedule_dt_data
                    ORDER BY dtNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['dtRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_popupschedule_dt_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function advertise() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_advertise_data
                    ORDER BY adNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();

        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_advertise_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }


    public function adcontent() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_adcontent_data
                    ORDER BY aCNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['aCRemoteIP']);
            unset($data[$i]['mbID']);
            unset($data[$i]['aCKey']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_adcontent_data SET ";
            foreach($v as $key => $val){  $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }


    public function program_comment() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_program_comment_data
                    ORDER BY pbcoNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbAccountType']);
            unset($data[$i]['mbFirstName']);
            unset($data[$i]['mbLastName']);
            unset($data[$i]['mbNick']);
            unset($data[$i]['mbLink']);
            unset($data[$i]['mbThumb']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_program_comment_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function livecontent() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_livecontent_data
                    ORDER BY lcNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['prName']);
            unset($data[$i]['lcThumb']);
            unset($data[$i]['lcThumbOrigin']);
            unset($data[$i]['lcRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_livecontent_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function liveschedule() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_liveschedule_data
                    ORDER BY lsNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['chName']);
            unset($data[$i]['lcName']);
            unset($data[$i]['lcDuration']);
            unset($data[$i]['lsRemoteIP']);
            unset($data[$i]['psNO']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_liveschedule_data SET ";
            foreach($v as $key => $val){ $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function mainFocus() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_mainFocus_data
                    ORDER BY mFNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();



        // 컬럼 제거
        for($i=0; $i<count($data); $i++) {
            unset($data[$i]['mbID']);
            unset($data[$i]['mFRemoteIP']);
        }



        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_mainFocus_data SET ";
            foreach($v as $key => $val){
                if($key == "mFCodeType")    $query .= "mFType = '". $val."',";
                else if($key == "mFCode")    $query .= "prCode = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function podcast() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_podcast_data
                    ORDER BY pcNO";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();
        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_podcast_data SET ";
            foreach($v as $key => $val){
                if($key == "pcAuthor")    $query .= "pcSpeaker = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }


    public function viewCount() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_viewStats_log
                    ORDER BY vSIdx";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();


        // 컬럼 변경
        $idx=1;
        foreach($data as $k => $v){
            $query = "INSERT gn_viewCount_log SET ";
            foreach($v as $key => $val){
                if($key == "vSIdx")    $query .= "vCNO = '". $val."',";
                elseif($key == "vSType")    $query .= "vCType = '". $val."',";
                elseif($key == "vSCount")    $query .= "vCCount = '". $val."',";
                elseif($key == "vSDate")    $query .= "vCDate = '". $val."',";
                elseif($key == "vSGroup")    $query .= "vCSection = '". $val."',";
                elseif($key == "vSRegDate")    $query .= "vCRegDate = '". $val."',";
                elseif($key == "vSKey")    $query .= "prCode = '". $val."',";
                else                    $query .= $key. " = '". $val."',";
            }
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function mypageFavor() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_mypage_favor_log
                    ORDER BY mpFIdx";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();


        // 컬럼 변경
        $idx=1;
        $chkType='';
        foreach($data as $k => $v){
            $query = "INSERT gn_mypage_log SET ";
            foreach($v as $key => $val){
                if($chkType){
                    if($chkType=="PROGRAM" )  $query.="prCode = '".$val."',";
                    else $query.="ctNO = '".$val."',";
                    $chkType='';
                }
                elseif($key == "mpFIdx")    $query .= "mpNO = '". $idx."',";
                elseif($key == "mpFType" ){
                    $query .= "mpType = '". $val."',";
                    if($val=='PROGRAM') $chkType='PROGRAM';
                    else $chkType='CONTENT';
                }
                elseif($key == "mpFRemoteIP")    $query .= "mpRemoteIP = '". $val."',";
                elseif($key == "mpFRegDate")    $query .= "mpRegDate = '". $val."',";
                elseif($key == "mbID")    $query .= "mbID = '". $val."',";
            }
            $query .="mpSection = 'FAVOR',";
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function mypageLike() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_mypage_like_log
                    ORDER BY mpLIdx";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();


        // 컬럼 변경
        $idx=35;
        $chkType='';
        foreach($data as $k => $v){
            $query = "INSERT gn_mypage_log SET ";
            foreach($v as $key => $val){
                if($chkType){
                    if($chkType=="PROGRAM" )  $query.="prCode = '".$val."',";
                    else $query.="ctNO = '".$val."',";
                    $chkType='';
                }
                elseif($key == "mpLIdx")    $query .= "mpNO = '". $idx."',";
                elseif($key == "mpLType" ){
                    if($val=='prCode'){
                        $chkType='PROGRAM';
                        $query .= "mpType = 'PROGRAM',";
                    }
                    else {
                        $chkType='CONTENT';
                        $query .= "mpType = 'CONTENT',";
                    }
                }
                elseif($key == "mpLRemoteIP")    $query .= "mpRemoteIP = '". $val."',";
                elseif($key == "mpLRegDate")    $query .= "mpRegDate = '". $val."',";
                elseif($key == "mbID")    $query .= "mbID = '". $val."',";
            }
            $query .="mpSection = 'LIKE',";
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }

    public function mypageView() {
        // 데이터 가져오기
        $this->GNTV = $this->load->database('gbs', TRUE);
        $query = "  SELECT *
                    FROM gn_mypage_view_log
                    ORDER BY mpVIdx";

        $result = $this->GNTV->query($query);
        $data = $result->result_array();


        // 컬럼 변경
        $idx=60;
        $chkType='';
        foreach($data as $k => $v){
            $query = "INSERT gn_mypage_log SET ";
            foreach($v as $key => $val){
                if($chkType){
                    if($chkType=="PROGRAM" )  $query.="prCode = '".$val."',";
                    else $query.="ctNO = '".$val."',";
                    $chkType='';
                }
                elseif($key == "mpVIdx")    $query .= "mpNO = '". $idx."',";
                elseif($key == "mpVType" ){
                    if($val=='prCode'){
                        $chkType='PROGRAM';
                        $query .= "mpType = 'PROGRAM',";
                    }
                    else {
                        $chkType='CONTENT';
                        $query .= "mpType = 'CONTENT',";
                    }
                }
                elseif($key == "mpVRemoteIP")    $query .= "mpRemoteIP = '". $val."',";
                elseif($key == "mpVRegDate")    $query .= "mpRegDate = '". $val."',";
                elseif($key == "mbID")    $query .= "mbID = '". $val."',";
            }
            $query .="mpSection = 'VIEW',";
            $query = substr($query,0,-1);
            $this->db->query($query);
            if($this->db->_error_message()) echo $idx."번째 : ".$this->db->_error_message()."<br>";
            $idx++;
        }

        echo "완료";
    }
}
