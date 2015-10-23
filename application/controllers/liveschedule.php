<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class liveschedule extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model('liveschedule_model');

    }

    public function index() {
        $params = $this->_get_sec();

        $secParam['chNO'] = ($params['chNO']) ? $params['chNO'] : 1;
        $secParam['lsIsView'] = 'YES';
        $secParam['lsDate'] = ($params['lsDate']) ? $params['lsDate'] : date('Y-m-d');
        $secParam['lsPrevDate'] = date("Y-m-d",strtotime ("-1 days",  strtotime($secParam['lsDate'])));
        $secParam['lsNextDate'] = date("Y-m-d",strtotime ("+1 days",  strtotime($secParam['lsDate'])));

        $secParam['oKey'] = "lsSTime";
        $secParam['oType'] = "ASC";
        $data = $secParam;
        $data['channelNames'] = $this->liveschedule_model->_select_channel_list(array('lsDate'=>date('Y-m-d')));
        $data['liveSchedule'] = $this->liveschedule_model->_select_live_list($secParam);
        for($i=0; $i<count($data['liveSchedule']); $i++){
            $arrSTime = explode(':',$data['liveSchedule'][$i]['lsSTime']);
            $data['liveSchedule'][$i]['lsSTime'] = $arrSTime[0].":". $arrSTime[1];
        }


        $day = ($params['lsDate'])? $params['lsDate'] : date('Y-m-d');
        $weekIdx = date('w', strtotime($day));
        $arrDw = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");

        $mondayDate = date("Y-m-d",strtotime ("-".$weekIdx." days",  strtotime($day)));
        for($i=0; $i<7; $i++) {
            $tempDate = strtotime("+".$i." days",  strtotime($mondayDate));
            if($i == $weekIdx)
                $arrWeek[$i] = array("lsDate" => date("Y-m-d",$tempDate), "strDate" => date("Y.m.d",$tempDate)." ".$arrDw[$i]);
            else
                $arrWeek[$i] = array("lsDate" => date("Y-m-d",$tempDate), "strDate" => date("m.d",$tempDate)." ".$arrDw[$i]);
        }

        $data['arrWeek'] = $arrWeek;
        $data['weekIdx'] = $weekIdx;
        $this->_print($data);
    }

}