<?php
/**
 * @purpose PopupSchedule Model
 * @author  JoonCh
 * @since   13. 7. 27.
 */

class popupschedule_model extends CI_Model {
    public function _select_list() {
        $query = "
                select
                    psName,
                    SUBSTRING(psSDate, 1, 10) as psSDate,
                    replace(SUBSTRING(psSDate, 6, 5),'-','.') as SDate,
                    SUBSTRING(psEDate, 1, 10) as psEDate,
                    replace(SUBSTRING(psEDate, 6, 5),'-','.') as EDate,
                    psLiveTime
                from gn_popupschedule_data
                where DATE_FORMAT(psSDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                order by psSDate desc
                limit 4" ;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_popupschedule_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && !strpos($key, 'SDate') && !strpos($key, 'EDate'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "psNO = (select maxNO from (select IFNULL(max(psNO),0) + 1 as maxNO from gn_popupschedule_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
