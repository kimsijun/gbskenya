<?php
/* -----------------------------------------------------------------------------------------
   IdiotMinds - http://idiotminds.com
   -----------------------------------------------------------------------------------------
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH ."controllers/common".EXT);

class test extends common {

    public function __construct(){
        parent::__construct();
    }
    public function index()
    {
	    $url = "https://graph.facebook.com/swllno/picture";
    
	    $ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_URL,            $url); 
	    curl_setopt($ch, CURLOPT_HEADER,         true); 
	    curl_setopt($ch, CURLOPT_NOBODY,         true); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	    curl_setopt($ch, CURLOPT_TIMEOUT,        15); 
	    $headers = curl_exec($ch); 
	    $headers = split("\n", $headers); 
	    
	    echo "<pre>";print_r($headers);exit;
		if (array_key_exists('Location', $headers))
		{
		    echo $headers['Location'];
		}
    }


}
