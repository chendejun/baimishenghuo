//设置cookie
function setCookie(cname, cvalue, exdays) {
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
    var user = getCookie("username");
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
    var cval=getCookie(name);  
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

