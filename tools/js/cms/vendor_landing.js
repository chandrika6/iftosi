var uid = customStorage.readFromStorage('userid');
var busiType = customStorage.readFromStorage('busiType');
var username = customStorage.readFromStorage('username');
var is_vendor = customStorage.readFromStorage('is_vendor');
var submiter2 = true;
var submiter3 = true;
var busiTypeSplt=0;
if(is_vendor == 1 || is_vendor === '1')
{
    $('#profileTab').removeClass('dn');
}
if(busiType==null || busiType=='' || busiType==undefined)
{
    window.location.assign(DOMAIN+'index.php?case=vendor_Form&uid='+uid);
}
var active = '';

document.getElementById('userName').innerHTML = username.split(' ')[0];
if ( catid == '10000')
{
   active = 'diamondFheader';
}
if ( catid == '10001')
{
    active = 'jewelleryFheader';
}
if ( catid == '10002')
{
    active = 'bullionFheader';
}


var activeVTab='';
switch(activePage)
{
    case 'vendor_landing':
        activeVTab ='prdTab';
    break;
    case 'vendor_enquiries':
        activeVTab ='enqTab';
    break;
    case 'pending':
        activeVTab ='pendingPrd';
    break;
    case 'rate_manager':
        activeVTab ='rate_m';
    break;
    default :
        activeVTab ='';
    break;
}


$('#'+activeVTab).addClass('vSelected');

var forDate;
$('#datepicker').datepicker({
    onSelect: function (dateText, inst)
    {
        var dateText = $.datepicker.formatDate("yy-mm-dd", new Date(dateText));
        $(this).val(dateText);
        $('#' + forDate).val(dateText);
        if (forDate === 'dateTo') {

        }
        $('#datepicker').addClass('dn');
    },
    maxDate: "+0D"
});

function showCalander(inpDiv)
{
    forDate = inpDiv;
    var pos = $('#' + inpDiv).position();
    var pTop = pos.top + 35;
    $('#datepicker').css({top: pTop + "px", left: 45 + "px"});
    $('#datepicker').removeClass('dn');
}

function checkToHide(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode === 8) {
        $('#datepicker').addClass('dn');
    }
}

var goldRate, silverRate, platinumRate, dollarRate = '';

var mxSc = 170;//$('.prdResults').offset().top;
var lastSc = 0;
var GtmpId='';

