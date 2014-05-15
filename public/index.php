<?php
require("../classes/DBSelects.php");

date_default_timezone_set("America/Toronto");
session_set_cookie_params(0);
session_start();

if(isset($_POST['email'], $_POST["password"])  && !isset($_SESSION["validated_user"]))
{
	$db = new DBSelects();
	$resp = $db->select("validate_logon", $_POST);
	
	if(!empty($resp))
	{
		$_SESSION['validated_user'] = $resp[0]["alias"];
		$_SESSION['validated_user_id'] = $resp[0]["user_id"];
		
		header('Location: user_home.php');
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Log In</title>
		<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
			<div style="float: right; margin-top: 1em;">
				<span style="color: blue;">Not a member?</span>
				<form action="create_account.php" style="display: inline;">
					<input type="submit" value="CREATE ACCOUNT" />
				</form>
			</div>
		</header>
		<div style="clear: both;"></div>
		<div style="margin-top: 2em;">
			<h1 style="color: gold; text-align: center;">Member Login</h1>
			<form action="index.php" method="post">
				<p style="text-align: center;">
					<img src="images/icons/mail.png" width="48px" height="48px" />
					<input style="padding: 0.5em;" type="text" size="30" name="email" id="email" placeholder="email" />
				</p>
				<p style="text-align: center;">
					<img src="images/icons/lock.png" width="48px" height="48px" />
					<input style="padding: 0.5em;" type="password" size="30" name="password" id="password" placeholder="password" />
				</p>
				<p style="text-align: center; margin-top: 0.1em; margin-bottom: 0.1em;">
					<button>Log In</button>
				</p>
			</form>
			<p style="text-align: center;">
				<a href="javascript:alert('Go to recover password page.');">Forgot your password?</a>
			</p>
		</div>
	</body>
</html>