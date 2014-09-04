<?php
require("../../includes/ensure_admin_logon.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Create Game</title>
		<link rel="stylesheet" href="../css/main.css" media="screen" />
		<script src="../js/datetimepicker/datetimepicker_css.js"></script>		
		<script>
			<?php include("../../includes/ajax.js"); ?>
		
			function createGame()
			{
				var cgf = document.getElementById("create_game");
				
				alert("Create Game! " + cgf);
				
				var keys = ["game_name", "game_seed", "sector_size", "star_density", "planet_density", "empty_sectors", "connect_sectors", "connect_stars", "game_start", "game_end"];
				var JSONObject = {requestType : "create_game"};
				
				for(var i = 0; i < keys.length; i++)
				{
					var element = document.getElementById(keys[i]);
					
					if(element.type == "checkbox")
						JSONObject[keys[i]] = element.checked;
					else
						JSONObject[keys[i]] = element.value;
				}
					
				
				var req = new AJAX("ajax_admin/ajax_create_game.php", handleResponse);
				
				req.doPost("parms=" + JSON.stringify(JSONObject), true);
			}
			
			function handleResponse(resp)
			{
				var json = JSON.parse(resp);
				
				alert("RESP=" + json);
			}
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</header>
		<div style="clear: both;"></div>
		<main style="margin-top: 1em;">
			<h2 style="color: gold; text-align: center;">Create Game</h2>
			<form id="create_game" action="ajax_admin/ajax_create_game.php">
				<div style="width: 25%; text-align: right;">
					<label style="display: block; margin: 0.2em;">Name:<input type="text" id="game_name" name="game_name" size="30" /></label>
					<label style="display: block; margin: 0.2em;">Seed:<input type="number" id="game_seed" name="game_seed" size="10" value="0" /></label>
					<label style="display: block; margin: 0.2em;">Sector Size:<input type="number" id="sector_size" name="sector_size" size="5" value="0" /></label>
					<label style="display: block; margin: 0.2em;">Star Density:<input type="number" id="star_density" name="star_density" size="5" value="0" /></label>
					<label style="display: block; margin: 0.2em;">Planet Density:<input type="number" id="planet_density" name="planet_density" size="5" value="0" /></label>
					<label style="display: block; margin: 0.2em;">Empty Sectors:<input type="number" id="empty_sectors" name="empty_sectors" size="5" value="0" /></label>
					<label style="display: block; margin: 0.2em;">Connect Sectors:<input type="checkbox" id="connect_sectors" name="connect_sectors" /></label>
					<label style="display: block; margin: 0.2em;">Connect Stars:<input type="checkbox" id="connect_stars" name="connect_stars" /></label>
					<label style="display: block; margin: 0.2em;">
						Start:<input type="text" id="game_start" name="game_start" size="25" disabled="true" />
						<img src="../js/datetimepicker/images/cal.gif" onclick="NewCssCal('game_start','yyyyMMdd','arrow',true,24,false,'future');" style="cursor:pointer"/>
					</label>
					<label style="display: block; margin: 0.2em;">
						End:<input type="text" id="game_end" name="game_end" size="25" disabled="true" />
						<img src="../js/datetimepicker/images/cal.gif" onclick="NewCssCal('game_end','yyyyMMdd','arrow',true,24,false,'future');" style="cursor:pointer"/>
					</label>
					<button type="button" onclick="createGame();">Create Game</button>
				</div>
			</form>
			<a href="admin_main.php">Back to main menu.</a>
		</main>
	</body>
</html>