$(document).ready(function ()
{
    $.ajax({url: DOMAIN + "apis/index.php?action=activateVendor&user_id=" + uid, success: function (result)
        {
            var obj = jQuery.parseJSON(result);
            var status = obj.results.flag;
            if(status !== undefined && status !== 'undefined' && status !== null && status !== 'null' && status !== '')
            {
                if(status == 1)
                {
                    $('.vendorSt').text('Active Till '+obj.results.expiry);
                    $('.vendorSt').removeClass('vDactive dn');
                    $('.vendorSt').addClass('vActive');
                }
                else
                {
                    $('.vendorSt').text('Deactive');
                    $('.vendorSt').removeClass('vActive dn');
                    $('.vendorSt').addClass('vDactive');
                }
            }
        }
    });
    $('.vTabs').eq(1).click();
     busiTypeSplt = busiType.split(',');

    for(var i = 0; i< busiTypeSplt.length; i++)
    {
        if(busiTypeSplt[i] == 1)
        {
            $('#dollarRateSpan').append(dollarRate).removeClass('dn');
            $('#dmdTab').removeClass('dn');
        }
        if(busiTypeSplt[i] == 2)
        {
            $('#jewTab').removeClass('dn');
        }
        if(busiTypeSplt[i] == 3)
        {
            $('#bullTab').removeClass('dn');
            $('#goldRateSpan').append(goldRate).removeClass('dn');
            $('#silverRateSpan').append(silverRate).removeClass('dn');
            $('#platinumRate').append(platinumRate).removeClass('dn');
        }
        if(busiTypeSplt.length == 1 && busiTypeSplt[i] === 1)
        {
            $('#dollarRateSpan').append(dollarRate).removeClass('dn');
            $('#dmdTab').removeClass('dn');
            $('#dollarRateSpan').addClass('fRight');
            $('#dollarRateSpan').attr('style','border-right:0px');
        }
         if((busiTypeSplt.length == 1 || busiTypeSplt.length == 2 ) && (busiTypeSplt[i] === 2))
         {
            $('#dollarRateSpan').html('').addClass('dn');
         }
        if(busiTypeSplt.length == 1 && busiTypeSplt[i] === 3)
        {
            $('#goldRateSpan').append(goldRate).removeClass('dn');
            $('#silverRateSpan').append(silverRate).removeClass('dn');
            $('#platinumRateSpan').append(platinumRate).removeClass('dn');
            $('#bullTab').removeClass('dn');
            $('#silverRateSpan').addClass('fRight');
            $('#goldRateSpan').addClass('fRight');
            $('#platinumRateSpan').addClass('fRight');
            $('#silverRateSpan').attr('style','border-right:0px');
        }
    }

    $('#dmdTab,#jewTab,#bullTab').bind('click', function ()
    {
        var id = $(this).attr('id');
        switch (id)
        {
            case'dmdTab':
                active = 'diamondFheader';
                $('#diamondPrds').removeClass('dn');
                $('#jewelleryPrds,#bullionPrds').addClass('dn');
                break;
            case'jewTab':
                active = 'jewelleryFheader';
                $('#jewelleryPrds').removeClass('dn');
                $('#diamondPrds,#bullionPrds').addClass('dn');
                break;
            case'bullTab':
                active = 'bullionFheader';
                $('#bullionPrds').removeClass('dn');
                $('#diamondPrds,#jewelleryPrds').addClass('dn');
                break;
        }
    });

    $(window).scroll(function ()
    {
        var sc = $(window).scrollTop();
        if (sc > mxSc)
        {
            $('#' + active).removeClass('pLHtransit dn');
        }
        else if (lastSc > sc)
        {
            $('#' + active).addClass('pLHtransit');
        }
        lastSc = sc;
    });

    $('.vFilBtn').click(function ()
    {
        showEnqFilter();
    });

    $('.shapeComm').bind('click', function ()
    {
    		if(pageName == 'jewellery-Form')
    		{
              //			var uthis = $(this);
              //			$('.shapeComm').each(function () {
              //				if ($(this).hasClass('shapeSelected') && $(this).attr('id') != uthis.attr('id'))
              //					$(this).removeClass('shapeSelected');
              //			});
              //			$(this).toggleClass('shapeSelected');
              //			if($(this).hasClass('shapeSelected'))
              //			{
              //				$('.diamondProp').removeClass('dn');
              //			}
              //			else
              //			{
              //				$('.diamondProp').addClass('dn');
              //			}
		    }
    		else
    		{
      			var uthis = $(this);
      			$('.shapeComm').each(function ()
            {
      				    if ($(this).hasClass('shapeSelected') && $(this).attr('id') != uthis.attr('id'))
                  {
            					$(this).removeClass('shapeSelected');
                  }
      			});

            $(this).toggleClass('shapeSelected');
            if($(this).hasClass('shapeSelected'))
      			{
    				    $('#allcontent').removeClass('dn');
      			}
      			else
      			{
    				    $('#allcontent').addClass('dn');
      			}
        }
    });

    $('.jshapeComm').bind('click', function ()
    {
        var uthis = $(this);
        $('.jshapeComm').each(function ()
        {
            if ($(this).hasClass('shapeSelected') && $(this).attr('id') != uthis.attr('id'))
                $(this).removeClass('shapeSelected');
                $("input[type=checkbox]").prop("checked", false);
        });

        $(this).toggleClass('shapeSelected');
        if ($(this).hasClass('shapeSelected'))
        {
            var tmpId = $(this).attr('id');
            tmpId = tmpId.toLowerCase();
            GtmpId=tmpId;
            if (((tmpId == 'gbars') || (tmpId == 'gcoins')))
            {
                showGoldRateForm();
                $('#gold_rate').val(goldRate);
                $('#goldErr,.subCatType').removeClass('dn');
                $('#' + tmpId + 'Type,#allcontent,.allprop').addClass('dn');
            }
            else if (((tmpId == 'sbars') || (tmpId == 'scoins')))
            {
                showSilverRateForm();
                $('#gold_rate').val(silverRate);
                $('#silverErr,.subCatType').removeClass('dn');
                $('#' + tmpId + 'Type,#allcontent,.allprop').addClass('dn');
            }
            else if ((tmpId == 'pbars') || (tmpId == 'pcoins'))
            {
                showPlatinumRateForm();
                $('#gold_rate').val(platinumRate);
                $('#platinumErr,.subCatType').removeClass('dn');
                $('#' + tmpId + 'Type,#allcontent,.allprop').addClass('dn');
            }
            else
            {
                $('.subCatType').addClass('dn');
                $('#' + tmpId + 'Type').removeClass('dn');
                $('#allcontent').removeClass('dn');
            }
        }
        else
        {
            var tmpId = $(this).attr('id');
            tmpId = tmpId.toLowerCase();
            $('#' + tmpId + 'Type').addClass('dn');
            $('#allcontent').addClass('dn');
        }
        var bthis = $(this);

        if ($(this).hasClass('shapeSelected'))
        {
            var id = $(this).attr('id');
            var tmpId = id.toLowerCase();
            if ((tmpId == 'gbars') || (tmpId == 'gcoins'))
            {
                $('.allprop').addClass('dn');
                $('.goldprop').removeClass('dn');
                $('#silverpurity').val('');
                $('#silverweight').val('');
                if (tmpId == 'gbars')
                {
                    $('#goldweight').attr('placeholder','eg. Kgs Or Gms');
                }
                else
                {
                    $('#goldweight').attr('placeholder','eg. Gms');
                }
            }
            else if ((tmpId == 'sbars') || (tmpId == 'scoins'))
            {
                $('.allprop').addClass('dn');
                $('.silverprop').removeClass('dn');
                $('#silverpurity').val('999');
                $('#goldpurity').val('');
                $('#goldweight').val('');
                if (tmpId == 'sbars')
                {
                    $('#silverweight').attr('placeholder','eg. Kgs Or Gms');
                }
                else
                {
                    $('#silverweight').attr('placeholder','eg. Gms');
                }
            }
            else if ((tmpId == 'pbars') || (tmpId == 'pcoins'))
            {
                $('.allprop').addClass('dn');
                $('.platinumprop').removeClass('dn');
                $('#platinumpurity').val('999');
                $('#goldpurity').val('');
                $('#goldweight').val('');
                if (tmpId == 'pbars')
                {
                    $('#platinumweight').attr('placeholder','eg. Kgs Or Gms');
                }
                else
                {
                    $('#platinumweight').attr('placeholder','eg. Gms');
                }
            }
        }
        else
        {
            $('.goldprop').addClass('dn');
            $('.silverprop').addClass('dn');
            $('.allprop').removeClass('dn');
        }
    });

    $('#dollar_rate').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            updateDollarRate();
        }
    });

    $('#gold_rate').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            updateGoldRate();
        }
    });

    $('#gold_rate1').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            updateGoldSilverRate();
        }
    });

    $('#silver_rate').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            updateSilverRate();
        }
    });

    $('#platinum_rate').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            updatePlatinumRate();
        }
    });

    $('#pr_otp').on('keyup', function(evnt)
    {
        var kcode = (evnt.which) ? evnt.which : evnt.keyCode;
        if(kcode == 13)
        {
            otpCheck();
        }
    });
});





