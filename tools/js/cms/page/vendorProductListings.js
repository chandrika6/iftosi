var loadDiamont = false;
var loadJewel = false;
var loadBullion = false;

var diamondPage = 1;
var jewellPage = 1;
var bullionPage = 1;
//$.ajax({url: common.APIWebPath() + "index.php?action=viewAll&uid="+uid, success: function (result) {
//    var obj = jQuery.parseJSON(result);
//    var busiType = obj['results'][1]['business_type'];
    if (/1/.test(busiType)) {
        $('#dmdTab').removeClass('dn');
        loadDiamont = true;
    }
    if (/2/.test(busiType)) {
        $('#jewTab').removeClass('dn');
        loadJewel = true;
    }
    if (/3/.test(busiType)) {
        $('#bullTab').removeClass('dn');
        loadBullion = true;
    }
    if (/1/.test(busiType) && catid==10000) {
        $('#diamondPrds').removeClass('dn');
        loadDiamonds();
    }
    else if (/2/.test(busiType) && catid==10001) {
        loadJewels();
        $('#jewelleryPrds').removeClass('dn');
    }
    else if (/3/.test(busiType) && catid==10002) {
        $('#bullionPrds').removeClass('dn');
        loadBullions();
    }
//}});

$(window).scroll(function () {
    if ($(window).scrollTop() == ($(document).height() - $(window).height())) {
        if(!searchScroll) {
            if (catid == 10000 && loadDiamont) {
                loadDiamonds();
            }
            if (catid == 10001 && loadJewel) {
                loadJewels();
            }
            if (catid == 10002 && loadBullion) {
                loadBullions();
            }
        } else {
            searchPage++;
            searchBarcode(searchScrollValue);
        }
    }
});
function loadDiamonds() {
   $.ajax({url: common.APIWebPath() + "index.php?action=getVproducts&vid=" + uid + "&page=" + diamondPage + "&limit=15&catid=10000", success: function (result) {
            loadDiamondCallback(result);
    }});
}
function loadDiamondCallback(res) {
    var obj = jQuery.parseJSON(res);
    if (obj['results'] != '') {
        var total = obj['results']['total_products'];
        $('#totalDiamonds').text(total);
        var total_pages = obj['results']['total_pages'];
        
        var str = '';
        if(total!=0) {
            if(total_pages==diamondPage) {
                loadDiamont = false;
            }
            var len = obj['results']['products'].length;
            var i = 0;
            while (i < len) {
                str += generateDiamondList(obj['results']['products'][i]);
                i++;
            }
            diamondPage++;
        } else {
            str = '<p class="noRecords"><span>Sorry! No Products Found!</span></p>';
            loadDiamont = false;
        }
        $('#DiamondsList').append(str);
    } else {
        loadDiamont = false;
    }
}
function generateDiamondList(obj) {
    var pro_name = obj['product_name'];
    if(pro_name == null || pro_name == '' || pro_name == 'null') {
        pro_name = obj['barcode'];
    }
    var barcode = obj['barcode'];
    if(barcode == 'null' || barcode == undefined || barcode == null || barcode == '' || barcode == '' ) {
        barcode = 'N-A';
    }
    
    var date = obj['update_time'].split(' ');
    var str = '<li>';
    str += '<div class="date fLeft"> ';
    str += '<span class="upSpan">' + date[0] + '</span>';
    str += '<span class="lwSpan">' + date[1] + '</span>';
    str += '</div>';
    str += '<div class="barcode fLeft">';
    str += '<span class="upSpan">' + barcode + '</span>';
    str += '<span class="lwSpan"><a href="'+ DOMAIN + '-' + obj['shape'] +'-clarity-'+ obj['clarity'] +'/did-'+ obj['id'] +'" target="_blank">View Details</a></span>';
    str += '</div>';
    str += '<div class="shape fLeft">' + obj['shape'] + '</div>';
    str += '<div class="carats fLeft">' + obj['carat'] + '</div>';
    str += '<div class="color fLeft">' + obj['color'] + '</div>';
    str += '<div class="clarity fLeft">' + obj['clarity'] + '</div>';
    str += '<div class="cert fLeft">' + obj['cert'] + '</div>';
    str += '<div class="price fLeft fmOpenB">&#36;' + obj['price']+ '</div>';
    str += '<div class="acct fLeft">';
    str += '<center>';
    str += '<a href="'+ DOMAIN + 'upload-image/pid-'+ obj['id'] +'" target="_blank"><div class="uploadBtn poR ripplelink"></div></a>';
    str += '<div class="deltBtn poR ripplelink" onclick="showConfirmDelete(' + obj['id'] + ',this)"></div>';
    str += '<a href="'+ DOMAIN +'index.php?case=diamond_Form&catid=10000&prdid='+ obj['id'] +'" target="_blank"><div class="editBtn poR ripplelink"></div></a>';
    str += '<div class="soldBtn poR ripplelink fmOpenR" id="isStock'+ obj['id'] +'" onclick="inStock(' + obj['id'] + ',this)">'+((obj['active_flag']==3) ? "STOCK" : "SOLD");+'</div>';
    str += '</center>';
    str += '</div>';
    str += '</li>';
    str += '';
    return str;
    
}


