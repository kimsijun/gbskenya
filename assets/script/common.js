var i;

$(function(){
	$(".viewTab > li").click(function(){
        $(".viewTab > li").removeClass('on');
        var tabIndex = $(".viewTab > li").index(this);
        $(".viewContainer").css("display", "none");
        $(".viewContainer").eq(tabIndex).css("display", "block");
        $(this).addClass('on');
    });
	
    $(".movie-selectbox4 > dl").click(function(){
        var tabIndex = $(".movie-selectbox4 > dl").index(this);
        $(".viewContainer").css("display", "none");
        $(".viewContainer").eq(tabIndex).css("display", "block");
    });
    
    $(".movie-selectbox > dl").click(function(){
        var tabIndex = $(".movie-selectbox > dl").index(this);
        $(".viewContainer").css("display", "none");
        $(".viewContainer").eq(tabIndex).css("display", "block");
    });

    $(".tabMenu > li").click(function(){
        $(".tabMenu > li").removeClass("selected");
        var tabIndex = $(".tabMenu > li").index(this);
        $(this).addClass("selected");
        $(".tabContainer").css("display", "none");
        $(".tabContainer").eq(tabIndex).css("display", "block");
    });

    $(".ctSearchTab > li").click(function(){
        var tabIndex = $(".ctSearchTab > li").index(this);
        $(".ctSearchArea").css("display", "none");
        $(".ctSearchArea").eq(tabIndex).css("display", "block");
    });

    $(".prSearchTab > li").click(function(){
        var tabIndex = $(".prSearchTab > li").index(this);
        $(".prSearchArea").css("display", "none");
        $(".prSearchArea").eq(tabIndex).css("display", "block");
    });

    $(".ReportTxt").bind("click",function(){
        if($(this).val()=='불편하거나 개선하고 싶은 사항이 있으면 입력하세요.')
            $(this).val("");
    });

    $(".programAllbtn").bind("click",function(){
        $(".programNavi").fadeIn();
        $(".programNavi_bg").fadeIn();
    });

    $(".prcloseBtn").click(function(){
        $(".programNavi").fadeOut();
        $(".programNavi_bg").fadeOut();
    });

    $(".relationContent0").css("display","block");
    $(".relativeMore0").css("display","block");
    $(".programResult0").css("display","block");
    $(".prSearchMore0").css("display","block");
    $(".ctViewResult0").css("display","block");
    $(".ctRecentResult0").css("display","block");
    $(".ctLikeResult0").css("display","block");
    $(".ctSearchMore0").css("display","block");

    $(".uncheck_all").bind("click",function() {
        $(".checker span").removeClass("checked");
        $(".chChildren input:checkbox").attr("checked", false);
    });

    $(".datepicker").focus(function(){
        $(this).datepicker({
            dateFormat: "yy-mm-dd"
        });
    });

    $('.tabContainer').eq(0).css("display","block");
    
    /*$('.top-menu > span').hover(
        function(){
            var idx = $(".top-menu > span").index(this);
            $('.top-menu > span > a > img').eq(idx).attr('src','/images/common/top_menu'+(idx+1)+'_on.gif');
        },function(){
            var idx = $(".top-menu > span").index(this);
            if(!$('.top-menu > span').eq(idx).hasClass('on')){
                $('.top-menu > span > a > img').eq(idx).attr('src','/images/common/top_menu'+(idx+1)+'.gif');
            }

        });*/
    $(".home").click(function(){
        $(this).attr('href','/');
    });

    $(".copySAdd").click(function(){
        var url = $('input[name="sAddress"]').val();
        var IE=(document.all)?true:false;
        if (IE) {
            if(confirm("Would you like to copy address to the clipboard?"))
                window.clipboardData.setData("Text", url);
        } else {
            temp = prompt("Press Ctrl + C to copy into your clipboard", url);
        }
    });

    $(".check_all").toggle(
        function() {
            $("input:checkbox").attr("checked", true);
        },
        function(){
            $("input:checkbox").attr("checked", false);
        }
    );
});

function chkTopSearch() {
    if($(".secTxt").val().length == 0){
        return false;
    }else if($(".secTxt").val().length < 2){
        alert("Enter at least two characters for search.");
        return false;
    }
}

function loginCheck(str){
    var param="mode=logincheck";
    $.post('/member/ajax_process',param,function(response){
         if(response!='true'){
             if(confirm('Would you like to log in?')){
                 if(str){
                     $('input[name="returnUrl"]').val(str);
                 }
                 $('.commonLogin').submit();
             }
         }else{
             location.href=str;
         }
    }, 'json');
}






