$(function(){
 
    $(document).on('change', 'select[name="nwType"]', function(){
        var nwType = $(this).val();
        $('.media_cate').parent().css('display','none');
        $('.'+ nwType + '_cate').parent().css('display','block');
        $('.'+ nwType + '_cate').css('display','block');
    });

    $('.media_cate').change(function(){
        var nwCate = $(this).val();
        var param       = 'mode=getApiContent&nwCate='+ nwCate + '&nwType=' + $('select[name="nwType"]').val();

        $.post('/adm/media/ajax_process', param, function(response){
            $('.nwSource').html(response);
        }, "json");
    });

    $(document).on('change', 'select[name="nwSource"]', function(){
        $('input[name="nwSource"]').val($('select[name="nwSource"]').val());
        var param       = 'mode=getApiInfo&nwSource='+ $(this).val() + '&nwType=' + $('select[name="nwType"]').val();
        $('input[name="isChangeContent"]').val('Y');
		//console.log(param);
        $.post('/adm/media/ajax_process', param, function(response){// console.log(response);
            $('input[name="nwName"]').val(response.video.apiTitle);
            $('textarea[name="nwContent"]').val(response.video.apiDescription);
            $('input[name="nwDuration"]').val(response.video.apiDuration);
            $('input[name="nwViewCount"]').val(response.video.apiViewCount);
            $('input[name="nwLikeCount"]').val(response.video.apiLikeCount);
            $('input[name="nwThumbS"]').val(response.video.apiThumb.s);
            $('input[name="nwThumbL"]').val(response.video.apiThumb.l);
            $('input[name="nwRegDate"]').val(response.video.apiRegDate);
        }, "json");
    });

    $(document).on('change', 'select[id="ncCode_nwRelative"]', function(){

        var ncCode_nwRelative = $(this).val();
        $('.nwList').parent().parent().css('display','none');
        $('.nwList').parent().css('display','none');
        $('.nwList').css('display','none');
        $('.'+ ncCode_nwRelative + '_nwRelative').parent().parent().css('display','block');
        $('.'+ ncCode_nwRelative + '_nwRelative').parent().css('display','block');
        $('.'+ ncCode_nwRelative + '_nwRelative').css('display','block');

    });



})



function video_cate_update(type){
    var param       = 'mode=videoCateUpdate&nwType=' + type;
    $('.cateLoading').html("<img src='/images/common/loading.gif'>");

    $.post('/adm/media/ajax_process', param, function(response){
        $('.cateLoading').html("");
    }, "json");
}

function video_content_update(type){
    var param       = 'mode=videoContentUpdate&nwType=' + type;
    $('.cateLoading').html("<img src='/images/common/loading.gif'>");

    $.post('/adm/media/ajax_process', param, function(response){
        $('.cateLoading').html("");
    }, "json");
}

function add_nwRelative(param){
    var num_nwRelative = $("#nwRelative_list tr ").length;
    var nwRelative = '.'+param+' option:selected';

    $("#nwRelative_list").append(
        '<tr>'+
            '<td id = "'+num_nwRelative+'">'+
            '<div class="span9"><input type="hidden" name = "nwRelative_nwNO[]" value="'+$(nwRelative).val()+'"/>'+
            '<input class="uniform-input text" style="float:left;" size="80" type="text" id="nwRelative['+num_nwRelative+']" value="'+$(nwRelative).text()+'"></div>'+
            '<div class="span2"><button class="btn btn-mini marginL10"id="del_tag"onclick="del_nwRelative('+num_nwRelative+');"> 삭제 </button></div>'+
            '</td>'+
            '</tr>'
    );
}

function del_nwRelative(param){
    var idx = "#"+param;
    $(idx).parent().remove();
}
