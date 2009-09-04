function superawesome(config){
  this.server1 = "server.php"
  this.server2 = "server.php"
  this.rsaExp = "10001";
  this.rsaBit = 256;
  this.aesBit = 256;
  for(var x in config){
    this[x] = config[x];
  }
  /////////////////////////////
  this.rsaKey = null;
  this.rsaCipher = "";
  this.rsaPlain = "";
  this.aesCipher = "";
  this.aesPlain = "";
}

superawesome.prototype.genRSA = function(){
  this.rsaKey = new RSAKey();
  this.rsaKey.generate(this.rsaBit, this.rsaExp);
}

superawesome.prototype.sendPublic = function(callback){
  var thc = this;
  _.X(this.server1, function(ciphertext){
    thc.rsaCipher = ciphertext;
    callback?callback():0
  },"act=getkey&size="+this.rsaBit+"&public="+this.rsaKey.e+"&modulus="+this.rsaKey.n.toString())
}

superawesome.prototype.decryptCipher = function(){
  return this.rsaPlain = this.rsaKey.decrypt(this.rsaCipher)
}

superawesome.prototype.AESEncode = function(data){
  this.aesPlain = data;
  return this.aesCipher = AESEncryptCtr(this.aesPlain, this.rsaPlain, this.aesBit)  
}

superawesome.prototype.sendSymmetric = function(callback){
  _.X(this.server2, function(response){
    callback?callback(response):0
  },"act=send&size="+this.aesBit+"&modulus="+this.rsaKey.n.toString()+"&data="+encodeURIComponent(this.aesCipher))

}

superawesome.prototype.send = function(dat, cbk){
  var start = new Date-0;
  var thc = this;
  this.genRSA();
  setTimeout(function(){
    thc.sendPublic(function(){
      setTimeout(function(){
        thc.decryptCipher();
        setTimeout(function(){
          thc.AESEncode(dat);
          setTimeout(function(){
            thc.sendSymmetric(function(resp){
              cbk?cbk(resp,new Date-start):0
            })
          },0);
        },0)
      },0)
    })
  },0)
}


superawesome.send = function(dat, cbk){
  (new superawesome()).send(dat, cbk)
}