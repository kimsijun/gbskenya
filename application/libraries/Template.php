<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/Template_.class'.EXT);
require_once(APPPATH.'libraries/Template_.compiler'.EXT);

/**
 * @author BahamuT
 * @version 1.0.0
 * @license copyRight By GD
 * @since 10. 7. 21 오전 1:09 ~
 * 코드이그나이터에서 템플릿 언더바를 사용할수 있게끔 해줍니다.
 */
class Template extends Template_
{
	var $compile_check =true;
	var $compile_ext   ="php";
	var $skin          ="";
	var $notice        =false;
	var $path_digest   =false;

	var $prefilter     ='';
	var $postfilter    ='';
	var $permission    =0777;
	var $safe_mode     =false;
	var $auto_constant =false;

	var $caching       =true;
    var $cache_dir     = '_cache';
	var $cache_expire  =3600;


	function Template(){
		$this->template_dir = APPPATH."views";
		$this->compile_dir	= FCPATH."_compile";
		$this->cache_dir	= FCPATH."_cache";
		$this->prefilter	= "adjustPath";
	}
}
?>
