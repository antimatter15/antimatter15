/*
 * New Concept Vector Graphic editor based off Raphael framework
 */
 

 
function OPEditor(elem, width, height){
    if (typeof(Raphael) != "function") { //check for the renderer
        return false; //if renderer isn't there, return false;
    }
    
    this.selected = null;
    
    this.shapes = [];
    
    this.mode = "rect";
    
    this.mouseaction = "";
    
    this.onHitXY = [0, 0];
    
    this.canvas = Raphael(elem, width, height);
    
    this.canvas.canvas.onmousedown = this.onMouseDown;
    this.canvas.canvas.onmousemove = this.onMouseMove;
    this.canvas.canvas.onmouseup = this.onMouseUp;
    this.canvas.canvas.ondblclick = this.onDblClick;
    

    
    this.tracker = null;
    
    this.trackerconfig = {
        bx: 10, //frame size
        sx: 5, //box size
        bx_ia_fl: "#fff", //inactive fill
        bx_ho_fl: "#f00", //hover fill
        bx_ia_sk: "green", //inactive stroke
        fe_ia_sk: "#ccc", //inactive bound frame
    }
    
    this.fill = "#f00"; //red
    this.stroke = "#000"; //black
}

OPEditor.prototype.onMouseDown = function(event){

    var shape = null;
    
    switch (ope.mode) {
        case "rect":
            shape = ope.canvas.rect(event.clientX, event.clientY, 1, 1);
            break;
        case "ellipse":
            shape = ope.canvas.ellipse(event.clientX, event.clientY, 1, 1);
            break;
		case "path":
			shape = ope.canvas.path({}).moveTo(event.clientX, event.clientY);
			shape.type = "path";
			break;
		case "polygon":
			if (ope.selected) {
				return ope.selected.lineTo(event.clientX, event.clientY)
				
			}
			else {
				shape = ope.canvas.path({}).moveTo(event.clientX, event.clientY).lineTo(event.clientX + 1, event.clientY + 1);
        shape.type = "path";
			}
			break;
        case "select":
            if (event.target == ope.canvas.canvas) {
                ope.unselect();
                ope.hideTracker();
            }
            return false
            break;
        default:
            return false
    }
    
    if (shape) {
        var shapeid = "shape:" + Math.random().toString().replace(".", ""); //replace with better UID generation system
        shape.attr("fill", ope.fill);
        shape.attr("stroke", ope.stroke);
        ope.shapes.push(shape);
        ope.selected = shape;
        shape.id = shapeid;
        shape[0].setAttribute("id", shapeid)
        shape[0].onmousedown = ope.onHit;
    }
    
}

OPEditor.prototype.setMode = function(mode){
    ope.unselect();
    ope.hideTracker();
    ope.mode = mode;
}

