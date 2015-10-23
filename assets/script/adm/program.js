$(function(){
	$('.media_cate').bind("change", function(){
        var ctCate = $(this).val();
        var param       = 'mode=getApiContent&ctCate='+ ctCate + '&ctType=youtube';

        $.post('/media/ajax_process', param, function(response){
            $('.ctSource').html(response);
        }, "json");
    });

    $('select[name=prCode]').change(function(){
        var param       = 'mode=getContent&prCode=' + $('select[name=prCode]').val();
        $.post('/adm/livecontent/ajax_process', param, function(response){
            $('.ctNO').html(response);
        }, "json");
    });

    $(document).on('change', '.secPrCode', function(){
        var param       = 'mode=getPrList&prCode=' + $(this).val();
        $.post('/adm/program/ajax_process', param, function(response){
            $('.prArea').html(response);
        }, "json");
    });


    $(".prIsSimpleDown").mousedown(function(){
/*
        var i = 0, idx = 0;
        var arrPrCode = new Array();
        var prDepth = $(this).val().length;
        var prIsSimpleDown = ($(this).is(":checked") == true)? "NO" : "YES";

        if(prIsSimpleDown == "YES"){
            for(i=prDepth; i>0; i-=3)
                arrPrCode[idx++] = $(this).val().substring(0, i);

            for(i=1; i<arrPrCode.length; i++){
                if(!$("." + arrPrCode[i]).attr("checked"))
                    $("." + arrPrCode[i]).click();
            }
            var strPrCode = arrPrCode.join();
        }else{
            strPrCode = $(this).val();
        }
*/

        var strPrCode = $(this).val();
        var prIsSimpleDown = ($(this).is(":checked") == true)? "NO" : "YES";
        var param       = 'mode=modifySimpleDown&prCode='+ strPrCode + '&prIsSimpleDown=' + prIsSimpleDown;
        $.post('/adm/program/ajax_process', param, function(response){
            var explicic = '';
        }, "json");
    });
})

function deleteOnSubmit(){
    var chkDelete = confirm("XML 파일을 사용중이라면, XML 파일까지 삭제 됩니다.\n삭제 하시겠습니까?");
    if(chkDelete)   return true;
    else   return false;
}