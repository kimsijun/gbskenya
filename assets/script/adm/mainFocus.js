$(function(){
    $('.btnMod').click(function(){
        $('input[name="mFNO"]').val($(this).attr('mFNO'));
        $('.frmMod').submit();
    });

    $('select[name="mFType"]').change(function(){
        var mFType = $(this).val();
        if(mFType == 'BANNER'){
            $('.PROGRAM_cate').addClass('hide');
            $('.CONTENT_cate').addClass('hide');
            $('.BANNER_cate').removeClass('hide');
        }else if(mFType == 'CONTENT'){
            $('.PROGRAM_cate').removeClass('hide');
            $('.CONTENT_cate').removeClass('hide');
            $('.BANNER_cate').addClass('hide');
        }else if(mFType == 'PROGRAM'){
            $('.PROGRAM_cate').removeClass('hide');
            $('.CONTENT_cate').addClass('hide');
            $('.BANNER_cate').addClass('hide');
        }
    });

    $('.PROGRAM_cate').on('change',function(){
        $('input[name="mFName"]').val($('.PROGRAM_cate option:selected').text());
    });

    $('select[name=prCode]').on('change',function(){
        if($('select[name="prCode"]').val() == "") return;
        var prCode = $(this).val();
        var param = 'mode=getProgram&prCode='+prCode;
        var html = "";
        $.post('/adm/content/ajax_process', param, function(response){
            if(response['data']){
                $('.prCode').html(response.html);
            }  else {
                var param2 = 'mode=getContent&prCode=' + prCode;
                $.post('/adm/livecontent/ajax_process', param2, function(response){
                    $('.ctNO').html(response.html);
                },"json");
            }
        }, "json");
    });

    $('.ctNO').on('change',function(){
        $('input[name="mFName"]').val( $('select[name=ctNO] option:selected').text());
    });

})