var isOpen = false;
var id = "";
$('.dropInput').on('mouseover',function ()
{
    id = $(this).attr('id');
    setTimeout(function ()
    {
        isOpen = true;
    }, 50);
    toggleDropDown(true, id);
});


$('.dropList').mouseleave(function ()
{
    toggleDropDown(false, id);
});

$(document).click(function ()
{
    if (isOpen)
    {
        toggleDropDown(false, id);
    }
});

$('.vTabs').mouseover(function ()
{
    var tid = $(this).attr('id');
    if (tid !== 'prdTab')
    {
        if (isOpen)
        {
            toggleDropDown(false, id);
        }
    }
});


function toggleDropDown(flag, id)
{
    if (flag)
    {
        $("#" + id + "List").velocity({opacity: 1, borderRadius: 0}, {duration: 200, display: "block"});
    }
    else
    {
        $("#" + id + "List").velocity({opacity: 0, borderRadius: '0%'}, {duration: 10, display: "none"});
    }
}

// $('#priceRange').ionRangeSlider({
//     type: "double",
//     grid: true,
//     min: 1000,
//     max: 1000000,
//     from: 0,
//     to: 500000,
//     decorate_both: false,
//     prettify_separator: ",",
//     force_edges: true,
//     drag_interval: true,
//     step: 1,
//     onFinish: function (data)
//     {
//         //FR();
//     }
// });


function showEnqFilter()
{
    var flag = $('.v_Filters').hasClass('transit100X');
    if (flag)
    {
        $('.v_Filters').removeClass('transit100X');
    }
    else
    {
        $('.v_Filters').addClass('transit100X');
    }
}


function submitDForm()
{
    window.location.href = DOMAIN+'index.php?case=vendor_landing';
}

$('#overlay').velocity({opacity:0},{delay:0,duration:0});
$('#uploadDiv,#dollarRateDiv,#goldRateDiv,#silverRateDiv,#goldSilverRateDiv').velocity({scale: 0}, {delay: 0, duration: 0});

