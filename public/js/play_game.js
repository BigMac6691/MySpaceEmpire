var GUI = new SVG_Model();
var CURSOR = {};
var SELECTED = {};

function getMousePos(evt) 
{
	evt = evt || window.event;

	if ( typeof evt.pageX != 'undefined') 
	{
		return [evt.pageX, evt.pageY];		// Firefox
	} 
	else// IE et al
	{
		var x = document.body.scrollLeft || document.documentElement.scrollLeft || window.pageXOffset || 0;
		var y = document.body.scrollTop || document.documentElement.scrollTop || window.pageYOffset || 0;

		return [evt.clientX + x, evt.clientY + y];
	}
}

function showMapInfo(evt) 
{
	var gameData = JSON.parse(evt.currentTarget.attributes.gameData.value);
	var svgPoint = document.getElementById("svg_map_root").createSVGPoint();
	var text = document.createTextNode(gameData.name);
	var tNode = document.getElementById("info_text");
	var tNode_box = document.getElementById("info_box");

	tNode.replaceChild(text, tNode.firstChild);

	var w = tNode.getBBox().width;
	var x = 0;
	var y = -1000;

	if (evt.currentTarget.id != "game_map") 
	{
		mouse = getMousePos(evt);

		svgPoint.x = mouse[0];
		svgPoint.y = mouse[1];
		svgPoint = svgPoint.matrixTransform(tNode.getScreenCTM().inverse());

		x = Math.max(5, svgPoint.x - w / 2);
		y = Math.max(15, svgPoint.y);

		x = Math.min(x, 995 - w);		// 5 unit pad already removed
	}
	 
	tNode.setAttributeNS(null, "x", x);
	tNode.setAttributeNS(null, "y", y);

	tNode_box.setAttributeNS(null, "x", x - 5);
	tNode_box.setAttributeNS(null, "y", y - 15);
	tNode_box.setAttributeNS(null, "width", w + 10);
}

function hideMapInfo()
{
	var tNode = document.getElementById("info_text");
	var tNode_box = document.getElementById("info_box");

	tNode.setAttributeNS(null, "y", -1000);
	tNode_box.setAttributeNS(null, "y", -1000);
}

function handleMapClick(evt)
{
	var target = evt.target.correspondingUseElement ? evt.target.correspondingUseElement : evt.target;
	var gameData = JSON.parse(target.getAttribute("gameData"));
	
	if (SELECTED == (gameData.type + gameData.id))
	{
		if (gameData.type == "star")
		{
			if(gameData.map == "galaxy")
				drawSolarSystem(gameData.id);
			else
				drawGalaxyMap(gameData.id);
		}
		else if(gameData.type == "wormhole")
		{
			drawSolarSystem(gameData.to_star_id);
			updateInfoPanel(gameData);
		}
		else if(gameData.type == "planet")
		{
			if(gameData.map == "system")
				drawPlanet(gameData.id);
			else
				drawSolarSystem(gameData.star_id);
		}
		
		hideMapInfo();
	}
	else
	{
		SELECTED = (gameData.type + gameData.id);
		
		if(gameData.type == "star" || gameData.type == "planet")
			updateInfoPanel(gameData);
		
		setSelectedObject(target);
	}
}

function setSelectedObject(svg)
{
	var r = svg.getBBox();
	
	if(svg.localName == "use")
	{
		r.x = svg.x.baseVal.value;
		r.y = svg.y.baseVal.value;
		r.width = svg.width.baseVal.value;
		r.height = svg.height.baseVal.value;
	}
			
	GUI.updateSVGObject(CURSOR, ["x", r.x - 2.5, "y", r.y - 2.5, "width", r.width + 5, "height", r.height + 5]);
}

function handleInfoClick(src)
{
	alert(src);
}

