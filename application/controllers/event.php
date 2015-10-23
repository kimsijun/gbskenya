<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/*
| -------------------------------------------------------------------
| @ TITLE   이벤트 페이지 컨트롤러
| @ AUTHOR  JPLEE
| @ SINCE   14. 9. 27.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class event extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model("content_model");
        $this->load->model('global_model');
		
    }

   public function index() {
        $this->_print();   
   }
 
   public function choralFestival2014() {
        $this->_print();
   }
   
   public function choralFestivalApplication() {
        $this->_print();
   }   
   
	public function downloadDocGreeting()
	{
		$file_name="GreetingByGBS.docx";
    	$file = './uploads/WelcomeGreetingByGBS.docx';    
		if (file_exists($file)) {
    	    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
    	}	
	}
       
}