<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| @ TITLE   자바스크립트 관련 라이브러리
| @ AUTHOR  JoonCh
| @ SINCE   14. 5. 2.
| @ PURPOSE 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러
| 프로그램 페이지 컨트롤러 프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러프로그램 페이지 컨트롤러
| -------------------------------------------------------------------
*/

class javascript_lib {

    /**
     * Javascript confirm
     * @param	$params array
     * @params	string : "msg", string : "nextUrl", string : "prevUrl"
     */
    function confirm($params=array())
    {
        echo "<script type='text/javascript'>
                if(confirm('".$params['msg']."')){
                    location.href='".$params['nextUrl']."';
                } else {
                    location.href='".$params['prevUrl']."';
                }
             </script>";
    }


    /**
     * Javascript location.href
     * @param	$params array
     * @params	string : "msg", string : "nextUrl"
     */
    function msgLocation($params=array())
    {
        echo "<script type='text/javascript'>
                alert('"+$params['msg']+"');
                location.href='"+$params['nextUrl']+"';
             </script>";
    }



    /**
     * Javascript history.back
     * @param	$params array
     * @params	string : "msg"
     */
    function msgBack($params=array())
    {
        echo "<script type='text/javascript'>
                alert('"+$params['msg']+"');
                history.back();
             </script>";
    }

}
