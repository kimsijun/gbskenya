$(function(){
    $(".searchReProgram").click(function(){
        var Url = $(".searchUrl").val();
        var searchUrl = Url + "/prCode/" + $(this).attr("prCode");
        location.href = searchUrl;
    });

})
function setDate(obj,from,to)
{
    var obj = document.getElementsByName(obj);
    obj[0].value = (from) ? from : "";
    obj[1].value = (from) ? to : "";
}

function chkMainSearch() {
    if($(".mainSecTxt").val().length < 2){
        alert("Enter at least two characters for search.");
        return false;
    }
}

function searchResultSetLog(searchData){
    var param       = 'mode=searchResultSetLog&scName='+ searchData.attr('scName') + '&scLink=' + searchData.attr('href') + '&scSource=' + searchData.attr('scSource') + '&scType=' + searchData.attr('scType');
    $.post('/search/ajax_process', param, function(response){

    }, "json");
}
