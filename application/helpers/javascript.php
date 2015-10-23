<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Javascript confirm
 * @param	$params array
 * @params	string : "msg", string : "nextUrl"
 */
function confirm($params=array())
{
    echo "<script>if(confirm('".$params['msg']."')){location.href='".$params['nextUrl']."';}else{location.href='".$_SERVER['HTTP_REFERER']."';}</script>";
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

