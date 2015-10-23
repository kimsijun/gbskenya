<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class uefacup extends common {

    public function index() {
        $url =  "./assets/json/config/uefa.json";
        $contents = file_get_contents($url);
        $uefa = json_decode($contents);
        $data['ctType'] = $uefa->ctType;
        $data['ctSource'] = $uefa->ctSource;

        $this->_print($data);
    }

}