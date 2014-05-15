var GUI = new SVG_Model();
var CURSOR = {};
var SELECTED = {};

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
			updateInfoPanel(gameData.to_star_id);
		}
		else if(gameData.type == "planet")
		{
			if(gameData.map == "system")
				drawPlanet(gameData.id);
			else
				drawSolarSystem(gameData.star_id);
		}
	}
	else
	{
		SELECTED = (gameData.type + gameData.id);
		
		if(gameData.type == "star")
			updateInfoPanel(gameData.id);
		
		setSelectedObject(target);
	}
}

function setSelectedObject(svg)
{
	var r = svg.getBBox();
	
	if(svg.localName == "use")
	{
		r.width = svg.width.baseVal.value;
		r.height = svg.height.baseVal.value;
	}
			
	GUI.updateSVGObject(CURSOR, ["x", r.x - 2.5, "y", r.y - 2.5, "width", r.width + 5, "height", r.height + 5]);
}

function updatePlayerData()
{
	var panel = document.getElementById("player_panel");
	var data = "<p style='color:green; float: left;'><span style='font-weight:bold; color:white;'>$</span>" + PLAYER[0]["cash"] + "</p>";
	data += "<p style='color:green; float: right;'><span style='color: white; font-weight:bold;'>Turn:</span>" + PLAYER[0]["turn"] + "</p>";

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

		map.appendChild(GUI.createSVGObject("line", ["x1", x1, "y1", y1, "x2", x2, "y2", y2, "stroke", "red", "stroke-width", "2"]));
	}

	for (var i = 0; i < STARS.length; i++)
	{
		var x = STARS[i]["star_x"] * 1000;
		var y = STARS[i]["star_y"] * 1000;
		var gameData = JSON.stringify({map : "galaxy", type : "star", id : STARS[i]["star_id"]});
		var starSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", x, "cy", y, "r", "6", "fill", "yellow"]);

		starSVG.addEventListener("click", handleMapClick, false);
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
		var gameData = JSON.stringify({map : "system", type : "planet", id : planets[i]["planet_id"]});
		var planetSVG = GUI.createSVGObject("circle", ["gameData", gameData, "cx", x, "cy", y, "r", "0.75%", "stroke", "lightgrey", "stroke-width", "1", "fill", "green"]);

		planetSVG.addEventListener("click", handleMapClick, false);
		map.appendChild(planetSVG);
	}
	
	var holes = getStarWormholes(id)
	
	for(var i = 0; i < holes.length; i++)
	{
		var x = 1000 * holes[i]["wormhole_x"];
		var y = 1000 * holes[i]["wormhole_y"];
		var gameData = JSON.stringify({map : "system", type : "wormhole", id : holes[i]["wormhole_id"], to_star_id : holes[i]["to_star_id"]});
		var holeSVG = GUI.createUseObject("#wormhole_svg", ["gameData", gameData, "x", x, "y", y, "width", "10", "height", "10"]);
		
		holeSVG.addEventListener("click", handleMapClick, false);
		map.appendChild(holeSVG);
	} 

	setSelectedObject(sunSVG);
}

function drawPlanet(id)
{
	var map = document.getElementById("game_map");
	var planet = getPlanet(id);
	var civ = getPlanetCivilization(id);
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
	}
	
	if(civ["shipyard"] > 0)
	{
		var shipyardSVG = GUI.createUseObject("#shipyard_svg", ["x", "475", "y", "25", "width", "50", "height", "50"]); 
		
		shipyardSVG.addEventListener("click", handleClick, false);
		map.appendChild(shipyardSVG);
		map.appendChild(GUI.createSVGText("Test", ["x", "475", "y", "100", "stroke", "white", "stroke-width", "0.5", "fill", "white"]));
	}
	
	setSelectedObject(planetSVG);
}

function handleClick(evt)
{
	var target = evt.target.correspondingUseElement ? evt.target.correspondingUseElement : evt.target;
	
	alert(target);
	
	setSelectedObject(target);
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

function updateInfoPanel(id)
{
	var star = getStar(id);
	var data = "";

	data += ("<p>" + star["star_name"] + "</p>");

	var planets = getStarPlanets(star["star_id"]);

	for (var i = 0; i < planets.length; i++)
	{
		var color = planets[i]["planet_owner"] > 0 ? "green" : "gray";

		data += ("<p style='color:" + color + ";'>" + planets[i]["planet_name"] + "</p>");

		if (planets[i]["planet_owner"] > 0)
		{
			var civ = getPlanetCivilization(planets[i]["planet_id"]);

			data += ("<p style='margin-left: 1em; color: green;'>P:" + civ["population"]);
			data += ("<span id='" + civ["civilization_id"] + "_industry" + "' style='margin-left: 1em; color: green;' onclick='improveCivilization(this);'>I:" + civ["industry"] + "</span>");
			data += ("<span id='" + civ["civilization_id"] + "_shipyard" + "' style='margin-left: 1em; color: green;' onclick='improveCivilization(this);'>S:" + civ["shipyard"] + "</span></p>");
		}
	}

	document.getElementById("info_panel").innerHTML = data;
}

function improveCivilization(evt)
{
	var civ = null;
	var id = evt.id.split("_")[0];
	var type = evt.id.split("_")[1];

	for (var i = 0; i < CIVILIZATIONS.length && civ == null; i++)
		if (CIVILIZATIONS[i]["civilization_id"] == id)
			civ = CIVILIZATIONS[i];
	
	var planet = getPlanet(civ["planet_id"]);
	
	document.getElementById("shipyard_title").innerHTML = "Shipyard at " + planet["planet_name"];		
	document.getElementById("shipyard_panel").style.visibility = "visible";
	
	retrun;

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