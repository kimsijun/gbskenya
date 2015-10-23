<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class cfg_board_model extends CI_Model {

    public function _select_list($params=array()) {
        $query = "
        select bcg.*, count(bo.bodID) as bo_cnt
        from gn_board_cfg as bcg
        left outer join gn_board_data as bo
        on bcg.bodID=bo.bodID";

        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' and ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $query .=" group by bcg.bodID";
        if(isset($params['limit']) && !$params['offset']) $query .=" limit ".$params['limit'];
        else if($params['offset']) $query .= " limit ".$params['offset'].", ".$params['limit'];
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }
}
?>
