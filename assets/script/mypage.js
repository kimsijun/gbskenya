$(function(){

    $(".mpL_del").bind("click",function(){
        if($("input:checkbox").is(":checked")){
            if(!$('.mbLoginId').val()){
                if(confirm('Would you like to log in?')){
                    $(".commonLogin").submit();
                }
            }else{
                if(confirm('Would you like to delete?')){
                    $(".frmMyPageLike").submit();
                }
            }
        }else{
            alert("Select one or more.")
        }
    });

    $(".mpF_del").bind("click",function(){
        if($("input:checkbox").is(":checked")){
            if(!$('.mbLoginId').val()){
                if(confirm('Would you like to log in?')){
                    $(".commonLogin").submit();
                }
            }else{
                if(confirm('Would you like to delete?')){
                    $(".frmMyPageFavor").submit();
                }
            }
        }else{
            alert("Select one or more.")
        }
    });

    $(".mpV_del").bind("click",function(){
        if($("input:checkbox").is(":checked")){
            if(!$('.mbLoginId').val()){
                if(confirm('Would you like to log in?')){
                    $(".commonLogin").submit();
                }
            }else{
                if(confirm('Would you like to delete?')){
                    $(".frmMyPageView").submit();
                }
            }
        }else{
            alert("Select one or more.")
        }
    });

    $(".mpT_del").bind("click",function(){
        if($("input:checkbox").is(":checked")){
            if(!$('.mbLoginId').val()){
                if(confirm('Would you like to log in?')){
                    $(".commonLogin").submit();
                }
            }else{
                if(confirm('Would you like to delete?')){
                    $(".frmMyPageTag").submit();
                }
            }
        }else{
            alert("Select one or more.")
        }
    });

    $(".check_all").toggle(
        function() {
            $("input:checkbox").attr("checked", true);
            $(".check_all").html('Deselect');
        },
        function(){
            $("input:checkbox").attr("checked", false);
            $(".check_all").html('Select all');
        }
    );

    var vcCnt = 0;
    $(".vcPage0").css("display","block");
    $(".page_more").click(function(){
        $(".vcPage ul").eq(++vcCnt).css("display","block");
    });

    var vpCnt = 0;
    $(".vpPage0").css("display","block");
    $(".page_more").click(function(){
        $(".vpPage ul").eq(++vpCnt).css("display","block");
    });

    var vnCnt = 0;
    $(".vnPage0").css("display","block");
    $(".page_more").click(function(){
        $(".vnPage ul").eq(++vnCnt).css("display","block");
    });

    var fcCnt = 0;
    $(".fcPage0").css("display","block");
    $(".page_more").click(function(){
        $(".fcPage ul").eq(++fcCnt).css("display","block");
    });

    var fpCnt = 0;
    $(".fpPage0").css("display","block");
    $(".page_more").click(function(){
        $(".fpPage ul").eq(++fpCnt).css("display","block");
    });
})