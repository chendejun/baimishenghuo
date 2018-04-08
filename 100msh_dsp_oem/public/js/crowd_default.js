(function () {
    var pList = $('#province_list'),
        cList = $('#city_list'),
        aList = $('#area_list'),
        inputPerson = $('#personNo'),
        crowdInfo = addSt2.crowd_info;
    const OPEN = 'cur',
        CHOSE = 'chose',
        CHILDCHOSE = 'childchose';
    if(crowdInfo){
        inputPerson.val(crowdInfo);
        if(crowdInfo==0){//不限
            $('#area_set').val('1');
            $('#crowd_nav span').eq(0).addClass('cur');
            $('#crowd_default').click();
            crowdInfo='';
            crowdDataArr = (crowdInfo || '').split(',');
            idArr = [].concat(crowdDataArr);
        }else{
            crowdDataArr = (crowdInfo || '').split(',');
            idArr = [].concat(crowdDataArr);
            $('#area_set').val('0');
            $('#crowd_nav span').eq(1).addClass('cur');
        }
    }
    getDefaultArea();//请求是否限制区域
    //地区列表请求
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
                        if(crowdInfo){
                            for(var j = 0; j < crowdDataArr.length; j++) {
                                 if(crowdDataArr[j] == v.c_tag_id) {
                                    checked = true;
                                    crowdDataArr.splice(j, 1);
                                    break;
                                 }
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
    //是否限制区域判断
    function getDefaultArea(){
        $.ajax({
            type: "GET",
            url: '/delivery/getDefaultArea',
            dataType:'json',
            async : false,
            success: function(rs){
                var areaData = rs.area_id,
                    ahtml='';
                if(rs.state == 1){
                    if(areaData=='0'){
                        $('#area_default').val('0');//0不限制，1限制
                    }else{
                        $('#area_default').val('1');//0不限制，1限制
                        $('#default_box').hide();
                        $('#crowd_default').hide();
                        $('.box_crow2').hide();
                        var pchecked = false,cchecked = false;
                        if(crowdInfo){
                            for(var j = 0; j < crowdDataArr.length; j++) {
                                if(crowdDataArr[j] == areaData.provinceId) {
                                    pchecked = true;
                                    crowdDataArr.splice(j, 1);
                                }
                                if(crowdDataArr[j] == areaData.cityId){
                                    cchecked = true;
                                    crowdDataArr.splice(j, 1);
                                }
                            }
                        }
                        $.each(areaData.list,function(k,v){
                            var achecked = false;
                            if(crowdInfo){
                                for(var j = 0; j < crowdDataArr.length; j++) {
                                    if(crowdDataArr[j] == v.c_tag_id) {
                                        achecked = true;
                                        crowdDataArr.splice(j, 1);
                                        break;
                                    }
                                }
                            }
                            ahtml +='<li class="lists" id="'+v.c_tag_id+'"><em class="checkbox ' + (pchecked ? ' chose' : '')+(cchecked ? 'chose' : '')+(achecked ? ' chose' : '') + '"></em>'+v.tag_name+'</li>';
                        })
                        pList.hide().html('<ul class="plist"><li class="lists" id="'+ areaData.provinceId +'"><em class="checkbox childchose"></em>'+ areaData.provinceName +'</li></ul>');
                        cList.html('<ul class="plist" pid="'+ areaData.provinceId +'"><li class="lists" id="'+ areaData.cityId +'"><em class="checkbox ' + (pchecked ? 'chose' : '') +(cchecked ? ' chose' : '') + '"></em>'+ areaData.cityName +'</li></ul>');
                        aList.html('<div pid="' + areaData.provinceId + '"><ul class="plist" pid="' + areaData.cityId + '">'+ ahtml +'</ul></div>');
                    }
                    if(crowdInfo && crowdInfo !=0){
                        setTimeout(function(){$('#crowd_id').click();},200);//请求省市
                    }
                }else{
                    tipTopShow('获取失败！');
                }
            },
            error:function(rs){
                tipTopShow("操作失败请重试！");
            }
        });
    }
    //选择的区域类型 不限or选择省市
    $('.crowd_nav span').bind('click',function(){
        var th = $(this);
            thisId = th.attr('_id');
        th.parent().find('span').removeClass('cur');
        th.addClass('cur');
        $('#area_set').val(thisId);
        if(thisId ==0){
            $('.crowd_box').show();
        }else{
            $('.crowd_box').hide();
        }
    })
    //选择省市标签
    $('body').on('click','#crowd_id', function(e){ //点击选择框获取省份列表
        var tagId = $(this).attr('_id');
        var isDefault = $('#area_default').val();// 0不限制，1限制
        if(isDefault ==1){//地区限制
            pList.hide();
            cList.show();
            return;
        };
        if(pList.html() == '') {
            var data = getAreas(tagId);
            pList.show().html('<ul class="plist">'+ data +'</ul>');
        }
    }).on('click','#province_list li', function(e){ //点击省份获取城市列表
        var tagId = $(this).attr('id'),
            ul = cList.find('[pid="' + tagId + '"]'),
            isChecked = $(this).find('.chose').length > 0;
        aList.hide();
        cList.show();
        $(this).addClass('cur').siblings('li').removeClass('cur');
        if(ul.length > 0) {
            ul.show().siblings('ul').hide();
        }else {
            var data = getAreas(tagId, isChecked);
            cList.children('ul').hide();
            cList.append('<ul class="plist" pid="' + tagId + '">'+ data +'</ul>');
            var cUl = cList.find('[pid="' + tagId + '"]').children('li'), //改变省份的选择状态
                cUlChose = cUl.find('.chose');
            if(!isChecked && cUlChose.length>0){
                if(cUl.length == cUlChose.length){
                    $(this).find('.checkbox').addClass('chose');
                }else{
                    $(this).find('.checkbox').addClass('childchose');
                }
            }
        }
    }).on('click','#city_list li', function(e){ //点击城市获取区列表
        var tagId = $(this).attr('id'),
            thParent = $(this).parent();
            pId = thParent.attr('pid'),
            child = aList.children('[pid="' + pId + '"]'),
            parent =pList.find('[id="' + pId + '"]'),
            ul = child.children('[pid="' + tagId + '"]'),
            isChecked = $(this).find('.chose').length > 0;
        aList.show();
        $(this).addClass('cur').siblings('li').removeClass('cur').parent().siblings('ul').find('li').removeClass('cur');
        if(child.length > 0) {
            child.show().siblings('div').hide();
            if(ul.length > 0) {
                ul.show().siblings('ul').hide();
            }else {
                var data = getAreas(tagId, isChecked);
                child.children('ul').hide()
                child.append('<ul class="plist" pid="' + tagId + '">'+ data +'</ul>');
                var aUl = child.find('[pid="' + tagId + '"]').children('li'), //改变城市的选择状态
                    aUlChose = aUl.find('.chose');
                if(!isChecked && aUlChose.length>0){
                    if(aUl.length == aUlChose.length){
                        $(this).find('.checkbox').addClass('chose');
                    }else{
                        $(this).find('.checkbox').addClass('childchose');
                    }
                }
            }
        }else {
            var data = getAreas(tagId, isChecked);
            aList.children('div').hide()
            aList.append('<div pid="' + pId + '"><ul class="plist" pid="' + tagId + '">'+ data +'</ul></div>');
            var child = aList.children('[pid="' + pId + '"]'),//改变省份的选择状态
                aUl = child.find('[pid="' + tagId + '"]').children('li'),
                aUlChose = aUl.find('.chose');
            if(!isChecked && aUlChose.length>0){
                if(aUl.length == aUlChose.length){
                    $(this).find('.checkbox').addClass('chose');
                }else{
                    $(this).find('.checkbox').addClass('childchose');
                }
            }
        }
        var allLiLen = thParent.children('li').length,
            chsLiLen = thParent.find('.chose').length,
            childChsLiLen = thParent.find('.childchose').length;
        if(chsLiLen + childChsLiLen ==0){
            parent.children('em').removeClass('chose childchose');
        }else if( chsLiLen == allLiLen){
            parent.children('em').removeClass('childchose').addClass('chose');
        }else{
            parent.children('em').removeClass('chose').addClass('childchose');
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
        var isDefault = $('#area_default').val();// 0不限制，1限制

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
        if(isDefault==1){
             pCheckbox.removeClass('chose').removeClass('childchose');
             pCheckbox.addClass('childchose');
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
        var isDefault = $('#area_default').val();// 0不限制，1限制
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
        if(isDefault==1){
             pCheckbox.removeClass('chose').removeClass('childchose');
             pCheckbox.addClass('childchose');
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
})();