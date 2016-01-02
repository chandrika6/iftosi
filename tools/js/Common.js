/* global DOMAIN */

var common = new Common();
common.checkLogin();

function Common() {
    var _this = this;

    this.validateEmail = function (email) {
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (!email.match(mailformat)) {
            return 1;
        }
        return 0;
    };

    this.isNumberKey = function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        else if (charCode == 13) {
            return false;
        }
        return true;
    };

    this.validateEmail = function (id) {
        var email = $('#' + id).val();
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };

    this.checkMobile = function (id) {
        var num = $('#' + id).val();
        var len = num.length;
        if ((num.charAt(0) == '9') && (len == 10) || (num.charAt(0) == '8') && (len == 10) || (num.charAt(0) == '7') && (len == 10)) {
            return true;
        } else {
            this.toast(0,'Please Enter Correct Mobile Number.')
            return false;
        }
    };
    function isValidMKey(evt, id) {
        var val = $('#' + id).val();
        var len = val.length;
        var match = /^[7-9]/g;
        var val = val.match(match);
        if (val == null) {
            $('#' + id).val('');
        }
        if (len == 10) {
            $('#tf_email').focus();
        }
    }
    this.checkLogin = function () {
        var isLoggedIn = customStorage.readFromStorage('isLoggedIn');
        var mob = customStorage.readFromStorage('tf_mobile');
        var uid = customStorage.readFromStorage('userid');
        var nm = customStorage.readFromStorage('username');
        var is_vendor = customStorage.readFromStorage('is_vendor');
        var type = customStorage.readFromStorage('busiType');
        var isComp = customStorage.readFromStorage('isComp');

		if(is_vendor != '' && (is_vendor == -1 || is_vendor == '-1'))
		{
			is_vendor = 0;
		}

        if(is_vendor == 1)
        {
            if((type != null) && (type != undefined))
            {
                type=type.charAt(0);
                switch(type)
                {
                    case '1':var catid=10000;
                    break;
                    case '2':var catid=10001;
                    break;        
                    case '3':var catid=10002;
                    break;
                    default: break;
                }
            }
        }
        var userMenuStr='';
        if (isLoggedIn === 'true') {
            if(nm !== undefined || nm !== 'undefined' || nm !== null || nm !== 'null' || nm !== '')
            {
                $('.signInUpTab').html('Hello ' + nm.split(' ')[0]).addClass('loggedIn');
            }
            $('#userMenu').removeClass('dn');

            if (is_vendor == 0)
            {
                //userMenuStr += '<li class="transition100">Profile</li>';
                //userMenuStr += '<li class="transition100">Orders</li>';
                //userMenuStr += '<li class="transition100" onclick="redirectToWishlist();">Wishlist (<span id="wishListCnt"></span>)</li>';
                $('#wishListCnt').removeClass('dn');

				$('#usrNm').html(nm.split(' ')[0]);

				$('#wshlstPg').removeClass('dn');
				$('#lgotPg').removeClass('dn');
				$('#usrNm').removeClass('dn');
            }
            else if (is_vendor == 2)
            {
                userMenuStr += '<li id="productstab" class="transition100" onclick="return showProductList();">Products</li>';
                userMenuStr += '<li id="vendorlist" class="transition100" onclick="return showProductList();">Vendor List</li>';
                //userMenuStr += '<li class="transition100" onclick="redirectToWishlist();">Wishlist (<span id="wishListCnt"></span>)</li>';
                $('#wishListHdr').addClass('dn');

				$('#usrNm').html(nm.split(' ')[0]);

				$('#wshlstPg').addClass('dn');
				$('#lgotPg').addClass('dn');
				$('#usrNm').addClass('dn');
            }
            else
            {
                $('#wishListCnt').removeClass('dn');
                //userMenuStr += '<li class="transition100" onclick="window.location.assign(\''+DOMAIN+'index.php?case=vendor_dashboard\');">Dashboard</li>';
                if(isComp === '2')
                {
                    var catid1=customStorage.readFromStorage('busiType').charAt(0);
                    catid=parseInt(catid1)-1;
                    userMenuStr += '<li class="transition100" onclick="window.location.assign(\''+DOMAIN+'index.php?case=vendor_landing&catid=1000'+ catid+'\');">Products</li>';
                    userMenuStr += '<li class="transition100" onclick="window.location.assign(\''+DOMAIN+'index.php?case=vendor_enquiries\');">Enquiry</li>';
                    //userMenuStr += '<li id="profileHeader1" class="transition100">Profile</li>';
                    userMenuStr += '<li id="profileHeader1" class="transition100" onclick="return showVendorProfile();">Profile</li>';
                    
                }
                else
                {
                    userMenuStr += '<li class="transition100" onclick="window.location.assign(\''+DOMAIN+'index.php?case=vendor_Form&uid='+uid+'\');">Profile</li>';
                }


				$('#usrNm').html(nm.split(' ')[0]);
				$('#lgotPg').removeClass('dn');
				$('#usrNm').removeClass('dn');
            }
			$('#lgnPg').addClass('dn');
            userMenuStr += '<li class="transition100" onclick="common.doLogout();">Log Out</li>';
            $('#hdropList').html(userMenuStr);
			_this.getWishListCount();
        }
        else
        {
            $('#wishListCnt').addClass('dn');
			$('#lgnPg').removeClass('dn');
			$('#wshlstPg').removeClass('dn');
        }
    };
        $(document).click(function (e) {
           
            if($('#hdropList').is(":visible"))
            {   
                console.log('1');
                hisOpen = false;
                $('#hdropList').hide();
            }
        });

    this.doLogout = function () {
        customStorage.removeAll();
		if(pageName !== undefined && pageName !== null && pageName !== '' && typeof pageName !== 'undefined')
		{
			if(pageName == 'wishlist')
			{
				window.location.href = DOMAIN + "index.php";
			}
			else
			{
				window.location.href = window.location;
			}
		}
		else
		{
			window.location.href = DOMAIN + "index.php";
		}

		$('#wishListHdr').removeClass('dn');
    };
    this.closeLoginForm = function () {
        $('#loginDiv').velocity({scale: 0,borderRadius:'100%'}, {delay: 0, ease: 'swing'});
        $('#overlay1').velocity({opacity: 0}, {delay: 100, ease: 'swing'});
        setTimeout(function () {
            $('#overlay1,#loginDiv').addClass('dn');
            $("#loginDiv,#overlay1").remove();
        }, 1010);
    }
    this.showLoginForm = function (vd) {
        var str = '<div id="overlay1" class="overlay transition300" style="opacity: 0;" onclick="common.closeLoginForm();"></div>';
        str += '<div id="loginDiv" class="loginDiv transition300" style="transform: scale(0);">';
        str += '<div class="lgTitle fLeft fmOpenR">One account. All about Diamonds</div>';
        str += '<div class="inputCont fLeft fmOpenR">';
        str += '<input type="tel" id="pr_mobile" name="pr_mobile" autocomplete="off" class="txtInput cOrange fmOpenR font14 mobileIcon" readonly>';
        str += '<label for="pr_mobile" class="inp-label transition100">MOBILE OR EMAIL</label>';
        str += '<div id="pr_mobile_inpText" class="inpText fRight transition300">enter<br>mobile number or email</div>';
        str += '</div>  ';
        str += ' <div class="inputCont fLeft fmOpenR">';
        str += '<input type="password" id="pr_pass" name="pr_pass" autocomplete="off" readonly maxlength="10" onKeyPress="common.eSubmit(event,\'lgSubmit\');" class="txtInput cOrange fmOpenR font14 passwordIcon">';
        str += '<label for="pr_pass" class="inp-label transition100">PASSWORD</label>';
        str += '<div id="pr_pass_inpText" class="inpText fRight transition300">enter your<br>password</div>';
        str += '</div>  ';
        str += '<div class="cancelLgBtn fLeft fmOpenR" id="lgCancel" onclick="common.closeLoginForm();"> CANCEL</div>';
        str += '<div class="loginBtn fLeft fmOpenR" id="lgSubmit" onclick="common.submitLoginForm('+vd+');">LOGIN</div>';
        str += '<div class="signuplink fmOpenB fLeft">';
        str += '<center class="w50 fLeft"><a href="'+ DOMAIN +'index.php?case=forgot" style="color:#171334;"><span>Forgot Password?</span></a></center>';
        str += '<center class="w50 fLeft"><a href="'+ DOMAIN +'index.php?case=signup"><span>Sign Up with Us</span></a></center>';
        str += '</div>';
        str += '</div>';
        $('body').append(str);
        $('input').bind('focus',function(){$(this).attr('readonly',false);});
        $('input[type=tel], input[type=password]').bind('focus',function() {
            $(this).siblings('label, i').addClass('labelActive');
            $(this).addClass('brOrange');//.removeClass('brRed');
            $('.bsText').addClass('op0');
        });
        
        $('input[type=tel], input[type=password]').bind('blur',function() {
            if ($(this).val().length === 0 && $(this).attr('placeholder') === undefined) {
                $(this).siblings('label, i').removeClass('labelActive');
                $(this).removeClass('brOrange brGreen');//.addClass('brRed');
            } else {
                $(this).removeClass(' ').addClass('brGreen');
            }
        });

        $('#loginDiv').velocity({scale: 0, borderRadius:'100%'}, {delay: 0, duration: 0});
        $('#overlay1').velocity({opacity: 0}, {delay: 0, duration: 0});
        var mobile = customStorage.readFromStorage('mobile');
        var name = customStorage.readFromStorage('name');
        var email = customStorage.readFromStorage('email');
        var isLoggedIn = customStorage.readFromStorage('isLoggedIn');
        if (isLoggedIn == undefined || isLoggedIn == '' || isLoggedIn == null || isLoggedIn == false)
        {
            $('#overlay1,#loginDiv').removeClass('dn');
            setTimeout(function () {
                $('#overlay1').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
                $('#loginDiv').velocity({scale: 1,borderRadius:'2px'}, {delay: 80, duration: 100, ease: 'swing'});
            }, 10);
        }
        else
        {

        }
    }
    this.submitLoginForm = function (vd) {
        var pr_mobile = $('#pr_mobile').val();
        var pr_pass = $('#pr_pass').val();
        if (pr_mobile == '') {
            customStorage.toast(0, 'Mobile Number Should Not Be Empty');
            $('#pr_mobile').focus();
            return;
        } else if (pr_pass == '') {
            customStorage.toast(0, 'Login Password Should Not Be Empty');
            $('#pr_pass').focus();
            return;
        } else {
            $.ajax({url: DOMAIN + "apis/index.php?action=logUser&mobile=" + pr_mobile + "&password=" + pr_pass, success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if (errCode == 0) {
                        var userid = obj['results']['uid'];
                        var username = obj['results']['username'];
                        var is_vendor = obj['results']['utype'];
                        var email = obj['results']['email'];
                        var isComp = obj['results']['isC'];
                        var pass_flag = obj['results']['pass_flag'];
                        var mobile = obj['results']['mobile'];
                        console.log(mobile);

                        customStorage.addToStorage('isLoggedIn', true);
                        customStorage.addToStorage('l', mobile);
                        customStorage.addToStorage('mobile', mobile);
                        customStorage.addToStorage('name', username);
                        customStorage.addToStorage('email', email);
                        customStorage.addToStorage('isComp', isComp);
                        customStorage.addToStorage('userid', userid);
                        customStorage.addToStorage('username', username);

                        var tmp_is_vendor = is_vendor;
                        if (is_vendor == 0 || is_vendor == "0")
                        {
                            tmp_is_vendor = -1;
                        }
                        customStorage.addToStorage('is_vendor', tmp_is_vendor);
                        if (pass_flag != 0) {
                            window.location.assign(DOMAIN + 'index.php?case=changepwd');return;
                        }
                        if (is_vendor == 1)
                        {
                            var busiType = obj['results']['busiType'];
                            customStorage.addToStorage('busiType', busiType);
                            if (isComp === '2')
                            {
                                var catid = parseInt(busiType.charAt(0)) - 1;
                                window.location.assign(DOMAIN + 'index.php?case=vendor_landing&catid=1000' + catid);
                            }
                            else
                            {
                                window.location.assign(DOMAIN + 'index.php?case=vendor_Form&uid=' + userid);
                            }
                        }
                        else if (is_vendor == 2)
                        {
                            window.location.assign(DOMAIN + 'index.php?case=product_list');
                        }
                        else
                        {
                            customStorage.removeFromStorage('busiType');
                            _this.checkLogin();
                            _this.closeLoginForm();

                            _this.changeStyle('all');

                            if (vd)
                            {
                                var dthis = $('#userSubmit');
                                showVendorDetails();
                                $('#userForm').velocity({scale: 0}, {delay: 0, ease: 'swing'});
                                $('#overlay').velocity({opacity: 0}, {delay: 100, ease: 'swing'});
                                setTimeout(function () {
                                    $('#overlay,#userForm').addClass('dn');
                                }, 1010);
                            }
                        }
                    } else {
                        customStorage.toast(0, 'Invalid Login Credentials');
                    }
                }});
        }
    }
    this.eSubmit = function (evt, btnId) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode === 13) {
            $('#' + btnId).click();
        }
    };

	this.validate_mobile = function(mobno) {
		var mobExp = /^[7,8,9]{1}[0-9]{9}$/;
		var flag = true;
		if(mobno == '' || mobno == null || mobno == undefined) {
			flag = false;
		} else if(mobExp.test(mobno) == false) {
			flag = false;
		}

		return flag;
	};

	this.validate_email = function(email, isFortigo) {
		var flag = true;
		if(email == '' || email == null || email == undefined) {
			flag = false;
		} else if(isFortigo && email.indexOf('4tigo') == -1) {
			flag = false;
		} else {
			flag = _this.isValidEmail(email);
		}
		return flag;
	};

	this.isValidEmail = function (email) {
		var flag = true;
		var atpos = email.indexOf("@");
		var dotpos = email.lastIndexOf(".");
		var isSpace = email.indexOf(" ");

		if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.length || isSpace != -1) {
			flag = false;
		}

		return flag;
	};

	this.changeStyle = function(fr)
	{
		if(fr == 'all')
		{
			if($('#ur_mobile') !== undefined && $('#ur_mobile') !== null && $('#ur_mobile') !== 'undefined' && typeof $('#ur_mobile') !== 'undefined')
			{
				$('#ur_mobile').addClass('mobileIcon');
				$('#ur_mobile').addClass('brOrange');
				$('#ur_mobile').addClass('brGreen');
				$('#ur_mobile').siblings('label').addClass('labelActive');
			}

			if($('#ur_name') !== undefined && $('#ur_name') !== null && $('#ur_name') !== 'undefined' && typeof $('#ur_name') !== 'undefined')
			{
				$('#ur_name').addClass('nameIcon');
				$('#ur_name').addClass('brOrange');
				$('#ur_name').addClass('brGreen');
				$('#ur_name').siblings('label').addClass('labelActive');
			}

			if($('#ur_email') !== undefined && $('#ur_email') !== null && $('#ur_email') !== 'undefined' && typeof $('#ur_email') !== 'undefined')
			{
				$('#ur_email').addClass('emailIcon');
				$('#ur_email').addClass('brOrange');
				$('#ur_email').addClass('brGreen');
				$('#ur_email').siblings('label').addClass('labelActive');
			}
		}
		else if (fr == 'mobile')
		{
			if($('#ur_mobile') !== undefined && $('#ur_mobile') !== null && $('#ur_mobile') !== 'undefined' && typeof $('#ur_mobile') !== 'undefined')
			{
				$('#ur_mobile').addClass('mobileIcon');
				$('#ur_mobile').addClass('brOrange');
				$('#ur_mobile').addClass('brGreen');
				$('#ur_mobile').siblings('label').addClass('labelActive');
			}
		}
		else if(fr == 'name')
		{
			if($('#ur_name') !== undefined && $('#ur_name') !== null && $('#ur_name') !== 'undefined' && typeof $('#ur_name') !== 'undefined')
			{
				$('#ur_name').addClass('nameIcon');
				$('#ur_name').addClass('brOrange');
				$('#ur_name').addClass('brGreen');
				$('#ur_name').siblings('label').addClass('labelActive');
			}
		}
		else if(fr == 'email')
		{
			if($('#ur_email') !== undefined && $('#ur_email') !== null && $('#ur_email') !== 'undefined' && typeof $('#ur_email') !== 'undefined')
			{
				$('#ur_email').addClass('emailIcon');
				$('#ur_email').addClass('brOrange');
				$('#ur_email').addClass('brGreen');
				$('#ur_email').siblings('label').addClass('labelActive');
			}
		}
	};

	this.getWishListCount = function() {
		var uid = customStorage.readFromStorage('userid');

		if(uid !== undefined && uid !== null && uid !== '')
		{
			var params = 'action=ajx&case=getWishListCount&userid='+encodeURIComponent(uid);
			var URL = DOMAIN + "index.php";
			$.get(URL, params, function(data) {
				if(data !== undefined && data !== null && data !== '')
				{
					$('#wishListCnt').html(' (' + data + ')');
					$('#lftWshCnt').html(data);
					$('#lftWshCnt').removeClass('dn');
				}
				else
				{
					$('#wishListCnt').html('(' + 0 + ')');
					$('#lftWshCnt').html(0);
					$('#lftWshCnt').removeClass('dn');
				}
			});
		}
	};

	this.goToUrl = function (url) {
		if(url !== undefined && url !== null && url !== '')
		{
			window.location.href = url;
		}
	};

	this.goToWishlist = function() {
		var isLoggedIn = customStorage.readFromStorage('isLoggedIn');
		var is_vendor = customStorage.readFromStorage('is_vendor');
		var uid = customStorage.readFromStorage('userid');

		if(is_vendor !== undefined && is_vendor !== null && typeof is_vendor !== 'undefined' && is_vendor !== '')
		{
			if(is_vendor == -1 || is_vendor == '-1')
			{
				is_vendor = 0;
			}

			if(is_vendor == 0)
			{
				window.location.href = DOMAIN + 'wishlist/' + uid;
			}
		}
		else
		{
			window.location.href = DOMAIN + '?case=login';
		}
	};
        
    this.IND_money_format = function(money)
    {
	var m = '';
	money = money.toString().split("").reverse();
	var len = money.length;
	for(var i=0;i<len;i++)
	{
		if(( i == 3 || (i > 3 && ( i - 1) % 2 == 0) ) && i !== len)
		{
			m += ',';
		}
		m += money[i];
	}

	return m.split("").reverse().join("");
    };    

	this.showCertificate = function(url)
	{
		var win = window.open(url, '_blank');
		win.focus();
	}
}
function showVendorProfile()
{
    var uid = customStorage.readFromStorage('userid');
    window.location.href=DOMAIN+'index.php?case=vendor_Form&uid='+uid;
}
function showProductList()
{
    var uid = customStorage.readFromStorage('userid');
    var is_vendor = customStorage.readFromStorage('is_vendor');
    if(is_vendor == 2)
    {
        $('#productstab').bind('click',function(){
        window.location.href=DOMAIN+'index.php?case=product_list&uid='+uid;
        });
        $('#vendorlist').bind('click',function(){
        window.location.href=DOMAIN+'index.php?case=vendorList&uid='+uid;
        });
    }
}

