<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class content_model extends CI_Model {

    public function _select_download_list($params=array()){
        $query = "SELECT * FROM gn_content_data as B WHERE prCode = ".$params['prCode']." and ctRegDate > date_add(now(),interval -2 day)";

        $rbSet = $this->db->query($query);$idx = 0;
        foreach ($rbSet->result() as $row){
            $result[$idx++]['ctNO'] = $row->ctNO;
        }
        return $result;
    }

    public function _select_newItem_list($params=array()){
        $query = "SELECT * FROM gn_content_data WHERE prCode = '".$params['prCode']."' and ctEventDate >=  '".$params['ctEventDate >='] ."'";

        $rbSet = $this->db->query($query);
        return $rbSet->num_rows();
    }

    public function _select_row($params=array()){

        $query = "  SELECT A.*,B.prName
                    FROM gn_content_data AS A
                    LEFT OUTER JOIN gn_program_data AS B
                    ON A.prCode = B.prCode
                   ";
        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' where '.$where;
        }

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->row_array();
    }

    public function _select_top10($params=array()){
        if(!$params['date'] || $params['date'] == "month")    $strQuery = "and vRegDate > date_add(now(),interval -30 day) ";
        else if($params['date'] == "week")    $strQuery = "and vRegDate > date_add(now(),interval -7 day) ";
        else if($params['date'] == "day")    $strQuery = "and vRegDate > date_add(now(),interval -1 day) ";

        $query = "  SELECT
                        C.prCode, C.prName,LOWER(C.prType) AS prType, A.ctNO, CONCAT(SUBSTRING(B.ctName, 1, 18),'...') AS ctName ,B.ctThumbS, B.ctEventDate, count(*) AS viewCount, SUBSTRING(B.ctRegDate, 1,10) AS ctRegDate,
                        ( select count(1) from gn_content_data C1 where C1.prCode = B.prCode and C1.ctRegDate = B.ctRegDate and DATE_FORMAT(C1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS new
                    FROM gn_view_log AS A
                    LEFT OUTER JOIN gn_content_data AS B
                    ON A.ctNO = B.ctNO
                    LEFT OUTER JOIN gn_program_data AS C
                    ON B.prCode = C.prCode
                    WHERE vType = 'CONTENT'
                   ";

        $query .= $strQuery;
        $query .= "GROUP BY ctNO
                  ORDER BY viewCount desc
                  limit 10";
        $rbSet = $this->db->query($query);
        return $rbSet->result_array();
    }

    public function _select_today_list() {
        $query = $this->db->query("
            select
                A.ctNO,
                A.prCode,
                B.prName,
                LOWER(B.prType) AS prType,
                CONCAT(SUBSTRING(A.ctName, 1, 18),'...') AS ctName ,
                A.ctEventDate,
                A.ctThumbS,
                SUBSTRING(A.ctRegDate, 1, 10) AS ctRegDate,
                ( select count(1) from gn_content_data C1 where C1.prCode = B.prCode and C1.ctRegDate = A.ctRegDate and DATE_FORMAT(C1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS new

            from gn_content_data AS A
            join gn_program_data AS B
            on A.prCode = B.prCode
            where A.prCode = '003001'
            order by A.ctRegDate desc
            limit 24
        ");
        return $query->result_array();
    }

    public function _select_ctRecent_list($params=array()) {
        $query = $this->db->query("
            select
                A.ctNO,
                A.prCode,
                B.prName,
                LOWER(B.prType) AS prType,
                CONCAT(SUBSTRING(A.ctName, 1, 25),'...') AS ctName ,
                A.ctEventDate,
                A.ctThumbS,
                A.ctViewCount,
                A.ctViewMonthCount,
                SUBSTRING(A.ctRegDate, 1, 10) AS ctRegDate,
                ( select count(1) from gn_content_data C1 where C1.prCode = B.prCode and C1.ctRegDate = A.ctRegDate and DATE_FORMAT(C1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS new
            from gn_content_data AS A
            join gn_program_data AS B
            on A.prCode = B.prCode
            where A.prCode != '003001'
            order by A.ctRegDate desc
            limit ".$params['limit']."
        ");
        return $query->result_array();
    }

    public function _select_admin_list($params=array()) {
        $query = "
            SELECT b.prName, a.* FROM gn_content_data AS a
            JOIN gn_program_data AS b
            ON a.prCode = b.prCode
            ";
        $where = "";
        foreach($params as $key => $val){
            if($key != 'oKey' && $key !='oType' && $key !='limit' && $key !='offset' && $key !='page' && $key !='gType')
                $where .= $key . " = '" . $val . "' AND ";
        }
        if($where){
            $where = substr($where,0,-4);
            $query .=' WHERE '.$where;
        }
        if(isset($params["gType"])) $query .=" group by ".$params["gType"];
        if(isset($params["oType"]) && isset($params["oKey"])) $query .=" order by ".$params["oKey"]." ".$params["oType"];
        if(isset($params['limit']) && !$params['offset']) $query .=" limit ".$params['limit'];
        else if($params['offset']) $query .= " limit ".$params['offset'].", ".$params['limit'];

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_all_list($params=array()) {
        $query = $this->db->query("select * from gn_content_data where prCode like '".$params['prCode']."%' and prCode <> '".$params['prCode']."' order by prCode asc, ctEventDate asc, ctRegDate asc");
        return $query->result_array();
    }

}
?>
