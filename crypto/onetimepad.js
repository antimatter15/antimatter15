var dict = "abcdefghijklmnopqrstuvwxyz";

function encrypt(plaintext,onetimepad,decrypt){
  var cipher = "";
  for(var i = 0; i< plaintext.length; i++){
    cipher+=dict.substr(((dict.indexOf(plaintext.substr(i,1))+((decrypt?-1:1)*dict.indexOf(onetimepad.substr(i,1))))%dict.length),1)
  }
  return cipher
}

function generatepad(length){
  var pad = ""
  for(var i = 0;i < length; i++){
    pad += dict.substr(Math.floor(Math.random()*dict.length),1)
  }
  return pad
}

function otp(text){
  var pad = generatepad(text.length)
  return [encrypt(text,pad),pad]
}