<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
	</head>
	<body>
		<div style="height: 3em; margin-left: 5em; margin-right: 5em;">
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
			<div style="float: right; margin-top: 1em; ">
				<span style="color: blue;">Not a member?</span>
				<form action="create_account.php" style="display: inline;">
					<input type="submit" value="CREATE ACCOUNT" />
				</form>
			</div>
		</div>
		<div style="clear: both;"></div>
		<div style="margin-top: 2em;">
			<h1 style="color: gold; text-align: center;">Member Login</h1>
			<p style="text-align: center;">
				<img src="images/icons/mail.png" width="48px" height="48px" />
				<input style="padding: 0.5em;" type="text" size="30" name="user_id" id="user_id" />
			</p>
			<p style="text-align: center;">
				<img src="images/icons/lock.png" width="48px" height="48px" />
				<input style="padding: 0.5em;" type="password" size="30" name="password" id="password" />
			</p>
			<p style="text-align: center; margin-top: 0.1em; margin-bottom: 0.1em;">
				<input type="button" value="Log In" onclick="javascript:alert('Attempt log on.')"/>
			</p>
			<p style="text-align: center;">
				<a href="javascript:alert('Go to recover password page.');">Forgot your password?</a>
			</p>
		</div>
	</body>
</html>