var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea, input[type=radio]';
var uid = customStorage.readFromStorage('userid');
var submiter = true;
var tagArray = new Array();
var nameArray = new Array();

if (vid !== uid || uid == '') {
    window.location.assign(DOMAIN);
}
$(document).ready(function ()
{
   /* if(tagCloud1 !== undefined && tagCloud1 !== null)
    {
        var allBankNames = tagCloud1.split(',');
        var strBankName = '';
        $('#allBankNames').val(tagCloud1);
        $.each(allBankNames,function(i,val)
        {
            if(val !== '')
            {
                var valz = val.split(" ").join("_");
	            var valz1 = val.split(" ");
                strBankName += "<div id='acc_name_" +valz+ "' class='tagcloud txtOver indNm fLeft'>" + valz + "</div>";
                tagArray.push("acc_name_"+valz);
            }
        });
        $('#bankNames').html(strBankName);
        $('#bankNames').removeClass('dn');
        bindTags();
    }
*/

    var pay = parseInt($('#payammo').val());
    $('#payingamt').html('&#8377 '+pay);
    $('#payammo2').html('&#8377 '+pay);

    if ($('#cperson').val() == '')
    {
        $('#cperson').val(customStorage.readFromStorage('username'));
    }
    if ($('#conMobile').val() == '')
    {
        $('#conMobile').val(customStorage.readFromStorage('mobile'));
    }
//                $('#forDiamond,#forJewellery,#forBullion').removeClass('comSelected');
//                $('#bullionDet,#jewelleryDet,#diamondDet').addClass('dn');

    var tmstmp = new Date().getTime();
    $.ajax({url: APIDOMAIN + "index.php?action=statusChecker&uid="+uid+"&timestamp"+tmstmp, success: function (result)
    {
        var obj = eval('('+result+')');
        var errCode = obj.error.code;
        if(errCode == '0')
        {
            var af = obj.result;
            if(af !== undefined && af !== 'undefined' && af !== null && af !== 'null' && af !== '')
            {
                if(af == 'no')
                {
                    $('.compComm').click( function(e)
                    {

                            var x = (document.all) ? event.x : e.pageX;
                            var y = (document.all) ? event.y : e.pageY;

                            setTimeout(function () {
                                x = e.pageX = e.pageX + 100;
                                y = e.pageY = e.pageY + 100;
                            }, 1000);

                            var id = $(this).attr('id');
                            $(this).toggleClass('comSelected');
                            if (id == 'forDiamond') {
                                $('#diamondDet').toggleClass('dn');
                            }
                            else if (id == 'forJewellery') {
                                $('#jewelleryDet').toggleClass('dn');
                                $('#bullionDet').toggleClass('dn');
                                }

                                var finalAmt = 0;
                                $('.compComm').each(function () {
                                    var tmpId = $(this).attr('id');
                                    if($(this).hasClass('comSelected'))
                                    {
                                        if(tmpId == 'forDiamond')
                                        {
                                            finalAmt += 30000;
                                        }
                                        else if(tmpId == 'forJewellery')
                                        {
                                            finalAmt += 25000;
                                        }
                                    }
                                });
                                pay = finalAmt;
                            $('#payingamt').html('&#8377 '+pay);
                            $('#payammo2').html('&#8377 '+pay);
                            $('#payammo').val(pay);

                    });


                }
                else if(af == 'yes')
                {
                    $('.compComm').bind('click',function () {
                        common.toast(0,'You are not allowed to change your plan once your profile is activated');
                    });
                }
            }
        }
        if(errCode == 1)
        {
            common.toast(0,'You are not allowed to access this page');
        }
    }
    });


    //


    $('#pan').bind('keyup', function () {
		if($(this) !== undefined && typeof $(this) !== 'undefined' && $(this).val() !== undefined && typeof $(this).val() !== 'undefined')
		{
			$(this).val($(this).val().toUpperCase());
		}
	});
});
function fnValidatePAN() {
    var Obj = $conv('#pan').val();
    var str = '';

}
function validateForm() {
    var orgname = $('#orgname').val().trim();
    var fulladd = $('#fulladd').val().trim();
    var pincode = $('#pincode').val();
    var area = $('#area').val().trim();
    var city = $('#city').val().trim();
    var state = $('#state').val().trim();
    var wbst = $('#wbst').val().trim();
    var pancard = $('#pan').val().trim();
    var vatno = $('#vat').val().trim();
   // var banker = '';
   // var totalBanker = $('.tagcloud').length;
   // var ifscs = $('#ifsc').val().split(',');
    var str = "V02554544";
    var patt1 = /^(C|V|c|v){1}([0-3]){2}([0-9]){2}/g;
    var result = str.match(patt1);
    var panPat = /^([A-Z]|[a-z]){5}([0-9]){4}([A-Z]|[a-z]){1}?$/;
    var code = /([C,c,P,p,H,h,F,f,A,a,T,t,B,b,L,l,J,j,G,g,K,k])/;
    var code_chk = pancard.substring(3, 4);
   // var ifscPattern    = /^[A-Z|a-z]{4}[0]([A-Z]|[a-z]|[0-9]){6}$/;
    var str = '';
    //var duplicateIfscs = find_duplicates(ifscs);
	  var isValid = true;

    /*$('.indNm').each( function()
    {
       banker += $(this).text()+',';
    });*/

    if (orgname == '')
    {
        str = 'Organization Name is Required\n';
        $('#orgname').focus();
		isValid = false;
    }

    if (isValid && str == '' && fulladd == '')
    {
        str = 'Address is Required';
        $('#fulladd').focus();
		isValid = false;
    }

    if (isValid && str == '' && (pincode == '' || pincode.length < 6 || isNaN(pincode)))
    {
        str = 'Pincode is Required';
        $('#pincode').focus();
		isValid = false;
    }

    if (isValid && str == '' && area == '')
    {
        str = 'Area is Required';
        $('#area').focus();
		isValid = false;
    }

    if (isValid && str == '' && city == '')
    {
        str = 'City is Required';
        $('#city').focus();
		isValid = false;
    }

    if (isValid && str == '' && state == '')
    {
        str = 'State is Required';
        $('#state').focus();
		isValid = false;
    }

	if (isValid && vatno !== '') {
        if (isValid && vatno.length == 12)
		{
            var vat_pattr = /^([0-9]){11}(C|V|c|v){1}/g;
            //var vat_pattr1 = /([0-9]*$)/;
            //var vatLtNo = vatno.substring(3, 11);
			if (vat_pattr.test(vatno) == false) {
                str = 'Invaild VAT No.';
                $('#vat').focus();
				isValid = false;
            }
        } else if(isValid) {
            str = 'Invaild VAT Number';
            $('#vat').focus();
			isValid = false;
        }
    }
    else if (isValid && (vatno == undefined || vatno == null || vatno == ''))
	{
        str = 'VAT Number Required';
        $('#vat').focus();
		isValid = false;
    }

	if (isValid && str == '' && pancard === '')
    {
        str = 'Pancard is mandatory';
        $('#pan').focus();
		isValid = false;
    }

    if(isValid && str == '' && pancard.match(panPat) == null)
    {
        str = 'Invalid PAN card number';
        $('#pan').focus();
		isValid = false;
    }

    if (isValid && str == '' && pancard.search(panPat) == -1) {
        str = 'Invalid PAN card number';
        $('#pan').focus();
		isValid = false;
    }

    if (isValid && str == '' && code.test(code_chk) == false) {
        str = 'Invalid PAN card number';
        $('#pan').focus();
		isValid = false;
    }

	//banker = banker.trim();

  /*if(isValid && (banker == undefined || banker == 'undefiend' || banker == null || banker == 'null' || banker == '' || banker == ' '))
	{
        str = 'Banker is empty';
        $('#banker').focus();
		isValid = false;
  }

  if(isValid && (banker == undefined || banker == 'undefiend' || banker == null || banker == 'null' || banker == '' || banker == ' '))
	{
        str = 'Banker is empty';
        $('#banker').focus();
		    isValid = false;
  }

  if(totalBanker > 0)
  {
      if($('#ifsc').val() == '')
      {
          str = 'IFSC is empty';
          $('#ifsc').focus();
          isValid = false;
      }
      if(totalBanker !== ifscs.length)
      {
          str = 'IFSC codes mismatch with bankers field';
          $('#ifsc').focus();
          isValid = false;
      }
      if(duplicateIfscs.length > 0)
      {
          console.log(duplicateIfscs);
          str = 'IFSC codes have duplicate entries';
          $('#ifsc').focus();
          isValid = false;
      }
      if($('#ifsc').val() !== '' && ifscs.length > 0 && totalBanker == ifscs.length)
      {
          $(ifscs).each(function(i,val)
          {
              if(isValid && ifscPattern.test(val) == false || val.length < 7)
              {
                  str = 'IFSC code is incorrect at position '+(i+1);
                  $('#ifsc').focus();
          		    isValid = false;
              }
          });
      }
  }*/
  if (isValid && str == '' && !$("#forDiamond").hasClass("comSelected") && !$("#forJewellery").hasClass("comSelected") && !$("#forBullion").hasClass("comSelected"))
	{
        str = 'Select business type';
		isValid = false;
    }

    if (isValid && str == '')
    {
        if (wbst !== '')
        {
            if (!common.validateUrl('wbst'))
            {
                str  = "Please enter proper website url";
				isValid = false;
            }
        }
    }

    if (isValid == false && str !== '')
    {
        common.toast(0, str);
        return false;
    }
    else
    {
        return true;
    }
}

