setInterval(function(){
  $.get("http://www.demonoid.com/", function(data){
    if(data.indexOf('<font class="red"><b>closed</b></font>') == -1){
     jetpack.notifications.show({title: 'Demonoid Registrations Open',
                                body: "Demonoid Registrations are Open",
                                icon: 'http://www.demonoid.com/favicon.ico'});
    }
  })
}, 3.145926535 * 60 * 1000)

