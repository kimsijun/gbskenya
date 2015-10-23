$(function(){

    $('select[name="tagMode"]').bind('change',function(){
        var tagMode = $(this).val();
        $(".tagOrderKey").val(tagMode);
        $(".tagList").submit();
    });

})