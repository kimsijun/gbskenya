<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class liveschedule_model extends CI_Model {
    public function _select_cnt($params=array()) {
        $query = "
        select a.*, b.lcDuration, b.lcName from gn_liveschedule_data as a
        inner join gn_livecontent_data as b
        on a.lcNO = b.lcNO
        where a.chNO = '".$params['chNO']."'
        and a.lsDate = '".$params['lsDate']."'
        order by a.lsSTime asc ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->num_rows();
    }

    public function _select_list($params=array()) {
        $query = "
        select a.*, b.lcDuration, b.lcName from gn_liveschedule_data as a
        inner join gn_livecontent_data as b
        on a.lcNO = b.lcNO
        where a.chNO = '".$params['chNO']."'
        and a.lsDate = '".$params['lsDate']."'
        order by a.lsSTime asc
        ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_row($lsNO) {
        $query = "
        select a.*, b.lcDuration, b.lcName from gn_liveschedule_data as a
        inner join gn_livecontent_data as b
        on a.lcNO = b.lcNO
        where a.lsNO = '".$lsNO."'
        ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->row_array();
    }

    public function _select_channel_list($params=array()) {
        $query = "
        select a.chNO, a.lsDate, b.chName from gn_liveschedule_data as a
        join gn_channel_data as b
        on a.chNO = b.chNO
        where a.lsDate = '".$params['lsDate']."'
        group by a.chNO
        ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_live_list($params=array()) {
        $query = "
        select
        a.chNO,
        a.lsDate,
        SUBSTRING(a.lsSTime, 1, 5) as lsSTime,
        a.lsETime,
        a.lsNO,
        a.lcNO,
        a.lsisView,
        a.lsIsSpecial,
        b.lcName
        from gn_liveschedule_data as a
        join gn_livecontent_data as b
        on a.lcNO = b.lcNO
        where a.chNO=".$params['chNO']."
        and a.lsIsView='".$params['lsIsView']."'
        and a.lsDate='".$params['lsDate']."'
        order by a.".$params['oKey']." ".$params['oType']."
        ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_liveschedule_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && strpos($key, 'lsDate')!="false")    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "lsNO = (select maxNO from (select IFNULL(max(lsNO),0) + 1 as maxNO from gn_liveschedule_data) as MaxNO)";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>