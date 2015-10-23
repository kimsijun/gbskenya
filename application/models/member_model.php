<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */


class Member_model extends CI_Model {
    protected $table = array("member_data");

    /*  QUERY 의 조건절 세팅    */
    private function _query($params) {

        if(isset($params["mbIsWithdraw"]))    $where["mbIsWithdraw"] = $params["mbIsWithdraw"];
        if(isset($params['mbHumanStatus'])){
            $humanDate = date("Ymd", strtotime(date('Ymd')." -".$params['mbHumanStatus']." day"));
            $where["mbLastLoginDate <="] = $humanDate;
        }
        if(isset($params["mbBirth_0"]))  $where["mbBirth >="] = $params["mbBirth_0"];
        if(isset($params["mbBirth_1"]))  $where["mbBirth <="] = $params["mbBirth_1"];
        if(isset($params["mbSex"])) $where["mbSex"] = $params["mbSex"];
        if(isset($params["mbAge"])){
            $year = date("Y")- ($params["mbAge"]-1);
            if($params["mbAge"] != '70')    $where["mbBirth <="] = $year.'-00-00';
            $where["mbBirth >="] = ($year-10).'-00-00';
        }
        if(isset($params["mbVisitCount_0"]))  $where["mbVisitCount >="] = $params["mbVisitCount_0"];
        if(isset($params["mbVisitCount_1"]))  $where["mbVisitCount <="] = $params["mbVisitCount_1"];
        if(isset($params["mbLastLoginDate_0"]))  $where["mbLastLoginDate >="] = $params["mbLastLoginDate_0"];
        if(isset($params["mbLastLoginDate_1"]))  $where["mbLastLoginDate <="] = $params["mbLastLoginDate_1"];
        if(isset($params["mbRegDate_0"]))  $where["mbRegDate >="] = $params["mbRegDate_0"];
        if(isset($params["mbRegDate_1"]))  $where["mbRegDate <="] = $params["mbRegDate_1"];

        if(isset($params["secKey"]) && isset($params["secTxt"])) {
            if($params["secKey"] == "all"){
                $this->db->or_like("mbFirstName", $params["secTxt"]);
                $this->db->or_like("mbLastName", $params["secTxt"]);
                $this->db->or_like("mbID", $params["secTxt"]);
                $this->db->or_like("mbNick", $params["secTxt"]);
                $this->db->or_like("mbEmail", $params["secTxt"]);
                $this->db->or_like("mbCellPhone", $params["secTxt"]);
            }else {
                $this->db->like($params["secKey"], $params["secTxt"]);
            }
        }
        $where["(1)"] = "1";
        return $where;
    }


    /*   QUERY 결과의 Rows 개수 반환     */
    public function _select_cnt($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $where = $this->_query($params);
        // 정렬관련
        if(isset($params["oType"]) && isset($params["oKey"])){
            $this->db->order_by($params["oKey"], $params["oType"]);
        }
        return $this->db->get_where($this->table[0], $where, $limit, $offset)->num_rows();
    }


    /*  QUERY 결과를 배열로 반환    */
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


    /*  QUERY 결과의 첫번째 하나의 ROW 반환    */
    public function _select_row($where) {
        return $this->db->where($where)->get($this->table[0], 1)->row_array();
    }


    /*  데이터 삽입    */
    public function _insert($data) {
        return $this->db->insert($this->table[0], $data);
    }


    /*  데이터 삭제    */
    public function _delete($where) {
        $this->db->where($where);
        $this->db->delete($this->table[0]);
    }


    /*   데이터 수정   */
    public function _update($data, $where) {
        return $this->db->update($this->table[0], $data, $where);
    }


    /*   데이터 수정 (조건 없이 일괄처리)   */
    public function _set($params) {
        $this->db->set($params, NULL, FALSE);
        return $this->db->update($this->table[0]);
    }


    /*  데이터 치환    */
    public function _replace($data) {
        return $this->db->replace($this->table[0], $data);
    }
}
?>
