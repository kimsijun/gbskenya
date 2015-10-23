<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * loginCheck
 *
 * @param	none
 */
function loginCheck($value, $type = 'log')
{
    if(!$this->session->userdata('mbID')){
        echo "<script>if(confirm('로그인하시겠습니까?')){location.href='/member/login';}else{location.href='".$_SERVER['HTTP_REFERER']."';}</script>";
    }
}

//------------------------------------------------------------------------------

/**
 * firephp
 *
 * @type	string : log, warn, error
 */
function firephp_last_query($type = 'log')
{
	if( $type != 'log' AND $type != 'warn' AND $type != 'error')
	{
		$type = 'warn';
	}
	$CI =& get_instance();
	$CI->firephp->{$type}($CI->db->last_query());
}

//------------------------------------------------------------------------------

