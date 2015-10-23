<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

clASs livecontent_model extends CI_Model {
    public function _select_cnt() {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT A.*, B.prCode, C.prName FROM gn_livecontent_data AS A
        LEFT OUTER JOIN gn_content_data AS B
        ON A.ctNO = B.ctNO
        LEFT OUTER JOIN gn_program_data AS C
        ON A.prCode = C.prCode
        LEFT OUTER JOIN gn_program_data AS D
        ON B.prCode = D.prCode";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }

        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" ORDER BY ".$params["oKey"]." ".$params["oType"];
        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->num_rows();
    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT A.*, B.prCode, C.prName FROM gn_livecontent_data AS A
        LEFT OUTER JOIN gn_content_data AS B
        ON A.ctNO = B.ctNO
        LEFT OUTER JOIN gn_program_data AS C
        ON A.prCode = C.prCode
        LEFT OUTER JOIN gn_program_data AS D
        ON B.prCode = D.prCode";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }

        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" ORDER BY ".$params["oKey"]." ".$params["oType"];
        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }
    public function _select_row($lcNO) {
        $query = "
        SELECT A.*, B.prCode, C.prName FROM gn_livecontent_data AS A
        LEFT OUTER JOIN gn_content_data AS B
        ON A.ctNO = B.ctNO
        LEFT OUTER JOIN gn_program_data AS C
        ON A.prCode = C.prCode
        LEFT OUTER JOIN gn_program_data AS D
        ON B.prCode = D.prCode
        where A.lcNO = ".$lcNO ;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->row_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_livecontent_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "lcNO = (SELECT maxNO FROM (SELECT IFNULL(max(lcNO),0) + 1 AS maxNO FROM gn_livecontent_data) AS MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
