$(function(){

    $('select[name="bo_select"]').bind('change',function(){
        var bodID = $(this).val();
        $(".comment_bodID").val(bodID);
        $(".bcoList").submit();
    });

})