OPEditor.prototype.onMouseMove = function(event){
    if (ope.selected) {
        switch (ope.mode) {
            case "rect":
                ope.selected.attr("width", event.clientX - ope.selected.getBBox().x)
                ope.selected.attr("height", event.clientY - ope.selected.getBBox().y)
                break;
            case "ellipse":
                ope.selected.attr("rx", (event.clientX - ope.selected.getBBox().x) / 2)
                ope.selected.attr("ry", (event.clientY - ope.selected.getBBox().y) / 2)
                break;
            case "path":
                ope.selected.lineTo(event.clientX, event.clientY);
                break;
            case "polygon":
                ope.selected.path[ope.selected.path.length-1].arg[0] = event.clientX
                ope.selected.path[ope.selected.path.length-1].arg[1] = event.clientY
                ope.selected.redraw();
                break;
            case "select":
                switch (ope.mouseaction) {
                    case "mv":
                        switch (ope.selected.type) {
                            default:
                                ope.selected.attr("x", event.clientX - ope.onHitXY[0])
                                ope.selected.attr("y", event.clientY - ope.onHitXY[1])
                                ope.showTracker(ope.selected.getBBox());
                                break;
                            case "ellipse":
                                ope.selected.attr("cx", (event.clientX - ope.onHitXY[0])+(ope.selected.getBBox().width/2))
                                ope.selected.attr("cy", (event.clientY - ope.onHitXY[1])+(ope.selected.getBBox().height/2))
                                ope.showTracker(ope.selected.getBBox());
                                break;
                            case "path":
                                var tx = (event.clientX - ope.onHitXY[0]) - ope.selected.getBBox().x;
                                var ty = (event.clientY - ope.onHitXY[1]) - ope.selected.getBBox().y;
                                
                                for(var i = 0; i < ope.selected.path.length -1; i++){
                                  ope.selected.path[i].arg[0] += tx
                                  ope.selected.path[i].arg[1] += ty
                                }
                                ope.selected.redraw();
                                ope.showTracker(ope.selected.getBBox());
                                break;
                        }
                        break;
                    case "br":
                        switch (ope.selected.type) {
                          case "rect":
                            ope.selected.attr("width", event.clientX - ope.selected.getBBox().x - ope.trackerconfig.bx)
                            ope.selected.attr("height", event.clientY - ope.selected.getBBox().y - ope.trackerconfig.bx)
                            ope.showTracker(ope.selected.getBBox());
                            break;
                          case "ellipse":
                            ope.selected.attr("rx", (event.clientX - ope.selected.getBBox().x - ope.trackerconfig.bx) / 2)
                            ope.selected.attr("ry", (event.clientY - ope.selected.getBBox().y - ope.trackerconfig.bx) / 2)       
                            ope.showTracker(ope.selected.getBBox());
                            break;                          
                          case "path":
                                var tx = (event.clientX - ope.trackerconfig.bx)/(ope.selected.getBBox().x+ope.selected.getBBox().width);
                                var ty = (event.clientY - ope.trackerconfig.bx)/(ope.selected.getBBox().y+ope.selected.getBBox().height);
                                
                                for(var i = 0; i < ope.selected.path.length -1; i++){
                                  ope.selected.path[i].arg[0] = ope.selected.getBBox().x + (ope.selected.path[i].arg[0] - ope.selected.getBBox().x) * tx
                                  ope.selected.path[i].arg[1] = ope.selected.getBBox().y + (ope.selected.path[i].arg[1] - ope.selected.getBBox().y) * ty
                                }
                                ope.selected.redraw();
                                ope.showTracker(ope.selected.getBBox());
                                break;                            
                        }
                        break;
                        
                }
                break;
        }
    }
}

OPEditor.prototype.onMouseUp = function(event){
    switch (ope.mode) {
        case "rect":
            ope.unselect();
            break;
        case "ellipse":
            ope.unselect();
            break;
		case "path":
			ope.selected.andClose();
			ope.unselect();
			break;
        case "select":
            ope.mouseaction = "";
            break;
    }
}

OPEditor.prototype.onDblClick = function(event){
	switch(ope.mode){
		case "polygon":
		ope.selected.andClose();
		ope.unselect();
		break;
	}
}

OPEditor.prototype.serialize = function(){
    var shapearray = [];
    for (var i = 0; i < this.shapes.length; i++) {
        shapearray.push({
            width: this.shapes[i].getBBox().width,
            height: this.shapes[i].getBBox().height,
            left: this.shapes[i].getBBox().x,
            top: this.shapes[i].getBBox().y,
            type: this.shapes[i].type
        })
    }
    return shapearray;
}


OPEditor.prototype.getMarkup = function(){
    return ope.canvas.canvas.parentNode.innerHTML;
}



OPEditor.prototype.onHit = function(event){
    switch (ope.mode) {
        case "select":
            ope.mouseaction = "mv";
            for (var i = 0; i < ope.shapes.length; i++) {
                if (ope.shapes[i].id == this.id) {
                    ope.selected = ope.shapes[i];
                    ope.showTracker(ope.shapes[i].getBBox());
                    ope.onHitXY = [event.clientX - ope.shapes[i].getBBox().x, event.clientY - ope.shapes[i].getBBox().y]
                    break;
                }
            }
            break;
    }
}

