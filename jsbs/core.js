var doc = null;
var circles = [];
var sparks = [];

function rand(dig,min){
  return Math.floor(Math.random()*dig)+(min?min:0);
}

function randcolor(){
  return "rgb("+rand(200,55)+","+rand(200,55)+","+rand(200,55)+")";
}

function drawcircle(){
  var circle = doc.circle(rand(550-100-16,50),rand(400-100-16,50), 8);
  circle.attr("fill", randcolor());
  circle.attr("stroke-width", 0);
  circle.dir = rand(360)*Math.PI/180;
  circles.push(circle);
  return circle;
}

$(document).ready(function(){
  doc = Raphael("canvas",550, 400);
  for(var i = 0; i < 35; i++){
    drawcircle();
  }
  
  $(doc.canvas).click(function(){
    var spark = doc.circle(e.pageX,e.pageX, 30);
    spark.attr("fill", "#FFFFFF");
    spark.attr("stroke-width", 0);
    sparks.push(spark);            
    setTimeout(function(){
      $(spark[0]).remove();
    },3000)
  })
  
  
  setInterval(function(){
    $.each(circles, function(){

      if(!this.stop){
      
        var x1 = parseFloat(this.attr("cx")),
            y1 = parseFloat(this.attr("cy")),
            r1 = parseFloat(this.attr("r")),
            ce = this;
            
            
        this.attr("cx", x1 + Math.cos(this.dir))
        this.attr("cy", y1 + Math.sin(this.dir))
        
        if(x1 > 500 || x1 < 50 || y1 > 350 || y1 < 50){
          this.dir = 180 - (this.dir/Math.PI*180)
        }
        
        $.each(sparks, function(){
          var dx = this.attr("cx") - x1,
          dy = this.attr("cy") - y1,
          radii = r1 + parseFloat(this.attr("r"));
          
          if ((dx*dx)+(dy*dy) < radii*radii){
            //collide
            //alert("kolliddeeedd");
            ce.stop = true;
            sparks.push(ce);
            ce.attr("r", 30);
            
            setTimeout(function(){
              $(ce[0]).remove();
            },3000)
          }
        })
      
      }
    })
  },10);
});

