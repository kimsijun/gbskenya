<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class supportad_model extends CI_Model {
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_supportAd_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && !strpos($key, 'SDate') && !strpos($key, 'EDate'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "sANO = (select maxNO from (select IFNULL(max(sANO),0) + 1 as maxNO from gn_supportAd_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
