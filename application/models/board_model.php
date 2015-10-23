<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class board_model extends CI_Model {
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_board_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "boNO = (select maxNO from (select IFNULL(max(boNO),0) + 1 as maxNO from gn_board_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }

    public function _select_list($params=array()) {
        $query = "
        select
            (select count(bcoNO) from gn_comment_data as bco) as bco_count,
		    bo.boNO, bo.boName, bo.mbID, bo.boHit, bo.boRegDate, bo.boDepth, bo.bodID, bo.boIsDelete, bo.boParent, bo.boStep, bo.boGroup, member.mbNick
        from gn_board_data as bo
        join gn_member_data as member
        on bo.mbID = member.mbID";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' and ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $query .=" group by bo.boNO, bo.boName, bo.mbID, bo.boHit, bo.boRegDate, bo.boDepth, bo.bodID order by boGroup DESC, boStep ASC";
        if(isset($params['limit']) && !$params['offset']) $query .=" limit ".$params['limit'];
        else if($params['offset']) $query .= " limit ".$params['offset'].", ".$params['limit'];
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _private_select_list($params=array()) {
        $query = "
        select
            (select count(bcoNO) from gn_comment_data as bco) as bco_count,
		    bo.boNO, bo.boName, bo.mbID, bo.boHit, bo.boRegDate, bo.boDepth, bo.bodID, bo.boIsDelete, bo.boParent, bo.boStep, bo.boGroup
        from gn_board_data as bo ";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' and ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $query .=" order by boGroup DESC, boStep ASC";
        if(isset($params['limit'])) $query .=" limit ".$params['limit'];
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }
}
?>
