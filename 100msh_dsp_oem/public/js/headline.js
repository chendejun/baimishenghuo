var infoId = getUrlParams('crowd_id') ? getUrlParams('crowd_id') : null; //人群信息id
var crowdArr = {region:[],regionVal:[],age:[],sex:[]},//ID数组初始化
    region = []; //城市数组初始化
var pList = $('#province_list'),cList = $('#city_list'),aList = $('#area_list');
var OPEN = 'cur',CHOSE = 'chose',CHILDCHOSE = 'childchose';
var backType = getUrlParams('type') ? getUrlParams('type'):'';//url参数来源于新增投放

//getData();//获取可选项列表

function init(){
    var data = getAreas(0);
    pList.show().html('<ul class="plist">'+ data +'</ul>');
}
init();//初始化

//点击提交
var commitBtn = $('#commit_btn');
commitBtn.click(function(){
    var sexData = $('#sexInput').val(),
        cpName =  $.trim($('input[name="campaign_name"]').val()),
        dyName =  $.trim($('input[name="delivery_name"]').val()),
        dyUrl =  $.trim($('input[name="delivery_url"]').val()),
        regionTy = $('#region').find('.crowd_nav .cur').attr('type'),
        ageTy = $('#age').find('.crowd_nav .cur').attr('type'),
        moneyDate = $.trim($('input[name="money"]').val()),
        dataTy = $('input[name="delivery_time_type"]').val(),
        dateInput = $('#dateInput').val(),
        timeTy = $('#timeType').val(),
        timeInput = $('#timeInput').val(),
        bidTy = $('input[name="bid_type"]').val(),
        bidMoney = $.trim($('input[name="bid_money"]').val()),
        imgTag = $.trim($('input[name="img_tag"]').val()),
        imgTy = $('input[name="img_type"]').val(),
        timeVal = timeInput.split('-');

    crowdArr.region = getRegion()[0];
    crowdArr.regionVal = getRegion()[1];
    crowdArr.sex = sexData ? [sexData] : [];

    if(!cpName || cpName.length>50){
        tipTopShow('请输入长度1-50的广告组名称！');
        return;
    }
    if(!dyUrl){
        tipTopShow('请输入链接地址！');
        return;
    }
    if(!checkUrl(dyUrl)){
        tipTopShow('请输入正确格式的链接地址！');
        return;
    }
    if(crowdArr.region.length ==0 && regionTy!=0) {
        tipTopShow('请选择地域!');
        return;
    }
    if(crowdArr.age.length ==0 && ageTy!=0) {
        tipTopShow('请选择年龄!');
        return;
    }
    if(!moneyDate){
        tipTopShow('请输入预算金额!');
        return;
    }
    if(!(/^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/).test(moneyDate)){
        tipTopShow('请输入正确的预算金额(2位小数)!');
        return;
    }
    if(parseFloat(moneyDate)<100){
        tipTopShow('预算金额最低100元!');
        return;
    }
     if(parseFloat(moneyDate)>parseFloat(9999999.99)){
        tipTopShow('预算金额最高9999999.99元!');
        return;
    }
    if(dataTy==2 && !dateInput){
        tipTopShow('请选择投放时间!');
        return;
    }
    if(timeTy==2){
        if(!timeInput){
            tipTopShow('请选择投放时段!');
            return;
        }
        if($.trim(timeVal[1])==='00:00:00'){
            tipTopShow('请选择正确的截止时间段');
            return;
        }
        if(CompareDate(timeVal[0],timeVal[1])){
            tipTopShow('截止时间段不能小于开始时间段');
            return;
        }
    }
    if(!(/^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/).test(bidMoney)){
        tipTopShow('请输入正确的出价金额(2位小数)!');
        return;
    }
    console.log(bidTy,parseFloat(bidMoney));
    if(bidTy==1 && (parseFloat(bidMoney)<4)){
        tipTopShow('选择CPM，最低出价4元!');
        return;
    }
    if(bidTy==1 && (parseFloat(bidMoney)>100)){
        tipTopShow('选择CPM，最高出价100元!');
        return;
    }
    if(bidTy==2 && (parseFloat(bidMoney)<0.2)){
        tipTopShow('选择CPC，最低出价0.2元!');
        return;
    }
    if(bidTy==2 && (parseFloat(bidMoney)>100)){
        tipTopShow('选择CPC，最高出价100元!');
        return;
    }
    if(!dyName || dyName.length>50){
        tipTopShow('请输入长度1-50的广告计划名称！');
        return;
    }
    var mat_info = checkMatt();
    if(!mat_info.success){
        tipTopShow(mat_info.msg);
        return;
    }
    if(!imgTag || imgTag.length>100){
        tipTopShow('创意标签总长度不超过100！');
        return;
    }

    var dataArr = $("form").serializeArray(),
        dataArrPost={};
    $.each(dataArr, function(i,field){
        dataArrPost[field.name]= field.value;
    });

    dataArrPost.delivery_info = JSON.stringify(crowdArr);
    dataArrPost.img_info = mat_info.data;
    submit(dataArrPost);
})
//时分秒校验
function CompareDate(t1,t2){
    var date = new Date();
    var a = t1.split(":");
    var b = t2.split(":");
    return date.setHours(a[0],a[1],a[2]) > date.setHours(b[0],b[1],b[2]);
}
//素材校验
function checkMatt(){
    var imgTy = $('input[name="img_type"]').val(),
        fileTy = (imgTy==2 ? $('input[name="file_type"]:checked').val() :'1'),
        urlDom = $('input[type="file"]:visible'),
        img_info = [],
        urlData='',
        hasUrl = false;
    urlDom.each(function(){
        var data_obj = {},
            thUrl = $(this).attr('_url')? $(this).attr('_url'):"";
        data_obj.img_url = thUrl;
        data_obj.img_label = fileTy;
        //data_obj.img_type = imgTy;
        img_info.push(data_obj)
        thUrl ? hasUrl = true : hasUrl = false;
    })

    if(!hasUrl){
        return {"success":false,msg:'请上传图片'};
    }
    img_info = JSON.stringify(img_info);
    return {"success":true,"data":img_info};
}
//提交操作
function submit(data){
    $.ajax({
        type : 'POST',
        url : '/v1.2/Headline/add',
        data : data,
        dataType : 'json',
        beforeSend : function() {
            //禁用按钮防止重复提交
            commitBtn.attr('disabled','disabled');
            //tipTopShow('正在创建人群包，请稍候...',8000);
        },
        success : function(rs) {
            tipTopHide();
            if (rs.api_code == 1) {
                //tipTopShow('人群新增成功');
                location.href ='/v1.2/headline/indexview';
            }else{
                tipTopShow(rs.msg);
            }
            commitBtn.removeAttr('disabled');
        },
        error : function(rs) {
            tipTopHide();
            commitBtn.removeAttr('disabled');
            tipTopShow('操作失败请重试！');
        }
    });
}
//图片尺寸校验
function createReader(file, fn) {
    var reader = new FileReader;
    reader.onload = function (evt) {
        var image = new Image();
        image.onload = function () {
            var width = this.width;
            var height = this.height;
            fn.call(this,width,height);
        };
        image.src = evt.target.result;
    };
    reader.readAsDataURL(file);
}
//图片上传
function imgPreview(fileDom,el) {
    var fileSize = 512000;
    var fwidth = $(fileDom).attr('_width');
    var fheight = $(fileDom).attr('_height');
    var width='',height='';
    console.log(fwidth);
    //判断是否支持FileReader
    if(window.FileReader) {
        var reader = new FileReader();
    } else {
        tipTopShow("您的设备不支持图片预览功能，如需该功能请升级您的设备！");
    }
    //获取文件
    var file = fileDom.files[0];
    var imageType = /^image\//;
    //是否是图片
    if(!imageType.test(file.type)) {
        tipTopShow("请选择图片！");
        $(fileDom).val('');//解决连续相同两张图片无法上传问题
        return;
    }
    var size = $(fileDom)[0].files[0].size;
    if(size >fileSize){
        tipTopShow('图片大小不能大于500KB');
         $(fileDom).val('');//解决连续相同两张图片无法上传问题
        return;
    }
    createReader(file, function (w, h) {
        if(w!= fwidth || h!=fheight){
            tipTopShow('图片尺寸不符合！');
            return;
        }
        var formData = new FormData();
        var ids = $(fileDom).attr('id');
        formData.append('file', $('#'+ids)[0].files[0]);
        $.ajax({
            url: '/material/uploadFileJr',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                var rs = eval("("+data+")");
                console.log(rs);
                if(rs.state==1){
                    tipTopShow('上传成功！');
                    document.getElementById(el).src = rs.url;
                    $('#'+ids).attr('_url',rs.url);
                }else{
                    $('#'+ids).removeAttr('_url');
                    tipTopShow(rs.msg);
                }
                $(fileDom).val('');//解决连续相同两张图片无法上传问题
            },
            error: function (data) {
                tipTopShow("上传失败");
                $(fileDom).val('');//解决连续相同两张图片无法上传问题
            }
        })
    });
}

