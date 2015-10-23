<?php
/**
 * @purpose
 * @author  JoonCh
 * @since   13. 6. 7.
 */

class news_model extends CI_Model {
    public function _select_cnt($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT * FROM gn_news_data AS A
        LEFT OUTER JOIN gn_news_category_data AS B
        ON A.ncCode = B.ncCode";

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
        return $result->num_rows();
    }

    public function _select_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT * FROM gn_news_data AS A
        LEFT OUTER JOIN gn_news_category_data AS B
        ON A.ncCode = B.ncCode";

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
    public function _select_sub_list($params=array()) {
        $limit = (isset($params["limit"])) ? $params["limit"] : NULL;
        $offset = (isset($params["offset"])) ? $params["offset"] : NULL;
        $query = "
            SELECT A.*, B.ncPreCode, B.ncName FROM gn_news_data AS A
            LEFT OUTER JOIN gn_news_category_data AS B
            ON A.ncCode = B.ncCode
            WHERE A.ncCode LIKE '".$params['A.ncCode']."%' AND A.nwIsSpecial = '".$params['nwIsSpecial']."'
            order by A.nwRegDate DESC";

        if($limit && !$offset) $query .= " LIMIT ".$limit;
        else if($offset) $query .= " LIMIT ".$offset.", ".$limit;
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->result_array();
    }

    public function _select_row($params=array()) {
        $query = "
            SELECT * FROM gn_news_data AS A
        LEFT OUTER JOIN gn_news_category_data AS B
        ON A.ncCode = B.ncCode";

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
        $result = $this->db->query($query);
        if(!$result) $this->common_lib->DBErrorCatch();
        return $result->row_array();
    }
}
?>
