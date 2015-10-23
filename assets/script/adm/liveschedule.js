$(function(){
    /* 편성표 가져오기 */
    $('.btnImport').click(function(){
        if(!confirm("편성표를 가져오시면 기존 날짜의 편성표는 삭제 됩니다. 계속하시겠습니까?")) return false;
    });

    /* 편성표등록 채널/날짜변경 시 해당 편성표로 페이지이동 */
    $(document).on('change', 'select[name="chNO"]', function(){
        $('input[name="chNO"]').val($(this).val());
    });
    $(document).on('change', 'input[name="lsDate"]', function(){
        location.href='/adm/liveschedule/index/chNO/' + $('input[name="chNO"]').val() + '/lsDate/' + $(this).val();
    });

    /* 프로그램/콘텐츠 선택 */
    $(document).on('change', 'select[name="prCode"]', function(){
        if($('select[name="prCode"]').val() == "") return;
        var prCode = $(this).val();
        var param = 'mode=getProgram&prCode='+prCode;
        var html = "";
        $.post('/adm/content/ajax_process', param, function(response){
            if(response['data']){
                $('.prCode').html(response.html);
            } else {
                var param2 = 'mode=getContent&prCode=' + prCode;
                $.post('/adm/liveschedule/ajax_process', param2, function(response){
                    $('.contents_container').html(response.html);
                },"json");
            }
        }, "json");
    });

    /* 프로그램 노출여부 */
    $(document).on('change', 'input[name="lsIsProgram"]', function(){
        if($(this).val()=="NO"){
            $(".inProgram").addClass('hide');
            $(".outProgram").removeClass('hide');
        }else{
            $(".outProgram").addClass('hide');
            $(".inProgram").removeClass('hide');
        }
    });

    /* 편성표 등록 */
    $('.btn_content_submit').click(function(){
        var param = $('.frmRegContent').serialize() + '&mode=add';
        $.post('/adm/liveschedule/ajax_process', param, function(response){
            if(response.success == "true")      $('.adm_liveschedule_list').html(response.source);
            else                                alert(response.msg);
        }, "json");
    })

    /* 편성표 삭제 */
    $(document).on('click', '.btnDelete', function(){
        var param = 'lsDate='+ $("input[name=lsDate]").val() +'&chNO='+ $("input[name=chNO]").val() +'&lsNO='+ $(this).attr('lsNO') +'&mode=delete';
        $.post('/adm/liveschedule/ajax_process', param, function(response){
            if(response.success == "true")      $('.adm_liveschedule_list').html(response.source);
            else                                alert(response.msg);
        }, "json");
    });

    /* 중간에 끼워넣기 */
    $(document).on('click', '.btnAddBetween', function(){
        $(".lsNO").val($(this).attr('lsNO'));
        $(".frmAddBetween").submit();
    });

    /* 편성표 수정 */
    $(document).on('click', '.btnModify', function(){
        $(".lsNO").val($(this).attr('lsNO'));
        $(".frmModify").submit();
    });

    /* 주요생중계 여부 */
    $(document).on('click', '.lsChangeIsSpecial', function(){
    	var param = 'lsNO='+ $(this).attr('lsNO') +'&mode=setSpecialLive';
        $.post('/adm/liveschedule/ajax_process', param, function(response){
            if(response.success == "true")      alert('변경되었습니다.');
        }, "json");
    });

    /* 노출 여부 */
    $(document).on('click', '.lsChangeIsView', function(){
        var param = 'lsNO='+ $(this).attr('lsNO') +'&mode=setView';
        $.post('/adm/liveschedule/ajax_process', param, function(response){
            if(response.success == "true")      alert('변경되었습니다.');
        }, "json");
    });

})