$("#pincode").keyup(function() {

    var pincode = $('#pincode').val();
    if (!pincode) {
        $('#area').val('');
        $('#city').val('');
        $('#state').val('');
    }
});

$("#area").keyup(function() {

    var area = $('#area').val();
    if (!area) {
        $('#pincode').val('');
        $('#city').val('');
        $('#state').val('');
    }
});

$("#area").focus(function() {
        $('#citySuggestDiv').addClass('dn');
        $('#stateSuggestDiv').addClass('dn');
});
$("#city").focus(function() {
        $('#areaSuggestDiv').addClass('dn');
        $('#stateSuggestDiv').addClass('dn');
});

$("#state").focus(function() {

        $('#areaSuggestDiv').addClass('dn');
        $('#citySuggestDiv').addClass('dn');
        $('#stateSuggestDiv').addClass('dn');
});
$("#state").focusout(function() {
        $('#areaSuggestDiv').addClass('dn');
        $('#citySuggestDiv').addClass('dn');
        $('#stateSuggestDiv').addClass('dn');
});

function find_duplicates(arr) {
  var len=arr.length,
      out=[],
      counts={};

  for (var i=0;i<len;i++) {
    var item = arr[i];
    counts[item] = counts[item] >= 1 ? counts[item] + 1 : 1;
  }

  for (var item in counts) {
    if(counts[item] > 1)
      out.push(item);
  }

  return out;
}

