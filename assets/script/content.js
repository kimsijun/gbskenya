(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));


$(function(){

	/*  페이스북 Feed Post 인증 받아올때 URL에 '#_=_' 가 붙어 오는 버그 없애기 위해서     */
	if (window.location.hash == '#_=_') {
	    window.location.hash = '';
	    history.pushState('', document.title, window.location.pathname);
	}
		
    $(".playlistPage").live('click',function(){
        var param = $(this).parent().serialize()+'&mode=playList';
        var html = "";
        var i = 0;
        $(".ctPage").val($(this).html());

        $.post('/content/ajax_process', param, function(response){
            html += '<ul>';
            for(i=0; i<response['data'].length; i++){
                if($(".ctNO").val() == response['data'][i]['ctNO']){
                    html += '<li class="contentView selected" ctNO="' + response['data'][i]['ctNO'] + '">';
                }else{3
                    html += '<li class="contentView" ctNO="' + response['data'][i]['ctNO'] + '">';
                }
                html += '<span class="mrt10 mt15">';
                if($(".ctNO").val() == response['data'][i]['ctNO']){
                    html += '>';
                }else{
                    html +=  i + response["offset"] +1 ;
                }
                html += '</span>';
                html += '<span class="mt5"><img src="/uploads/content/thumbs/' + response['data'][i]["ctThumbS"] + '" width="47" height="35"></span>';
                html += '<span class="txt mlt10">' + response['data'][i]["ctName"] + '</span>';
                html += '</li>';
            }

            html += '</ul>';
            html += '<div class="contentPage"  style="width:';
            html += response["total"] * 25;
            html += 'px">';
            for(i=0; i<response["total"]; i++){
                html += '<form class="frmplaylistPage" method="post">'+
                        '   <input type="hidden" name="total" value="' + response["total"] + '">'+
                        '   <input type="hidden" name="page" value="' + (i+1) + '">'+
                        '   <input type="hidden" name="prCode" value="' + response['prCode'] + '">'+
                        '   <input type="hidden" name="ctNO" value="' + response['ctNO'] + '">'+
                        '   <span class="playlistPage">' + (i+1) + '</span>'+
                        '</form>';
                    }
            html += '</div>';

            $(".contentPlayList").html(html);
        }, "json");
    });


    $(".contentView").live("click", function(){
        location.href = '/content/view/ctNO/' + $(this).attr("ctNO") + '/ctPage/' +$(".ctPage").val();
    });


    $('.tagArea').click(function(){
        $('.tagAdd').focus();
    });

    $('.tagAdd').live('keypress',function(e) {
        var html="";
        if (e.keyCode == 13 || e.keyCode == 44) {
            var param = $(".frmTag").serialize();
            if($("input[name=tagTxt]").val()==''){
                alert("Enter your tags.");
                return false;
            }else{
                $.post('/content/ajax_process', param, function(response){
                    //console.log(response);
                    if(response=='false'){
                        if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                    }else{
                        if(response['tagCnt'].length==3){
                            alert("Up to three");
                            $("input[name=tagTxt]").val("");
                            return false;
                        }else if(response['tag']){
                            alert("The tag has already been registered.");
                            $("input[name=tagTxt]").val("");
                            return false;
                        }else{
                            html += '<input type="hidden" name="mode" value="addTag">';
                            html += '<input type="hidden" name="ctNO" value="'+ response['param']['ctNO'] +'">';

                            for(var i=0; i<response['tagList'].length; i++){
                                html += '<div class="tagList">'
                                        + '<span class="tagTxt" tgTag="' + response['tagList'][i]['tgTag'] + '">' + response['tagList'][i]['tgTag'] + '</span>';
                                html += '<span class="tagDel" tgNO="'+response['tagList'][i]['tgNO']+'"></span>' +
                                        '</div>';
                            }
                            html += '<input class="tagAdd" type="text" name="tagTxt">';
                            $(".frmTag").html(html);
                            $(".tagAdd").focus();
                        }
                    }
                },"json");
            }

        }
    });

    $('.tagTxt').live('click',function(){
        $('input[name=mode]').val('modTag');
        //$(this).parent('div').remove();
        var html = '<input type="text" name="tgTag" value="'+$(this).attr('tgTag')+'">';
        html += '<input type="hidden" name="tgNO" value="'+$(this).next().attr('tgNO')+'">';
        $(this).parent().html(html);
        $('input[name=tgTag]').focus();
    });

    $('input[name=tgTag]').live('keypress',function(e) {
        var html="";
        if (e.keyCode == 13 || e.keyCode == 44) {
            var param = $(".frmTag").serialize();

            if($("input[name=tgTag]").val()==''){
                alert("Enter your tags.");
                return false;
            }else{
                $.post('/content/ajax_process', param, function(response){
                    if(response=='false'){
                        if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                    }else{
                        html += '<input type="hidden" name="mode" value="addTag">';
                        html += '<input type="hidden" name="ctNO" value="'+ response['param']['ctNO'] +'">';

                        for(var i=0; i<response['tagList'].length; i++){
                            html += '<div class="tagList">'
                                + '<span class="tagTxt" tgTag="' + response['tagList'][i]['tgTag'] + '">' + response['tagList'][i]['tgTag'] + '</span>';
                            html += '<span class="tagDel" tgNO="'+response['tagList'][i]['tgNO']+'"></span>' +
                                '</div>';
                        }
                        html += '<input class="tagAdd" type="text" name="tagTxt">';
                        $(".frmTag").html(html);
                        $(".tagAdd").focus();
                    }
                },"json");
            }

        }
    });
    $('.tagDel').live('click',function(){
        if(confirm("Are you sure?")){
            $(this).parent('div').remove();
            var param =  'mode=delTag&tgNO='+$(this).attr('tgNO');
            $.post('/content/ajax_process', param, function(response){
                if(response=='false'){
                    if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                }
            },"json");
        }
    });

    $(".Like").click(function(){
        var param = $(".frmlike").serialize()+'&mode=write';
        $.post('/mypage_like/ajax_process', param, function(response){
            if(!response['login']){
                if(response.success == "true"){
                    alert("Registered.");
                    if(response['mpLType']=="ctNO") $(".likeCount").html(response['content']['ctLikeCount']);
                    else $(".likeCount").html(response['program']['prLikeCount']);
                }
                else
                    alert("It has already been registered.");
            }else{
                if(confirm(response['login'])){
                    $(".commonLogin").submit();
                }
            }
        }, "json");

    });

    $(".Favor").click(function(){
        var param = $(".frmfavor").serialize()+'&mode=write';

        if(confirm('Would you like to register?')){
            $.post('/mypage_favor/ajax_process', param, function(response){
                if(!response['login']){
                    if(response.success == "true")
                        alert("Registered.");
                    else
                        alert("It has already been registered.");
                }else{
                    if(confirm(response['login'])){
                        $(".commonLogin").submit();
                    }
                }
            }, "json");
        }
    });

	$(".ctCommentTxt").live("click", function(){
        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?')){
                $(".commonLogin").submit();
            }
        }
    });



    $(".ctCommentSubmit").live("click", function(){
        if($(this).attr('mode')=='add'){
            if(!$(".ctCommentTxt").val())   return false;
        }else{
            if(!$(".ctCommentModify").val())   return false;
        }

        var param = $(this).parent().parent().serialize();
        $.post('/content_comment/ajax_process', param, function(response){
            if(response=='false'){
                if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
            }else{
                if($(".fbPostCheck").is(":checked") == true){
                    var fbParam = "cbcoContent="+response.cbcoContent+"&ctNO="+response.ctNO;
                    $.post('/content_comment/fb_feed', fbParam, function(response){

                    }, "json");
                } else if($(".twPostCheck").is(":checked") == true){
                    var twParam = "cbcoContent="+response.cbcoContent+"&ctNO="+response.ctNO;
                    $.post('/content_comment/tw_feed', twParam, function(response){

                    }, "json");
                }

                if(confirm('Would you like to register?')){
                    $('.ctCommentList').html(response.html);
                }
            }
        }, "json");
    });

    $(".ctCommentModify").live('keypress',function(e){
        if(e.keyCode == 13 && !e.shiftKey){
            var param = $(this).next().serialize();
            $.post('/content_comment/ajax_process', param, function(response){
                if(response=='false'){
                    if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                }else{
                    if(confirm('Would you like to modify?')){
                        $('.ctCommentList').html(response.html);
                    }
                }
            }, "json");
        }
    });

    $(".ctCommentMod").live('click',function(){
        var html = '';
        html += '<form method="post" class="frmCommentMod">';
        html += '<p>';
        html += '<input type="hidden" name="mode" value="modify"/>';
        html += '<input type="hidden" name="ctNO" value="'+$(this).attr('ctNO')+'"/>';
        html += '<input type="hidden" name="cbcoNO" value="'+$(this).attr('cbcoNO')+'">';
        html += '<p class="replay-txt">';
        html += '<textarea class="ctCommentModify txt" name="cbcoContent" maxlength="250" style="width:490px;height:30px">'+$(this).parent().next('div').attr('cbcoContent') +'</textarea>';
        html += '<span class="ctCommentSubmit"  mode="mod"style="height:16px;padding:12px 10px;">Save</span>';
        html += '</p>';
        html += '</form>';

        $(this).parent().next('div').html(html);
    });

    $(".ctCommentDel").live('click',function(){
        var param = $(this).parent().serialize();
        $.post('/content_comment/ajax_process', param, function(response){
            if(response=='false'){
                if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
            }else{
                if(confirm('Are you sure?')){
                    $('.ctCommentList').html(response.html);
                }
            }
        }, "json");

    });

    $(".ctCommentReply").live('click',function(){
        $(this).next().toggle();
        $('.ctCommentReplyTxt').val("");
    });

    $(".ctCommentReplyTxt").live('keypress',function(e){
        if(e.keyCode == 13 && !e.shiftKey){
            var param = $(this).parent().parent().serialize();
            //alert(param);return false;
            $.post('/content_comment/ajax_process', param, function(response){
                if(response=='false'){
                    if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                }else{
                    $('.ctCommentReplyTxt').val("");
                    $('.ctCommentList').html(response);
                }
            }, "json");
        }
    });

    $(".chansublist").hide();
    $(".chansub2list").hide();
    $(".chansub3list").hide();

    $(".channellist li").click(function(){
        $(".chansublist").hide();
        $(".chansub2list").hide();
        $(".chansub3list").hide();

        $(".channellist li").removeClass("selected");
        var liIndex = $(".channellist li").index(this);
        $(this).addClass("selected");
        $(".chansublist").fadeOut();
        $(".chansublist").eq(liIndex).fadeIn();

    });

    $(".chansublist li").click(function(){
        $(".chansub2list").hide();
        $(".chansub3list").hide();
        $(".chansublist li").removeClass("selected");
        var liIndex = $(".chansublist li").index(this);
        $(this).addClass("selected");
        $(".chansub2list").fadeOut();
        $(".chansub2list").eq(liIndex).fadeIn();

    });

    $(".chansub2list li").click(function(){
        $(".chansub2list li").removeClass("selected");
        var liIndex = $(".chansub2list li").index(this);
        $(this).addClass("selected");
        $(".chansub3list").fadeOut();
        $(".chansub3list").eq(liIndex).fadeIn();

    });

    $(".channel_close").toggle(
        function() {
            $(".channelbox").animate({left:-75},'slow');
        },
        function(){
            $(".channelbox").animate({left:0},'slow');
        }
	);
        
    
    $('.downloadBtn').hover(
        function() {
            $('.downloadContainer').fadeIn();
        }, function() {
            $('.downloadContainer').fadeOut();
        }
    );
    
    $('.snsShare').click(function(){
        var type;
        var param;
        if($(this).hasClass("facebook") == true) type = "facebook";
        else type = "twitter";
        if($("input[name=ctNO]").val()){
            param = 'prCode=' +$("input[name=prCode]").val() +'&ctNO='+$("input[name=ctNO]").val()+'&mode=snsShare&type='+type;
        }else param = 'prCode=' +$("input[name=prCode]").val() +'&mode=snsShare&type='+type;

        $.post("/content/ajax_process",param,function(response){
            var fullUrl;
            var url = $(".baseUrl").val() + "content/view/ctNO/" + $("input[name=ctNO]").val();
            var image = $(".baseUrl").val()+'uploads/content/thumbl/'+$('.ctThumb').val();
            var title = $(".ctName").val();
            var summary;
            if($(".prContent").val()) summary= $(".prContent").val();
            else summary=$(".ctContent").val();

            var pWidth = 640;
            var pHeight = 380;
            var pLeft = (screen.width - pWidth) / 2;
            var pTop = (screen.height - pHeight) / 2;

            if(type == 'facebook'){
                //fullUrl = "http://www.facebook.com/share.php?u=" + url;
                fullUrl = "http://www.facebook.com/share.php?s=100&p[url]="+ url+"&p[images][0]="+ image+"&p[title]="+ title+"&p[summary]="+ summary;
                fullUrl = fullUrl.split("#").join("%23");
                fullUrl = encodeURI(fullUrl);
                window.open(fullUrl,"","width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

            } else {
                var wp = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + ". " + encodeURIComponent(summary) + " " + encodeURIComponent(url), "twitter", "width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

                if ( wp ) {
                    wp.focus();
                }
            }
        },'json');
    });

    $('.sns_login li').live("click", function() {
        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?')){                $(".commonLogin").submit();        return false;
            }else                                                                return false;
        }
        if($(".mbAccountType").val() == $(this).attr("type"))   return false;
        var loginUrl = $(this).attr("loginUrl");

        if(loginUrl){
            location.href = loginUrl;

        } else if($(this).attr("type") == "TWITTER" && !$(".mbTWSession").val()){
            location.reload();

        } else if($(this).attr("type") == "FACEBOOK" && !$(".mbFBSession").val()){
            FB.login(function(response) {
                if(response.authResponse) {
                    parent.location =$(".baseUrl").val()+$(".uri_string").val()+'/fbLogin/true';
                }
            },{scope: 'publish_stream'});

        } else {
            $(".mbAccountType").val($(this).attr("type"));
            var btnIndex = $('.sns_login li').index(this) + 1;
            var param = "mode=setCookieSnsLogin&type=" + $(this).attr("type");
            var thisHtml = $(this);
            $.post('/content/ajax_process', param, function(response){
                // snsBtn Image on/off 처리
                $('.sns_login li').each(function(){
                    var liIndex = $('.sns_login li').index(this)+1;
                    $(this).attr("class","sns0"+liIndex);
                });
                thisHtml.attr("class","snsCurrent0"+btnIndex+" currentLogin");
            }, "json");
        }
    });




    /*
     | -------------------------------------------------------------------
     | # 콘텐츠 리스트 Swipe 슬라이드 관련 스크립트
     | -------------------------------------------------------------------
     */
    var mySwiperLc = $('.swiper-container-lc').swiper({
        pagination: '.lcPagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });
    $('.arrow-left-lc').on('click', function(e){
        e.preventDefault()
        mySwiperLc.swipePrev()
    })
    $('.arrow-right-lc').on('click', function(e){
        e.preventDefault()
        mySwiperLc.swipeNext()
    })


    var mySwiperRc = $('.swiper-container-rc').swiper({
        pagination: '.rcPagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });
    $('.arrow-left-rc').on('click', function(e){
        e.preventDefault()
        mySwiperRc.swipePrev()
    })
    $('.arrow-right-rc').on('click', function(e){
        e.preventDefault()
        mySwiperRc.swipeNext()
    })

    var mySwiperCt = $('.swiper-container-ct').swiper({
        pagination: '.ctPagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });
    $('.arrow-left-ct').on('click', function(e){
        e.preventDefault()
        mySwiperCt.swipePrev()
    })
    $('.arrow-right-ct').on('click', function(e){
        e.preventDefault()
        mySwiperCt.swipeNext()
    })

    setTimeout(function(){
        $('.adSkip').fadeIn('slow');}, 5000);

    var adtime = $('.adContent').attr('aCDuration');
    setTimeout(function(){
        $('.adContent').css('display','none');
        $('.orgContent').fadeIn('slow');
    }, adtime*1000);

    $('.adSkip').click(function(){
        $('.adContent').remove();
        $('.orgContent').css('display','inline');
    });
})


function getsize(str){
    var chk = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_0123456789\~!@#$%^&*()_+| ";
    var length = 0;

    for(var i=0; i<str.length; i++){
        if(chk.indexOf(str.charAt(i)) >= 0 ){
            length++;
        }else{
            length+=2;
        }
    }

    if(length > 250) return false;
    $('.reply-sum').html("("+length+" / 250)");
}

