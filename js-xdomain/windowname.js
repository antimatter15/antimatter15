var _=_?_:{}
_.S=function(j,d,t){if(d)return eval('('+j+')');if(!j)return j+'';t=[];if(j.pop){for(x in j)t.push(_.S(j[x]));j='['+t.join(',')+']'}else if(typeof j=='object'){for(x in j)t.push(x+':'+_.S(j[x]));j='{'+t.join(',')+'}'}else if(j.split)j="'"+j.replace(/\'/g,"\\'")+"'";return j}

//_.wnc("http://www.antimatter15.com/misc/target.htm",{sdblank: "http://localhost/blank.htm", url: "data.txt"}, function(e){console.log(e)})

_.wnc = function(url, params, callback){
  var sdblank = "http://localhost/blank.htm";
  
  var ie = (navigator.appVersion.match(/MSIE (\d\.\d)/)) && (navigator.userAgent.toLowerCase().indexOf("opera") == -1);
  
  var xfnx = "xifnf"+Math.floor(Math.random()*12345)+"ud";
  var frameid = xfnx+"ifrm";
  
  var el = document.createElement("div");
  
  document.body.appendChild(el); //add to document
  
  if (!ie) {
    var frame = document.createElement("iframe") //submit target
    frame.id = frameid; //set iframe id
    frame.name = frameid; //set frame name
    el.appendChild(frame);
  }else{ //internets exploders
    el.innerHTML = "<iframe id=\""+frameid+"\" name=\""+frameid+"\" onload=\"window['"+xfnx+"']()\"></iframe>";
    var frame = document.getElementById(d);
  }
  
  frame.style.display = "none"; //ssh! it's secret!
  
  var loadable = false;
  
  window[xfnx] = frame.onload = function(){
    try{
      if(frame.contentWindow.location =='about:blank'){
        return;
      }else{
         callback?callback(frame.contentWindow.name):0;
      
        if(el.parentNode){
          el.parentNode.removeChild(el)
        };
        delete window[xfnx];
      }
    }catch(err){
      setTimeout(function(){
        if(window[xfnx])
          window[xfnx]()
      }, 500);
    }
    
  };
  
  frame.contentWindow.name = _.S(params);
  
  frame.src = url;
  
  frame.contentWindow && (frame.contentWindow.name = _.S(params)); // IE likes it afterwards
  
  return frame;
}
