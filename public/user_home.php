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
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
			<h1 style="color: gold; float: right; margin-top: 1em;">Hello <?php echo $_SESSION["validated_user"] ?></h2>
		</header>
		<div style="clear: both;"></div>
		<div style="clear: both;"></div>
		<div style="margin-left: 3em;">
			<h3 style="display: inline-block; width: 43%; vertical-align: top;">Games Playing In</h3>
			<div style="display: inline-block; width: 43%; vertical-align: top;">
				<h3>Games You Can Join</h3>
				<ol>
				<?php
				$joinable = $db->query("joinable_games");
			
				foreach($joinable as $jgame)
					echo "<li>".$jgame["game_name"]."</li>";
				?>
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