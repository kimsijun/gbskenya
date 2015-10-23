var callRcStatus = 0;

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
        var param = $(this).parent().serialize()+'&mode=playList&curCtPage='+$(this).html();
        var html = "";
        var i = 0;
        $(".ctPage").val($(this).html());

        $.post('/program/ajax_process', param, function(response){
            $(".contentPlayList").html(response.html);
        }, "json");
    });

    $(".programView").live("click", function(){
        if($(this).attr("ctNO")){
            if($(".ctPage").val()) location.href = '/program/view/prCode/'+ $(this).attr("prCode") +'/prName/'+ $(this).attr("prName") +'/ctNO/' + $(this).attr("ctNO") + '/ctPage/' +$(".ctPage").val();
            else                   location.href = '/program/view/prCode/'+ $(this).attr("prCode") +'/prName/'+ $(this).attr("prName") +'/ctNO/' + $(this).attr("ctNO") + '/ctPage/1';
        } else
            location.href = '/program/view/prCode/'+ $(this).attr("prCode") + '/prName/'+ $(this).attr("prName");

    });


    $('.tagArea').click(function(){
        $('.tagAdd').focus();
    });

    $('.tagAdd').live('keypress',function(e) {
        var html="";
        var param = $(".frmTag").serialize();

        if (e.keyCode == 13 || e.keyCode == 44) {
            $.post('/content/ajax_process', param, function(response){
                if(response=='false'){
                    if(confirm('Would you like to log in?'))                $(".commonLogin").submit();
                    else{
                        $(this).blur();
                        return false;
                    }
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
                            html += '<span class="tagList">'
                                + '<span class="tagTxt" tgTag="' + response['tagList'][i]['tgTag'] + '">' + response['tagList'][i]['tgTag'] + '</span>';
                            html += '<span class="tagDel" tgNO="'+response['tagList'][i]['tgNO']+'"></span>' +
                                '</span>';
                        }
                        html += '<input class="tagAdd" type="text" name="tagTxt">';
                        $(".frmTag").html(html);
                        $(".tagAdd").focus();
                    }
                }
            },"json");
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
        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?'))                $(".commonLogin").submit();
            else                                           $(this).blur();
            return false;
        }

        if(confirm('Are you sure?')){
            $(this).parent('span').remove();
            var param =  'mode=delTag&tgNO='+$(this).attr('tgNO')
            $.post('/program/ajax_process', param, function(response){

            },"json");
        }
    });

    $(".Like").click(function(){

        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?'))                $(".commonLogin").submit();
            else                                           $(this).blur();
            return false;
        }

        var param = $(".frmlike").serialize()+'&mode=write';
        if(confirm('Would you like to register?')){
            $.post('/mypage_like/ajax_process', param, function(response){console.log(response);
                if(!response['login']){
                    if(response.success == "true"){
                        alert("Registered.");
                        if(response['mpType']=="CONTENT") $(".likeCount").html(response['content']['ctLikeCount']);
                        else $(".likeCount").html(response['program']['prLikeCount']);
                    }
                    else
                        alert("It has already been registered.");
                }
            }, "json");
        }
    });

    $(".Favor").click(function(){
        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?'))                $(".commonLogin").submit();
            else                                           $(this).blur();
            return false;
        }

        var param = $(".frmfavor").serialize()+'&mode=write';
        if(confirm('Would you like to register?')){
            $.post('/mypage_favor/ajax_process', param, function(response){
                if(!response['login']){
                    if(response.success == "true")
                        alert("Registered.");
                    else
                        alert("It has already been registered.");
                }
            }, "json");
        }
    });

    $(".prCommentSubmit").live("click", function(){
        if($(this).attr('mode')=='add'){
            if(!$(".prCommentTxt").val())   return false;
        }else{
            if(!$(".prCommentModify").val())   return false;
        }
        var param = $(this).parent().parent().serialize();
        $.post('/program_comment/ajax_process', param, function(response){
            if(response=='false'){
                if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                else{
                    return false;
                }
            }else{
                if(confirm('Would you like to register?')){
                    $('.prCommentList').html(response.html);
                }
            }
        }, "json");
    });


    $(".prCommentMod").live('click',function(){
        var html = '';
        html += '<form method="post" class="frmCommentMod">';
        html += '<p>';
        html += '<input type="hidden" name="mode" value="modify"/>';
        html += '<input type="hidden" name="prCode" value="'+$(this).attr('prCode')+'"/>';
        html += '<input type="hidden" name="pbcoNO" value="'+$(this).attr('pbcoNO')+'">';
        html += '<p class="replay-txt">';
        html += '<textarea class="prCommentModify txt" name="pbcoContent" maxlength="250" style="width:490px;height:30px">'+$(this).parent().next('div').attr('pbcoContent') +'</textarea>';
        html += '<span class="prCommentSubmit"  mode="mod"style="height:16px;padding:12px 10px;">Save</span>';
        html += '</p>';
        html += '</form>';

        $(this).parent().next('div').html(html);
    });

    $(".prCommentDel").live('click',function(){
        var param = $(this).parent().serialize();
        $.post('/program_comment/ajax_process', param, function(response){
            if(response=='false'){
                if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                else{
                    return false;
                }
            }else{
                if(confirm('Are you sure?')){
                    $('.prCommentList').html(response.html);
                }
            }
        }, "json");

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
        if(confirm('Are you sure?')){
            var param = $(this).parent().serialize();
            $.post('/content_comment/ajax_process', param, function(response){
                if(response=='false'){
                    if(confirm('Would you like to log in?'))  $(".commonLogin").submit();
                }else{
                    $('.ctCommentList').html(response.html);
                }
            }, "json");
        }
    });


    $(".prCommentReply").live("click", function(){
        var mbNick = "@"+$(this).attr("mbNick")+" ";
        $(".prCommentTxt").val(mbNick).focus();
    });

    $(".prCommentReplyTxt").live('keypress',function(e){
        if(e.keyCode == 13 && !e.shiftKey){
            var param = $(this).parent().parent().serialize();
            //alert(param);return false;
            $.post('/program_comment/ajax_process', param, function(response){
                $('.prCommentReplyTxt').val("");
                $('.prCommentList').html(response);
            }, "json");
        }
    });

    $(".chansublist").hide();
    $(".chansub2list").hide();
    $(".chansub3list").hide();
    $(".chansub4list").hide();
    $(".chansub5list").hide();
    $(".chansub6list").hide();

    $(".channellist li").live('click',function(){
        $(".chansublist").hide();
        $(".chansub2list").hide();
        $(".chansub3list").hide();
        $(".chansub4list").hide();
        $(".chansub5list").hide();
        $(".chansub6list").hide();

        $(".channellist li").removeClass("selected");
        $(this).addClass("selected");
        $(".chansublist").fadeOut();
        $(".chansublist").fadeIn();

        var param = 'mode=getContent&prCode='+$(this).attr('prCode');

        var html = "";
        var i=0;
        var idx=0;
        $.post('/program/ajax_process', param, function(response){

            if(response['data'].length>0){
                for(i=0; i<response['data'].length; i++){
                    idx = (i<5)? 1 : Math.floor( i/5 ) + 1;
                    if(response['data'][i]['prType'] ==="SERMON"){
                        if(response['data'][i]['ctName']){
                            html +='<li><a href="/sermon/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                        }else{
                            html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                        }
                    }else{
                        if(response['data'][i]['ctName']){
                            html +='<li><a href="/program/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                        }else{
                            html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                        }
                    }
                }
            }else
                html +='<li>There are no registered content.</li>';
            $(".chansublist").html(html);
        },'json');

    });

    $(".chansublist li").live('click',function(){
        $(".chansub2list").hide();
        $(".chansub3list").hide();
        $(".chansub4list").hide();
        $(".chansub5list").hide();
        $(".chansub6list").hide();

        $(".chansublist li").removeClass("selected");
        $(".chansub2list li").removeClass("selected");
        $(".chansub3list li").removeClass("selected");
        $(".chansub4list li").removeClass("selected");
        $(".chansub5list li").removeClass("selected");
        $(".chansub6list li").removeClass("selected");
        var liIndex = $(".chansublist li").index(this);
        $(this).addClass("selected");
        $(".chansub2list").fadeOut();
        $(".chansub2list").fadeIn();
        if($(this).attr('prCode')){
            var param = 'mode=getContent&prCode='+$(this).attr('prCode');
            var html = "";
            var i=0;
            var idx=0;

            $.post('/program/ajax_process', param, function(response){
                if(response['data'].length>0){
                    for(i=0; i<response['data'].length; i++){
                        idx = (i<5)? 1 : Math.floor( i/5 ) + 1;
                        if(response['data'][i]['prType'] ==="SERMON"){
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/sermon/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }else{
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/program/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }
                    }
                }else
                    html +='<li>There are no registered content.</li>';
                $(".chansub2list").html(html);
            },'json');
        }else{

        }
    });

    $(".chansub2list li").live('click',function(){
        $(".chansub3list").hide();
        $(".chansub4list").hide();

        $(".chansub2list li").removeClass("selected");
        $(".chansub3list li").removeClass("selected");
        $(".chansub4list li").removeClass("selected");
        var liIndex = $(".chansub2list li").index(this);
        $(this).addClass("selected");
        $(".chansub3list").fadeOut();
        $(".chansub3list").fadeIn();

        var param = 'mode=getContent&prCode='+$(this).attr('prCode');
        var html = "";
        var i=0;
        var idx=0;

        if($(this).attr('prCode')){
            $.post('/program/ajax_process', param, function(response){
                if(response['data'].length>0){
                    for(i=0; i<response['data'].length; i++){
                        idx = (i<5)? 1 : Math.floor( i/5 ) + 1;
                        if(response['data'][i]['prType'] ==="SERMON"){
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/sermon/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }else{
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/program/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }
                    }
                }else
                    html +='<li>There are no registered content.</li>';
                $(".chansub3list").html(html);
            },'json');
        }

    });

    $(".chansub3list li").live('click',function(){
        $(".chansub4list").hide();

        $(".chansub3list li").removeClass("selected");
        $(".chansub4list li").removeClass("selected");
        var liIndex = $(".chansub3list li").index(this);
        $(this).addClass("selected");
        $(".chansub4list").fadeOut();
        $(".chansub4list").fadeIn();

        var param = 'mode=getContent&prCode='+$(this).attr('prCode');
        var html = "";
        var i=0;
        var idx=0;

        if($(this).attr('prCode')){
            $.post('/program/ajax_process', param, function(response){
                if(response['data'].length>0){
                    for(i=0; i<response['data'].length; i++){
                        idx = (i<5)? 1 : Math.floor( i/5 ) + 1;
                        if(response['data'][i]['prType'] ==="SERMON"){
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/sermon/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }else{
                            if(response['data'][i]['ctName']){
                                html +='<li><a href="/program/view/prCode/'+response['data'][i]['prCode']+'/ctNO/'+response['data'][i]['ctNO']+'/ctPage/'+ idx + '">'+response['data'][i]['ctName']+'</a></li>';
                            }else{
                                html +='<li prCode="'+response['data'][i]['prCode']+'">'+response['data'][i]['prName']+'</li>';
                            }
                        }
                    }
                }else
                    html +='<li>There are no registered content.</li>';
                $(".chansub4list").html(html);
            },'json');
        }

    });
    $('.snsShare').click(function(){
        var type;
        var param;
        if($(this).hasClass("facebook") == true) type = "facebook";
        else type = "twitter";
        if($("input[name=ctNO]").val()){
            param = 'prCode=' +$("input[name=prCode]").val() +'&ctNO='+$("input[name=ctNO]").val()+'&mode=snsShare&type='+type;
        }else param = 'prCode=' +$("input[name=prCode]").val() +'&mode=snsShare&type='+type;

        $.post("/program/ajax_process",param,function(response){
            var fullUrl;
            var url = $(".baseUrl").val();
            var image = $(".baseUrl").val()+'uploads/program/'+$('.prThumb').val();
            var title = $(".prName").val();
            if($(".ctName").val()){
                title = $(".ctName").val();
                image = $(".baseUrl").val()+'uploads/content/thumbl/'+$('.ctThumb').val();
            }
            var summary = $(".prContent").val();

            var pWidth = 640;
            var pHeight = 380;
            var pLeft = (screen.width - pWidth) / 2;
            var pTop = (screen.height - pHeight) / 2;
            var appId = $(".appId").val();


            if(type == 'facebook'){
                if($("input[name=ctNO]").val())
                    url += "program/view/prCode/" + $("input[name=prCode]").val() + "/ctNO/" + $("input[name=ctNO]").val();
                else
                    url += "program/view/prCode/" + $("input[name=prCode]").val();
                /*
                FB.ui( {
                        method      : 'feed',
                        display     : 'popup',
                        name        : 'example_name',
                        picture     : 'http://pr.gnn.or.kr/images/common/logo.gif',
                        link        : url,
                        caption     : 'Example',
                        description : 'This is example'
                },{scope: 'publish_stream'});
    */

                /*
                fullUrl = "http://www.facebook.com/dialog/feed?"+
                        "app_id="+ appId +
                        "&link="+ url + "/tt" +
                        "&picture=http://pr.gnn.or.kr/images/common/logo.gif"+
                        "name=이름"+
                        "caption=캡션"+
                        "&description=디스크립션"+
                        "&message=메세지"+
                        "&redirect_uri=";
                        //"&redirect_uri="+ url + "/DD";

    */
                //fullUrl = "http://www.facebook.com/share.php?u=" + url + "&t=" + title;
                //fullUrl = "http://www.facebook.com/share.php?u=" + url;

                fullUrl = "http://www.facebook.com/share.php?s=100&p[url]="+ url+"&p[images][0]="+ image+"&p[title]="+ title+"&p[summary]="+ summary;
                fullUrl = fullUrl.split("#").join("%23");
                fullUrl = encodeURI(fullUrl);
                window.open(fullUrl,"","width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

            } else {

                if($("input[name=ctNO]").val())
                    url += "s/cp/" + $("input[name=ctNO]").val();
                else
                    url += "s/p/" + $("input[name=prCode]").val();

                if(summary.length > 80){
                    var temp = summary.substring(0,80);
                    summary = temp + '...';
                }
                var wp = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + ". " + encodeURIComponent(summary) + " " + encodeURIComponent(url), "twitter", "width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");
                if ( wp ) {
                    wp.focus();
                }
            }
        },'json');

    });


    $(".prCommentTxt").live("click", function(){
        if(!$('.mbLoginId').val()){
            if(confirm('Would you like to log in?'))                $(".commonLogin").submit();
            else                                                                 $(this).blur();
        }
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

    $(".callrc").live("click", function(){
        if(callRcStatus == 0) {
            callRcStatus = 1;
            var contentLeft = $(".content-main").offset().left;
            $(".channelbox").css({"display":"block"}).animate({"left":contentLeft + "px"});
        } else {
            $(".chansublist").css("display","none");
            $(".chansub2list").css("display","none");
            $(".chansub3list").css("display","none");
            $(".chansub4list").css("display","none");
            $(".chansub5list").css("display","none");
            $(".chansub6list").css("display","none");
            $(".channellist li").removeClass("selected");
            callRcStatus = 0;
            $(".channelbox").animate({"left":"-250px"});
        }
    });

    $('.downloadBtn').hover(
        function() {
            $('.downloadContainer').fadeIn();
        }, function() {
            $('.downloadContainer').fadeOut();
        }
    );

    /*
     | -------------------------------------------------------------------
     | # 프로그램 리스트 Swipe 슬라이드 관련 스크립트
     | -------------------------------------------------------------------
     */
    var mySwiperPr = $('.swiper-container-pr').swiper({
        pagination: '.prPagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });
    $('.arrow-left-pr').on('click', function(e){
        e.preventDefault()
        mySwiperPr.swipePrev()
    })
    $('.arrow-right-pr').on('click', function(e){
        e.preventDefault()
        mySwiperPr.swipeNext()
    })
    

    /*
     | -------------------------------------------------------------------
     | # 프로그램 재생목록 Ajax Pagination Prev, Next 이벤트
     | -------------------------------------------------------------------
     */
    $(".pageMove").live("click", function(){
        var secPage = ($(this).hasClass("pageNext")) ? parseInt($("input[name=page]").val()) + 6 : parseInt($("input[name=page]").val());
        var param = "prCode="+ $("input[name=prCode]").val() + "&ctNO="+ $('input[name=ctNO]').val() + "&ctPage="+ $('.ctPage').val() + "&total="+ $("input[name=total]").val() + "&page=" + secPage + "&mode=playList&curCtPage="+secPage;
        var html = "";
        var i = 0;

        $(".ctPage").val(secPage);

        $.post('/program/ajax_process', param, function(response){
            $(".contentPlayList").html(response.html);
        }, "json");
    });


    if($(".ctType").val()=="VIMEO" && $(".ctSource").val()){
        var param       = 'ctSource='+ $('.ctSource').val();
        $.post('/program/isTranscoding', param, function(response){
            if(response.isTranscoding == "1") {
                $('.movie').html("<div id='mediaplayer'></div>");
                jwplayer('mediaplayer').setup({
                    file: $(".vodServer").val(),
                    height: 480,
                    width: 720,
                    autostart: true,
                    fallback: false
                });

            }else {
                var html = '<div class="orgContent" ctType = "'+$(".ctType").val()+'">'+
                    '<iframe width="720" id="vimeo_player" height="480" src="//player.vimeo.com/video/'+$(".ctSource").val()+'?api=1&autoplay=1&wmode=opaque&player_id=vimeo_player" frameborder="0" wmode="Opaque" allowfullscreen ></iframe></div>';
                $('.movie').html(html);

                // Enable the API on each Vimeo video
                $('.movie iframe').each(function(){
                    Froogaloop(this).addEvent('ready', ready);
                });

                function ready(playerID){
                    Froogaloop(playerID).addEvent('finish', finish);
                }
                function finish() {
                    location.href='/program/view/prCode/'+$(".prCode").val()+'/ctNO/'+$(".nextCtNO").val()+'/prName/'+ $(".prName").val().replace(" ", "-")+'/ctPage/'+$(".nextCtPage").val()+'';
                }
            }
        }, "json");
    }
})

/*
window.fbAsyncInit = function() {
    FB.init({
        appId: $(".appId").val(), // App ID
        cookie:true, // enable cookies to allow the server to access the session
        status:true, // check login status
        xfbml:true, // parse XFBML
        oauth : true //enable Oauth
    });
};
*/

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


function del_tag(param){
    var idx = "#"+param;
    $(idx).remove();
}
