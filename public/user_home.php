<?php
require("../includes/ensure_user_logon.php");
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
			<h2 style="color: gold; float: right; margin-top: 1em; line-height: 2.5em; vertical-align: bottom;">Hello <?php echo $_SESSION["validated_user"] ?></h2>
		</header>
		<div style="clear: both;"></div>
		<div style="margin-left: 3em;">
			<h3 style="display: inline-block; width: 30%">Messages</h3>
			<h3 style="display: inline-block; width: 30%">Events</h3>
			<h3 style="display: inline-block; width: 30%">News</h3>
		</div>
	</body>
</html>