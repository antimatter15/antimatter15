function send_data(url, params, callback){
  var id = "wk_xfnx"+Math.floor(Math.random()*12345)
  var div = document.createElement("div");
  var form = document.createElement("form");

  document.body.appendChild(div);  
  
  window[id] = function(){
    callback();
    delete window[id];
    setTimeout(function(){
      if(div.parentNode){
        div.parentNode.removeChild(div);
      }
    }, 50000)
  };
  
  div.style.display = "none";
  
  div.innerHTML = "<iframe id=\""+id+"\" name=\""+id+"\" onload=\"window['"+id+"']()\"></iframe>"
  
  form.target = id;
  form.action = url;
  form.method = "POST";
  
  div.appendChild(form);  
  
  for(var key in params){
    var input = document.createElement("input"); //create new input
    input.type = "hidden"; //it's hidden! SSH!!!!
    input.name = key; //set name
    input.value = params[key]; //set value
    form.appendChild(input);
  }
  
  form.submit();
  
  console.log(form)
}