$('#upProds').click(function ()
{
    $.ajax({url: common.APIWebPath() + "index.php?action=getDollerRate&vid="+ uid, success: function (result)
          {
              var obj = jQuery.parseJSON(result);
              var dollarRate=obj['results']['dollar_rate'];
              if(dollarRate != 0.00)
              {
                  $('#overlay,#uploadDiv').removeClass('dn');
                  setTimeout(function ()
                  {
                      $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
                      $('#uploadDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
                  }, 10);
                  loadDiamont = false;
              }
              else
              {
                  uploadButton = true;
                  showDollarRateForm();
                  loadDiamont = true;
              }
          }
      });
});

var uploadButton = false;
$('#upDolRt').click(function ()
{
	   uploadButton = true;
     showDollarRateForm();
});

$('#dollarRateSpan').click(function ()
{
    uploadButton = true;
    showDollarRateForm();
});

$('#goldRateSpan').click(function ()
{
    $('#overlay,#goldSilverRateDiv').removeClass('dn');
        setTimeout(function ()
        {
            $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
            $('#goldSilverRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
        }, 10);
        $('#gold_rate1').val(goldRate);
});

$('#platinumRateSpan').click(function ()
{
    $('#overlay,#platinumRateDiv').removeClass('dn');
        setTimeout(function ()
        {
            $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
            $('#platinumRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
        }, 10);
        $('#platinum_rate').val(platinumRate);
});


/* For Jewellery section plain gold */
$('#askGoldRate').click(function ()
{
    $('#overlay,#goldSilverRateDiv').removeClass('dn');
        setTimeout(function ()
        {
            $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
            $('#goldSilverRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
        }, 10);
        $('#gold_rate1').val(goldRate);
});

function revertSelect()
{
    $("input[name='Certficate']").attr('disabled',false);
    $('#jewellery_mix').prop('checked', true);
    $('#Pendants_10026').attr('disabled', false);
    $('#Bangles_10034').attr('disabled', false);
    $('#Earrings_10020').attr('disabled', false);
    $('#Rings_10012').attr('disabled', false);
    $("input[name='metal']").prop('disabled',false);
    $("input[name='combination']").prop('disabled',false);
    $('.dShapeTitle').removeClass('dn');
    $('#diamondShapeCont').removeClass('dn');
    $('#prdprice').prop('readonly',false);
}
/* Section ends here */

$('#silverRateSpan').click(function ()
{
    showSilverRateForm();
});


function showDollarRateForm()
{
    $('#overlay,#dollarRateDiv').removeClass('dn');
    setTimeout(function ()
    {
        $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
        $('#dollarRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
    }, 10);

    $.ajax({url: common.APIWebPath() + "index.php?action=getDollerRate&vid=" + uid, success: function (result)
            {
                var obj = jQuery.parseJSON(result);
                var errCode = obj['error']['code'];
                if (errCode == 0)
                {
              			var dlrRate = obj['results']['dollar_rate'];
              			if(dlrRate !== undefined && dlrRate !== null && dlrRate !== '' && typeof dlrRate !== 'undefined' && dlrRate !== '0.00' && dlrRate !== 0.00)
              			{
              				$('#dollar_rate').val(obj['results']['dollar_rate']);
              			}
              			else
              			{
              				$('#dollar_rate').val('');
              			}
                }
            }
        });
}

$('#upCancel').bind('click', function ()
{
    if(uploadStart)
    {
        common.toast(0,'Please wait,Upload Process is running')
    }
    else
    {
        if(pageName == 'jewellery-Form')
        {
            revertSelect();
        }
        closeAllForms();
    }
});

function closeAllForms()
{
    $('#uploadDiv,#dollarRateDiv,#goldRateDiv,#silverRateDiv,#goldSilverRateDiv,#platinumRateDiv').velocity({scale: 0}, {delay: 0, ease: 'swing'});
    $('#overlay').velocity({opacity: 0}, {delay: 100, ease: 'swing'});
    
    setTimeout(function ()
    {
        $('#overlay,#uploadDiv,#dollarRateDiv,#goldRateDiv,#silverRateDiv,#goldSilverRateDiv,#platinumRateDiv').addClass('dn');
        submiter2 = true;
    }, 1010);
    loadDiamont = true;
}

$(document).ready(function()
{
    $('.cancelBtn').click( function ()
    {
        $('#overlay').velocity({opacity: 0}, {delay: 0, duration: 300, ease: 'swing'});
        $('#overlay').addClass('dn');
        $('.shapeSelected').removeClass('shapeSelected');
    });
    var tmstmp = new Date().getTime();
    $.ajax({url: DOMAIN + "apis/index.php?action=getAllRatesByVID&vid="+uid+"&timestamp="+tmstmp, success: function(result)
          {
                      var obj = jQuery.parseJSON(result);
                      var errCode = obj['error']['code'];
                      if(errCode==0)
                      {
          					      obj = obj['results'];
          					      if(obj.dollar_rate !== '' && obj.dollar_rate !== undefined && obj.dollar_rate !== 'undefined'  &&  obj.dollar_rate !== 'null'  &&  obj.dollar_rate !== null )
                          {
                              $('#dollarRateSpan').html(''+obj.dollar_rate);
                              dollarRate = obj.dollar_rate;
                					}
                					else
                          {
            						      $('#dollarRateSpan').html('0.00');
                              dollarRate = obj.dollar_rate;
                					}

                          if(obj.silver_rate !== ''  &&  obj.silver_rate !== undefined  &&  obj.silver_rate !== 'undefined'  &&  obj.silver_rate !== 'null'  &&  obj.silver_rate !== null )
                          {
              	              $('#silverRateSpan').html(''+obj.silver_rate);
                              silverRate = obj.silver_rate;
          	              }
          		            else
                					{
            						      $('#silverRateSpan').html('0.00');
                              silverRate = obj.silver_rate;
                					}

                          if(obj.platinum_rate !== ''  &&  obj.platinum_rate !== undefined  &&  obj.platinum_rate !== 'undefined'  &&  obj.platinum_rate !== 'null'  &&  obj.platinum_rate !== null )
                          {
              	              $('#platinumRateSpan').html(''+obj.platinum_rate);
                              platinumRate = obj.platinum_rate;
          	              }
          		            else
                					{
            						      $('#platinumRateSpan').html('0.00');
                              platinumRate = obj.platinum_rate;
                					}

          		            if(obj.gold_rate !== ''  &&  obj.gold_rate !== undefined  &&  obj.gold_rate !== 'undefined'  &&  obj.gold_rate !== 'null'  &&  obj.gold_rate !== null )
                          {
                              $('#goldRateSpan').html(''+obj.gold_rate);
                              goldRate = obj.gold_rate;
                          }
                  				else
                  				{
                					     $('#goldRateSpan').html('0.00');
                               goldRate = obj.gold_rate;
                  				}
                    }
            }
      });
});

function updateDollarRate()
{
    if(submiter2 == true)
    {
        submiter2 = false;
        var dollar_rate = $("#dollar_rate").val();
        if(dollar_rate=='' || dollar_rate <= 0 || dollar_rate == undefined)
        {
            common.toast(0,'Invaild Rate');
        }
        else if(dollar_rate.length >= 11 || dollar_rate > 9999999.99)
        {
            common.toast(0,'Please enter proper rate!');
        }
        else
        {
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "/apis/index.php?action=updateDollerRate&vid="+uid+"&dolRate="+dollar_rate+"&timestamp="+tmstmp, success: function(result)
                {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if(errCode==0)
                    {
                        common.toast(1,obj['error']['msg']);
                        $('#dollarRateSpan').html(''+dollar_rate);
                        dollarRate = dollar_rate;
                        closeAllForms();
                        if(uploadButton == false)
                        {
                                setTimeout(function () { window.location.assign(DOMAIN+"index.php?case=diamond_Form&catid=10000&vid="+uid); }, 1500);
                        }
                        if(loadDiamont)
                        {
                            //setTimeout(function () {  $('#upProds').click(); }, 1500);
                            if(pageName == 'Products' && pageName !== undefined && pageName !== 'undefined' && pageName !== null && pageName !== 'null')
                            {
                                loadDiamonds(1);
                            }
                        }
                    }
                    else if(errCode==1)
                    {
                        common.toast(0,obj['error']['msg']);
                    }
                }
            });
        }
    }
}

/* For bullion silver rate of vendor */
$('#upSilRt').click(function ()
{
    showSilverRateForm();
});
/* For bullion platinum rate of vendor */
$('#upPlatRt').click(function ()
{
    showPlatinumRateForm();
});


function showSilverRateForm()
{
    $('#overlay,#silverRateDiv').removeClass('dn');
    setTimeout(function ()
    {
        $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
        $('#silverRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
    }, 10);
    $('#silver_rate').val(silverRate);
}

function showPlatinumRateForm()
{
    $('#overlay,#platinumRateDiv').removeClass('dn');
    setTimeout(function ()
    {
        $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
        $('#platinumRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
    }, 10);
    $('#platinum_rate').val(platinumRate);
}

function updateSilverRate()
{
    if(submiter2 == true)
    {
        submiter2 = false;
        var silver_rate = $("#silver_rate").val();
        if(pageName == 'Products')
        {
            if(silver_rate == undefined || silver_rate == 'undefined' || silver_rate === '0.00' || silver_rate == 0.00 || silver_rate=='' || silver_rate <= 0 || isNaN(silver_rate))
            {
                    common.toast(0,'Silver rate is must to fill');
                    return false;
            }
            if(silver_rate == undefined || silver_rate == 'undefined' || silver_rate=='' || silver_rate <= 0 || isNaN(silver_rate))
            {
                silver_rate = '0.00';
            }
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "apis/index.php?action=updateSilverRate&vid="+uid+"&silRate="+silver_rate+"&timestamp="+tmstmp, success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if(errCode == 0)
                    {
                        common.toast(1,obj['error']['msg']);
                        $('#silverRateSpan').html(''+silver_rate);
                        silverRate = silver_rate;
                        closeAllForms();
                        if(pageName  == 'Products' && catid == '10002' && pageName !== undefined && pageName  !== 'undefined')
                        {
                            loadBullions(1);
                        }
                        if(activePage  == 'jewellery_Form')
                        {
                            calculateJPrice();
                            valueOnChange();
                        }
                    }
                    else if(errCode == 1)
                    {
                        common.toast(0,obj['error']['msg']);
                        closeAllForms();
                    }
                }
            });
        }
        else if(pageName === 'bullion-Form')
        {
            if(silver_rate == undefined || silver_rate == 'undefined' || silver_rate === '0.00' || silver_rate == 0.00 || silver_rate=='' || silver_rate <= 0 || isNaN(silver_rate))
            {
                    common.toast(0,'Silver rate is must to fill');
                    return false;
            }
            else
            {
                var tmstmp = new Date().getTime();
                $.ajax({url: DOMAIN + "apis/index.php?action=updateSilverRate&vid="+uid+"&silRate="+silver_rate+"&timestamp="+tmstmp, success: function(result)
                    {
                        var obj = jQuery.parseJSON(result);
                        var errCode = obj['error']['code'];
                        if(errCode==0)
                        {
                            common.toast(1,obj['error']['msg']);
                            silverRate = silver_rate;
                            showJewelleryImps(GtmpId);
                            $('#silverRateSpan').html(''+silver_rate);
                            closeAllForms();
                        }
                        else if(errCode==1)
                        {
                            common.toast(0,obj['error']['msg']);
                        }
                    }
                });
           }
        }
        else
        {
            if(silver_rate == undefined || silver_rate == 'undefined' || silver_rate === '0.00' || silver_rate == 0.00 || silver_rate=='' || silver_rate <= 0 || isNaN(silver_rate))
            {
                    common.toast(0,'Silver rate is must to fill');
                    return false;
            }
            if(silver_rate == undefined || silver_rate == 'undefined' || silver_rate=='' || silver_rate <= 0 || isNaN(silver_rate))
            {
                silver_rate = '0.00';
            }
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "apis/index.php?action=updateSilverRate&vid="+uid+"&silRate="+silver_rate+"&timestamp="+tmstmp, success: function(result)
                {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if(errCode == 0)
                    {
                        common.toast(1,obj['error']['msg']);
                        $('#silverRateSpan').html(''+silver_rate);
                        silverRate = silver_rate;
                        closeAllForms();
                    }
                    else if(errCode == 1)
                    {
                        common.toast(0,obj['error']['msg']);
                        closeAllForms();
                    }
                }
            });
        }
    }
}

