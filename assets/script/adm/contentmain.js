$(function(){

    $('.contentMainDel').bind("click", function(){
        $(this).parent('tr').remove();
    });

    $('.contentMainSlcProgram').change(function(){
        if($('.contentMainSlcProgram').val() == "") return;
        var param       = 'mode=getContent&prCode=' + $('.contentMainSlcProgram').val();

        $.post('/adm/contentmain/ajax_process', param, function(response){
            $('.contents_container').html(response);
        }, "json");
    });

    $('.contentMainContentSubmit').click(function(){
        var ctNO = $('.slc_content').val();
        var prName = $('.ctNO'+ctNO).attr("prName");
        var ctName = $('.ctNO'+ctNO).attr("ctName");


        var html = "<tr>" +
            "<td><input type='hidden' name='ctNO[]' value='"+ ctNO+"'>"+ prName +"</td>" +
            "<td>"+ctName+"</td>" +
            "<td style='cursor:pointer;text-align:center;' class='contentMainDel'>x</td>"
        "</tr>";


        $('.admContentMainList').append(html);
    });


})