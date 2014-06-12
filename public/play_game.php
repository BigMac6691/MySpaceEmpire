<?php
require("../includes/ensure_user_logon.php");
require("../classes/DBSelects.php");

$db = new DBSelects();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Game On!</title>
		<link rel="stylesheet" href="css/main.css" media="screen" />
		<link rel="stylesheet" href="css/dual_list.css" media="screen" />
		<script>
			<?php
				$stars = $db->select("game_stars", $_POST);
				$wormholes = $db->select("game_wormholes", $_POST);
				$planets = $db->select("game_planets", $_POST);
				$civilizations = $db->select("game_civilizations", $_POST);;
				$player_data = $db->select("get_player_details", $_POST);
				$game_data = $db->select("get_game_data", $_POST);
				$user_data = $db->select("get_user_details", $_SESSION);
				$ship_type_data = $db->query("get_ship_type_data");
				$build_queue_data = $db->select("get_build_queue_data", $_POST);
				
				include("js/DualList.js");
			?>
			
			var STARS = <?php echo json_encode($stars); ?>;
			var WORMHOLES = <?php echo json_encode($wormholes); ?>;
			var PLANETS = <?php echo json_encode($planets); ?>;
			var CIVILIZATIONS = <?php echo json_encode($civilizations); ?>;
			var PLAYER = <?php echo json_encode($player_data); ?>;
			var GAME = <?php echo json_encode($game_data); ?>;
			var PLAYERS = <?php echo json_encode($user_data); ?>;
			var SHIP_TYPES = <?php echo json_encode($ship_type_data); ?>;
			var BUILD_QUEUE = <?php echo json_encode($build_queue_data); ?>;
			
			var SHIPYARD_DIALOG = {};
			
			function calculateShipCost(ship)
			{
				return (ship["mass"] - ship["cargo_space"]) * (ship["move_type"] / 2 + 0.5);
			}
			
			function shipyardLeftRowFormatter()
			{
				var shipTypes = "";
				for(var i = 0; i < this.leftData.length; i++)
				{
					var move = "";
					if(this.leftData[i]["move_type"] == 0)
						move = "Static";
					else if(this.leftData[i]["move_type"] == 1)
						move = "System";
					else
						move = "Interstellar";
		
					var cost = (this.leftData[i]["mass"] - this.leftData[i]["cargo_space"]) * (this.leftData[i]["move_type"] / 2 + 0.5);
		
					shipTypes += "<tr><td>" + this.leftData[i]["name"] + "</td>"
					+ "<td>" + this.leftData[i]["light_tubes"] + "/" + this.leftData[i]["medium_tubes"] + "/" + this.leftData[i]["heavy_tubes"] + "</td>"
					+ "<td>" + this.leftData[i]["counter_tubes"] + "</td>"
					+ "<td>" + this.leftData[i]["pd_laser_nodes"] + "</td>"
					+ "<td>" + this.leftData[i]["fighter_bays"] + "</td>"
					+ "<td>" + move + "</td>"
					+ "<td>" + this.leftData[i]["armour"] + "</td>"
					+ "<td>" + this.leftData[i]["cargo_space"] + "</td>"
					+ "<td>" + calculateShipCost(this.leftData[i]) + "</td></tr>";
				}
	
				return shipTypes;
			}
			
			function shipyardRightRowFormatter()
			{
				var content = "";
	
				for(var i = 0; i < this.rightData.length; i++)
					content += "<tr><td>" + this.rightData[i]["name"] + "</td><td>1</td></tr>";
					
				return content;
			}
			
			function handleShipyardOrder(q, src)
			{
				if(q.length == 0)
					return;
				
				for(var i = 0; i < BUILD_QUEUE.length;)
					if(BUILD_QUEUE[i]["civilization_id"] == src["civilization_id"] && BUILD_QUEUE[i]["queue_type"] == 0)
						BUILD_QUEUE.splice(i, 1);
					else
						i++;
				
				for(var i = 0; i < q.length; i++)
					BUILD_QUEUE.push({game_id : src["game_id"], civilization_id : src["civilization_id"], queue_type : 0, item_sqnbr : i, item_id : q[i]["ship_type_id"]});
			}
		
			window.onload = function()
			{
				var map = document.getElementById("game_map");
				
				CURSOR = GUI.createSVGObject("rect", ["x", "-1000", "y", "-1000", "width", "20.5", "height", "20.5", "stroke", "yellow", "stroke-width", "1", "fill", "none"]);
   			CURSOR.appendChild(GUI.createSVGObject("animate", ["dur", "1.5s", "values", "0;1;0", "attributeName", "opacity", "repeatCount", "indefinite"]));
                                           
   			map.parentNode.appendChild(CURSOR);
   			
   			//(div, title, rows, dLeft, dRight, fLeft, fRight, hLeft, rHead, callback)
   			var leftHeader = ["Name", "Tubes S/M/L", "Counter", "Laser", "Fighter", "Move", "Armour", "Cargo", "Cost"];
   			var rightHeader = ["Name", "Count"];
   			SHIPYARD_DIALOG = new DualList("shipyard_panel", "", 15, SHIP_TYPES, [], shipyardLeftRowFormatter, shipyardRightRowFormatter, leftHeader, rightHeader, handleShipyardOrder);
   			
   			drawGalaxyMap();
   			updatePlayerData();
			}
			
			<?php include("../includes/ajax.js"); ?>
			<?php include("js/util/SVG_Model.js"); ?>
			<?php include("js/play_game.js"); ?>
			
			function joinGame(id)
			{
				var JSONObject = {requestType : "join_game", game_id : id};
				var req = new AJAX("ajax/ajax_user_home.php", handleResponse);
				
				req.doPost("parms=" + JSON.stringify(JSONObject));
			}
			
			function handleResponse(resp)
			{
				alert(resp);
				
				var json = JSON.parse(resp);
				
				alert(json["resp"]);
			}
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
			<h1 style="color: gold; float: right; margin-top: 1em;">Hello <?php echo $_SESSION["validated_user"] ?></h2>
			<div style="width: 3em; height: 3em; float: right; margin-top: 1em; margin-right: 0.5em;">
				<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 100 100'> 
					<defs>
						<symbol id='<?php echo $_SESSION["validated_user"] ?>_logo' viewBox='0 0 100 100'>
						<?php echo $user_data[0]["icon"]; ?>
						</symbol>
					</defs>
					<use x='0' y='0' width='100' height='100' xlink:href='#<?php echo $_SESSION["validated_user"] ?>_logo' />
				</svg>
			</div>
		</header>
		<div style="clear: both;"></div>
		<div style="display: inline-block; margin-left: 1em; width:70%; height:90%; border: 3px solid blue; margin-top: 0.5em;">
			<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width="100%" height="100%" viewBox='0 0 1000 1000' preserveAspectRatio="none"> 
				<defs>
					<symbol id='<?php echo $_SESSION["validated_user"] ?>_logo' viewBox='0 0 100 100'>
						<?php echo $user_data[0]["icon"]; ?>
					</symbol>
					<symbol id="wormhole_svg" viewBox="0 0 100 100">
						<circle cx="50" cy="50" r="50" fill="black" />
						<circle cx="50" cy="50" r="50" fill="aqua">
							<animate begin="0s" dur="3s" repeatCount="indefinite" attributeName="r" from="50" values="50; 10; 50;" fill="freeze" />
						</circle>
					</symbol>
					<symbol id="shipyard_svg" viewBox="0 0 100 100">
						<line x1="50" y1="10" x2="50" y2="90" stroke="blue" stroke-width="5" />
						<line x1="25" y1="10" x2="75" y2="10" stroke="blue" stroke-width="5" />
						<line x1="25" y1="0" x2="25" y2="10" stroke="blue" stroke-width="5" />
						<line x1="75" y1="0" x2="75" y2="10" stroke="blue" stroke-width="5" />
						<line x1="25" y1="90" x2="75" y2="90" stroke="blue" stroke-width="5" />
						<line x1="25" y1="90" x2="25" y2="100" stroke="blue" stroke-width="5" />
						<line x1="75" y1="90" x2="75" y2="100" stroke="blue" stroke-width="5" />
						
						<line x1="10" y1="50" x2="90" y2="50" stroke="blue" stroke-width="5" />
						<line x1="10" y1="25" x2="10" y2="75" stroke="blue" stroke-width="5" />
						<line x1="0" y1="25" x2="10" y2="25" stroke="blue" stroke-width="5" />
						<line x1="0" y1="75" x2="10" y2="75" stroke="blue" stroke-width="5" />
						<line x1="90" y1="25" x2="90" y2="75" stroke="blue" stroke-width="5" />
						<line x1="90" y1="25" x2="100" y2="25" stroke="blue" stroke-width="5" />
						<line x1="90" y1="75" x2="100" y2="75" stroke="blue" stroke-width="5" />
						<circle cx="50" cy="50" r="20" fill="white" />
						<circle cx="50" cy="50" r="10" fill="red" />
					</symbol>
				</defs>
				<g id="game_map">
				</g>
			</svg>
		</div>
		<div style="display: inline-block; vertical-align: top; width: 25%;">
			<div id="player_panel" style="background-color: #444; overflow: hidden;"></div>
			<div style="clear: both;"></div>
			<div id="info_panel" style="margin-top: 0.5em;"></div>
		</div>
		<div id="shipyard_panel" class="DualList">
		</div>
	</body>
</html>