<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class mypage_model extends CI_Model {
    public function _select_nwView_list($params=array()) {
        $query = $this->db->query( "
        select a.mbID, a.mpNO, a.mpSection, a.mpType, a.nwNO, SUBSTRING(a.mpRegDate, 1,10) as mpRegDate,CONCAT(SUBSTRING(b.nwName, 1, 15),'...') as nwName, b.ncCode, b.nwViewCount, b.nwThumbS,c.ncCode, c.ncName, c.ncPreCode
        from gn_mypage_log as a
        left outer join gn_news_data as b
        on a.nwNO = b.nwNO
        left outer join gn_news_category_data as c
        on b.ncCode = c.ncCode
        where a.mpSection='VIEW'
        and a.mbID = '".$params['mbID']."'
        and a.mpType='NEWS'
        order by mpRegDate desc
        " );
        return $query->result_array();
    }
    public function _select_ctView_list($params=array()) {
        $query = $this->db->query( "
        select a.mbID, a.mpNO, a.mpSection, a.mpType, a.prCode, a.ctNO, SUBSTRING(a.mpRegDate, 1,10) as mpRegDate,CONCAT(SUBSTRING(b.ctName, 1, 15),'...') as ctName, b.ctViewCount, b.ctThumbS, c.prThumb, c.prCode, c.prName, c.prPreCode, LOWER(c.prType) as prType
        from gn_mypage_log as a
        left outer join gn_content_data as b
        on a.ctNO = b.ctNO
        left outer join gn_program_data as c
        on b.prCode = c.prCode
        where a.mpSection='VIEW'
        and a.mbID = '".$params['mbID']."'
        and a.mpType='CONTENT'
        order by mpRegDate desc
        " );
        return $query->result_array();
    }

    public function _select_ctFavor_list($params=array()) {
        $query = $this->db->query( "
        select a.mbID, a.mpNO, a.mpSection, a.mpType, a.prCode, a.ctNO, SUBSTRING(a.mpRegDate, 1,10) as mpRegDate,CONCAT(SUBSTRING(b.ctName, 1, 15),'...') as ctName, b.ctViewCount, b.ctThumbS, c.prThumb, c.prCode, c.prName, c.prPreCode, LOWER(c.prType) as prType
        from gn_mypage_log as a
        left outer join gn_content_data as b
        on a.ctNO = b.ctNO
        left outer join gn_program_data as c
        on b.prCode = c.prCode
        where a.mpSection='FAVOR'
        and a.mbID = '".$params['mbID']."'
        and a.mpType='CONTENT'
        order by mpRegDate desc
        " );
        return $query->result_array();
    }

    public function _select_prFavor_list($params=array()) {
        $query = $this->db->query( "
        select a.mbID, a.mpNO, a.mpSection, a.mpType, a.prCode, a.ctNO, SUBSTRING(a.mpRegDate, 1,10) as mpRegDate, c.prThumb, c.prCode, c.prName, c.prPreCode, LOWER(c.prType) as prType
        from gn_mypage_log as a
        left outer join gn_program_data as c
        on a.prCode = c.prCode
        where a.mpSection='FAVOR'
        and a.mbID = '".$params['mbID']."'
        and a.mpType='PROGRAM'
        order by mpRegDate desc
        " );
        return $query->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_mypage_log SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "mpNO = (select maxNO from (select IFNULL(max(mpNO),0) + 1 as maxNO from gn_mypage_log) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