function updatePlatinumRate()
{
    if(submiter3 == true)
    {
        submiter2 = false;
        var platinum_rate = $("#platinum_rate").val();
        if(pageName == 'Products')
        {
            if(platinum_rate == undefined || platinum_rate == 'undefined' || platinum_rate === '0.00' || platinum_rate == 0.00 || platinum_rate == '' || platinum_rate <= 0 || isNaN(platinum_rate))
            {
                    common.toast(0,'Platinum rate is must to fill');
                    submiter3 = true;
                    return false;
            }
            if(platinum_rate == undefined || platinum_rate == 'undefined' || platinum_rate == '' || platinum_rate <= 0 || isNaN(platinum_rate))
            {
                platinum_rate = '0.00';
            }
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "apis/index.php?action=updatePlatinumRate&vid="+uid+"&platRate="+platinum_rate+"&timestamp="+tmstmp, success: function(result)
                    {
                          var obj = jQuery.parseJSON(result);
                          var errCode = obj['error']['code'];
                          if(errCode == 0)
                          {
                              common.toast(1,obj['error']['msg']);
                              $('#platinumRateSpan').html(''+platinum_rate);
                              platinumRate = platinum_rate;
                              closeAllForms();
                              if(pageName  == 'Products' && catid == '10002' && pageName !== undefined && pageName  !== 'undefined')
                              {
                                  loadBullions(1);
                              }
                              if(activePage  == 'jewellery_Form')
                              {
                                  calculateJPrice();
                                  valueOnChange();
                              }
                          }
                          else if(errCode == 1)
                          {
                              common.toast(0,obj['error']['msg']);
                              closeAllForms();
                          }
                      }
              });
        }
        else if(pageName === 'bullion-Form')
        {
            if(platinum_rate == undefined || platinum_rate == 'undefined' || platinum_rate === '0.00' || platinum_rate == 0.00 || platinum_rate=='' || platinum_rate <= 0 || isNaN(platinum_rate))
            {
                common.toast(0,'Platinum rate is must to fill');
                return false;
            }
            else
            {
                var tmstmp = new Date().getTime();
                $.ajax({url: DOMAIN + "apis/index.php?action=updatePlatinumRate&vid="+uid+"&platRate="+platinum_rate+"&timestamp="+tmstmp, success: function(result)
                    {
                        var obj = jQuery.parseJSON(result);
                        var errCode = obj['error']['code'];
                        if(errCode==0)
                        {
                            common.toast(1,obj['error']['msg']);
                            platinumRate = platinum_rate;
                            showJewelleryImps(GtmpId);
                            $('#platinumRateSpan').html(''+platinum_rate);
                            closeAllForms();
                        }
                        else if(errCode==1)
                        {
                            common.toast(0,obj['error']['msg']);
                        }
                    }
                });
           }
        }
        else
        {
            if(platinum_rate == undefined || platinum_rate == 'undefined' || platinum_rate === '0.00' || platinum_rate == 0.00 || platinum_rate == '' || platinum_rate <= 0 || isNaN(platinum_rate))
            {
                    common.toast(0,'Platinum rate is must to fill');
                    return false;
            }
            if(platinum_rate == undefined || platinum_rate == 'undefined' || platinum_rate == '' || platinum_rate <= 0 || isNaN(platinum_rate))
            {
                platinum_rate = '0.00';
            }
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "apis/index.php?action=updatePlatinumRate&vid="+uid+"&platRate="+platinum_rate+"&timestamp="+tmstmp, success: function(result)
                {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if(errCode == 0)
                    {
                        common.toast(1,obj['error']['msg']);
                        $('#paltinumRateSpan').html(''+silver_rate);
                        platinumRate = platinum_rate;
                        closeAllForms();
                    }
                    else if(errCode == 1)
                    {
                        common.toast(0,obj['error']['msg']);
                        closeAllForms();
                    }
                }
            });
        }
    }
}

