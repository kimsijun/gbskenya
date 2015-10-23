<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class program_comment_model extends CI_Model {

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_program_comment_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "pbcoNO = (select maxNO from (select IFNULL(max(pbcoNO),0) + 1 as maxNO from gn_program_comment_data where prCode = '".$params['prCode']."') as MaxNO),";
        $query .= "pbcoGroup = (select maxNO from (select IFNULL(max(pbcoNO),0) + 1 as maxNO from gn_program_comment_data where prCode = '".$params['prCode']."') as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT  A.*, B.mbNick, B.mbFirstName, B.mbLastName, C.prType, C.prName
            FROM gn_program_comment_data AS A
            LEFT JOIN gn_member_data AS B
            ON A.mbID=B.mbID
			LEFT JOIN gn_program_data AS C
			ON A.prCode=C.prCode";

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

}
?>
