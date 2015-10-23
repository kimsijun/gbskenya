$(function(){
	$('.media_cate').on("change", function(){
        var ctCate = $(this).val();
        var param       = 'mode=getApiContent&ctCate='+ ctCate + '&ctType=youtube';

        $.post('/media/ajax_process', param, function(response){
            $('.ctSource').html(response);
        }, "json");
    });

    $('select[name=prCode]').on('change',function(){
        if($('select[name="prCode"]').val() == "") return;
        var prCode = $(this).val();
        var param = 'mode=getProgram&prCode='+prCode;
        var html = "";
        $.post('/adm/content/ajax_process', param, function(response){
            if(response['data']){
                $('.prCode').html(response.html);
            } else {
                var param2 = 'mode=getContent&prCode=' + prCode;
                $.post('/adm/livecontent/ajax_process', param2, function(response){
                    $('.ctNO').html(response.html);
                },"json");
            }
        }, "json");
    });

    $('.ctNO').on('change',function(){
        $('input[name="lcName"]').val( $('select[name=ctNO] option:selected').text());
    });

    $(".secPrCode").on("change", function(){
        var param       = 'mode=getPrList&prCode=' + $(this).val();
        $.post('/adm/program/ajax_process', param, function(response){
            $('.prArea').html(response);
        }, "json");
    });
})

function deleteOnSubmit(){
    var chkDelete = confirm("XML 파일을 사용중이라면, XML 파일까지 삭제 됩니다.\n삭제 하시겠습니까?");
    if(chkDelete)   return true;
    else   return false;
}