function validateStep2Form() {
    var str = 'This Field is Required';
    if (/1/.test(busiType)) {
        var memcert = $('#memcert').val().trim();
        //var bdbc = $('#bdbc').val();
        //var othdbaw = $('#othdbaw').val();
        var ofcity = $('#ofcity').val().trim();
        var ofcountry = $('#ofcountry').val().trim();
        if (memcert == '') {
            $('#memcert').focus();
            common.toast(0, 'Enter GJEPC Membership Certificate');
            return false;
        }
        /*
        else if (bdbc == '') {
            common.toast(0, 'Enter Bharat Diamond Bourse Certificate');
            $('#bdbc').focus();
            return false;
        }
        else if (othdbaw == '') {
            common.toast(0, 'Enter Details Of Membership Of Other Diamond Bourse Around The World');
            $('#othdbaw').focus();
            return false;
        }
        else if (ofcity == '') {
            common.toast(0, 'Enter Offices In other Cities');
            $('#ofcity').focus();
            return false;
        }
        else if (ofcountry == '') {
            common.toast(0, 'Enter Offices In other Countries');
            $('#ofcountry').focus();
            return false;
        }
        */
    }
    if (/2/.test(busiType)) {
       var showroomname = $('#showroomname').val().trim();
        var showroomno = $('#showroomno').val().trim();
        var mdbw = $('#mdbw').val().trim();
        if (showroomname == undefined || showroomname == 'undefined' || showroomname == null || showroomname == 'null' || showroomname == '') {
            $('#showroomname').focus();
            common.toast(0, 'Show Room Name is Required');
            return false;
        }
        /*else if (showroomno == '' || isNaN(showroomno) || showroomno == 0 || showroomno === '0') {
            common.toast(0, 'Enter Number of Showrooms');
            $('#showroomno').focus();
            return false;
        }*/
        else if (mdbw == '') {
            common.toast(0, 'Membership Of Council / Jewellers Association is Required');
            $('#mdbw').focus();
            return false;
        }
    }
    if (/3/.test(busiType)) {
        var bul_mdbw = $('#bul_mdbw').val().trim();
        if (bul_mdbw == '') {
            common.toast(0, 'Membership Of Council / Jewellers Association is Required');
            $('#bul_mdbw').focus();
            return false;
        }
    }
    if (!(/1/.test(busiType)) && !(/2/.test(busiType)) && !(/3/.test(busiType))) {
        common.toast(0, 'Select business type');
        return false;
    }
    return true;
}

