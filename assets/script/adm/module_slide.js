function addSlideImg(){
    $(".slideImages").append("<div><button type='button' class='btn btn-default pull-right' onclick='delSlideImg($(this))' >제거</button><input type='file' name='siSlideImage[]'><br></div>");
}


function delSlideImg(btnDel){
    btnDel.parent().remove();
}