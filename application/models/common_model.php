<?php
/*
| -------------------------------------------------------------------
| @ TITLE   공통 모델
| @ AUTHOR  JoonCh
| @ SINCE   14. 5. 9.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class common_model extends CI_Model {
    private function _query($params) {

        /**
         * ctNO로 검색
         */
        if(isset($params["ctNOs"])) {
            foreach($params["ctNOs"] as $v){
                $this->db->or_where('ctNO', $v);
            }
            unset($params["ctNOs"]);
        }


        /**
         * 관리자 회원 상세검색
         */
        if(isset($params["mbHumanStatus"])){
            $humanDate = date("Ymd", strtotime(date("Ymd")." -".$params["mbHumanStatus"]." day"));
            $where["mbLastLoginDate <"] = $humanDate;
            unset($params["mbHumanStatus"]);
        }
        if(isset($params["mbAge"])){
            $year = date("Y")- ($params["mbAge"]-1);
            if($params["mbAge"] != "70")    $where["mbBirth <"] = $year."-00-00";
            $where["mbBirth >"] = ($year-10)."-00-00";
            unset($params["mbAge"]);
        }

        /**
         * 키워드 선택검색
         */
        if(isset($params["secKey"]) && isset($params["secTxt"])) {
            if($params["secKey"] == "all"){
                $secColumn = $params['secColumn'];
                $secTxt = $params["secTxt"];
                unset($params['secColumn']);
            }else   $this->db->like($params["secKey"], $params["secTxt"]);
                //$where[$params["secKey"]." LIKE"] = "%".$params["secTxt"]."%";
            unset($params["secKey"]); unset($params["secTxt"]);
        }
        /**
         * 일반 컬럼들 선택검색
         */
        foreach($params as $key => $val){
            if($key != "oKey" && $key != "oType" && $key != 'gType' && $key != 'limit' && $key != 'offset' && $key != 'page'){
                if(count(explode("_0",$key)) >1 ){
                    $where[current(explode("_0",$key))." >"] = $val;
                } else if(count(explode("_1",$key)) >1 ){
                    $where[current(explode("_1",$key))." <"] = $val;
                }else {
                    $where[$key] = $val;
                }
            }
        }


        /**
         * 키워드 전체검색
         */
        if(isset($secColumn)) {
            foreach($secColumn as $val)
                $this->db->or_like($val, $secTxt);
                //$where[$val." LIKE"] = "%".$secTxt."%";
        }
        $where["(1)"] = "1";
        return $where;
    }
    public function _select_cnt($secTable, $params=array()) {
        $query = "select * from gn_".$secTable;
        $array = $this->_query($params);
        $where = "";
        foreach($array as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }
        if(isset($params["gType"])) $query .=" group by ".$params["gType"];
        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" order by ".$params["oKey"]." ".$params["oType"];
        if(isset($params['limit']) && !$params['offset']) $query .=" limit ".$params['limit'];
        else if($params['offset']) $query .= " limit ".$params['offset'].", ".$params['limit'];

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->num_rows();
    }

    public function _select_list($secTable, $params=array()) {
        $query = "select * from gn_".$secTable;
        $array = $this->_query($params);
        $where = "";
        foreach($array as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . "= '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }
        if(isset($params["gType"])) $query .=" group by ".$params["gType"];
        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" order by ".$params["oKey"]." ".$params["oType"];
        if(isset($params['limit']) && !$params['offset']) $query .=" limit ".$params['limit'];
        else if($params['offset']) $query .= " limit ".$params['offset'].", ".$params['limit'];

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_row($secTable, $params=array()) {
        $query = "select * from gn_".$secTable;
        $array = $this->_query($params);
        $where = "";
        foreach($array as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->row_array();
    }

    public function _insert($secTable, $params=array()) {
        $query = "INSERT INTO gn_".$secTable." SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && !strpos($key, 'SDate') && !strpos($key, 'EDate')&& !strpos($key, 'lsDate')&& !strpos($key, 'EventDate'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query = substr($query,0,-1);

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    public function _delete($secTable, $params=array()) {
        $query = "DELETE FROM gn_".$secTable." ";
        $array = $this->_query($params);
        $where = "";
        foreach($array as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    public function _update($secTable, $params, $params2) {
        $query = "UPDATE gn_".$secTable." SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && !strpos($key, 'RegDate') && !strpos($key, 'SDate') && !strpos($key, 'EDate')&& !strpos($key, 'lsDate')&& !strpos($key, 'EventDate'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query = substr($query,0,-1);
        $array = $this->_query($params2);
        $where = "";
        foreach($array as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
