<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class mypage_favor_model extends CI_Model {
    protected $table = array("mypage_favor_log");

    private function _query($params) {

        if(isset($params["mbID"])) {
            $where["mbID"] = $params["mbID"];
        }

        if(isset($params["mpFType"])) {
            $where["mpFType"] = $params["mpFType"];
        }

        if(isset($params["mpFKey"])) {
            $where["mpFKey"] = $params["mpFKey"];
        }

        if(isset($params["secKey"]) && isset($params["secTxt"])) {
            $this->db->like($params["secKey"], $params["secTxt"]);
        }
        $where["(1)"] = "1";
        return $where;
    }

    public function _select_cnt($params=array()) {
        $where = $this->_query($params);
        $this->db->where($where);
        $this->db->from($this->table[0]);
        return $this->db->count_all_results();
    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $where = $this->_query($params);
        // 정렬관련
        if(isset($params["oType"]) && isset($params["oKey"])){
            $this->db->order_by($params["oKey"], $params["oType"]);
        }
        return $this->db->get_where($this->table[0], $where, $limit, $offset)->result_array();
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

    public function _favor_count($params){
        $this->db->select("count(mpFKey) as cntNO , mpFType, mpFKey");
        $this->db->from($this->table[0]);
        $where = $this->_query($params);
        $this->db->where($where);
        $this->db->group_by("mpFKey");
        $this->db->order_by("cntNO", "desc");
        return $this->db->get()->result_array();

    }

}
?>
