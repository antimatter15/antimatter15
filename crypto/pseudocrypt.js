"hello".split("").map(function(x){return String.fromCharCode(x.charCodeAt(0)+Math.ceil(Math.random()*3))})

.split("").map(function(x){return String.fromCharCode(x.charCodeAt(0)-1)})

function v(k){
return Math.floor((parseInt(k.split("").map(function(x){return x.charCodeAt(0)+k.length}).join("").split("").map(function(x){return Math.ceil((x+k.length)/4)}).map(function(x){return parseInt(x)%(k.length%2?2:3)?(x-1):""}).join(""))-k.length*k.length)/(k.length%2?3:4)).toString().split("").map(function(x){return x-15})
}

v("hello")