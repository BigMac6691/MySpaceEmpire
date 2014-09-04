function DualList(div, title, rows, dLeft, dRight, fLeft, fRight, hLeft, rHead, callback)
{
	this.container = div;
	this.title = title;
	this.visibleRows = rows;
	this.leftData = dLeft || [];
	this.rightData = dRight || [];
	this.formatLeft = fLeft;
	this.formatRight = fRight;
	this.leftHeader = hLeft;
	this.rightHeader = rHead;
	this.callback = callback;
	
	this.source = null;
	this.leftSelection = null;
	this.rightSelection = null;
	this.leftIndex = -1;
	this.rightIndex = -1;
	
	this.init();
}

DualList.prototype.init = function()
{
	var content = "<button id='" + this.container + "_close' class='DualListCloseButton'>X</button>";
	
	content += "<p id='" + this.container + "_title' class='DualListTitle'>" + this.title + "</p>";
	content += "<button id='" + this.container + "_ok' class='DualListOKButton'>&nbsp;OK&nbsp;</button>";
	
	if(this.leftHeader)
	{
		content += "<div class='DualListTableDiv'><table><thead>";
		
		for(var i = 0; i < this.leftHeader.length; i++)
			content += "<th>" + this.leftHeader[i] + "</th>";
		
		content += "</thead><tbody id='" + this.container + "_leftTable'></tbody></table></div>";
	}
	
	content += "<div style='display: inline-block; margin-top: " + (this.visibleRows / 2) + "em;'><table><tbody>"
					+ "<tr><td colspan='2' class='NavButtonsTopBottom'><button id='" + this.container + "_upButton' class='DualListRotate90'>&lt;</button></td></tr>"
            	+ "<tr><td style='border: 0;'><button id='" + this.container + "_removeButton'>&lt;</button></td>"
            	+ "<td style='border: 0; padding-left: 1.25em; padding-right: 0;'><button id='" + this.container + "_addButton'>&gt;</button></td></tr>"
            	+ "<tr><td colspan='2' class='NavButtonsTopBottom'><button id='" + this.container + "_downButton' class='DualListRotate90'>&gt;</button></td></tr>"
            	+ "</tbody></table></div>";
	
	if(this.rightHeader)
	{
		content += "<div class='DualListTableDiv'><table><thead>";
		
		for(var i = 0; i < this.rightHeader.length; i++)
			content += "<th>" + this.rightHeader[i] + "</th>";
		
		content += "</thead><tbody id='" + this.container + "_rightTable'></tbody></table></div>";
	}
	
	document.getElementById(this.container).innerHTML = content;
	document.getElementById(this.container + "_close").onclick = function(src){return function(){src.close();};}(this);	
	document.getElementById(this.container + "_ok").onclick = function(src){return function(){src.ok();};}(this);
	document.getElementById(this.container + "_upButton").onclick = function(src){return function(){src.up();};}(this);
	document.getElementById(this.container + "_addButton").onclick = function(src){return function(){src.add();};}(this);
	document.getElementById(this.container + "_removeButton").onclick = function(src){return function(){src.remove();};}(this);
	document.getElementById(this.container + "_downButton").onclick = function(src){return function(){src.down();};}(this);
	
	document.getElementById(this.container + "_leftTable").innerHTML = this.formatLeft();
	
	var leftBody = document.getElementById(this.container + "_leftTable");
	var src = this;
	
	for(var i = 0; i < leftBody.rows.length; i++)
		leftBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setLeftSelection(event, ri);};})(i);
}

DualList.prototype.setTitle = function(t)
{
	this.title = t;
	document.getElementById(this.container + "_title").innerHTML = this.title; 
}

DualList.prototype.close = function()
{
	document.getElementById(this.container).style.visibility = 'hidden';
}

DualList.prototype.ok = function()
{
	this.callback(this.rightData, this.source);
	this.rightData = [];
	document.getElementById(this.container).style.visibility = 'hidden';
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
}