function updatePlayerData()
{
	var panel = document.getElementById("player_panel");
	var data = "<p style='color:green; float: left;'><span style='font-weight:bold; color:white;'>$</span>" + PLAYER["cash"] + "</p>";
	data += "<p style='color:green; float: right;'><span style='color: white; font-weight:bold;'>Turn:</span>" + PLAYER["turn"] + "</p>";

	panel.innerHTML = data;
}

function drawGalaxyMap(id)
{
	var map = document.getElementById("game_map");
	GUI.clear(map);

	for (var i = 0; i < WORMHOLES.length; i++)
	{
		var from = getStar(WORMHOLES[i]["from_star_id"]);
		var to = getStar(WORMHOLES[i]["to_star_id"]);
		var x1 = from["star_x"] * 1000;
		var y1 = from["star_y"] * 1000;
		var x2 = to["star_x"] * 1000;
		var y2 = to["star_y"] * 1000;

		map.appendChild(GUI.createSVGObject("line", ["x1", x1, "y1", y1, "x2", x2, "y2", y2, "stroke", "red", "stroke-width", "1"]));
	}

	for (var i = 0; i < STARS.length; i++)
	{
		var x = STARS[i]["star_x"] * 1000;
		var y = STARS[i]["star_y"] * 1000;
		var gameData = JSON.stringify({map : "galaxy", type : "star", id : STARS[i]["star_id"], name : STARS[i]["star_name"]});
		var planets = getStarPlanets(STARS[i]["star_id"]);
		var starColorFlag = [false, false]
		
		for(var j = 0; j < planets.length; j++)
			if(planets[j]["planet_owner"] == PLAYER["user_id"])
				starColorFlag[1] = true;
			else if(planets[j]["planet_owner"] > 0)
				starColorFlag[0] = true;
		
		var starColor = "#";
		if(starColorFlag[0] || starColorFlag[1])
		{
			starColor += starColorFlag[0] ? "FF" : "00";
			starColor += starColorFlag[1] ? "FF" : "00";
			starColor += "00";
		}
		else
			starColor += "808080";
		
		var starSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", x, "cy", y, "r", "6", "fill", starColor]);

		starSVG.addEventListener("click", handleMapClick, false);
		starSVG.addEventListener("mouseover", showMapInfo, false);
		starSVG.addEventListener("mouseout", hideMapInfo, false);
		map.appendChild(starSVG);

		if (id && STARS[i]["star_id"] == id)
			setSelectedObject(starSVG);
	}
}

function drawSolarSystem(id)
{
	var map = document.getElementById("game_map");
	var planets = getStarPlanets(id);
	var gameData = JSON.stringify({map : "system", type : "star", id : id});
	var sunSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", "50%", "cy", "50%", "r", "1.75%", "fill", "yellow"]);

	sunSVG.addEventListener("click", handleMapClick, false);
	GUI.clear(map);
	map.appendChild(sunSVG);

	for (var i = 0; i < planets.length; i++)
	{
		var r = 500 * planets[i]["orbit_radius"];
		map.appendChild(GUI.createSVGObject("circle", ["cx", "50%", "cy", "50%", "r", r, "stroke", "lightgrey", "fill", "none"]));

		var angle = ((GAME[0]["game_seed"] + GAME[0]["game_turn"]) % planets[i]["orbit_period"]) * (Math.PI * 2 / planets[i]["orbit_period"]);
		var x = 500 + Math.cos(angle) * 500 * planets[i]["orbit_radius"];
		var y = 500 + Math.sin(angle) * 500 * planets[i]["orbit_radius"];
		var gameData = JSON.stringify({map : "system", type : "planet", id : planets[i]["planet_id"], star_id : planets[i]["star_id"], name : planets[i]["planet_name"]});
		var planetColor = "gray";
		
		if(planets[i]["planet_owner"] > 0)
			planetColor = planets[i]["planet_owner"] == PLAYER["user_id"] ? "green" : "red";
		 
		var planetSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", x, "cy", y, "r", "0.75%", "stroke", "lightgrey", "stroke-width", "1", "fill", planetColor]);

		planetSVG.addEventListener("click", handleMapClick, false);
		planetSVG.addEventListener("mouseover", showMapInfo, false);
		planetSVG.addEventListener("mouseout", hideMapInfo, false);
		map.appendChild(planetSVG);
	}
	
	var holes = getStarWormholes(id)
	
	for(var i = 0; i < holes.length; i++)
	{
		var x = 1000 * holes[i]["wormhole_x"];
		var y = 1000 * holes[i]["wormhole_y"];
		var star = getStar(holes[i]["to_star_id"]);
		var gameData = JSON.stringify({map : "system", type : "wormhole", id : holes[i]["wormhole_id"], to_star_id : holes[i]["to_star_id"], name : "To " + star["star_name"]});
		var holeSVG = GUI.createUseObject("#wormhole_svg", ["gameData", gameData, "x", x, "y", y, "width", "10", "height", "10"]);
		
		holeSVG.addEventListener("click", handleMapClick, false);
		holeSVG.addEventListener("mouseover", showMapInfo, false);
		holeSVG.addEventListener("mouseout", hideMapInfo, false);
		map.appendChild(holeSVG);
	} 

	setSelectedObject(sunSVG);
}

