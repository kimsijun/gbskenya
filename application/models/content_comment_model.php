<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class content_comment_model extends CI_Model {

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_content_comment_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "cbcoNO = (select maxNO from (select IFNULL(max(cbcoNO), 0) + 1 as maxNO from gn_content_comment_data where ctNO = '".$params['ctNO']."') as MaxNO),";
        $query .= "cbcoGroup = (select maxNO from (select IFNULL(max(cbcoNO), 0) + 1 as maxNO from gn_content_comment_data where ctNO = '".$params['ctNO']."') as MaxNO)";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();

    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT A.* , B.mbNick, B.mbFirstName, B.mbLastName
            FROM gn_content_comment_data A
            JOIN gn_member_data B
               ON A.mbID = B.mbID";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit')
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