OPEditor.prototype.unselect = function(){
    ope.selected = null;
}

OPEditor.prototype.deleteAll = function(){
    while (this.canvas.canvas.firstChild) {
        this.canvas.canvas.removeChild(paper.canvas.firstChild);
    }
}

OPEditor.prototype.hideTracker = function(){
    if (ope.tracker) {
        ope.tracker[0].parentNode.removeChild(ope.tracker[0]);
        ope.tracker = null;
    }
}


OPEditor.prototype.showTracker = function(bounds){
    //settings
    var bx = ope.trackerconfig.bx; //frame size
    var sx = ope.trackerconfig.sx; //box size
    var bx_ia_fl = ope.trackerconfig.bx_ia_fl; //inactive fill
    var bx_ho_fl = ope.trackerconfig.bx_ho_fl; //hover fill
    var bx_ia_sk = ope.trackerconfig.bx_ia_sk; //inactive stroke
    var fe_ia_sk = ope.trackerconfig.fe_ia_sk; //inactive bound frame
    ope.hideTracker();
    
    this.tracker = ope.canvas.group();
    
    
    var frame = this.tracker.rect(bounds.x - bx, bounds.y - bx, bounds.width + (2 * bx), bounds.height + (2 * bx))
    var tl = this.tracker.rect(bounds.x - bx - sx, bounds.y - bx - sx, 2 * sx, 2 * sx);
    var tr = this.tracker.rect(bounds.x - bx + bounds.width + (2 * bx) - sx, bounds.y - bx - sx, 2 * sx, 2 * sx);
    var bl = this.tracker.rect(bounds.x - bx - sx, bounds.y - bx + bounds.height + (2 * bx) - sx, 2 * sx, 2 * sx);
    var br = this.tracker.rect(bounds.x - bx + bounds.width + (2 * bx) - sx, bounds.y - bx + bounds.height + (2 * bx) - sx, 2 * sx, 2 * sx);
    
    frame.attr("stroke", fe_ia_sk);
    
    tl.attr("fill", bx_ia_fl)
    tr.attr("fill", bx_ia_fl)
    bl.attr("fill", bx_ia_fl)
    br.attr("fill", bx_ia_fl)
    
    tl.attr("stroke", bx_ia_sk)
    tr.attr("stroke", bx_ia_sk)
    bl.attr("stroke", bx_ia_sk)
    br.attr("stroke", bx_ia_sk)
    
    tl[0].onmouseover = function(){
        tl.attr("fill", bx_ho_fl)
    }
    
    tr[0].onmouseover = function(){
        tr.attr("fill", bx_ho_fl)
    }
    
    bl[0].onmouseover = function(){
        bl.attr("fill", bx_ho_fl)
    }
    
    br[0].onmouseover = function(){
        br.attr("fill", bx_ho_fl)
    }
    
    
    tl[0].onmouseout = function(){
        tl.attr("fill", bx_ia_fl)
    }
    
    tr[0].onmouseout = function(){
        tr.attr("fill", bx_ia_fl)
    }
    
    bl[0].onmouseout = function(){
        bl.attr("fill", bx_ia_fl)
    }
    
    br[0].onmouseout = function(){
        br.attr("fill", bx_ia_fl)
    }
    
    
    
    tl[0].onmousedown = function(event){
        console.log("Top Left")
        ope.mouseaction = "tl";
    }
    
    tr[0].onmousedown = function(event){
        console.log("Top Right")
        ope.mouseaction = "tr";
    }
    
    bl[0].onmousedown = function(event){
        console.log("Bottom Left")
        ope.mouseaction = "bl";
    }
    
    br[0].onmousedown = function(event){
        console.log("Bottom Right")
        ope.mouseaction = "br";
    }
    
}
