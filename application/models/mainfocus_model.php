<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class mainfocus_model extends CI_Model {
    public function _select_list() {
        $query = "
            SELECT
                SUBSTRING(A.mFName, 1, 20) AS mFName,
                A.mFNO, A.mFType, A.prCode, A.ctNO, A.mFLink, A.mFOrder, A.mFSDate, A.mFEDate, A.mFThumb,
                B.prPreCode,
                LOWER(B.prType) AS prType,
                B.prThumb, B.prName
                , ( SELECT COUNT(1) FROM gn_program_data b1 WHERE b1.prPreCode = B.prCode ) AS prSub
				,C.ctName, C.ctThumbS
                , ( SELECT COUNT(1) FROM gn_content_data c1 WHERE c1.prCode = B.prCode AND DATE_FORMAT(c1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS new
            FROM gn_mainFocus_data AS A
            LEFT JOIN gn_program_data AS B
            ON A.prCode=B.prCode
            LEFT JOIN gn_content_data AS C
            ON A.ctNO = C.ctNO
            WHERE A.mFEDate > CURRENT_DATE() AND A.mFSDate < CURRENT_DATE()
            ORDER BY A.mFOrder ASC
        ";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_mainFocus_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date') && !strpos($key, 'SDate') && !strpos($key, 'EDate'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "mFNO = (select maxNO from (select IFNULL(max(mFNO),0) + 1 as maxNO from gn_mainFocus_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
