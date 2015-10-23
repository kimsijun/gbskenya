var num_id = 0;


$(function(){
    $(".popDtpicker").focus(function(){
        $(this).appendDtpicker({
            "minuteInterval": 15
        });
        setTimeout("newtest()", 100);

    });
});
function newtest() {
    $(".popDtpicker").next("div").css("top","30px");
}

function add_dt(){
    $("#evt_dt_frm").append(
        '<tr>'+
            '<td width="30px"><input type="checkbox" class="dtChk" name="dtChk[]" class="form-control" ></td>'+
            '<td><input type="text" name="dtName[]" class="form-control" ></td>'+
            '<td><input type="text" name="dtSTime[]" class="form-control popDtpicker"></td>'+
            '<td><input type="text" name="dtETime[]" class="form-control popDtpicker"></td>'+
            '<td><input type="text" name="dtVideo[]" class="form-control"></td>'+
            '<td><input type="text" name="dtMp3[]" class="form-control"></td>'+
            '</tr>');


    $(".popDtpicker").focus(function(){
        $(this).appendDtpicker({
            "minuteInterval": 15
        });
        setTimeout("newtest()", 100);
    });

}

//상세일정 삭제
function del_dt(){
    $('.dtChk:checked').each(function(){
        $(this).parent().parent("tr").remove();
    });
}

function add_dt_mod(){
    num_id = $("#evt_dt_list tr").length;

    $("#evt_dt_list").append(
        '<tr>'+
            '<td width="25px"><input type="checkbox" name="dtChk[]" class="form-control" ></td>'+
            '<td><input type="text" name="dtName[]" class="form-control" ></td>'+
            '<td><input type="text" name="dtSTime[]" class="form-control popDtpicker"></td>'+
            '<td><input type="text" name="dtETime[]" class="form-control popDtpicker"></td>'+
            '<td><input type="text" name="dtVideo[]" class="form-control"></td>'+
            '<td><input type="text" name="dtMp3[]" class="form-control"></td>'+
            '</tr>');
    num_id++;
    $(".popDtpicker").focus(function(){
        $(this).appendDtpicker({
            "minuteInterval": 15
        });
        setTimeout("newtest()", 100);
    });
}