function loadJewels() {
    $.ajax({url: common.APIWebPath() + "index.php?action=getVproducts&vid=" + uid + "&page=" + jewellPage + "&limit=15&catid=10001", success: function (result) {
            loadJewellCallback(result);
    }});
}
function loadJewellCallback(res) {
    var obj = jQuery.parseJSON(res);
    if (obj['results'] != '') {
        var total = obj['results']['total_products'];
        $('#totalJewells').text(total);
        var total_pages = obj['results']['total_pages'];
            
        var str = '';
        if(total!=0) {
            if(total_pages==jewellPage) {
                loadJewel = false;
            }
            var len = obj['results']['products'].length;
            var i = 0;
            while (i < len) {
                str += generateJewellList(obj['results']['products'][i]);
                i++;
            }
            jewellPage++;
        } else {
            str = '<p class="noRecords"><span>Sorry! No Products Found!</span></p>';
            loadJewel = false;
        }
        $('#JewellsList').append(str);
    } else {
        loadJewel = false;
    }
}
function generateJewellList(obj) {
    if(obj !== undefined && obj !== null && obj !== '')
    {
        var pro_name = obj['product_name'];
        var shape = obj['shape'];
        var category= obj['category'][1]['cat_name'];
        var metal = obj['metal'];
        if(category == 'Bangles/Bracelets')
        {
            category = '<span class="upSpan">Bangles / Bracelets</span>';
        }
        var barcode = obj['barcode'];
        if(barcode == undefined || barcode == null || barcode == '' || barcode == 'null') {
            barcode = 'N-A';
        }
        if(pro_name == undefined || pro_name == null || pro_name == '' || pro_name == 'null') {
            pro_name = barcode;
        }
        if(shape == undefined || shape == null || shape == '' || shape == 'null') {
            shape = 'N/A';
        }
        
        if(metal == undefined || metal == null || metal == '' || metal == 'null') {
            metal = 'N/A';
        }
        var date = obj['update_time'].split(' ');
        var str = '<li>';
        str += '<div class="date fLeft"> ';
        str += '<span class="upSpan">' + date[0] + '</span>';
        str += '<span class="lwSpan">'+ date[1] +'</span>';
        str += '</div>';
        str += '<div class="barcode fLeft">';
        str += '<span class="upSpan">' + barcode + '</span>';
        str += '<span class="lwSpan"><a href="'+ DOMAIN + barcode +'/jid-'+ obj['id'] +'" target="_blank">View Details</a></span>';
        str += '</div>';
        str += '<div class="metal fLeft">' + metal.split('~')[0] + '</div>';
        str += '<div class="catg fLeft">' + shape +'</div>';
        str += '<div class="degno fLeft">' + obj['dwt'] + '</div>';
        str += '<div class="subType fLeft">' + obj['gold_weight'] + '</div>';
        str += '<div class="price fLeft fmOpenB">&#8377;' + obj['price']+ '</div>';
        str += '<div class="acct fLeft">';
        str += '<center>';
        str += '<a href="'+ DOMAIN + 'upload-image/pid-'+ obj['id'] +'" target="_blank"><div class="uploadBtn poR ripplelink"></div></a>';
        str += '<div class="deltBtn poR ripplelink" onclick="showConfirmDelete(' + obj['id'] + ',this)"></div>';
        str += '<a href="'+ DOMAIN +'index.php?case=jewellery_Form&catid=10001&prdid='+ obj['id'] +'" target="_blank"><div class="editBtn poR ripplelink"></div></a>';
        str += '<div class="soldBtn poR ripplelink fmOpenR" id="isStock'+ obj['id'] +'" onclick="inStock(' + obj['id'] + ',this)">'+((obj['active_flag']==3 ) ? "STOCK" : "SOLD");+'</div>';
        str += '</center>';
        str += '</div>';
        str += '</li>';
        str += '';
        return str;
        }
 }

