$(function(){

    $(document).on('change', 'select[name="ctType"]', function(){
        var ctType = $(this).val();
        $('.media_cate').css('display','none');
        $('.'+ ctType + '_cate').parent().css('display','block');
        $('.'+ ctType + '_cate').css('display','block');
    });

    $('.media_cate').change(function(){
        var ctCate = $(this).val();
        var param       = 'mode=getApiContent&ctCate='+ ctCate + '&ctType=' + $('select[name="ctType"]').val();

        $.post('/media/ajax_process', param, function(response){
            $('.ctSource').html(response);
        }, "json");
    });


    $(document).on('change', '.soundCloudPlaylist', function(){
        var param   = 'mode=getSoundcloud&playlistID='+ $(this).val();
        $.post('/adm/content/ajax_process', param, function(response){
            $('.ctMP3Container').html(response);
        }, "json");
    });

    $(document).on('change', 'select[name="ctSource"]', function(){
        $('input[name="ctSource"]').val($('select[name="ctSource"]').val());
        var param       = 'mode=getApiInfo&ctSource='+ $(this).val() + '&ctType=' + $('select[name="ctType"]').val();
        $('input[name="isChangeContent"]').val('Y');

        $.post('/media/ajax_process', param, function(response){
            $('input[name="ctName"]').val(response.video.apiTitle);
            $('textarea[name="ctContent"]').val(response.video.apiDescription);
            $('input[name="ctDuration"]').val(response.video.apiDuration);
            $('input[name="ctViewCount"]').val(response.video.apiViewCount);
            $('input[name="ctLikeCount"]').val(response.video.apiLikeCount);
            $('input[name="ctThumbS"]').val(response.video.apiThumb.s);
            $('input[name="ctThumbL"]').val(response.video.apiThumb.l);
            $('input[name="ctRegDate"]').val(response.video.apiRegDate);
        }, "json");
    });

    $(document).on('change', 'select[name="prCode"]', function(){
        if($(this).val() == "") return false;
        var prCode = $(this).val();
        var param = 'mode=getProgram&prCode='+prCode;
        var html = "";
        $.post('/adm/content/ajax_process', param, function(response){
            console.log(response);
            html += '<option value="">선택</option>';
            for(var i=0; i<response['data'].length;i++){
                html += '<option value="' + response['data'][i]['prCode'] + '">' + response['data'][i]['prName'] + '</option>';
            }
            html += '<option value="'+ response['secParams']['prPreCode'] +'back">뒤로</option>';
            $('select[name="prCode"]').html(html);
        }, "json");
    });

    $(document).on('change', 'select[id="prCode_ctRelative"]', function(){
        var param = 'prCode='+$(this).val();
        var html = "";
        $.post('/adm/content/ajax_process', param, function(response){
            html += '<option value="">선택</option>';
            for(var i=0; i<response['data'].length;i++){
                html += '<option value="' + response['data'][i]['prCode'] + '">' + response['data'][i]['prName'] + '</option>';
            }
            $('select[id="prCode_ctRelative"]').html(html);
        }, "json");

        var prCode_ctRelative = $(this).val();
        $('.ctList').parent().parent().css('display','none');
        $('.ctList').parent().css('display','none');
        $('.ctList').css('display','none');
        $('.'+ prCode_ctRelative + '_ctRelative').parent().parent().css('display','block');
        $('.'+ prCode_ctRelative + '_ctRelative').parent().css('display','block');
        $('.'+ prCode_ctRelative + '_ctRelative').css('display','block');
    });

})

function video_cate_update(type){
    $("input[name=ctType]").val(type);
	$(".videoCateUpdate").submit();
}

function video_content_update(type){
	$("input[name=ctType]").val(type);
	$(".videoContentUpdate").submit();
}

function add_ctRelative(param){
    var num_ctRelative = $("#ctRelative_list tr ").length;
    var ctRelative = '.'+param+' option:selected';

    $("#ctRelative_list").append(
        '<tr>'+
            '<td id = "'+num_ctRelative+'">'+
            '<div class="col-lg-10"><input type="hidden" name = "ctRelative_ctNO[]" value="'+$(ctRelative).val()+'"/>'+
            '<input class="uniform-input text form-control" style="float:left;" size="80" type="text" id="ctRelative['+num_ctRelative+']" value="'+$(ctRelative).text()+'"></div>'+
            '<div class="col-lg-2"><button class="btn btn-mini marginL10"id="del_tag"onclick="del_ctRelative('+num_ctRelative+');"> 삭제 </button></div>'+
            '</td>'+
            '</tr>'
    );
}

function del_ctRelative(param){
    var idx = "#"+param;
    $(idx).parent().remove();
}
