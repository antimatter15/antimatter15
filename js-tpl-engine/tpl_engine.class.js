/*
           Template Engine v0.1 
    Released under the GNU GPL v3 License
   <http://www.gnu.org/licenses/gpl.html>
   
     For more information check out
    http://js-tpl-engine.googlecode.com
  
   You are free to modify this script and
       redistribute it as you wish
       
   This script is provided "AS IS" with no
  warranty whatso ever, the creators and or
  distributors of this script are not liable
 for anything that may happen from using this
                  script!
*/

function Template(data){
  //now let's set up some initial variables...
  this.data = data?data:""; //if there's no intital template, use blank string
  this.vars = {}; //where all the magic is stored
}

Template.prototype.assignVar = function(variable, value, overwrite){
  if(typeof variable == "string"){
    var tmp = {};
    tmp[variable] = value;
    variable = tmp;
  }else{
    overwrite = value;
  }
  for(var x in variable){ //loopy!
    if(!this.vars[x] || overwrite){
      this.vars[x] = variable[x];
    }
  }
}

Template.prototype.parseTpl = function(tpl, output){
  if(!tpl) tpl = this.data;
  for(var x in this.vars){
    tpl = tpl.replace(new RegExp("{"+x+"}","g"), this.vars[x]);
  }
  return tpl;
}

Template.prototype.fileTpl = function(file, output){
  /*Based on my vX Ajax Function*/
  var x=new(this.ActiveXObject?ActiveXObject:XMLHttpRequest)('Microsoft.XMLHTTP');
  x.open('GET',file,!0);
  x.onreadystatechange=function(){
    x.readyState==4&&output?output(this.parseTpl(x.responseText)):0;
  };
  x.send();
}