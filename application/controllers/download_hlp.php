<?php
/**
 * Download helper
 *
 * Created on 2011. 11. 18.
 * @author 불의회상 <hoksi2k@hanmail.net>
 * @package helper
 * @subpackage controllers
 * @version 1.0
 * http://sample.cikorea.net/sample_view/helper/download 
 */
class Download_hlp extends CI_Controller {
    function __construct()
    {
        parent::__construct();
         
        // Cookie Helper Load
        $this->load->helper('download');
    }
     
    function index() {
        $data = '';
        $this->load->view('download_hlp_sample', $data);
    }
 
    function download()
    {
        $data = 'Here is some text!';
        $name = 'mytext.txt';
 
        force_download($name, $data);
    }
     
    function download_file()
    {
        $data = file_get_contents("./images/mypic.jpg"); // Read the file's contents
        $name = 'myphoto.jpg';
 
        force_download($name, $data);
    }
    
   function downloadDocChoral(){
   		// 이 메서드를 사용하지 않음. event 콘트롤러에서 메서드 정의하여 헤더를 통한 데이터통신
        $data = file_get_contents("/uploads/WelcomeGreetingByGBS.docx"); // Read the file's contents
        $name = 'WelcomeGreetingByGBS.docx';
         force_download($name, $data);
   }    
    
}