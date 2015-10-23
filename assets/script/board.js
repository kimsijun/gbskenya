
/**
 * 프로그램 리스트
 * 프로그램 리스트 프로그램 리스트 프로그램 리스트 프로그램 리스트
 * @param array $exceptParams array("edDcType","edIsOver");
 * @return json_encode(array)
 */
function modifyComment(bcoNO){
    var params = "mode=selectRow&bcoNO="+bcoNO;
    $.post('/adm/comment/ajax_process', params, function(response){
        $('.mode').val("modify");
        $('input[name="bcoNO"]').val(bcoNO);
        $('input[name="mbNick"]').val(response.mbNick);
        $('textarea[name="bcoContent"]').val(response.bcoContent);
    }, "json");
}

/**
 * 프로그램 리스트
 * 프로그램 리스트 프로그램 리스트 프로그램 리스트 프로그램 리스트
 * @param array $exceptParams array("edDcType","edIsOver");
 * @return json_encode(array)
 */
function replyComment(bcoNO){
    $('.mode').val("reply");
    $('input[name="bcoNO"]').val(bcoNO);
}


/**
 * 프로그램 리스트
 * 프로그램 리스트 프로그램 리스트 프로그램 리스트 프로그램 리스트
 * @param array $exceptParams array("edDcType","edIsOver");
 * @return json_encode(array)
 */
$(function(){
    $('.boDelete').click(function(){
        if(confirm('Are you sure?')){
            $('form[name="boDelete"]').submit();
        }
    });
});