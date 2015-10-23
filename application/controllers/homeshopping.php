<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class homeshopping extends common {

    public function index() {
        $url =  "./assets/json/config/homeshopping.json";
        $contents = file_get_contents($url);
        $homeshopping = json_decode($contents);
        $data['ctType'] = $homeshopping->ctType;
        $data['ctSource'] = $homeshopping->ctSource;
        $this->_print($data);
    }

}