function validateStep3Form() {
    var cperson = $('#cperson').val().trim();
    var position = $('#position').val().trim();
    var conMobile = $('#conMobile').val().trim();
    var email = $('#email').val().trim();
    var landline = $('#landline').val().trim();
    var str = '';
    if (cperson == '') {
        common.toast(0, 'Contact Person Name is Required');
        $('#cperson').focus();
        return false;
    }
    else if (position == '') {
        common.toast(0, 'Position is Required');
        $('#position').focus();
        return false;
    }
    if (!common.checkMobile('conMobile')) {
        $('#conMobile').focus();
        return false;
    }
    var altmbNo1_Mobile = $('#altmbNo1_Mobile').val();
    var altmbNo2_Mobile = $('#altmbNo2_Mobile').val();
    if (altmbNo1_Mobile!='' || altmbNo2_Mobile!='') {
        if(conMobile==altmbNo1_Mobile) {
            $('#altmbNo1_Mobile').val('').focus();
            common.toast(0, 'Please enter unique mobile number.');
            return false;
        } else if(altmbNo1_Mobile==altmbNo2_Mobile) {
            $('#altmbNo1_Mobile').val('').focus();
            common.toast(0, 'Please enter unique mobile number.');
            return false;
        } else if(conMobile==altmbNo2_Mobile) {
            $('#altmbNo2_Mobile').val('').focus();
            common.toast(0, 'Please enter unique mobile number.');
            return false;
        }
    }
    var llNoArr = new Array;
    var llUnique=true;
    $('.txtCCode').each(function () {
        var llNo = $(this).val() + '' + $(this).siblings('.lnNo').val();
        if (llNo != '') {
            if (llNoArr.indexOf(llNo) != -1) {
                llUnique = false;
            }
            llNoArr.push(llNo);
        }
    });
    if(!llUnique) {
        common.toast(0, 'Please enter unique Landline number.');
        return false;
    }
    if (!common.validateEmail('email')) {
        common.toast(0, 'Contact Email is Required');
        $('#email').focus();
        return false;
    }
    if (!common.checkLandline('lnCode', 'landline')) {
		return false;
	}
    return  true;
}

function clickThis(id, isDirect) {
    var obj = document.getElementById(id);

	if(id == 'step1')
	{
		changeTab('step1');
	}
    else if (id == 'step2' && validateForm()) {
                if(submiter == true)
                {
                    submiter = false;
                    submitForm(obj);
                    $('html,body').animate({ scrollTop: 0 }, 'slow');

                }
                else
                {
                   common.toast(0, 'Please wait Submit is in process!');
                }
    }
    else if (id == 'step3') {
		if(isDirect !== undefined && isDirect !== null && isDirect !== '')
		{
			if(validateForm())
			{
                            if(submiter == true)
                            {
                                submiter = false;
				submitForm();
				setTimeout(function() {
                                    var isValidForm2 = validateStep2Form();
                                    if(isValidForm2)
                                    {
                                            submitStep2Form(obj);
                                            $('html,body').animate({ scrollTop: 0 }, 'slow');
                                    }
                                }, 800);
                                submiter = true;
                            }
                            else
                            {
                                common.toast(0, 'Please wait Submit is in process!');
                            }
			}
		}
		else
		{
			var isValidForm2 = validateStep2Form();
			if(isValidForm2)
			{
                            if(submiter == true)
                            {
                                submiter = false;
				submitStep2Form(obj);
				$('html,body').animate({ scrollTop: 0 }, 'slow');
                                submiter = true;
                            }
                            else
                            {
                                common.toast(0, 'Please wait Submit is in process!');
                            }
			}
		}
    }
    else if (id == 'step4' && validateStep3Form()) {
                if(submiter == true)
                {
                    submiter = false;
                    submitStep3Form(obj);
                    submiter = true;
                }
                else
                {
                    common.toast(0, 'Please wait Submit is in process!');
                }
    }
}

