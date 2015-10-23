<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose 회원 Controller Class
 * @author  JoonCh
 * @since   13. 6. 11.
 */

class customer extends common {

    public function __construct(){
        parent::__construct();
        $this->load->model('member_model');
        $this->load->model('cfg_board_model'); $this->load->model('board_model'); $this->load->model('comment_model');
    }

    public function index() {
        $this->_print();
    }
    public function notice() {
        $this->_print();
    }
}