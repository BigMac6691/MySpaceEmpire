// Globals
var SVGNS = "http://www.w3.org/2000/svg";
var XLINKNS = "http://www.w3.org/1999/xlink";

function SVG_Model()
{
}

SVG_Model.prototype.clear = function(g)
{
   while (g.hasChildNodes()) 
      g.removeChild(g.lastChild);
}

SVG_Model.prototype.createSVGObject = function(s, a)
{
   var shape = document.createElementNS(SVGNS, s);
  
   for(var i = 0; i < a.length; i += 2)
      shape.setAttributeNS(null, a[i], a[i+1]);
     
   return shape;
}

SVG_Model.prototype.createUseObject = function(href, attr)
{
   var shape = this.createSVGObject("use", attr);
   
   shape.setAttributeNS(XLINKNS, "href", href);
   
   return shape;
}

SVG_Model.prototype.createSVGText = function(t, a)
{
	var text = this.createSVGObject("text", a);
	
	text.appendChild(document.createTextNode(t));
	
	return text;
}

/*
 * var textelement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
textelement.setAttribute("id", "text1");
textelement.setAttribute("x", 100);
textelement.setAttribute("y", 100);
textelement.textContent = "this is some text";
textelement.addEventListener("click", svgclick, false);
textelement.addEventListener("keypress", onkeypress, false);

} 
 */

SVG_Model.prototype.updateSVGObject = function(s, a)
{
	for(var i = 0; i < a.length; i += 2)
      s.setAttributeNS(null, a[i], a[i+1]);
}
