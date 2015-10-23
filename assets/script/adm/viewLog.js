$(function(){
    $('select[name="ctSectionChange"]').bind('change',function(){
        location.href='/adm/viewLog/ctViewLog/vCSection/'+$(this).val();
    });

    $('select[name="prSectionChange"]').bind('change',function(){
        location.href='/adm/viewLog/prViewLog/vCSection/'+$(this).val();
    });
})