function submitForm() {
    var complete = customStorage.readFromStorage('isComp');
    if(complete !== '2' && complete !== 2)
    {
        customStorage.removeFromStorage('isComp');
    }
    var val = new Array("orgname", "fulladd", "add1", "pincode", "area", "city", "state", "vat", "pan", "tovr", "wbst");
    var data = new Object;
    var res = formatData(val);
    var payamt = $('#payammo').val();
    var busiTypeVal = new Array;
    if ($("#forDiamond").hasClass("comSelected")) {
        busiTypeVal.push(1);
    }
    if ($("#forJewellery").hasClass("comSelected")) {
        busiTypeVal.push(2);
    }
    if ($("#forJewellery").hasClass("comSelected")) {
        busiTypeVal.push(3);
    }
    payamt = payamt.trim();
    busiType = busiTypeVal.join(',');
    res['busiType'] = busiType;
    res['country'] = country;
    res['lng'] = lng;
    res['lat'] = lat;
    res['uid'] = uid;
    data['result'] = res;
    var area  = res.area;
    var city  = res.city;
    var state = res.state;
    var fulladdress  = res.fulladd;
    var pincode     =  res.pincode;
    var tmstmp = new Date().getTime();
    $.ajax({url: common.APIWebPath() + "index.php?action=checkArea&fulladd="+encodeURIComponent(fulladdress)+"&area="+encodeURIComponent(area)+"&state="+encodeURIComponent(state)+"&city="+encodeURIComponent(city)+"&pincode="+encodeURIComponent(pincode)+"&timestamp="+tmstmp, success: function (result)
    {
        var obj = jQuery.parseJSON(result);
        var errCode = obj.results;
        if (errCode.status == 'Success')
        {
                    data = JSON.stringify(data);
                    $.ajax({url: common.APIWebPath() + "index.php?action=udtProfile&payamt="+payamt+"&lat="+errCode.lat+"&lng="+errCode.lng+"&dt=" + encodeURIComponent(data)+"&timestamp="+tmstmp, success: function (result) {
                            var obj = jQuery.parseJSON(result);
                            customStorage.addToStorage('busiType', busiType);
                            var errCode = obj['error']['code'];
                            var errMsg = obj['error']['msg'];
                            if (errCode == 0) {
                                $('#showroomname').val(res['orgname']);
                                //common.toast(1, errMsg);
                                                changeTab('step2');
                            } else {
                                common.toast(0, errMsg);
                            }
                        }});
        submiter = true;
        }
        else
        {
            common.toast(0,'Please mention the correct area location');
        }
    }
    });
}

function submitStep2Form() {
    var res = new Object;
    var data = new Object;
    var mdbw = '';
    if (/1/.test(busiType)) {
        var val = new Array('memcert', 'bdbc', 'othdbaw', 'ofcity', 'ofcountry');
        res = formatData(val);
    }
    if (/2/.test(busiType)) {
        res['showroomname'] = $('#showroomname').val();
        res['showroomno'] = $('#showroomno').val();
        mdbw = $('#mdbw').val();
    }
    if (/3/.test(busiType)) {
        mdbw += '~' + $('#bul_mdbw').val();
    }
    if (mdbw != '') {
        mdbw = mdbw.replace(/~*$/, "");
        res['mdbw'] = mdbw;
    }
    res['uid'] = uid;
    data['result'] = res;

    data = JSON.stringify(data);
    var complete = customStorage.readFromStorage('isComp');
    if(complete !== '2' && complete !== 2)
    {
        customStorage.addToStorage('isComp', 1);
    }
    else
    {
        customStorage.addToStorage('isComp', 2);
    }
    var tmstmp = new Date().getTime();
    $.ajax({url: common.APIWebPath() + "index.php?action=udtProfile&isC=1&dt=" + encodeURIComponent(data)+"&timestamp="+tmstmp, success: function (result) {
            var obj = jQuery.parseJSON(result);
            var errCode = obj['error']['code'];
            var errMsg = obj['error']['msg'];
            if (errCode == 0) {
                //common.toast(1, errMsg);
                changeTab('step3');
            } else {
                common.toast(0, errMsg);
            }
        }});
    submiter = true;
}

