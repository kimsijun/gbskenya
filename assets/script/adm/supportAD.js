$(function(){
    $('.btnMod').click(function(){
        $('input[name="sANO"]').val($(this).attr('sANO'));
        $('.frmMod').submit();
    });

})