//标签切换
$('body').on('click','.crowd_nav span', function(e){
    var th = $(this);
    var type =  th.attr('type');
    var parentDom =  th.closest('.from-row');
    /*if(th.hasClass('cur')){
        return;
    }*/
    th.closest('.crowd_nav').find('span').removeClass('cur');
    th.addClass('cur');
    parentDom.attr('type', th.attr('type'));
    if(type==0){ //不限
        parentDom.find('.crowd_box').hide();
    }else if(type==1){ //省市
        var tagId = $(this).attr('_id');
        if(pList.html() == '') {
            var data = getAreas(tagId);
            pList.show().html('<ul class="plist">'+ data +'</ul>');
        }
        parentDom.find('.crowd_box').show();
    } else if(type==2){//性别
        var sexData = th.attr('data');
        $('#sexInput').val(sexData);
    } else{//其他
        parentDom.find('.crowd_box').show();
    }
});
//时间
$('body').on('click','.mod_data span',function(e){
    var th = $(this);
    var data = th.attr('data');
    var stDate = $('input[name="delivery_start_date"]'),
        edDate = $('input[name="delivery_end_date"]'),
        dateInput = $('#dateInput').val().split(' - ');
    $('input[name="delivery_time_type"]').val(data);
    if(data==1){ //不限
        stDate.val(get_date(0));
        edDate.val('');
    }else{
        stDate.val(dateInput ? dateInput[0]:'');
        edDate.val(dateInput ? dateInput[1]:'');
    }
})
//时间段
$('body').on('click','.mod_time span',function(e){
    var th = $(this);
    var data = th.attr('data');
    var allDay = ['00:00:00','23:59:59'];
    var stTime = $('input[name="delivery_start_time"]'),
        edTime = $('input[name="delivery_end_time"]'),
        timeInput = $('#timeInput').val().split(' - ');
    $('#timeType').val(data);
    if(data==1){ //不限
        stTime.val(allDay[0]);
        edTime.val(allDay[1]);
    }else{
        stTime.val(timeInput ? timeInput[0]:'');
        edTime.val(timeInput ? timeInput[1]:'');
    }
})