function submitStep3Form() {
    var val = new Array('cperson', 'position', 'email');
    var data = new Object;
    var res = formatData(val);
    var conMobile = $('#conMobile').val();
    if (altmbNo1_Mobile != '') {
        res['cmobile'] = conMobile;
    }
    var altmbNo1_Mobile = $('#altmbNo1_Mobile').val();
    if (altmbNo1_Mobile != '') {
        res['alt_cmobile'] = altmbNo1_Mobile;
    }
    var altmbNo2_Mobile = $('#altmbNo2_Mobile').val();
    if (altmbNo2_Mobile != '') {
        res['alt_cmobile'] += '|~|' + altmbNo2_Mobile;
    }

    /*
     var landline = $('#landline').val();
     var lnCode = $('#lnCode').val();
     if(landline!='' && lnCode!='') {
     res['landline'] = lnCode + '-' + landline;
     }
     var landline1 = $('#landline1').val();
     var lnCode1 = $('#lnCode1').val();
     if(landline1!='' && lnCode1!='') {
     res['landline'] += '~'+lnCode1 + '-' + landline1;
     }
     var landline2 = $('#landline2').val();
     var lnCode2 = $('#lnCode2').val();
     if(landline2!='' && lnCode2!='') {
     res['landline'] += '~'+lnCode2 + '-' + landline2;
     }
     var landline3 = $('#landline3').val();
     var lnCode3 = $('#lnCode3').val();
     if(landline3!='' && lnCode3!='') {
     res['landline'] += '~'+lnCode3 + '-' + landline3;
     }
     */

    res['landline'] = common.getLandlineNo();
    res['uid'] = uid;
    data['result'] = res;
    data = JSON.stringify(data);
    var tmstmp = new Date().getTime();
    $.ajax({url: common.APIWebPath() + "index.php?action=udtProfile&isC=2&dt=" + encodeURIComponent(data)+"&timestamp="+tmstmp, success: function (result) {
            var obj = jQuery.parseJSON(result);
            var errCode = obj['error']['code'];
            var errMsg = obj['error']['msg'];
            if (errCode == 0) {
                common.toast(1, errMsg);
                var isComp = 2;
                customStorage.addToStorage('isComp', isComp);

                var bsType = parseInt(busiType.charAt(0));
                bsType = bsType - 1;
                var tmstmp = new Date().getTime();
              /*  if(bsType>0)
                {
                     alert("here");
                    $('#dollarRateSpan').html('').addClass('dn');
                }*/
                customStorage.addToStorage('username', $('#cperson').val());
                console.log(customStorage.readFromStorage('username'));
                $.ajax({url: common.APIWebPath() + "index.php?action=viewAll&uid="+res['uid']+"&timestamp="+tmstmp,success: function (result)
                {
                    var obj = jQuery.parseJSON(result);
                    var isactive = obj.results[1]['active_flag'];

                    if(isactive == 1 || isactive == '1')
                    {
                        setTimeout(function() {
                            window.location.href = 'index.php?case=vendor_landing&catid=1000' + bsType;
                        },800);
                    }
                    else if(isactive == 0 || isactive == '0')
                    {
                        var pr_name = customStorage.readFromStorage('username');
                        var pr_mobile = customStorage.readFromStorage('mobile');
                        var pr_email = customStorage.readFromStorage('email');
                        var isVendor = customStorage.readFromStorage('is_vendor');
                        var uid = customStorage.readFromStorage('userid');
                        $.ajax({url: DOMAIN + "apis/index.php?action=sendWelcomeMailSMS&username="+pr_name +"&mobile="+pr_mobile +"&email="+pr_email +"&isVendor="+isVendor, success: function (result) {
                            var obj = eval('('+result+')');
                            var errCode = obj.error.code;
                            if(errCode == 0)
                            {
                                customStorage.toast(1,'Registration Successfully Done');
                                setTimeout(function () {window.location.href = 'index.php?case=inactive_vendor&uid=' + uid },2500);
                            }
                        }});
                    }
                }});
            } else {
                common.toast(0, errMsg);
            }
        }});

    submiter = true;

}

function formatData(val) {
    var data = new Object;
    for (var i = 0; i < val.length; i++)
    {
        data[val[i]] = $('#' + val[i]).val();
    }
    var bankers = '';
    $('.indNm').each( function()
    {
       bankers += $(this).text()+',';
    });
    data['banker'] = bankers;
    return data;
}
function changeTab(id) {
    var obj = document.getElementById(id);
    common.changeTab(obj);
}

