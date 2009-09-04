var dct = "abcdefghijklmnopqrstuvwxyz".split("");
function td(a,b){return a.split("").map(function(x){return dct[(dct.indexOf(x)+b)%26]})}
var qry = "wkhtxlfneurzqiramxpsvryhuwkhodcbgrj";
for(var i = 0; i < 26; i++){
console.log(td(qry,i));
}


function gc(s){
var l = "";
s
  .split("")
  .sort()
  .forEach(function(x){
    if(l.substr(0,1)!=x){
      console.log(l.substr(0,1),l.length);
      l=''
    };
    l+=x
  });
  console.log(l.substr(0,1),l.length)
}

theencryptionstepperformedbyacaesarcipherisoftenincorporatedaspartofmorecomplexschemessuchasthevigenrecipher,andstillhasmodernapplicationintherot13systemaswithallsinglealphabetsubstitutionciphersthecaesarcipheriseasilybrokenandinpracticeoffersessentiallynocommunicationsecurity