//年龄选择
$('body').on('click','.checkbox_box li',function(e){
    var th = $(this);
    var data = th.attr('data');
    var parentId = th.closest('.from-row').attr('id');
    if(th.hasClass('active')) {
        setValue(crowdArr, parentId, data, false);
        th.removeClass('active');
    }else{
        setValue(crowdArr, parentId, data, true);
        th.addClass('active');
    }
})
//出价选择
$('body').on('click','.mod_bid span',function(e){
    var th = $(this);
    var cost =  th.attr('cost');
    var data =  th.attr('data');
    $('input[name="bid_type"]').val(data);
    $('input[name="bid_money"]').attr('placeholder','出价金额'+cost+'-100元');
})
//素材选择
$('body').on('click','.mod_mat span',function(e){
    var th = $(this);
    var idx = th.index();
    var data = th.attr('data');
    $('input[name="img_type"]').val(data);
    $('.mat_list li').eq(idx).show().siblings('li').hide();
})
$('input[name="file_type"]').change(function(){
    var th = $(this),
        val = th.val();
    $('.file_set').find('.ip'+val).show().siblings().hide();
})
 //选中或取消区
$('body').on('click', '.items', function(e) {
    e.stopPropagation();
    var t = $(this),
        id = t.attr('id'),
        em = t.find('.checkbox'),
        parentId = t.closest('.from-row').attr('id');
    if(em.hasClass('chose')) {
        setValue(crowdArr, parentId, id, false);
        em.removeClass('chose');
    }else {
        em.addClass('chose');
        setValue(crowdArr, parentId, id, true);
    }
})
//设置性别
function setSex(id){
    crowdArr.sex = id ? [id] : [];
}
//查看设置的值是否已经存在
function find(arr, callback) {
  var i = 0, len = arr.length;
  while(i < len) {
    var item = arr[i];
    if(callback.call(callback, item, i)) {
      return [item, i];
    }
    i++;
  }
  return undefined;
}
//设置值
function setValue(obj, name, value, add) {
  var arr = obj[name] || [];
  var existed = find(arr, function(item) { return item === value });
  if(existed === undefined) {
    add && arr.push(value)
  }else {
    !add && arr.splice(existed[1], 1)
  }
}

