<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
	</head>
	<body>
		<div style="height: 3em; margin-left: 5em; margin-right: 5em;">
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</div>
		<div style="clear: both;"></div>
		<div style="margin-top: 2em; margin-left: 5em;">
			<h1 style="color: gold; text-align: center;">Create Account</h1>
			<p style="">
				<img src="images/icons/user_info.png" width="48px" height="48px" />
				<input style="padding: 0.5em;" type="text" size="30" name="alias" id="alias" />
				<span id="alias_msg"></span>
			</p>
			<p style="">
				<img src="images/icons/mail.png" width="48px" height="48px" />
				<input style="padding: 0.5em;" type="text" size="30" name="user_id" id="user_id" />
				<span id="email_msg"></span>
			</p>
			<p style="">
				<img src="images/icons/lock.png" width="48px" height="48px" />
				<input style="padding: 0.5em;" type="password" size="30" name="password" id="password" />
				<span id="password_msg">Must contain at least one letter and one number and be at least 8 characters long.</span>
			</p>
			<p style="margin-top: 0.1em; margin-bottom: 0.1em;">
				<input type="button" value="Create Account" onclick="javascript:alert('Attempt to create account.')"/>
			</p>
		</div>
	</body>
</html>