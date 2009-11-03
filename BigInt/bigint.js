function BigInt(str){
  if(str.BigInt){
    return str;
  }else{
    return new BigInt.Core(str)
  }
}
BigInt.Core = function(str){
  if(typeof str != "string") throw("Argument not string");
  this.str = str;
  this.BigInt = true;
}

BigInt.Core.prototype.eq = function(comp){
  comp = BigInt(comp);
  return this.str == comp.str
}

BigInt.Core.prototype.gt = function(comp){
  comp = BigInt(comp);
  if(this.eq(comp)) return false;
  var diff = this.str.length - comp.str.length;
  if(diff != 0) return diff > 0;
  //a.len == b.len && a != b
  var ctr = 0;
  while(this.str[ctr] == comp.str[ctr]){
    ctr++; //probably could have stuck this up above and made it a 1 liner
  }
  return parseInt(this.str[ctr]) > parseInt(comp.str[ctr])
}

BigInt.Core.prototype.lt = function(comp){
  comp = BigInt(comp);
  if(this.eq(comp)) return false;
  return !this.gt(comp);
}

BigInt.Core.prototype.add = function(comp){
  comp = BigInt(comp);
  if(comp.str.length > this.str.length){
    this.str = (new Array(comp.str.length-this.str.length+1)).join("0")+this.str
  }else{  
    comp.str = (new Array(this.str.length-comp.str.length+1)).join("0")+comp.str
  }
  for(var i = 0, out = ""; i < comp.str.length; i++){
    out += parseInt(this.str[i]) + parseInt(comp.str[i]);
  }
  return out;
}

BigInt.Core.prototype.sub = function(comp){
  comp = BigInt(comp);
  if(this.lt(comp)) throw("Cant handle negatives");
  //add padding, 900-12 = 900-012
  comp.str = (new Array(this.str.length-comp.str.length+1)).join("0")+comp.str
  for(var i = this.str.length, result = [], borrow = false; i--;){
    var val = parseInt(this.str[i]) - parseInt(comp.str[i]);
    if(borrow){
      val--;
      borrow = false;
    }
    if(val < 0){
      result.push(10+val)
      borrow = true;
    }else{
      result.push(val)
    }
  }
  return result.reverse().join("");
}

BigInt.Core.prototype.mul = function(comp){
  comp = BigInt(comp)
}
