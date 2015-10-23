<?php
/**
 * @purpose PopupSchedule_dt Model
 * @author  JoonCh
 * @since   13. 7. 27.
 */

class popupschedule_dt_model extends CI_Model {

    public function _select_list($params=array()) {
        $query = "
        select *, DATE_FORMAT(dtSTime, '%Y-%m-%d') as dtSDate, DATE_FORMAT(dtETime, '%Y-%m-%d') as dtEDate
        from gn_popupschedule_dt_data
        where DATE_FORMAT(dtSTime, '%Y-%m-%d') = '".$params['lsDate']."'";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_popupschedule_dt_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "dtNO = (select maxNO from (select IFNULL(max(dtNO),0) + 1 as maxNO from gn_popupschedule_dt_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
