<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class view_model extends CI_Model {
    /*****
        view_log 테이블에 콘텐츠/ 프로그램 view log 삽입
    ******/
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_view_log SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "vNO = (select maxNO from (select IFNULL(max(vNO),0) + 1 as maxNO from gn_view_log) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    /*****
    viewCount_log 테이블에서 목록 조회 (CONTENT)
    ******/
    public function _select_ctlist($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "SELECT A.*,B.ctNO, B.prCode, B.ctName, C.prName FROM gn_viewCount_log AS A
                    JOIN gn_content_data AS B
                    ON A.ctNO = B.ctNO
                    JOIN gn_program_data AS C
                    ON B.prCode=C.prCode";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }

        $query .=" ORDER BY A.vCDate DESC,A.vCCount DESC";
        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    /*****
    viewCount_log 테이블에서 목록 조회 (PROGRAM)
     ******/
    public function _select_prlist($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "SELECT A.*,  B.prCode, B.prName, B.prPreCode, LOWER(B.prType) AS prType FROM gn_viewCount_log AS A
                    JOIN gn_program_data AS B
                    ON A.prCode = B.prCode ";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }

        $query .=" ORDER BY A.vCDate DESC,A.vCCount DESC";
        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

}
?>
