function setDate(obj,from,to)
{
    var obj = document.getElementsByName(obj);
    obj[0].value = (from) ? from : "";
    obj[1].value = (from) ? to : "";
}

$(function(){
    $(".uncheck_all").click(function() {
        $(".checker span").removeClass("checked");
        $(".chChildren input:checkbox").attr("checked", false);
    });

    $('.btnMod').click(function(){
        var param = $(this).attr('param');
        $('input[name="'+param+'"]').val($(this).attr(param));
        $('.frmMod').submit();
    });
})