function drawPlanet(id)
{
	var map = document.getElementById("game_map");
	var planet = getPlanet(id);
	var gameData = JSON.stringify({map : "planet", type : "planet", id : id, star_id : planet["star_id"]});
	var planetSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", "50%", "cy", "50%", "r", "25%", "fill", "green"])
	
	planetSVG.addEventListener("click", handleMapClick, false);
	GUI.clear(map);
	map.appendChild(planetSVG);
	
	if(planet["planet_owner"] > 0)
	{
		var player = getPlayer(planet["planet_owner"]);
		var href = "#" + player["alias"] + "_logo";
		map.appendChild(GUI.createUseObject(href, ["x", "350", "y", "350", "width", "300", "height", "300", "pointer-events", "none"]));
		
		var civ = getPlanetCivilization(id);
		var ind = getCivilizationIndustry(civ["civilization_id"])
		var yard = null;
		
		for(var i = 0; i < ind.length; i++)
			if(ind[i]["type"] == 1)
				yard = ind[i];
	
		if(yard != null)
		{
			var gameData = JSON.stringify({map : "planet", type : "industry", id : yard["industry_id"]});
			var shipyardSVG = GUI.createUseObject("#shipyard_svg", ["gameData", gameData, "x", "475", "y", "25", "width", "50", "height", "50"]); 
		
			shipyardSVG.addEventListener("click", handleMapClick, false);
			map.appendChild(shipyardSVG);
			map.appendChild(GUI.createSVGText("Test", ["x", "475", "y", "100", "stroke", "white", "stroke-width", "0.5", "fill", "white"]));
		}
	}
	
	setSelectedObject(planetSVG);
}

function getStar(id)
{
	for (var i = 0; i < STARS.length; i++)
		if (STARS[i]["star_id"] == id)
			return STARS[i];

	return null;
}

function getStarPlanets(id)
{
	var p = new Array();

	for (var i = 0; i < PLANETS.length; i++)
		if (PLANETS[i]["star_id"] == id)
			p.push(PLANETS[i]);

	return p;
}

function getPlanetCivilization(id)
{
	for (var i = 0; i < CIVILIZATIONS.length; i++)
		if (CIVILIZATIONS[i]["planet_id"] == id)
			return CIVILIZATIONS[i];

	return null;
}

function getStarWormholes(id)
{
	var h = new Array();
	
	for(var i = 0; i < WORMHOLES.length; i++)
		if(WORMHOLES[i]["from_star_id"] == id)
			h.push(WORMHOLES[i]);
			
	return h;
}

function getPlanet(id)
{
	for(var i = 0; i < PLANETS.length; i++)
		if(PLANETS[i]["planet_id"] == id)
			return PLANETS[i];
			
	return null;
}

