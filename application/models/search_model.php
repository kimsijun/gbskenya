<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class search_model extends CI_Model {
    protected $table = array("content_data","program_data");
    protected $tableIdx = 0;

    private function _query($params) {
        /*  컨텐츠, 프로그램 키워드 & 행사일 검색    */
        $where = "(1) = 1";
        if($this->tableIdx == 0){
            if(isset($params["ctEventDate_0"]))
                $where .= " AND ctEventDate >= " . $params["ctEventDate_0"];
            if(isset($params["ctEventDate_1"]))
                $where .= " AND ctEventDate <= " . $params["ctEventDate_1"];
            if(isset($params["prCode"]))
                $where .= " AND prCode LIKE '" . $params["prCode"]."%'";

            $params["secTxt"] = iconv("euc-kr", "utf-8", $params["secTxt"]);

            if(isset($params["secKey"]) && isset($params["secTxt"])){
                if($params["secKey"] == "all"){
                    $where .= " AND ";
                    for($i=0;$i<count($params["secTxt"]);$i++){
                        $where .= "(ctName LIKE '%".$params["secTxt"][$i]."%' or ctContent LIKE '%".$params["secTxt"][$i]."%' or ctSpeaker LIKE '%".$params["secTxt"][$i]."%'";;
                        $where .= $i<count($params["secTxt"])-1 ? ") or ": ") ";

                    }
                }else {
                    $where .= " AND ";
                    for($i=0;$i<count($params["secTxt"]);$i++){
                        $where .= $params["secKey"]." LIKE '%".$params["secTxt"][$i]."%'";
                        if($i<count($params["secTxt"])-1) $where .= " or ";
                    }

                }
            }

        } else if($this->tableIdx == 1){
            if(isset($params["secKey"]) && isset($params["secTxt"])){
                $where .=" AND ";
                for($i=0;$i<count($params["secTxt"]);$i++){
                    $where .= "prName LIKE '%".$params["secTxt"][$i]."%'";
                    $where .= $i<count($params["secTxt"])-1 ? " or ": " ";
                }
            }
        }
        if($where) $this->db->where($where);    unset($where);
        $where["(1)"] = "1";
        return $where;
    }

    private function _and_query($params) {
        /*  컨텐츠, 프로그램 키워드 & 행사일 검색    */
        $where = "(1) = 1";
        if($this->tableIdx == 0){
            if(isset($params["ctEventDate_0"]))
                $where .= " AND ctEventDate >= " . $params["ctEventDate_0"];
            if(isset($params["ctEventDate_1"]))
                $where .= " AND ctEventDate <= " . $params["ctEventDate_1"];
            if(isset($params["prCode"]))
                $where .= " AND prCode LIKE '" . $params["prCode"]."%'";

            for($i=0;$i<count($params["secTxt"]);$i++){
                if(isset($params["secKey"]) && isset($params["secTxt"][$i])){
                    if($params["secKey"] == "all"){
                            $where .= " AND (ctName LIKE '%".$params["secTxt"][$i]."%' or ctContent LIKE '%".$params["secTxt"][$i]."%' or ctSpeaker LIKE '%".$params["secTxt"][$i]."%')";
                    }else {
                            $where .= " AND ".$params["secKey"]." LIKE '%".$params["secTxt"][$i]."%'";
                    }
                }
            }

        } else if($this->tableIdx == 1){
            if(isset($params["secKey"]) && isset($params["secTxt"])){
                for($i=0;$i<count($params["secTxt"]);$i++){
                    $where .= " AND prName LIKE '%".$params["secTxt"][$i]."%'";
                }
            }
        }
        if($where) $this->db->where($where);    unset($where);
        $where["(1)"] = "1";
        return $where;
    }

    public function _select_cnt($params=array(), $tableIdx) {
        $this->tableIdx = $tableIdx;
        $where = $this->_query($params);
        $this->db->where($where);
        $this->db->from($this->table[$tableIdx]);
        return $this->db->count_all_results();
    }

    public function _select_list($params=array(), $tableIdx) {
        $this->tableIdx = $tableIdx;
        if($tableIdx == 0){
            $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
            $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        }
        $where = $this->_query($params);
        // 정렬관련
        if(isset($params["oType"]) && isset($params["oKey"])){
            $this->db->order_by($params["oKey"], $params["oType"]);
        }
        return $this->db->get_where($this->table[$tableIdx], $where, $limit, $offset)->result_array();
    }
    public function _select_and_list($params=array(), $tableIdx) {
        $this->tableIdx = $tableIdx;
        if($tableIdx == 0){
            $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
            $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        }
        $where = $this->_and_query($params);
        // 정렬관련
        if(isset($params["oType"]) && isset($params["oKey"])){
            $this->db->order_by($params["oKey"], $params["oType"]);
        }
        return $this->db->get_where($this->table[$tableIdx], $where, $limit, $offset)->result_array();
    }
    public function _select_row($where) {
        return $this->db->where($where)->get($this->table[0], 1)->row_array();
    }

    public function _insert($data) {
        return $this->db->insert($this->table[0], $data);
    }

    public function _delete($where) {
        $this->db->where($where);
        $this->db->delete($this->table[0]);
    }

    public function _update($data, $where) {
        return $this->db->update($this->table[0], $data, $where);
    }

    public function _set($params) {
        $this->db->set($params, NULL, FALSE);
        return $this->db->update($this->table[0]);
    }

    public function _replace($data) {
        return $this->db->replace($this->table[0], $data);
    }
}
?>