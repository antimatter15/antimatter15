var query = "";
var queue = ["google","crss.exe","explorer.exe","cygwin.dll"]
var queries = {};

function search(){
  var s = document.createElement("script");
  s.src = "http://www.google.com/uds/GwebSearch?callback=sr&v=1.0&context=0&q="+query;
  document.body.appendChild(s)
}
function sr(a,b,c,d,e){
  queries[query] = b.cursor.estimatedResultCount;
  var delay =  Math.floor(Math.random()*1337) + 1337;
  console.log("Found: ",query, "Results: ", queries[query], "Queued Delay: ", delay)
  
  setTimeout(next, delay)
}
function next(){
  if(queue.length > 0){
    query = queue.splice(0,1)[0];
    search();
  }else{
    console.log("Nothing left to do...")
  }
}