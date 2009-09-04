/*
todo: hierichal action system
*/

console.log("Loading Parser");





function error(msg,data){
  throw msg+" @ Line "+data.nline;
}

function countIndent(data){
  for(var i = 0; data.line.split("")[i] == " "; i++){};
  if(i % 2 != 0){
    error("WRONG INDENT SIZE", data)
  }
  return i;
}


function parseBlock(lines){
  for(var i = 0; i < lines.length; i++){
    var line = lines[i];
    
  }
}

/* Create a list of code*/
function mapStructure(lines){
  var cfunc = null;
  var struc = {};
  
  for(var i = 0; i < lines.length; i++){

    data = {};
    data.nline = i;
    data.line = lines[i];
    data.indent = countIndent(data);
    data.l_line = (i > 0)?lines[i - 1]:"";
    data.l_indent = countIndent({line: data.l_line});
    
    
    if(data.indent - data.l_indent > 2){
      error("INDENT GROWTH ERROR", data)
    }
    
    if(data.indent <= countIndent({line:cfunc?cfunc:""})){
      cfunc = null;
    }
    
    if(cfunc){ //on code set queue
        struc[cfunc].push(data.line.substr(countIndent({line:cfunc})+2));
    }else{ //or else
      if(data.indent - data.l_indent == 2){ //(indent change) decklaring variable
          cfunc = data.l_line; //it's declaring variables
          struc[cfunc] = [data.line.substr(countIndent({line:cfunc})+2)]
      }
    }
  }
  return struc;
}





/*


function parse(code,variables){
  if(!code){code=document.getElementById("src").value}

  function setVariable(name, value){
    console.log("Set Variable",name, value)
    variables[name] = value
  }

  function addVariable(name, value){
    console.log("Add Variable",name,value)
    variables[name]+= "\n"+value
  }

  function checkName(name){
    //disallowed things: [comma] [period] [space]
    //console.log("Check name", name)
    return name.match(/[,\. ]/ig) == null
  }

  function countIndent(line){
    for(var i = 0; line.split("")[i] == " "; i++){};
    //console.log("Count Indent",line,"("+i+")")
    return i;
  }

  console.log("Parsing")
  
  if(!variables){variables = {}}
  
  var lines = code.split(/[\n,]/ig).concat([""]); //two delimiters, a new line, a comma
  var setvar = null;

  for(var nLine = 0; nLine < lines.length; nLine++){
    var line = lines[nLine]; //current line
    var lline = (nLine>0)?lines[nLine-1]:""; //last line, if exists
    //check for errors/
    if(countIndent(line) % 2 != 0){throw "WRONG SIZE INDENT ERROR @"+nLine}
    if(countIndent(line) - countIndent(lline) > 2){throw "INDENT GROWTH ERROR @"+nLine}
    //end error check/

    if(countIndent(line) <= countIndent(setvar?setvar:"")){ //if indent goes down
      //action = 0; 
      if(setvar && setvar.indexOf(":") == 0){
          console.log("exec",line,parse(variables[setvar]))
          exec(variables[setvar.substr(1)], parse(variables[setvar]));
          delete variables[setvar];
      }
      setvar = null //reset
    }

    if(setvar){ //on set variable queue
        addVariable(setvar,line.substr(countIndent(setvar)+2)); //add to variable
    }else{ //or else
        if(countIndent(line) - countIndent(lline) == 2){ //(indent change) decklaring variable

          if(!checkName(lline)){
            throw "INVALID FUNCTION NAME ERROR @"+(nLine-1);
          }
          setvar = lline; //it's declaring variables
          setVariable(lline,line.substr(countIndent(setvar)+2)); //set variable!
        
        }
    }
  }

  console.log("done");
  return (lines.length>1)?variables:code;
}

function superparse(){
  code = document.getElementById("src").value;
  var rvar = parse(code);
  
  return rparse(rvar);
}

function run(){
  var gvar = superparse();
  for(var i in gvar){
    if(i.indexOf(":") == 0){
      //run code
    }
  }
  console.log(gvarx, gexec);
  for(var i in gexec){
    
  }
}

function exec(code,v){
  var chr = code.split("")
  var buf1 = "";
  var buf2 = "";
  var nt = "";
  
  function commit(o,a,b){
    switch(o){
      case "+":
        return a + val(b,v)
        break;
      case "-":
        return a - val(b,v)
        break;
      case "":
        return val(b,v)
    }
  }
  
  for(var i = 0; i < chr.length; i++){
    
    switch(chr[i]){
      case "+":
      
        buf2 = commit(nt, buf2, buf1);
        nt = "+";
        buf1="";
        break;
      case "-":
        buf2 = commit(nt, buf2, buf1);
        nt = "-";
        buf1="";
        break;
      default:
        buf1 += chr[i]
    }
  }
  
  buf2 = commit(nt, buf2, buf1);
  
  console.log("*****************************",buf2);
  
  return buf2;
}

function val(b,v){
  if(b.replace(/[0-9\. ]/g,"")==""){
    return parseFloat(b);
  }else{
    for(var i in v){
      if(i == b.replace(/\s/g,"")){
        return parseFloat(v[i]); //i donno how to use vi
      }
    }
    throw "ERROR VARIABLE NOT DEFINED @ UNKNOWN";
  }
}

function rparse(rvar){
  if(typeof rvar == "object"){
    for(var i in rvar){
      rvar[i] = rparse(parse(rvar[i]));
      console.log("--------------------");
    }
  }
  return rvar
}

*/
