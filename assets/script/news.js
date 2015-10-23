$(function(){

	$('.snsShare').click(function(){
        var fullUrl;
        //alert($(this).attr("uri_string"));
        var url = "http://gbskenya.com/" + $(this).attr("uri_string");
        var image = "http://img.naver.net/static/newsstand/up/2012/1119/nsd11502601.gif";
        var title = "GBS Kenya";
        var summary = "Hello. This is GBS in Kenya. ^ㅡ^";

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

        } else {
            var wp = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + ". " + encodeURIComponent(summary) + " " + encodeURIComponent(url), "twitter", "width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop +",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");

            if ( wp ) {
                wp.focus();
            }
        }
    });
    
   $(".newsPrint").click(function(){
       var printContents = $(".news_view").html();
       var originalContents = $(document.body).html();
       $(document.body).html(printContents);
       window.print();
       $(document.body).html(originalContents);
   });
});