var country = '';
var lng = '';
var lat = '';
function loadAreaList() {
    var pincode = $('#pincode').val();
    var city = $('#city').val();
    if (pincode.length == 6)
    {
        $.ajax({url: common.APIWebPath() + "index.php?action=viewbyPincode&city="+city+"&code=" + pincode, success: function (result)
                {
                    var obj = jQuery.parseJSON(result);
                    //var results = new Object;
                    var results = obj['results'];
                    var errCode = obj.error.code;

                    if (results !== null && results !== '' && results[0]['city'] !== null && results[0]['city'] !== undefined && results[0]['area'] !== null && results[0]['area'] !== undefined && results[0]['state'] !== null && results[0]['state'] !== undefined)
                    {
                        areaList = results;
                        $('#area').val(results[0]['area']);
                        $('#city').val(results[0]['city']);
                        $('#state').val(results[0]['state']);
                        country = results[0]['country'];
                        lat = results[0]['latitude'];
                        lng = results[0]['longitude'];
                    }
                    else if(results == null && errCode == 1)
                    {
                        common.toast(0,'There is no as such Pincode in the city you have mentioned');
                    }
                }
            });
    }
}
loadAreaList();
$('#pincode').keyup(function () {
    loadAreaList();
});


var pincode1;
$(document).ready(function () {
pincode1 = $('#pincode').val();
    $('#pincode').bind('keyup keydown',function(event)
    {
        if(pincode1 == 'undefined' || pincode1 == null || pincode1 == undefined || pincode1 == 'null')
        {
            pincode1 = '';
        }
    });


    var msuggest = '';

    /* For suggestions of City */
    $('#area').bind('keyup focus', function(event)
    {
        if ($(this).attr('id') == 'area')
        {
            pincode1 = $('#pincode').val();
            msuggest = 'areaSuggestDiv';
            var params = '';
            if(pincode1.length == 0)
            {
                params = 'action=areaSuggest&area='+escape($(this).val());
            }
            else
            {
                params = 'action=areaSuggest&pincode='+pincode1+'&area='+escape($(this).val());
            }
            if($(this).val() !== '')
            {

                new Autosuggest($(this).val(), '#area', '#areaSuggestDiv', DOMAIN + "apis/index.php", params, true, '', '', event);
            }
            else
            {
                $('#areaSuggestDiv').addClass('dn');
            }
        }
    });

    $('#city').bind('keyup focus', function(event){
        if ($(this).attr('id') == 'city')
        {
            msuggest = 'citySuggestDiv';
            var params = 'action=citySuggest&name=' + escape($(this).val());
            if($(this).val() !== '')
            {
                new Autosuggest($(this).val(), '#city', '#citySuggestDiv', DOMAIN + "apis/index.php", params, true, '', '', event);
            }
            else
            {
                $('#citySuggestDiv').addClass('dn');
            }
        }
    });

    $('#state').bind('keyup focus', function(event)
    {
        if ($(this).attr('id') == 'state')
        {
            msuggest = 'stateSuggestDiv';
            var params = 'action=stateSuggest&name=' + escape($(this).val());
            if($(this).val() !== '')
            {
                new Autosuggest($(this).val(), '#state', '#stateSuggestDiv', DOMAIN + "apis/index.php", params, true, '', '', event);
            }
            else
            {
                $('#stateSuggestDiv').addClass('dn');
            }
        }
    });

    /*$('#banker').bind('keyup focus', function(event)
    {
        if ($(this).attr('id') == 'banker')
        {
            msuggest = 'bankerSuggestDiv';
            var params = 'action=bankSuggest';
            new Autosuggest($(this).val(), '#banker', '#bankerSuggestDiv', DOMAIN + "apis/index.php", params, true, '', '', event);
        }
    });*/

    $('body').bind('click',function() {
        $('#'+ msuggest).addClass('dn');
    });
});

