var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea, input[type=radio]';
var pw = $(window).width();
var ph = $(window).height();
var isMobile = false;
if (pw < 768) {
    isMobile = true;
}

var validMob = true;


    if (isMobile) {
        $(input_selector).focus(function () {
            var npos = $('body').scrollTop() + 40;
            $('body').animate({scrollTop: npos}, 100);
        });
    }
    
    $(input_selector).bind('focus',function() {
        $(this).siblings('label, i').addClass('labelActive');
        $(this).addClass('brOrange');//.removeClass('brRed');
        $('.bsText').addClass('op0');
    });

    $(input_selector).bind('blur',function() {
        if ($(this).val().length === 0 && $(this).attr('placeholder') === undefined) {
            $(this).siblings('label, i').removeClass('labelActive');
            $(this).removeClass('brOrange brGreen');//.addClass('brRed');
        } else {
            $(this).removeClass(' ').addClass('brGreen');
        }
    });

    $('#signupCancel').click(function () {
        $("input").val('');
        $("#isVendor").removeAttr('checked');
        window.history.back();
    });
    $('#signupSubmit').bind('click', function () {
        var pr_name = $('#pr_name').val();
        var pr_mobile = $('#pr_mobile').val();
        var pr_email = $('#pr_email').val();
        var pr_pass = $('#pr_pass').val();
        var isVendor = $('#isVendor').is(':checked');
        var amIVendor = $("input[type=checkbox]:checked").length;
        var userType =1;
        if(isVendor)
            isVendor=1;
        else 
            isVendor=-1;

		if(pr_mobile.length==10) {
			$.ajax({url: DOMAIN + "apis/index.php?action=checkUser&mobile=" + pr_mobile, success: function (result) {
				var obj = jQuery.parseJSON(result);
				var errCode = obj['error']['Code'];
				if(errCode == 1) {
					validMob = false;
				} else {
					validMob = true;
				}
			}});
		}

		setTimeout(function () {
			if(pr_name.length==0 || isNaN(pr_name)!==true) {
				customStorage.toast(0,'Invalide format for Name'); 
				$('#pr_name').focus();
				return false;
			} else if(pr_mobile=='' || pr_mobile.length!=10 || isNaN(pr_mobile)) {
				customStorage.toast(0,'Invalid format for Mobile'); 
				$('#pr_mobile').focus();
				return false;
			} else if(pr_email=='') {
				customStorage.toast(0,'Email is Required!'); 
				$('#pr_email').focus();
				return false;
			} else if(!common.validateEmail('pr_email')) {
				customStorage.toast(0,'Email is Not Valid!'); 
				$('#pr_email').focus();
				return false;
			} else if(pr_pass=='') {
				customStorage.toast(0,'Password is Required!'); 
				$('#pr_pass').focus();
				return false;
			} else if(!validMob) {
				customStorage.toast(0,'This mobile number is already registered!'); 
				$('#pr_mobile').focus();
				return false;
			}
			else if(amIVendor == undefined || amIVendor == 'undefined' || amIVendor == '' || amIVendor == null || amIVendor == 'null' || amIVendor == 0){
				customStorage.toast(0,'You have not Selected the type of user!'); 
				return false;
			} else {
					if(isVendor == -1)
					{
						userType = 0;
					}
				
				$.ajax({url: DOMAIN + "apis/index.php?action=userReg&username=" + pr_name +"&password=" + pr_pass +"&mobile=" + pr_mobile + "&email="+pr_email+'&isvendor='+userType, success: function (result) {
					var obj = eval('('+result+')');
					var errCode = obj.error.code;
					if(errCode == 0) {
						var userid = obj.userid;
						customStorage.addToStorage('isLoggedIn',true);
						customStorage.addToStorage('userid',userid);
						customStorage.addToStorage('mobile',pr_mobile);
						customStorage.addToStorage('username',pr_name);
						customStorage.addToStorage('is_vendor',isVendor);
						if(isVendor===1) {
							customStorage.removeFromStorage('busiType');
							window.location.assign(DOMAIN + 'index.php?case=vendor_Form&uid='+userid);
						} else {
							customStorage.toast(1,'Registration Successfull Done');
							//window.history.back();
							setTimeout(function () {window.location.assign(DOMAIN + 'index.php'); },1500);
						}
					} else {
						customStorage.toast(0,'Registration Unsuccessfull');
					}
				}});
			}
		}, 250);
    });
    
function isValidMKey(evt, id) {
    var val = $('#' + id).val();
    var len = val.length;
    var match = /^[7-9]/g;
    var val = val.match(match);
    if (val == null) {
        $('#' + id).val('');
    }
}

$(".amIVendor").change(function () {
    $(".amIVendor").not(this).prop('checked', false);
});