//获取城市数据
function getAreas(id, isChecked) {
    var html = '';
    $.ajax({
        type: "GET",
        url: '/dcrowd/getTag/type/1/pid/'+id,
        dataType:'json',
        async : false,
        success: function(rs){
            var data = rs.data;
            if(rs.state == 1){
                $.each(data,function(k,v){
                    var checked = false;
                    for(var j = 0; j < region.length; j++) {
                         if(region[j] == v.c_tag_id) {
                            checked = true;
                            region.splice(j, 1);
                            break;
                         }
                    }
                    html +='<li class="lists" id="'+v.c_tag_id+'"><em class="checkbox ' + (isChecked ? 'chose' : '') +(checked ? ' chose' : '') + '"></em>'+v.tag_name+'</li>';
                })
            }else{
                tipTopShow('获取失败！');
            }
        },
        error:function(rs){
            tipTopShow("操作失败请重试！");
        }
    });
    return html;
}
//选择省市标签
$('body').on('click','#province_list li', function(e){ //点击省份获取城市列表
    var tagId = $(this).attr('id'),
    ul = cList.find('[pid="' + tagId + '"]'),
    isChecked = $(this).find('.chose').length > 0;
    aList.hide();
    cList.show();
    $(this).addClass('cur').siblings('li').removeClass('cur');
    if(ul.length > 0) {
        ul.show().siblings('ul').hide()
    }else {
        var data = getAreas(tagId, isChecked);
        cList.children('ul').hide()
        cList.append('<ul class="plist" pid="' + tagId + '">'+ data +'</ul>');
    }
}).on('click','#city_list li', function(e){ //点击城市获取区列表
    var tagId = $(this).attr('id'),
        pId = $(this).parent().attr('pid'),
      child = aList.children('[pid="' + pId + '"]'),
      ul = child.children('[pid="' + tagId + '"]'),
      isChecked = $(this).find('.chose').length > 0;
    aList.show();
    $(this).addClass('cur').siblings('li').removeClass('cur').parent().siblings('ul').find('li').removeClass('cur');
    if(child.length > 0) {
        child.show().siblings('div').hide()
        if(ul.length > 0) {
            ul.show().siblings('ul').hide()
        }else {
            var data = getAreas(tagId, isChecked);
            child.children('ul').hide()
            child.append('<ul class="plist" pid="' + tagId + '">'+ data +'</ul>');
        }
    }else {
        var data = getAreas(tagId, isChecked);
        aList.children('div').hide()
        aList.append('<div pid="' + pId + '"><ul class="plist" pid="' + tagId + '">'+ data +'</ul></div>');
    }
}).on('click', '#area_list .checkbox', function(e) { //选中或取消区
    e.stopPropagation()
    var t = $(this),
        plist = t.closest('.plist'),
        pSize = plist.children().length,
        pid = plist.attr('pid'),
        ppid = t.closest('div').attr('pid'),
        pplist = cList.children('[pid="' + ppid + '"]'),
        ppSize = pplist.children().length,
        cCheckbox = cList.find('#' + pid).children('.checkbox'),
        pCheckbox = pList.find('#' + ppid).children('.checkbox');

    if(t.hasClass('chose')) {
        t.removeClass('chose');
        cCheckbox.removeClass('chose');
        if(plist.find('.chose').length == 0) {
            cCheckbox.removeClass('childchose');
        }else {
            cCheckbox.addClass('childchose');
        }

        pCheckbox.removeClass('chose');
        if(pplist.find('.chose').length == 0) {
            if(pplist.find('.childchose').length == 0) {
                pCheckbox.removeClass('childchose');
            }else {
                pCheckbox.addClass('childchose');
            }
        }else {
            pCheckbox.addClass('childchose');
        }
    }else {
        t.addClass('chose');
        if(plist.find('.chose').length == pSize) {
            cCheckbox.addClass('chose').removeClass('childchose');
        }else {
            cCheckbox.addClass('childchose');
        }

        if(pplist.find('.chose').length == ppSize) {
            pCheckbox.addClass('chose').removeClass('childchose');
        }else {
            pCheckbox.addClass('childchose');
        }
    }
}).on('click', '#city_list .checkbox', function(e) { //选中或取消城市
    e.stopPropagation();
    var t = $(this),
        id = t.parent().attr('id'),
        plist = t.closest('.plist'),
        pSize = plist.children().length,
        pid = plist.attr('pid'),
        aCheckbox = aList.find('[pid="' + id + '"]').find('.checkbox'),
        pCheckbox = pList.find('#' + pid).children('.checkbox');

    if(t.hasClass('chose')) {
        t.removeClass('chose childchose');
        aCheckbox.removeClass('chose');

        pCheckbox.removeClass('chose');
        if(plist.find('.chose').length == 0) {
            if(plist.find('.childchose').length == 0) {
                pCheckbox.removeClass('childchose');
            }else {
                pCheckbox.addClass('childchose');
            }
        }else {
            pCheckbox.addClass('childchose');
        }
    }else {
        t.removeClass('childchose').addClass('chose');
        aCheckbox.addClass('chose');

        if(plist.find('.chose').length == pSize) {
            pCheckbox.addClass('chose').removeClass('childchose')
        }else {
            pCheckbox.addClass('childchose')
        }
    }
}).on('click', '#province_list .checkbox', function(e) { //选中或取消省份
    e.stopPropagation();
    var t = $(this),
        id = t.parent().attr('id'),
        cCheckbox = cList.find('[pid="' + id + '"]').find('.checkbox'),
        aCheckbox = aList.children('[pid="' + id + '"]').find('.checkbox');

    if(t.hasClass('chose')) {
        t.removeClass('chose childchose');
        cCheckbox.removeClass('chose');
        aCheckbox.removeClass('chose');

    }else {
        t.removeClass('childchose').addClass('chose');
        cCheckbox.addClass('chose').removeClass('childchose');
        aCheckbox.addClass('chose');
    }
})

//取选中的城市id
function getRegion(){
    var regionId = $('#region'),
        areaId =[],
        areaArr =[],
        result=[];
    pList.find('.chose').each(function() {
        var id = $(this).parent().attr('id');
        var text = $(this).parent().text();
        areaId.push(id);
        areaArr.push(text);
    })
    pList.find('.childchose').each(function() {
        var pid = $(this).parent().attr('id');
        var cListChose = cList.find('[pid="' + pid + '"]').find('.chose');

        cListChose.each(function() {
            var id = $(this).parent().attr('id');
            var text = $(this).parent().text();
            areaId.push(id);
            areaArr.push(text);
        })
    })
    cList.find('.childchose').each(function() {
        var pid = $(this).parent().attr('id');
        var aListChose = aList.find('[pid="' + pid + '"]').find('.chose');

        aListChose.each(function() {
            var id = $(this).parent().attr('id');
            var text = $(this).parent().text();
            areaId.push(id);
            areaArr.push(text);
        })
    })
    //result[0] = areaId.concat(region);
    result[0] = areaId.concat(region);
    result[1] = areaArr;
    return result;
}
