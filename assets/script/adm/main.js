$(function(){

    $('select[name="change_comment"]').bind('change',function(){
        var param = 'mode='+ $(this).val();
        if($(this).val() == "Content")  $(".cm_mode").children("a").attr("href","/adm/content_comment/index");
        else if($(this).val() == "Program")  $(".cm_mode").children("a").attr("href","/adm/program_comment/index");
        else if($(this).val() == "Board")  $(".cm_mode").children("a").attr("href","/adm/comment/index/bodID/qna");

        $.post('/adm/main/ajax_process', param, function(response){
            $('.commentList').html(response);
        }, "json");
    });

    $('select[name="changeContent"]').bind('change',function(){
        var param = 'mode=contentTop10&date='+ $(this).val();
        $.post('/adm/main/ajax_process', param, function(response){
            $('.contentTop10').html(response);
        }, "json");
    });

    $('select[name="changeProgram"]').bind('change',function(){
        var param = 'mode=programTop10&date='+ $(this).val();
        $.post('/adm/main/ajax_process', param, function(response){
            $('.programTop10').html(response);
        }, "json");
    });
})