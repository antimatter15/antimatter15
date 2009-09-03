jetpack.future.import("storage.simple");

setInterval(function(){
  $.getJSON("http://search.twitter.com/trends/current.json",function(d){
    var store = jetpack.storage.simple;
    if(!store.current_trends || !store.to_show_trends){
      store.current_trends = []
      store.to_show_trends = []
    }
    for(var x in d.trends){
      var c=store.current_trends.join(','),n='';
      store.current_trends = [];
      for(var i = 0,l = d.trends[x].length; i < l; i++){
        n = d.trends[x][i].name
        store.current_trends.push(n)
        if(c.indexOf(n) == -1){
          store.to_show_trends.push([d.trends[x][i].name,d.trends[x][i].query])
        }
      }
    }
    show_trend();
  });
}, 3.145926535 * 60 * 1000)

function show_trend(){
  var store = jetpack.storage.simple;
  if(store.to_show_trends.length > 0){
    tsm = store.to_show_trends.splice(0,1)[0]
    jetpack.notifications.show({title: 'New Twitter Trending Topic',
                            body: tsm[0],
                            icon: 'http://assets1.twitter.com/images/favicon.ico'});
    setTimeout(show_trend,10*1000)
  }
}
