<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class global_model extends CI_Model {

    public function _insert($params=array()) {
        $query = "UPDATE gn_db_error_log
                SET eUserMemo = '".$params['eUserMemo']."'
                WHERE eNO=".$params['eNO']. "
                  AND codeSeq =".$params['codeSeq'];

        $this->db->query($query);

    }
}
?>
