<?php
/*
| -------------------------------------------------------------------
| @ TITLE   프로그램 관련 데이터 처리 모델
| @ AUTHOR  JoonCh
| @ SINCE   13. 6. 7.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class program_model extends CI_Model {

    public function _select_list($params=array()) {
        $whichTable = ($params['prCode'])? "b" : "a";
        $query = "
              select a.prCode AS prPreCode
                 , a.prName
                 , a.prSort
                 , b.prCode
                 , b.prName
                 , b.prSort
                 , b.prType
                 , b.prThumb
                 , ( select count(1) from gn_program_data b1 where b1.prPreCode = b.prCode ) AS prSub
                 , ( select count(1) from gn_content_data c1 where c1.prCode = b.prCode and DATE_FORMAT(c1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS prNew
            from gn_program_data a
               , gn_program_data b
            where ".$whichTable.".prPreCode = '".$params['prCode']."'
              and a.prType = '".$params['prType']."'
              and b.prPreCode = a.prCode
            order by a.prSort, b.prSort";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }
    public function _select_list_all($params=array()) {
        $whichTable = ($params['prCode'])? "b" : "a";
        $query = "
              select a.prCode AS prPreCode
                 , a.prName
                 , a.prSort
                 , b.prCode
                 , b.prName
                 , b.prSort
                 , b.prType
                 , b.prThumb
                 , ( select count(1) from gn_program_data b1 where b1.prPreCode = b.prCode ) AS prSub
                 , ( select count(1) from gn_content_data c1 where c1.prCode = b.prCode and DATE_FORMAT(c1.ctRegDate, '%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) AS prNew
            from gn_program_data a
               , gn_program_data b
            where ".$whichTable.".prPreCode = '".$params['prCode']."'
              and b.prPreCode = a.prCode
            order by a.prSort, b.prSort
        ";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_top10($params=array()){
        if(!$params['date'] || $params['date'] == "month")    $strQuery = "and vRegDate > date_add(now(),interval -30 day) ";
        else if($params['date'] == "week")    $strQuery = "and vRegDate > date_add(now(),interval -7 day) ";
        else if($params['date'] == "day")    $strQuery = "and vRegDate > date_add(now(),interval -1 day) ";

        $query = "SELECT A.prCode, B.prName, count(*) AS viewCount, SUBSTRING(B.prRegDate, 1,10) AS prRegDate,LOWER(B.prType) AS prType
                    FROM gn_view_log AS A
                    LEFT OUTER JOIN gn_program_data AS B
                    ON A.prCode = B.prCode
                    WHERE vType = 'PROGRAM'";
        $query .= $strQuery;
        $query .= "GROUP BY
                    prCode
                  ORDER BY
                    viewCount DESC
                  LIMIT 10";

        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }
}
?>
