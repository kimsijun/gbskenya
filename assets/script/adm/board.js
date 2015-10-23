function modifyComment(bcoNO){
    var params = "mode=selectRow&bcoNO="+bcoNO;
    $.post('/adm/comment/ajax_process', params, function(response){
        $('.mode').val("modify");
        $('input[name="bcoNO"]').val(bcoNO);
        $('input[name="mbNick"]').val(response.mbNick);
        $('textarea[name="bcoContent"]').val(response.bcoContent);
    }, "json");
}

function replyComment(bcoNO){
    $('.mode').val("reply");
    $('input[name="bcoNO"]').val(bcoNO);
}