function loadBullions() {
    $.ajax({url: common.APIWebPath() + "index.php?action=getVproducts&vid=" + uid + "&page=" + bullionPage + "&limit=15&catid=10002", success: function (result) {
            loadBullionsCallback(result);
        }});
}
function loadBullionsCallback(res) {
    var obj = jQuery.parseJSON(res);
    if (obj['results'] != '') {
        var total = obj['results']['total_products'];
        $('#totalBullions').text(total);
        var total_pages = obj['results']['total_pages'];
        
        var str = '';
        if(total!=0) {
            if(total_pages==bullionPage) {
                loadBullion = false;
            }
            var len = obj['results']['products'].length;
            var i = 0;
            while (i < len) {
                str += generatBullionsList(obj['results']['products'][i]);
                i++;
            }
            bullionPage++;
        } else {
            str = '<p class="noRecords"><span>Sorry! No Products Found!</span></p>';
            loadBullion = false;
        }
        $('#BullionsList').append(str);
    } else {
        loadBullion = false;
    }
}
function generatBullionsList(obj) {
    var barcode = obj['barcode'];
    if(barcode == null || barcode == '' || barcode == 'null') {
        barcode = 'N-A';
    }
    var pro_name = obj['product_name'];
    if(pro_name == null || pro_name == '' || pro_name == 'null') {
        pro_name = '';
    }
    
    var date = obj['update_time'].split(' ');
    var str = '<li>';
    str += '<div class="date fLeft"> ';
    str += '<span class="upSpan">' + date[0] + '</span>';
    str += '<span class="lwSpan">'+ date[1] +'</span>';
    str += '</div>';
    str += '<div class="barcode fLeft">';
    str += '<span class="upSpan">' + barcode + '</span>';
    str += '<span class="lwSpan"><a href="'+ DOMAIN + barcode +'/bid-'+ obj['id'] +'" target="_blank">View Details</a></span>';
    str += '</div>';
    str += '<div class="btype fLeft">' + obj['type'] + '</div>';
    str += '<div class="metal fLeft">' + obj['metal'].split('~')[0] + '</div>';
    str += '<div class="purity fLeft">' + obj['gold_purity'] + '</div>';
    str += '<div class="weight fLeft">' + obj['gold_weight'] + '</div>';
    str += '<div class="price fLeft fmOpenB">&#8377;' + obj['price'].toFixed(3)+ '</div>';
    str += '<div class="acct fLeft">';
    str += '<center>';
    str += '<a href="'+ DOMAIN + 'upload-image/pid-'+ obj['id'] +'" target="_blank"><div class="uploadBtn poR ripplelink"></div></a>';
    str += '<div class="deltBtn poR ripplelink" onclick="showConfirmDelete(' + obj['id'] + ',this)"></div>';
    str += '<a href="'+ DOMAIN +'index.php?case=bullion_Form&catid=10002&prdid='+ obj['id'] +'" target="_blank"><div class="editBtn poR ripplelink"></div></a>';
    str += '<div class="soldBtn poR ripplelink fmOpenR" id="isStock'+ obj['id'] +'" onclick="inStock(' + obj['id'] + ',this)">'+((obj['active_flag']==3 ) ? "STOCK" : "SOLD");+'</div>';
    str += '</center>';
    str += '</div>';
    str += '</li>';
    str += '';
    return str;
}
var catName='';

var productDelId = '', productDelEle = '';

function closeConfirmDelete()
{
	productDelId = '', productDelEle = '';
	$('#confirmDelete').velocity({scale: 0}, {delay: 0, ease: 'swing'});
	$('#overlay1').velocity({opacity: 0}, {delay: 100, ease: 'swing'});
	setTimeout(function () {
		$('#overlay1,#confirmDelete').addClass('dn');
	}, 1010);
}

function showConfirmDelete(proId, ele)
{
	productDelId = proId;
	productDelEle = ele;
	$('#overlay1,#confirmDelete').removeClass('dn');
	setTimeout(function () {
		$('#overlay1').velocity({opacity: 1}, {delay: 0, duration: 300, ease: 'swing'});
		$('#confirmDelete').velocity({scale: 1}, {delay: 80, duration: 100, ease: 'swing'});
	}, 10);
}


