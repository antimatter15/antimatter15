function xDB(items){
  this.data = [];
  this.onAdd = function(){}
  if(items)
    this.add(items);
}

xDB.prototype.add = function(items){
  if(!(items.constructor + '').match(/array/i)){
    items = [items];
  }
  
  if(this.onAdd(items) === false){
    return this;
  }
  
  for(var i = items.length; i--;){
    this.data.push(items[i])
  }
  return this;
}


xDB.prototype.index = function(v){
  for(var i=this.length;i--&&this[i]!=v;);
  return i
}

xDB.prototype.get = function(criteria){
  function compare(a,b){
    var v = a.substr(1);
    
    switch(a.substr(0,1)){
      case ">":
        return v < b;
        break;
      case "<":
        return v > b;
        break;
      case "!":
        return v != b;
      case "=":
        return v == b;
        break;
      default:
        return a == b;
        break;
    }
  }
  var db = this;
  var matches = this.data.slice(0); //clone
  for(var i in criteria){
    for(var x = matches.length; x--;){
      if(!compare(criteria[i],matches[x][i])){
        matches.splice(x, 1);
      }
    }
  }
  matches.update = function(data){
    for(var x in matches){
      for(var y in data){
        matches[x][y] = data[y];
      }
    }
    return matches;
  }
  
  matches.remove = function(){
    for(var x = matches.length; x--;){
      db.data.splice(db.index(matches[x]),1);
    }
  }
  
  return matches;
}
