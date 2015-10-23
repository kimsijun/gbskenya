<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class news_category extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("news_model");
        $this->load->model('news_category_model');
    }

    public function index() {
        $params = $this->_get_sec();
        $data["params"] = $params;
        $data["newsCategory"] = $this->common_model->_select_list('news_category_data',array('LENGTH(ncCode)'=>'3'));
        $data["headLine"] = $this->news_model->_select_row(array('A.nwIsHeadline'=>'YES'));
        $data["headLine"]["nwContent"] = strip_tags($data["headLine"]["nwContent"]);
        $data["headLine"]["nwContent"] = $this->common_class->cut_str_han($data["headLine"]["nwContent"], 390,"...");
        $data["specialNews"] = $this->news_model->_select_list(array('A.nwIsSpecial'=>'YES', 'limit'=>'2'));
        for($i=0; $i<count($data["specialNews"]); $i++){
            $data["specialNews"][$i]["nwContent"] = strip_tags($data["specialNews"][$i]["nwContent"]);
            $data["specialNews"][$i]["nwContent"] = $this->common_class->cut_str_han($data["specialNews"][$i]["nwContent"], 190,"...");
        }
        for($i=0; $i<count($data["newsCategory"]); $i++){
            $paramNews['limit']='3';
            $paramNews['A.ncCode']=$data["newsCategory"][$i]['ncCode'];
            $paramNews['oKey'] = 'nwModDate';
            $paramNews['oType'] = 'DESC';
            $data["newsCategory"][$i]['newsList'] = $this-> news_model->_select_list($paramNews);
        }
        $data["newsCategory"][4]['subCate'] = $this->common_model->_select_list('news_category_data',array('ncPreCode'=>'005','LENGTH(ncCode)'=>'6'));
        for($j=0; $j<count($data["newsCategory"][4]['subCate']); $j++){
            $paramSub['limit'] = '3';
            $paramSub['oKey'] = 'nwModDate';
            $paramSub['oType'] = 'DESC';
            $paramSub['A.ncCode'] = $data["newsCategory"][4]['subCate'][$j]['ncCode'];
            $data["newsCategory"][4]['subCate'][$j]['newsList'] = $this-> news_model->_select_list($paramSub);
        }

        $data["newsPr"] = $this->common_model->_select_list('program_data',array('prPreCode'=>'001','LENGTH(prCode)'=>'6'));
		
        $paramPopular["oKey"] = 'nwViewCount';
        $paramPopular["oType"] = 'DESC';
        $paramPopular["limit"] = '10';
        $data["mostPopular"] = $this-> news_model->_select_list($paramPopular);
        $this->db->like("ncCode", "005", "after");
        $data["international"] = $this->news_model->_select_list(array('SUBSTRING(A.ncCode, 1, 3)'=>'005' ,'oKey'=>'nwRegDate','oType'=>'DESC','limit'=>'8'));
        $this->_print($data);
    }

    public function view() {
        $params = $this->_get_sec();
        unset($params['news_category']);
        $data = $this->common_model->_select_row('news_category_data',array('ncCode'=>$params['ncCode']));
        $params['oKey'] = "nwRegDate";
        $params['oType'] = "DESC";
        $data["params"] = $params;
        $data["newsCategory"] = $this->common_model->_select_list('news_category_data',array('LENGTH(ncCode)'=>'3'));

        $subCate = $this->news_category_model->_select_sub_list(array('ncCode'=>$params['ncCode']));
        if( strlen($data['ncCode']) > 3 ){
            $data['ncCodePrev'] = substr($data['ncCode'],0,3);
            $data['ncPreCodeData'] = $this->common_model->_select_row('news_category_data',array('ncCode'=>$data['ncPreCode']));
        }else{
            $data['ncCodePrev'] = $data['ncCode'];
        }

        $paramSpecial = $params;
        $paramSpecial['A.ncCode'] = $paramSpecial['ncCode'];unset($paramSpecial['ncCode']);
        $paramSpecial['nwIsSpecial'] = 'YES';
        $paramNewsList = $params;
        $paramNewsList['A.ncCode'] = $paramNewsList['ncCode'];unset($paramNewsList['ncCode']);
        $paramNewsList['nwIsSpecial'] = 'NO';
        if(count($subCate)==1){
            $paramSpecial['limit'] = '10';
            $data["specialNews"] = $this-> news_model->_select_list($paramSpecial);
            $paramNewsList['limit'] = '30';
            $data["newsList"] = $this-> news_model->_select_list($paramNewsList);
            for($i=0; $i<count($data["specialNews"]); $i++){
                $data["specialNews"][$i]['nwContent'] = strip_tags($data["specialNews"][$i]['nwContent']);
                $data["specialNews"][$i]['nwContent'] = $this->common_class->cut_str_han($data['specialNews'][$i]['nwContent'],240,"...");
            }
            for($i=0; $i<count($data["newsList"]); $i++){
                $data["newsList"][$i]['nwContent'] = strip_tags($data["newsList"][$i]['nwContent']);
                $data["newsList"][$i]['nwContent'] = $this->common_class->cut_str_han($data['newsList'][$i]['nwContent'],200,"...");
            }

        }else{
            for($i=0; $i<count($subCate); $i++){
                $paramSpecial['limit'] = '3';
                $data["specialNews"] = $this-> news_model->_select_sub_list($paramSpecial);
                $paramNewsList['limit'] = '6';
                $data["newsList"] = $this-> news_model->_select_sub_list($paramNewsList);
                for($j=0; $j<count($data["specialNews"]); $j++){
                    if(strpos($data["specialNews"][$j]['nwContent'],'<img')!=false){
                        $specialNewsArr = explode('<',$data["specialNews"][$j]['nwContent']);
                        $data["specialNews"][$j]["photo"] = '<'.$specialNewsArr[2];
                    }
                    $data["specialNews"][$j]['nwContent'] = strip_tags($data["specialNews"][$j]['nwContent']);
                    $data["specialNews"][$j]['nwContent'] = $this->common_class->cut_str_han($data['specialNews'][$j]['nwContent'],240,"...");
                }
                for($j=0; $j<count($data["newsList"]); $j++){
                    $data["newsList"][$j]['nwContent'] = strip_tags($data["newsList"][$j]['nwContent']);
                    $data["newsList"][$j]['nwContent'] = $this->common_class->cut_str_han($data['newsList'][$j]['nwContent'],200,"...");
                }
            }
        }
        $paramPopular["oKey"] = 'nwViewCount';
        $paramPopular["oType"] = 'DESC';
        $paramPopular["limit"] = '10';
        $data["mostPopular"] = $this-> news_model->_select_list($paramPopular);

        $paramVideoNews['A.ncCode'] = $params['ncCode'];
        $paramVideoNews['nwType'] = 'NONE';
        $paramVideoNews['limit'] = '3';
        $data["videoNews"] = $this-> news_model->_select_list($paramVideoNews);


		$data['title'] = $data['metaDescription'] = "GBS News ". $data['ncName'];
        $this->_print($data);
    }

}