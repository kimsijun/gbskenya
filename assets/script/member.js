$(function(){
	/*  페이스북 Feed Post 인증 받아올때 URL에 '#_=_' 가 붙어 오는 버그 없애기 위해서     */
	if (window.location.hash == '#_=_') {
	    window.location.hash = '';
	    history.pushState('', document.title, window.location.pathname);
	}

    $.validator.addMethod('withdraw', function(mbID) {
        var postURL = "/member/withdraw_validate";
        $.ajax({
            cache:false,
            async:false,
            type: "POST",
            data: "mbID=" + mbID,
            url: postURL,
            success: function(msg) {
                result = (msg=='TRUE') ? true : false;
            }
        });
        return result;
    }, 'User account is withdrew already.');


    $(".frmMemberJoin").validate({
        rules:{
            mbID:{
                required:true
                ,email:true
                ,remote:"/member/remote_validate"
            }
            ,mbPW:{
                required:true
                ,minlength:6
                ,maxlength:12
            }
            ,mbName:"required"
            ,mbNick:{
                required:true
                ,remote:"/member/remote_validate"
            }
            ,mbPhone:"required"
            ,mbCellPhone:{
                required:true
                ,digits:true
            }
            ,mbPWChk:{
                equalTo:".mbPW"
            }
        }
    });

    $(".frmMemberMod").validate({
        rules:{
            mbID:{
                required:true
                ,email:true
                ,remote:"/member/remote_validate"
            }
            ,mbPW:{
                minlength:6
                ,maxlength:12
            }
            ,mbName:"required"
            ,mbNick:{
                required:true
                ,remote:"/member/remote_validate"
            }
            ,mbPhone:"required"
            ,mbCellPhone:{
                required:true
                ,digits:true
            }
            ,mbPWChk:{
                equalTo:".mbPW"
            }
        }
    });
    
    $(".placeholderTxt").focus(function(){
        //$(this).val('');
    });
    
    $('.joinSubmit').click(function(){
        if( $('input:checkbox[name="ckPrivacy"]').is(":checked")){
            if(confirm('Would you like to sign up?'))$(".frmMemberJoin").submit();
        }else if(! $('input:checkbox[name="ckPrivacy"]').is(":checked")) {
            alert('Please, Check the GBS Terms of Service and Privacy Policy.');
        }
    });

    $('.modsubmit').click(function(){
        if(confirm('Would you like to modify?')) $(".frmMemberMod").submit();
    });

    $('.wdSubmit').click(function(){
        if(confirm('Would you like to withdraw?')) $(".frmMemberWithdraw").submit();
    });

    $(".login-pw").bind('keypress',function(e){
        if(e.keyCode == 13 && !e.shiftKey){
            $(".frmlogin").submit();
        }
    });
    
    $(".removeFBInfoBtn").live("click", function(){
        var param = 'mode=removeFBInfo';
        var logoutUrl = $(".mbFBLogoutUrl").val();
        $.post('/member/process', param, function(response){
            if(logoutUrl)
                location.href = logoutUrl;
        }, "json");
    });

    $(".removeTWInfoBtn").live("click", function(){
        var param = 'mode=removeTWInfo';
        $.post('/member/process', param, function(response){
            location.href="/member/view";
        }, "json");
    });

    $(".recoverySubmit").live('click',function(){
        var param = $(".frmRecovery").serialize();
        var html = "";

        $.post('/member/ajax_process',param,function(response){
            if(response['preoption']=='1'){
                if(response['data'] && response['Email']){
                    html += '<div class="profile" active="true" token="recovery/profile" style="">';
                    html += '<span class="Ar">Is this you?</span>';
                    html += '<div class="Cr">';
                    html += '<span class="FM"><img src="/uploads/member/thumb/'+ response['data']['mbThumb'] + '"';
                    html += 'width="60" height="60"></span>';
                    html += '<span class="hn">'+ response['data']['mbFirstName'] + ' '+ response['data']['mbLastName'] + '</span>';
                    html += '<span class="Dr">'+ response['data']['mbID'] + '</span>' ;
                    html += '</div>';
                    html += '</div>';
                    html += '<form method="post" class="frmRecovery">';
                    html += '<input type="hidden" value="'+ response['data']['mbID'] +'" name="mbID">';
                    html += '<input type="hidden" value="sendEmailFrm" name="mode">';
                    html += '<p class="DM">';
                    html += '<input class="Xh gn rcSubmit" type="button" value="Yes, continue."  >';
                    html += '<input class="Xh recoveryBack" type="button" value="No, it\'s not me." >';
                    html += '</p>';
                    html += '</form>'
                    $(".recovery").html(html);
                }else if(!response['data'] && response['Email']){
                    html += 'This user account does not exist.';
                    $(".errormsg").html(html);
                }else {
                    html += 'Please enter a valid email address.';
                    $(".errormsg").html(html);
                }
            }else if(response['preoption']=='2'){
                html += '<span class="Ar">Forgot your user account?</span>';
                html += '<form method="post" class="frmRecovery">';
                html += '<input type="hidden" name="mode" value="recoveryUserName">';
                html += '<label class="stacked-label" for="fu_first" style="display:block;margin-bottom:10px;"><strong>Enter the name on the account</strong></label>';
                html += '<div >';
                html += '<span style="display:block;margin-bottom:30px;">';
                html += '<input id="fu_first" class="realname placeholder-text" type="text" name="mbFirstName" placeholder="First Name">'+" ";
                html += '<input id="fu_last" class="realname placeholder-text" type="text" name="mbLastName" placeholder="Last Name"><br/><br/>';
                html += '<input id="fu_nick" class="realname placeholder-text" type="text" name="mbNick" placeholder="Nick Name">';
                html += '<div class="errormsg"></div>';
                html += '</span>';
                html += '<input class="Xh gn rcSubmit" type="button" value="Submit.">';
                html += '</p>';
                html += '</div>';
                html += '</form>';
                $(".recovery").html(html);
            }else if(response['preoption']=='3'){
                html += '<span style="display: block;">Please enter the problem.</span>';
                html += '<form method="post" class="frmRecovery">';
                html += '<input type="hidden" name="mode" value="errorReport">';
                html += '<input type="hidden" value="'+ response['Email2'] +'" name="Email">';
                html += '<textarea name="errorReport" style="display:block;width:300px;height:100px; margin:10px 0;"></textarea>';
                html += '<input class="Xh gn rcSubmit" type="button" value="Submit.">';
                html += '</form>';
                $(".recovery").html(html);
            }


        }, 'json');
    });

    $(".recoveryBack").live('click',function(){
        location.href="/member/recovery";
    });

    $(".rcSubmit").live('click',function(){
        var param = $(".frmRecovery").serialize();
        var html = "";
        $.post('/member/ajax_process',param,function(response){
            if(response['mode']=='recoveryUserName'){
                if(response['mbFirstName'] && response['mbLastName'] && response['mbNick']){
                	if(response['data']){
                    html += '<span class="Ar">Your User account is as follows.</span>';
                    html += '<span style="margin-bottom:10px;display: block;">' + response['data']['mbAccount'] + '</span>';
                    html += '<p class="recovery-submit">' +
                        '<input type="button" value="go to Sign-in" class="button g-button g-button-submit " style="cursor:pointer;" onclick="location.href=\'/member/login\';">' +
                        '</p>';
                    $(".recovery").html(html);
                    }else{
                    	alert('Not Exist.')
                    }
                }else if(!response['mbFirstName']){
                    html += 'Please enter the FirstName.';
                    $(".errormsg").html(html);
                }else if(!response['mbLastName']){
                    html += 'Please enter the LastName.';
                    $(".errormsg").html(html);
                }else if(!response['mbNick']){
                    html += 'Please enter the NickName.';
                    $(".errormsg").html(html);
                }
            } else if(response['mode']=='sendEmailFrm'){
                html += '<span class="Ar">Do you receive a temporary password?</span>';
                html += '<span style="margin-bottom:10px;display: block;">'+ response['mbID'] +'</span>';
                html += '<form method="post" class="frmRecovery">';
                html += '<input type="hidden" name="mode" value="emailPassword">';
                html += '<input type="hidden" value="'+ response['mbID'] +'" name="mbID">';
                html += '<input class="Xh gn emailSubmit" type="button" value="continue.">';
                html += '</form>';
                $(".recovery").html(html);
            } else if(response['mode']=='errorReport'){
                alert('Registered.');
                location.href="/";
            }
        }, 'json');
    });

    $(".emailSubmit").live('click',function(){
        var param = $(".frmRecovery").serialize();
        $.post('/member/ajax_process',param,function(response){
            if(response['mode']=='emailPassword'){
                alert(response['result']);
                location.href="/";
            }
        }, 'json');
    });
    
    $(".mbMod").live('click',function(){
        var param = $(".frmmod").serialize();
        var html = "";

        $.post('/member/ajax_process',param,function(response){
            if(response['mode']=='mbMod'){
                html += '<div class="mbAgain">';
                html += '<p class="mb10">Enter your password again.</p>';
                html += '<form method="post" action="/member/modify" name="frm">';
                html += '<input type="hidden" name="mbID" value="'+response['mbID']+'">';
                html += '<input type="hidden" class="mbFBLogoutUrl" value="'+response['mbFBLogoutUrl']+'">';
                html += '<input type="password" name="mbPW" size="40">&nbsp;&nbsp;&nbsp;';
                html += '<span class="btn-mini" onclick="document.frm.submit();">Enter</span>';
                html += '</form> ';
                html += '</div>';
                $(".mypagelistbox").html(html);
            }
        }, 'json');
    });

    $(".mbWithdraw").live('click',function(){
        var param = $(".frmwithdraw").serialize();
        var html = "";

        $.post('/member/ajax_process',param,function(response){
            if(response['mode']=='mbWithdraw'){
                html += '<div class="mbAgain">';
                html += '<p>Enter your password again.</p>';
                html += '<form method="post" action="/member/withdraw" name="frm">';
                html += '<input type="hidden" name="mbID" value="'+response['mbID']+'">';
                html += '<input type="hidden" class="mbFBLogoutUrl" value="'+response['mbFBLogoutUrl']+'">';
                html += '<input type="password" name="mbPW" size="40">&nbsp;&nbsp;&nbsp;';
                html += '<span class="btn-mini" onclick="document.frm.submit();">Enter</span>';
                html += '</form> ';
                html += '</div>';
                $(".mypagelistbox").html(html);
            }
        }, 'json');
    });

    var reason = $(':radio[name="mbWdReason"]:checked').val();
    if(reason == 4) $(".mbWdReasonTxt").css('display','block');
    else $(".mbWdReasonTxt").css('display','none');

    $('input:radio[name="mbWdReason"]').live("click", function(){
        var reason = $(':radio[name="mbWdReason"]:checked').val();
        if(reason == 4) $(".mbWdReasonTxt").css('display','block');
        else $(".mbWdReasonTxt").css('display','none');
    });

    $(".datepicker").datepicker({
        changeYear:true,
        changeMonth:true,
        yearRange: '1960:2014',
        dateFormat: 'yy-mm-dd',
        defaultDate:'1980-01-01'
    });
});

jQuery.validator.addMethod('usernameCheck', function(username) {
    var postURL = "user/json_username_check";
    $.ajax({
        cache:false,
        async:false,
        type: "POST",
        data: "username=" + username,
        url: postURL,
        success: function(msg) {
            result = (msg=='TRUE') ? true : false;
        }
    });
    return result;
}, '');


function recoveryOptionSelected() {
    var radioOptions = document.getElementsByName("preoption");
    for (var i = 0; i < radioOptions.length; ++i) {
        var confirmBox = document.getElementById("hideable-box" + radioOptions[i].id);
        if (confirmBox) {
            if (radioOptions[i].checked) {
                confirmBox.style.height = confirmBox.scrollHeight + 'px';
            } else {
                confirmBox.style.height = '0px';
            }
        }
    }
}