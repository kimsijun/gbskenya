<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class podcast_model extends CI_Model {
    public function _insert($params=array()) {
        $query = "INSERT INTO gn_podcast_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= " pcNO = (select maxNO from (select IFNULL(max(pcNO), 0) + 1 as maxNO from gn_podcast_data where prCode = '".$params['prCode']."') as MaxNO)";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
