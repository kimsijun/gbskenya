<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class tag_model extends CI_Model {

    public function _cnt_tag_list($params=array()) {
        $query = "select count(tgTag) as tgTagCnt, tgTag, ctNO from gn_tag_data";
        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType')
                $where .= $key . " = '" . $val . "' and ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }
        $query .= " group by tgTag having tgTagCnt > 0 order by tgTagCnt desc";
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "  select
                        a.mbID, a.tgNO, a.ctNO, a.tgTag, SUBSTRING(a.tgRegDate,1,10)as tgRegDate,
                        b. prCode, CONCAT(SUBSTRING(b.ctName,1,20),'...') as ctName, b.ctThumbS, b.ctViewCount,
                        c.prName, c.prPreCode, LOWER(c.prType) as prType
                    from gn_tag_data as a
                    left outer join gn_content_data as b
                    on a.ctNO = b.ctNO
                    left outer join gn_program_data as c
                    on b.prCode = c.prCode ";
        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }

        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" ORDER BY ".$params["oKey"]." ".$params["oType"];
        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _insert($params=array()) {
        $query = "INSERT INTO gn_tag_data SET ";

        foreach($params as $key => $val){
            if(strpos($key, 'Date'))    $query .= $key . " = " . $val . ",";
            else                        $query .= $key . " = '" . $val . "',";
        }
        $query .= "tgNO = (select maxNO from (select IFNULL(max(tgNO),0) + 1 as maxNO from gn_tag_data) as MaxNO)";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
    }
}
?>