DualList.prototype.show = function(t, src)
{
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	this.setTitle(t);
	
	this.source = src;
	
	this.setRightData(this.rightData);
	
	document.getElementById(this.container).style.visibility = 'visible';
}

DualList.prototype.up = function()
{
	if(this.rightIndex < 1)
		return;
	
	var temp = this.rightData[this.rightIndex - 1];
	this.rightData[this.rightIndex - 1] = this.rightData[this.rightIndex];
	this.rightData[this.rightIndex] = temp;
	
	this.rightIndex--;
	
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	
	var rightBody = document.getElementById(this.container + "_rightTable");
	var evt = {currentTarget : rightBody.rows[this.rightIndex]};
	var src = this;
	
	for(var i = 0; i < rightBody.rows.length; i++)
		rightBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setRightSelection(event, ri);};})(i);
		
	this.setRightSelection(evt, this.rightIndex);
}

DualList.prototype.add = function()
{
	if(this.leftIndex < 0)
		return;
	
	this.rightData.push(this.leftData[this.leftIndex]);
		
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	
	var rightBody = document.getElementById(this.container + "_rightTable");
	var src = this;
	
	for(var i = 0; i < rightBody.rows.length; i++)
		rightBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setRightSelection(event, ri);};})(i);
}

DualList.prototype.remove = function()
{
	if(this.rightIndex < 0)
		return;
		
	this.rightData.splice(this.rightIndex, 1);
		
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	
	var rightBody = document.getElementById(this.container + "_rightTable");
	var src = this;
	
	for(var i = 0; i < rightBody.rows.length; i++)
		rightBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setRightSelection(event, ri);};})(i);
		
	this.rightIndex--;
	
	if(this.rightIndex < 0 && this.rightData.length > 0)
		this.rightIndex = 0;
	
	if(this.rightIndex < 0)
		this.rightSelection = null;
	else
		this.setRightSelection({currentTarget : rightBody.rows[this.rightIndex]}, this.rightIndex);
}

DualList.prototype.down = function()
{
	if(this.rightIndex >= this.rightData.length - 1)
		return;
	
	var temp = this.rightData[this.rightIndex + 1];
	this.rightData[this.rightIndex + 1] = this.rightData[this.rightIndex];
	this.rightData[this.rightIndex] = temp;
	
	this.rightIndex++;
	
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	
	var rightBody = document.getElementById(this.container + "_rightTable");
	var src = this;
	
	for(var i = 0; i < rightBody.rows.length; i++)
		rightBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setRightSelection(event, ri);};})(i);
		
	this.setRightSelection({currentTarget : rightBody.rows[this.rightIndex]}, this.rightIndex);
}

DualList.prototype.setLeftSelection = function(evt, i)
{
	if(this.leftSelection == evt.currentTarget)
	{
		this.add();
		return;
	}
	
	if(this.leftSelection != null)
		this.leftSelection.style.backgroundColor = "";
		
	evt.currentTarget.style.backgroundColor = "#444444";
	this.leftSelection = evt.currentTarget;
	this.leftIndex = i; 
}

DualList.prototype.setRightSelection = function(evt, i)
{
	if(this.rightSelection == evt.currentTarget)
	{
		this.remove();
		return;
	}
	
	if(this.rightSelection != null)
		this.rightSelection.style.backgroundColor = "";
		
	evt.currentTarget.style.backgroundColor = "#444444";
	this.rightSelection = evt.currentTarget;
	this.rightIndex = i; 	
}

DualList.prototype.setRightData = function(data)
{
	this.rightData = data;
	document.getElementById(this.container + "_rightTable").innerHTML = this.formatRight();
	
	var rightBody = document.getElementById(this.container + "_rightTable");
	var src = this;
	
	for(var i = 0; i < rightBody.rows.length; i++)
		rightBody.rows[i].onclick = (function(){var ri = i; return function(event){src.setRightSelection(event, ri);};})(i);
}
