var idx="";
var eqIdx = new Array();
var i=0;

$(function(){
    $(".popupProgramLoad").hide();
    $(".popupProgramLoad").eq(0).show();
    $('.livePopupContainer').eq(0).css("display","block");

    $(".back").bind("click", function(){
        $(".popupProgramLoad").hide();
        if($(this).attr("idx")) {
            $("." + $(this).attr("idx")).eq($(this).attr("eqIdx")).fadeIn();
        } else {
            $(".livePopupContentContainer").remove();
            if(idx == "fst")   $("." + idx).eq(0).fadeIn();
            else if(idx == "scd")   $("." + idx).eq(eqIdx[1]).fadeIn();
            else if(idx == "trd")   $("." + idx).eq(eqIdx[2]).fadeIn();
            else if(idx == "fth")   $("." + idx).eq(eqIdx[3]).fadeIn();

        }
    });


    $('.popupProgramLoad li').bind("click",function(){
        if($(this).hasClass("back")) return false;
        var param       = 'mode=getProgram&prCode='+ $(this).attr("prCode");
        var listIndex = 0;
        var i =0;

        $.post('/live/ajax_process', param, function(response){
            if(response['mode'] == "content") {
                var pcSource = '<ul class="livePopupContentContainer"><li class="back">Back</li>';
                for(i=0; i<response['data'].length; i++){
                    pcSource += '<li class="popupContentLoad" ctNO=' + response['data'][i]['ctNO'] + '> ' +
                        '<span class="time"> ' +
                        '<img src="/uploads/content/thumbs/' + response['data'][i]['ctThumbS'] + '" width="47" height="35" alt="" /> ' +
                        '</span> ' +
                        '<span class="txt mlt10">' + response['data'][i]['ctName'] + '</span> ' +
                        '</li>';
                }
                pcSource += '</ul>';
                $(".popupProgramLoad").hide();
                $('.livePopupProgramContainer').append(pcSource);
                return false;
            }

        }, "json");

        if($(this).parent("ul").hasClass("fst")) {
            idx = "fst";
            listIndex = eqIdx[1] = $(".fst li").index(this);
            $('.fst').hide();
            $('.scd').eq(listIndex).fadeIn();
        } else if($(this).parent("ul").hasClass("scd")) {
            idx = "scd";
            listIndex = eqIdx[2] = $(".scd li").index(this)-1;
            $('.scd').hide();
            $('.trd').eq(listIndex).fadeIn();
        } else if($(this).parent("ul").hasClass("trd")) {
            idx = "trd";
            listIndex = eqIdx[3] = $(".trd li").index(this)-1;
            $('.trd').hide();
            $('.fth').eq(listIndex).fadeIn();
        }
    });


    /*  팝업 컨텐츠 리스트 Ajax 페이징    */
    $(".playPopuplistPage").bind('click',function(){
        var param = $(this).parent("form").serialize()+'&mode=playPopupList&ctNO='+$(".popupCtNO").val();
        var html = "";
        var i =0;

        $.post('/live/ajax_process', param, function(response){
            html += '<ul>';
            for(i=0; i<response['data'].length; i++) {
                if(response['ctNO']==response['data'][i]['ctNO']){
                    html += '<li class="selected">';
                }else{
                    html +='<li class="popupContentLoad" ctNO="'+ response['data'][i]['ctNO'] +'">';
                }
                html +='<span class="mrt10 mt15">';
                if(response['ctNO']==response['data'][i]['ctNO']){
                    html += '>';
                }else{
                    html += i + response["offset"] +1 ;
                }
                html +='</span>';
                html +='<span class="mt5"><img src="/uploads/content/thumbs/' + response['data'][i]["ctThumbS"] + '" width="47" height="35"></span>';
                html +='<span class="txt mlt10">' + response['data'][i]["ctName"] + '</span>';
                html +='</li>';
            }

            html += '</ul>';
            html += '<div class="contentPage"  style="width:';
            html += response["total"] * 25;
            html += 'px">';


            for(i=0; i<response["total"]; i++){
                html += '<form class="frmplayPopuplistPage" method="post">' +
                    '<input type="hidden" name="total" value="' + response["total"] + '">' +
                    '<input type="hidden" name="page" value="' + (i+1) + '">' +
                    '<input type="hidden" name="ctNO" value="' + response['ctNO'] + '">' +
                    '<input type="hidden" name="listPopup" value="'+ response['listPopup'] + '">'+
                    '<span class="playPopuplistPage">' + (i+1) + '</span>' +
                    '</form>';
                    }
            html += '</div>';

            $(".listPopup"+response['listPopup']).html(html);
        }, "json");
    });


    $('.popupContentLoad').bind("click",function(){
        var param       = 'mode=getContent&ctNO='+ $(this).attr("ctNO");
        $(".popupCtNO").val($(this).attr("ctNO"));
        $(".popupContentLoad").removeClass("selected");
        $(this).addClass("selected");
        $.post('/live/ajax_process', param, function(response){
            $('.movie').html(response);
        }, "json");
    });

    $('.videoQuality').click(function(){
        if($(this).attr("video") == "") return false;
        var param       = 'mode=getVideo&chNO='+ $('input[name=chNO]').val() + '&video=' + $(this).attr("video");
        $.post('/live/ajax_process', param, function(response){
            $('.movie').html(response);
        }, "json");
    });

    $('.videoLanguage').change(function(){
        if($(this).attr("video") == "") return false;

        var langUrl = "http://www.goodnews.or.kr/" + $(this).attr("video") + "/gntv/asx/live.asx";
        var html ="<div id='EPlayer'></div> " +
            "<script type='text/javascript'> " +
            "var obj = new Object(); " +
            "obj.classid = 'CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95'; " +
            "obj.name = 'Player'; " +
            "obj.id = 'Player'; " +
            "obj.width = '721'; " +
            "obj.height = '480'; " +
            "obj.codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,02,902'; " +
            "var param = [ " +
            "['FileName',''], " +
            "['src','" + langUrl + "'], " +
            "['quality','high'], " +
            "['wmode','transparent'], " +
            "['bgcolor','#FFFFFF'], " +
            "['ShowStatusBar','1'], " +
            "['pluginspage','http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/'], " +
            "['AUTOSTART','1'], " +
            "] " +
            "obj.param = param; " +
            "daumActiveX(obj,'EPlayer'); " +
            "</script>";

        $('.movie').html(html);
    });

    $('.snsShareBtn').hover(
        function() {
            $('.snsShareContainer').fadeIn();
        }, function() {
            $('.snsShareContainer').fadeOut();
        }
    );



    $('.snsShare').click(function(){
        var fullUrl;
        var url = "http://gbs.com/" + $(this).attr("url_string");
        var image = "http://img.naver.net/static/newsstand/up/2012/1119/nsd11502601.gif";
        var title = "GBS";
        var summary = "";

        var pWidth = 640;
        var pHeight = 380;
        var pLeft = (screen.width - pWidth) / 2;
        var pTop = (screen.height - pHeight) / 2;

        if($(this).hasClass("facebook") == true){
            fullUrl = "http://www.facebook.com/share.php?s=100&p[url]="+ url
                +"&p[images][0]="+ image
                +"&p[title]="+ title
                +"&p[summary]="+ summary;
            fullUrl = fullUrl.split("#").join("%23");
            fullUrl = encodeURI(fullUrl);
            window.open(fullUrl,"","width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

        } else if ($(this).hasClass("twitter") == true) {
            var wp = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + ". " + encodeURIComponent(summary) + " " + encodeURIComponent(url), "twitter", "width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

            if ( wp ) {
                wp.focus();
            }
        } else {
            var wp = window.open("https://plus.google.com/share?url=" + encodeURIComponent(url), "google+",",left="+ pLeft +",top="+ pTop +",menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");

            if ( wp ) {
                wp.focus();
            }
        }

    });


    $(".videoQualityBtn").hover(
        function() {
            $('.videoQualityContainer').fadeIn();
        }, function() {
            $('.videoQualityContainer').fadeOut();
        }
    );

    $(".videoLanguageBtn").hover(
        function() {
            $('.videoLanguageContainer').fadeIn();
        }, function() {
            $('.videoLanguageContainer').fadeOut();
        }
    );

    $('.livePopup').click(function(){
        var pLeft = (screen.width - 1000) / 2;
        var pTop = (screen.height - 518) / 2;
        window.open("/live/popup","","width=985,height=560,left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");
    });

    $('.livePopupBtn').live("click",function(){
        $(".livePopupBtn").removeClass("selected");
        var tabIndex = $('.livePopupBtn').index(this);
        $(this).addClass("selected");
        $('.livePopupContainer').css("display","none");
        $('.livePopupContainer').eq(tabIndex).fadeIn();
        if(tabIndex!=0){
            $(".videoQualityBtn").css("display","none");
        }else{
            $(".videoQualityBtn").css("display","");
        }
    });

    $('.popupContentLoad').bind("click",function(){
        var param       = 'mode=getContent&ctNO='+ $(this).attr("ctNO");
        $.post('/live/ajax_process', param, function(response){
            $('.movie').html(response);
        }, "json");
    });


    $('.popupProgramListLoad').bind("click",function(){
        var param       = 'mode=getProgramList';
        var i =0;

        $.post('/live/ajax_process', param, function(response){
            var pcSource = '<ul>';
            for(i=0; i<response.length; i++){
                pcSource += '<li class="popupProgramLoad" prCode="' + response[i]['prCode'] + '"> ' +
                    '<span class="time"> ' +
                    '<img src="/uploads/program/' + response[i]['prThumb'] + '" width="47" height="35" alt="" /> ' +
                    '</span> ' +
                    '<span class="txt mlt10">' + response[i]['prName'] + '</span> ' +
                    '</li>';
            }
            pcSource += '</ul>';

            $('.livePopupProgramContainer').html(pcSource);
        }, "json");
    });
});