function arrangeData(data, id, divHolder, nextxt)
{
    if (data.results)
    {
        var suggest = "<ul class='smallField fmRoboto font14 pointer border1 transition300' style='overflow-y:auto;'>";
        $.each(data.results, function(i, vl) {

            if (id == '#area' && (vl.n !== null && vl.n !== undefined && vl.n !== 'undefined' && vl.n !== 'null' && vl.n !== ''))
                suggest += "<li id='suggest" + i + "' class='autoSuggestRow transition300 txtCaCase txtOver' onclick='setAreaSuggestValue(\"" + vl.n + "\",\"" + vl.city + "\",\"" + vl.state + "\",\"" + vl.pincode + "\",\"area\");'>&nbsp;&nbsp;" + vl.n+"</li>";
            if (id == '#city')
                suggest += "<li id='suggest" + i + "' class='autoSuggestRow w100 transition300 txtCaCase' onclick='setCitySuggestValue(\"" + vl.n + "\",\"city\");'>&nbsp;&nbsp;" + vl.n + "</li>";
            if (id == '#state')
                suggest += "<li id='suggest" + i + "' class='autoSuggestRow w100 transition300 txtCaCase' onclick='setStateSuggestValue(\"" + vl.n + "\",\"state\")'>&nbsp;&nbsp;" + vl.n + "</li>";
            //if (id == '#banker')
                //suggest += "<li id='suggest" + i + "' class='autoSuggestRow w100 transition300 txtCaCase' onclick='setBankSuggestValue(\"" + vl.n + "\",\"banker\")' onkeyup='setBankSuggestValue(\"" + vl.n + "\",\"banker\")'>&nbsp;&nbsp;" + vl.n + "</li>";
        });
        suggest += "</ul>";
        return suggest;
    }
    else
        return '';
}

function setAreaSuggestValue(val,city,state,pin,id) {
    if(val !== undefined && val !== 'undefined' && val !== null && val !== 'null')
    {
        $('#'+id).val(val.trim());
    }
    if(city !== undefined && city !== 'undefined' && city !== null && city !== 'null')
    {
        $('#city').val(city.trim());
    }
    if(state !== undefined && state !== 'undefined' && state !== null && state !== 'null')
    {
        $('#state').val(state.trim());
    }
    if(pin !== undefined && pin !== 'undefined' && pin !== null && pin !== 'null')
    {
        $('#pincode').val(pin.trim());
    }
    setTimeout(function () {
        $('#areaSuggestDiv').addClass('dn');
    }, 50);
}

function setCitySuggestValue(val, id) {
    $('#'+id).val(val);
    setTimeout(function () {
        $('#citySuggestDiv').addClass('dn');
    }, 50);
}

function setStateSuggestValue(val, id) {
    $('#'+id).val(val);
    setTimeout(function () {
        $('#stateSuggestDiv').addClass('dn');
    }, 50);
}

function onlyAlphabets(evt, t) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode < 47)
    {
        return true
    }
    else if (charCode > 31 && (charCode < 48 || charCode > 57))
    {
        return true;
    }
    return false;
}

/*function setBankSuggestValue(showVal,id)
{
    if(showVal !== undefined && showVal !== 'undefined' && showVal !== null && showVal !== 'null')
    {
        $('#'+id).val(showVal.trim());
        addAttrValues('#banker');
    }
    setTimeout(function ()
    {
        $('#bankerSuggestDiv').addClass('dn');
    }, 50);
}*/


function addAttrValues(obj)
{
    var cloudValue = $(obj).val().trim(' ');
    var vals = cloudValue.trim(',');
    var avals = vals.split(" ").join("_");
    var txtid = "acc_name_"+avals;

    if (tagArray.indexOf(txtid) == -1 && vals.length!=0)
    {
        var str = "<div id='" + txtid + "' class='tagcloud indNm txtOver fLeft'>" + vals + "</div>";
        $('#bankNames').append(str);
        tagArray.push(txtid);
        nameArray.push(txtid.replace('acc_name_',''));
        $('#allBankNames').val(nameArray);
        bindTags();

        setTimeout(function()
        {
            $(obj).val('');
        }, 100);
    }
    else
    {
        setTimeout(function()
        {
            $(obj).val('');
        }, 100);
    }
}
function bindTags()
{
    $('.tagcloud').click(function()
    {
        var id = $(this).attr('id');
        console.log(id);
        var _th = this;
        setTimeout(function()
        {
            $(_th).remove();
            var removeItem = id;
            tagArray = jQuery.grep(tagArray, function(value)
            {
                return value !== removeItem;
            });
        }, 100);
    });
}