function getPlayer(id)
{
	for(var i = 0; i < PLAYERS.length; i++)
		if(PLAYERS[i]["user_id"] == id)
			return PLAYERS[i];
			
	return null;
}

function getCivilizationIndustry(id)
{
	var ind = new Array();
	
	for(var i = 0; i < INDUSTRY.length; i++)
		if(INDUSTRY[i]["civilization_id"] == id)
			ind.push(INDUSTRY[i]);
			
	return ind;
}

function updateInfoPanel(gameData)
{
	var starId = "";
	
	if(gameData.type == "wormhole")
		starId = gameData.to_star_id;
	else if(gameData.type == "star")
		starId = gameData.id;
	else if(gameData.type == "planet")
		starId = gameData.star_id;
	
	var star = getStar(starId);
	var data = "";

	if(gameData.type == "wormhole" || gameData.type == "star")
		data += ("<p style='background-color: #444444;'>" + star["star_name"] + "</p>");
	else
		data += ("<p>" + star["star_name"] + "</p>");

	var planets = getStarPlanets(star["star_id"]);

	for (var i = 0; i < planets.length; i++)
	{
		var color = planets[i]["planet_owner"] > 0 ? "green" : "gray";

		if(gameData.type == "planet" && gameData.id == planets[i]["planet_id"])
			data += ("<p style='cursor : pointer; color:" + color + "; background-color: #444444;'>" + planets[i]["planet_name"] + "</p>");
		else
			data += ("<p style='cursor : pointer; color:" + color + ";' onclick='handleInfoClick(this);'>" + planets[i]["planet_name"] + "</p>");

		if (planets[i]["planet_owner"] > 0)
		{
			var civ = getPlanetCivilization(planets[i]["planet_id"]);
			var ind = getCivilizationIndustry(civ["civilization_id"]);

			data += ("<p style='margin-left: 1em; color: green; cursor : default;'>Population:" + civ["population"]);
			
			var ind_labels = ["Industry", "Shipyard"];
			
			for(var j = 0; j < ind.length; j++)
				data += ("<span data-industry_id='" + ind[j]["industry_id"] + "' data-civilization_id='" + civ["civilization_id"] + "' class='Industry' onclick='improveCivilization(this);'>" + ind_labels[ind[j]["type"]] + ":" + ind[j]["size"] + "</span>");
				
			data += "</p>";
		}
	}

	document.getElementById("info_panel").innerHTML = data;
}

function improveCivilization(src)
{
	var civ = null;
	var ind_id = src.getAttribute("data-industry_id");
	var civ_id = src.getAttribute("data-civilization_id");

	for (var i = 0; i < CIVILIZATIONS.length && civ == null; i++)
		if (CIVILIZATIONS[i]["civilization_id"] == civ_id)
			civ = CIVILIZATIONS[i];

	var data = [];			
	for(var i = 0; i < BUILD_QUEUE.length; i++)
		if(BUILD_QUEUE[i]["civilization_id"] == civ["civilization_id"] && BUILD_QUEUE[i]["queue_type"] == 0)
			data.push(SHIP_TYPES[BUILD_QUEUE[i]["item_id"] - 1]);
	
	var planet = getPlanet(civ["planet_id"]);
	
	SHIPYARD_DIALOG.setRightData(data);
	SHIPYARD_DIALOG.show("Shipyard at " + planet["planet_name"], civ);
	
	return;

	var intRegex = /^\d+$/;
	var value = 0 / 0;

	while (isNaN(value))
	{
		value = prompt("Improve " + type + ":");

		if (value != null)
			value = intRegex.test(value) ? value : 0 / 0;
		else
			value = 0;
	}

	civ[type] += parseInt(value);

	document.getElementById(evt.id).innerHTML = type.charAt(0).toUpperCase() + ":" + civ[type];
}