function deleteProduct() {
	var proId = productDelId;
	var ele = productDelEle;
    $.ajax({url: common.APIWebPath() + "index.php?action=vDeletePrd&vid=" + uid + "&prdid=" + proId, success: function (result) {
        var obj = jQuery.parseJSON(result);
        if(obj['error']['Code']==0) {
            $(ele).parent().parent().parent().remove();
            if(catid==10000) {
                catName='Diamonds';
            } else if(catid==10001) {
                catName='Jewells';
            } else if(catid==10002) {
                catName='Bullions';
            }
            var total=$('#total'+catName).text();
            total--;
            $('#total'+catName).text(total);
            console.log(total);
            console.log(catName);
            if(total==0) {
                loadBullion = false;
            }
            var count = $("#"+catName+"List li").length;
            if(count<15) {
                $("#"+catName+"List").html('');
                if(catid==10000) {
                    diamondPage = 1;
                    loadDiamonds();
                } else if(catid==10001) {
                    jewellPage = 1;
                    loadJewels();
                } else if(catid==10002) {
                    bullionPage = 1;
                    loadBullions();
                }
            }
            console.log("#"+catName+"List");
            common.toast(1,obj['error']['Msg']);
        } else {
            common.toast(0,obj['error']['Msg']);
        }
    }});
	closeConfirmDelete();
}

function inStock(proId,ele) {
    $.ajax({url: common.APIWebPath() + "index.php?action=togglePrdstatus&vid=" + uid + "&prdid=" + proId, success: function (result) {
    var obj = jQuery.parseJSON(result);
        if(obj['error']['Code']==0) {
            var stockid="isStock"+proId;
            //$(ele).parent().parent().parent().remove();
            if(catid==10000) {
                catName='Diamonds';
             } else if(catid==10001) {
                catName='Jewells';
            } else if(catid==10002) {
                catName='Bullions';
            }
            var total=$('#total'+catName).text();
            $('#total'+catName).text(total);
            common.toast(1,obj['error']['Msg']);
            $('#'+stockid).text()=='SOLD'?$('#'+stockid).text('STOCK'):$('#'+stockid).text('SOLD');
        } else {
            common.toast(0,obj['error']['Msg']);
        }
    }});
}

var searchScrollValue= '';
var searchScroll= false;
var searchIDName = '';
var searchPage = 1;
$('.prdSeachTxt').val('');
function searchBarcode(val) {
    if(catid==10000) {
        searchIDName='Diamonds';
    }else if(catid==10001) {
        searchIDName='Jewells';
    } else if(catid==10002) {
        searchIDName='Bullions';
    }
    if(val!='') {
        if(searchPage == 1)
        {
            $('#'+searchIDName+'List').removeClass('dn');
            $('#s'+searchIDName+'List').html('').addClass('dn');
        }
        $.ajax({url: common.APIWebPath() + "index.php?action=getVProductsByBcode&bcode="+ val +"&vid="+ uid +"&catid="+catid+"&page="+searchPage+"&limit=15", success: function (result) {
            searchBarcodeCallback(result);
        }});
        searchScrollValue = val;
    } else {
        $('#'+searchIDName+'List').removeClass('dn');
        $('#s'+searchIDName+'List').html('').addClass('dn');
    }
}
function searchBarcodeCallback(res) {
    var obj = jQuery.parseJSON(res);
    if (obj['results'] !== '') {
        var total = obj['results']['total_products'];
        //$('#total'+searchIDName).text(total);
        
        $('#'+searchIDName+'List').addClass('dn');
        $('#s'+searchIDName+'List').removeClass('dn');
        if(total!=0) {
            searchScroll = true;
            var len = obj['results']['products'].length;
            var i = 0;
            var catName;
            if(catid==10000) {
                var str = '';
                while (i < len) {
                    str += generateDiamondList(obj['results']['products'][i]);
                    i++;
                }
            } else if(catid==10001) {
                var str = '';
                while (i < len) {
                    str += generateJewellList(obj['results']['products'][i]);
                    i++;
                }
            } else if(catid==10002) {
                var str = '';
                while (i < len) {
                    str += generatBullionsList(obj['results']['products'][i]);
                    i++;
                }
            }
            if(str !== undefined && str !== null && str !== '')
            {
                $('#s'+searchIDName+'List').append(str);
            }
        }
        else if(searchPage==1) {
            searchScroll = false;
            var str = '<p class="noRecords"><span>Sorry! No Products Found!</span></p>';
            $('#s'+searchIDName+'List').html(str);
        }
    } else {
        searchScroll = false;
        var str = '<p class="noRecords"><span>Sorry! No Products Found!</span></p>';
        $('#s' + searchIDName + 'List').html(str);
    }
}