$('#upGoldRt').click(function ()
{
	   uploadButton = true;
     showGoldRateForm();
});

function showGoldRateForm()
{
    $('#overlay,#goldRateDiv').removeClass('dn');
    setTimeout(function ()
    {
        $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
        $('#goldRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
    }, 10);
    $('#gold_rate').val(goldRate);
}

function updateGoldRate()
{
    var gold_rate = $("#gold_rate").val();
    gold_rate=parseFloat(gold_rate);
    if(gold_rate=='' || gold_rate <= 0 || gold_rate == undefined || isNaN(gold_rate) == true)
    {
        common.toast(0,'Gold Rate is Must to fill');
        return false;
    }
    else
    {
        if(submiter2 === true)
        {
            submiter2 = false;
            var tmstmp = new Date().getTime();
            $.ajax({url: DOMAIN + "/apis/index.php?action=updateGoldRate&vid="+uid+"&goldRate="+gold_rate+"&timestamp="+tmstmp, success: function(result) {
                var obj = jQuery.parseJSON(result);
                var errCode = obj['error']['code'];
                if(errCode==0) {
                    common.toast(1,obj['error']['msg']);
                    $('#goldRateSpan').html(''+gold_rate);
                    //window.location.reload(1);
                    goldRate = gold_rate;
                    closeAllForms();
                    submiter2 = true;
                    showJewelleryImps(GtmpId);
                } else if(errCode==1) {

                    common.toast(0,obj['error']['msg']);
                }
            }});
        }
    }
}

function showgoldSilverRateForm()
{
    $('#overlay,#goldSilverRateDiv').removeClass('dn');
    setTimeout(function ()
    {
        $('#overlay').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
        $('#goldSilverRateDiv').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
    }, 10);
    $('#gold_rate1').val(goldRate);
}

function updateGoldSilverRate()
{
    var gold_rate = $("#gold_rate1").val();
    if(gold_rate=='' || gold_rate <= 0 || gold_rate == undefined || isNaN(gold_rate) == true)
    {
        common.toast(0,'Gold Rate is Must to fill');
        return false;
    }
    if(gold_rate == undefined || gold_rate == 'undefined' || gold_rate == '' || gold_rate <= 0 || isNaN(gold_rate) == true )
    {
        gold_rate = '0.00';
    }
    if(submiter2 == true)
    {
        submiter2 = false;
        var tmstmp = new Date().getTime();
        $.ajax({url: DOMAIN + "/apis/index.php?action=updateGoldRate&vid="+uid+"&goldRate="+gold_rate+"&timestamp="+tmstmp, success: function(result)
               {
                    var obj = jQuery.parseJSON(result);
                    var errCode = obj['error']['code'];
                    if(errCode==0)
                    {
                        common.toast(1,obj['error']['msg']);
                        $('#goldRateSpan').html(''+gold_rate);
                        //window.location.reload(1);
                        goldRate = gold_rate;
                        customStorage.readFromStorage('rateErr');
                        if(pageName  == 'Products' && catid == '10002' && pageName !== undefined && pageName  !== 'undefined')
                        {
                            loadBullions(1);
                        }
                        if(activePage  == 'jewellery_Form')
                        {
                            calculateJPrice();
                            valueOnChange();
                        }
                        closeAllForms();
                        submiter2 = true;
                    }
                    else if(errCode==1)
                    {
                            common.toast(0,obj['error']['msg']);
                    }
                }
            });
    }
}

function isValidFloatKey(obj, e, allowDecimal)
{
    var key;
    var isCtrl = false;
    var keychar;
    var reg;

    if (window.event)
    {
        key = e.keyCode;
        isCtrl = window.event.ctrlKey
    }
    else if (e.which)
    {
        key = e.which;
        isCtrl = e.ctrlKey;
    }
    if (isNaN(key))
        return true;

    keychar = String.fromCharCode(key);
    if (key == 8 || isCtrl)
    {
        return true;
    }
    if(key == 13)
    {
       // updateDollarRate();
    }
    reg = /\d/;
    var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
    return isFirstD || reg.test(keychar);
}

var uploadStart=false;
$("#upSubmit").on('click',(function(e)
{
    if($("#up_file").val()=='' || ValidateFile()==false)
    {
        common.toast(0,'Please Select Valid CSV File');
    }
    else if(uploadStart)
    {
        common.toast(0,'Upload process is running');
    }
    else
    {
        $('#upSubmit').text('Uploading Data');
        uploadStart=true;
        var tmstmp = new Date().getTime();
        $.ajax({url: DOMAIN + "apis/index.php?action=bulkInsertProducts&vid="+uid+"&timestamp="+tmstmp+"&catid="+catid,
            type: "POST",
            data: new FormData($('form')[0]),
            async:false,
            contentType: false,
            cache: false,
            processData:false,
            success: function(result)
            {   
                $('#upSubmit').text('Upload');
                var obj = jQuery.parseJSON(result);
                $("#up_file").val('');
                var errCode = obj['error']['Code'];
                if(errCode==0)
                {
                    $("#DiamondsList").html('');
                    diamondPage = 1;

                    if(catid == '10000')
                    {
                        loadDiamonds(1);
                    }
                    if(catid == '10001')
                    {
                        loadJewels(1);
                    }
                    else if(catid == '10002')
                    {
                        loadBullions(1);
                    }
                    loadDiamond = false;
                    errCode=1;
                    closeAllForms();
                }
                else if(errCode==1)
                {
                    errCode=0;
                }
                common.toast(errCode,obj['error']['Msg']);
                uploadStart=false;
            }
        });
    }
}));


function ValidateFile()
{
    var allowedFiles = ["csv","xls","xlsx"];
    var fileUpload = document.getElementById("up_file").value;
    //alert(fileUpload);
    var fileExt = fileUpload.split('.').pop();
    if (allowedFiles.indexOf(fileExt)!=-1)
    {
        return true;
    }
    return false;
}

function getcatid()
{
    var btype = customStorage.readFromStorage('busiType');
    if (btype !=='' || btype !== undefined || btype !== null )
    {
        var catid = parseInt(btype.charAt(0))-1;
    }
    return 1000+catid.toString();
}
function addDiamond()
{
//    $.ajax({url: common.APIWebPath() + "index.php?action=getDollerRate&vid="+ uid, success: function (result) {
//        var obj = jQuery.parseJSON(result);
//            var dollarRate=obj['results']['dollar_rate'];
//            if(dollarRate!=0.00) {
//                window.location.assign(DOMAIN+"index.php?case=diamond_Form&catid=10000&vid="+uid);
//            } else {
                uploadButton = false;
                showDollarRateForm();
                $('#dollarErr').removeClass('dn');
//            }
//    }});
            }
function addBulion()
{
    window.location.assign(DOMAIN+"index.php?case=bullion_Form&catid="+catid+"&vid="+uid);
}

function showVendorProfile()
{
    window.location.href=DOMAIN+'index.php?case=vendor_Form&uid='+uid;
}



//super Admin Access to vendor profile.
/*function checkVendorProfile()
{
    window.location.href=DOMAIN+'index.php?case=check_Vendor_Form&uid='+uid+'&isAdmin=1';
}*/



$('#rate_m').click(function(){
     
     if((busiTypeSplt.length == 1 || busiTypeSplt.length == 2 ) && (busiTypeSplt[0] == 2))
         {
            $('#dollarRateSpan').parent().addClass('dn');
         }
  $('.rateDiv').slideToggle(300);
  $('.contentCont').toggleClass('pad55');
});
