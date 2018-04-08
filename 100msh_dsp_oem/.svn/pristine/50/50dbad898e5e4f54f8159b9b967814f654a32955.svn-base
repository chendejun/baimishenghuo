var $,tab,skyconsWeather;
layui.config({
	base : "/public/js/"
}).use(['bodyTab','form','element','layer','jquery'],function(){
	var form = layui.form(),
		layer = layui.layer,
		element = layui.element();
		$ = layui.jquery;
		tab = layui.bodyTab();

	//锁屏
	function lockPage(){
		layer.open({
			title : false,
			type : 1,
			content : $("#lock-box"),
			closeBtn : 0,
			shade : 0.9
		})
	}
	$(".lockcms").on("click",function(){
		window.sessionStorage.setItem("lockcms",true);
		lockPage();
	})
	// 判断是否显示锁屏
	if(window.sessionStorage.getItem("lockcms") == "true"){
		lockPage();
	}
	// 解锁
	$("#unlock").on("click",function(){
		if($(this).siblings(".admin-header-lock-input").val() == ''){
			layer.msg("请输入解锁密码！");
		}else{
			if($(this).siblings(".admin-header-lock-input").val() == "123456"){
				window.sessionStorage.setItem("lockcms",false);
				$(this).siblings(".admin-header-lock-input").val('');
				layer.closeAll("page");
			}else{
				layer.msg("密码错误，请重新输入！");
			}
		}
	});
	$(document).on('keydown', function() {
		if(event.keyCode == 13) {
			$("#unlock").click();
		}
	});

	//手机设备的简单适配
	var treeMobile = $('.site-tree-mobile'),
		shadeMobile = $('.site-mobile-shade')

	treeMobile.on('click', function(){
		$('body').addClass('site-mobile');
	});

	shadeMobile.on('click', function(){
		$('body').removeClass('site-mobile');
	});

	// 添加新窗口
	$(".layui-nav .layui-nav-item a").on("click",function(){
		addTab($(this));
		$(this).parent("li").siblings().removeClass("layui-nav-itemed");
	});
	


	//刷新后还原打开的窗口
	if(localStorage.getItem("str_menu")){
		str_menu = JSON.parse(localStorage.getItem("str_menu"));
		curmenu = localStorage.getItem("curmenu");
		var openTitle = '';
		for(var i=0;i<str_menu.length;i++){
			openTitle = '';
			// if(menu[i].icon.split("-")[0] == 'icon'){
			// 	openTitle += '<i class="iconfont '+menu[i].icon+'"></i>';
			// }else{
			// 	openTitle += '<i class="layui-icon"></i>';
			// }
			openTitle += '<i class="layui-icon"></i>';
			openTitle += '<cite>'+str_menu[i].title+'</cite>';
			openTitle += '<i class="layui-icon layui-unselect layui-tab-close" data-id="'+str_menu[i].layId+'">&#x1006;</i>';
			element.tabAdd("bodyTab",{
				title : openTitle,
		        content :"<iframe src='"+str_menu[i].href+"' data-id='"+str_menu[i].layId+"'></frame>",
		        id : str_menu[i].layId
			})
			//定位到刷新前的窗口
			if(curmenu != "undefined"){
				if(curmenu == '' || curmenu == "null"){  //定位到后台首页
					element.tabChange("bodyTab",'');
				}else if(JSON.parse(curmenu).title == str_menu[i].title){  //定位到刷新前的页面
					element.tabChange("bodyTab",str_menu[i].layId);
				}
			}else{
				element.tabChange("bodyTab",str_menu[str_menu.length-1].layId);
			}
		}
	}

})

//打开新窗口
function addTab(_this){
	tab.tabAdd(_this);
}

//设置cookie
function setCookie(cname, cvalue, exdays) {
	var exdays = exdays ? exdays :'365';
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
//清除cookie  
function clearCookie(name) {  
    setCookie(name, "", -1);  
}  
function checkCookie() {
    var user = localStorage.getItem("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}
function delCookie(name) {                   //删除一个cookie  
    var exp = new Date();  
    exp.setTime(exp.getTime() - 1);  
    var cval=localStorage.getItem(name);  
    if(cval!=null)  
    document.cookie= name + "='';expires="+exp.toUTCString();  
}  
// 设置每天显示一次
function setCookieOneDay(cname, cvalue) {
    var x = new Date();
    x.setDate(x.getDate()+1);
    x.setHours(0);
    x.setMinutes(0);
    x.setSeconds(0);
    // console.log(x.Format('yyyy-MM-dd hh:mm:ss'));
    var y = new Date();
    // console.log(y.Format('yyyy-MM-dd hh:mm:ss'));
    // var interval_zero = 24*3600*1000-(y.getTime()-x.getTime());
    var interval_zero = (x.getTime()-y.getTime());
    // console.log(interval_zero);
    // console.log(interval_zero/1000/3600);
    y.setTime(y.getTime() + interval_zero);
    var expires = "expires="+y.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

