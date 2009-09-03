/*
n72, IO, NEXT
n105, IO, NEXT
ZERO, ZERO, END

ZERO, ZERO, A

START:
print "hello"
goto START


START:
ch, IO, NEXT
ce, IO, NEXT
cl, IO, NEXT
cl, IO, NEXT
co, IO, NEXT
JMP START

ADD, JMP, MOV, MUL, SUB
*/


var code = "a,zero;\
zero,b;\
h,-1,-1;\
zero,zero,-1;\
.zero:0;\
.a:30;\
.b:12;\
.h:72"

var opcodes = {
  add: "{0},zero;zero,{1},zero;zero",
  sub: "{0},{1}",
  subleq: "{0},{1},{2}",
  biz: "{0},zero,L1;zero,zero,OT;L1:zero,zero;zero,{0},{1};OT:",
  beq: "sub {0},{1};biz {1},{2}", //not functional
  bleq: "zero,{0},{1}",
  out: "{0},-1",
  mov: "{1},{1};{0},zero;zero,{1};zero",
  jmp: "zero,zero,{0}",
  exit: "zero,zero,-1"
}

var compc = ".startloop;bleq ptr,endloop;sub one,ptr;mov sta,temp;add ptr,temp;out temp;jmp startloop;.endloop;exit;.one 1;.temp 0;.ptr 26;.sta 65"


var lines = compc.split(/;|\n/);
var temp = [];
var out = [];
for(var i = 0; i < lines.length; i++){
  if((temp = lines[i].split(" "))[0].substr(0,1) == "."){
    if(!temp[1])temp[1]='';
    out.push(temp[0]+":"+temp[1])
  }else{
    if(!temp[1])temp[1]='';
    var ins = opcodes[temp[0]];
    temp[1].split(",").forEach(function(x,y){
      ins = ins.split("{"+y+"}").join(x);
    })
    out.push(ins);
  }
}
out.push("zero:0")
console.log("Compiled Out: ", out)
parse(assemble(out.join(";").split(":;").join(":")))

function assemble(code){
  var labels = {};
  var lines = code.split(/;|\n/);
  var temp = [];

  for(var i = 0; i < lines.length; i++){
    if(lines[i].substr(0,1) != "."){
      switch((temp = lines[i].split(/\s|,/)).length){
        case 1:
          lines[i] += "," + lines[i];
        case 2:
          lines[i] += ",next"
        break;
      }
    }else{
      lines[i] = lines[i].substr(1);
      if(lines[i].indexOf('"') != -1){
        temp = [];
        for(var c = lines[i].indexOf('"')+1; lines[i].substr(c,1) != '"'; c++){
          temp.push(lines[i].charCodeAt(c))
        }
        lines[i] = lines[i].split(":")[0] + ":" + temp.join(",")
      }
    }
  }
  console.log("shorthand parsed:", lines)
  var sects = lines.join(";").split(/;|,/);

  for(var i = 0; i < sects.length; i++){
    if((temp = sects[i].split(":")).length == 2){
      labels[temp[0]] = i;
      sects[i] = temp[1];
    }
    if(sects[i].match(/next|\?/)){
      sects[i] = i+1;
    }
  }

  console.log("labels found: ", labels)
  console.log("Progress: ",sects);

  for(var i = 0; i < sects.length; i++){
    if(labels[sects[i]] !== undefined) sects[i] = labels[sects[i]];
  }
  
  console.log("pre-out", sects)
  
  sects = sects.map(function(x){return parseInt(x+'')})

  console.log('out: ', sects)
  return sects;
}


function parse(mem){
  var ip = 0; //instruction counter
  var count = 0; //counter
  var out = "";
  var log = 0;

  while(ip >= 0){
    if(count > 1000){
      console.error("Execution Limit Reached")
      break;
    }
    if(ip + 3 > mem.length){
      console.error("Incomplete Instruction",ip,mem.slice(ip));
      break;
    }
    a = mem[ip];
    b = mem[ip + 1];
    c = mem[ip + 2];
    
    if(b == -1){
      //log result
      out += String.fromCharCode(mem[a]);
      if(log) console.info(String.fromCharCode(mem[a]), "("+mem[a]+")");
      ip += 3;
    }else{
      mem[b] -= mem[a];
      if(log) console.log(ip,":",[a,b,c],"A=",mem[a],"B=",mem[b]);
      
      if(mem[b] > 0){
        //continue normal operation
        ip += 3;
      }else{
        //less than or equal to zero
        ip = c;
      }
    }
    count++;
  }

  console.log("Executed with ",count," operations");
  console.info(out)
}
