<?php
require("../includes/ensure_user_logon.php");
require("../classes/DBSelects.php");

$db = new DBSelects();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - User Home</title>
		<link rel="stylesheet" href="css/main.css" media="screen" />
		<script>
			window.onload = function()
			{
				displayJoinable();
				displayJoined();
			}
			
			<?php
				$joinable = $db->select("joinable_games", $_SESSION);
				$joined = $db->select("joined_games", $_SESSION);
			?>
			
			JOINABLE = <?php echo json_encode($joinable); ?>;
			JOINED = <?php echo json_encode($joined); ?>;
		
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
				
				var found = -1;
				for(var i = 0; i < JOINABLE.length && found < 0; i++)
					if(JOINABLE[i]["game_id"] == json["resp"])
						found = i;
						
				if(found < 0)
					return;
						
				JOINED[JOINED.length] = JOINABLE[found];
				JOINABLE.splice(found, 1);
				
				displayJoinable();
				displayJoined();
			}
			
			function displayJoinable()
			{
				loj = "";
				for(var i = 0; i < JOINABLE.length; i++)
					loj += ("<li><a href='javascript:joinGame(" + JOINABLE[i]['game_id'] + ");'>" + JOINABLE[i]['game_name'] + "</a></li>");
					
				document.getElementById("list_of_joinable").innerHTML = loj;
			}
			
			function displayJoined()
			{
				loj = "";
				for(var i = 0; i < JOINED.length; i++)
					loj += ("<li><a href='javascript:playGame(" + JOINED[i]['game_id'] + ");'>" + JOINED[i]['game_name'] + "</a></li>");
					
				document.getElementById("list_of_joined").innerHTML = loj;
			}
			
			function playGame(id)
			{
				document.getElementById("game_id").value = id;
				document.getElementById("game_on").submit();
			}
		
			<?php include("../includes/ajax.js"); ?>
		</script>
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
			<h1 style="color: gold; float: right; margin-top: 1em;">Hello <?php echo $_SESSION["validated_user"] ?></h2>
		</header>
		<div style="clear: both;"></div>
		<form name='game_on' id="game_on" action='play_game.php' method='post'>
			<input type="hidden" name="game_id" id="game_id" value=""/>
		</form>
		<div style="margin-left: 3em;">
			<div style="display: inline-block; width: 43%; vertical-align: top;">
				<h3>Games Playing In</h3>
				<ol id="list_of_joined">
				</ol>
			</div>
			<div style="display: inline-block; width: 43%; vertical-align: top;">
				<h3>Games You Can Join</h3>
				<ol id="list_of_joinable">
				</ol>
			</div>
			<div style="display: inline-block; width: 10%; height: 10%; margin-right: 3%;">
			<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 100 100'> 
				<defs>
					<symbol id='<?php echo $_SESSION["validated_user"] ?>_logo' viewBox='0 0 100 100'>
						<?php
						$user_data = $db->select("get_user_details", $_SESSION);
						
						echo $user_data[0]["icon"];
						?>
					</symbol>
				</defs>
				<use x='0' y='0' width='100' height='100' xlink:href='#<?php echo $_SESSION["validated_user"] ?>_logo' />
			</svg>
		</div>
		</div>
		<div style="margin-left: 3em; margin-top: 1.5em;">
			<h3 style="display: inline-block; width: 30%">Messages</h3>
			<h3 style="display: inline-block; width: 30%">Events</h3>
			<h3 style="display: inline-block; width: 30%">News</h3>
		</div>
	</body>
</html>