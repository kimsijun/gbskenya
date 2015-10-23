<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class advertise_model extends CI_Model {
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_advertise_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "adNO = (select maxNO from (select IFNULL(max(adNO),0) + 1 as maxNO from gn_advertise_data) as MaxNO)";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
