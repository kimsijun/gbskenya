<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class comment_model extends CI_Model {
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_comment_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "bcoNO = (select maxNO from (select IFNULL(max(bcoNO),0) + 1 as maxNO from gn_comment_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    public function _select_list($params=array()) {
        $query = "
                    SELECT A.*, B.bodID
                    FROM gn_comment_data AS A
                    LEFT OUTER JOIN gn_board_data AS B
                    ON A.boNO = B.boNO
                    ";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' and ";
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

    public function _select_cnt($params=array()) {
        $query = "
                    SELECT A.*, B.bodID
                    FROM gn_comment_data AS A
                    LEFT OUTER JOIN gn_board_data AS B
                    ON A.boNO = B.boNO
                    ";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' and ";
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
}
?>
