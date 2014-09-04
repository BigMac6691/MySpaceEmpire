<?php
require("../../classes/DBAdmin.php");
		
session_set_cookie_params(0);
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>My Space Empire - Admin Main</title>
		<link rel="stylesheet" href="../css/main.css" media="screen" />
	</head>
	<body>
		<header>
			<h1 style="color: red; margin-top: 1em; float: left;">My Space Empire</h1>
		</header>
		<div style="clear: both;"></div>
		<?php
      if(isset($_POST['logoff']))
		{
			unset($_SESSION['validated_admin']);
			validatePassword();
		}
		elseif(isset($_SESSION['validated_admin']) && $_SESSION['validated_admin'])
      	showOptions();
      else
      	validatePassword();
		
		function validatePassword()
		{
			if(isset($_POST['uid']) && isset($_POST['password']))
			{
				$dal = new DBAdmin();
				$rows = $dal->select("validate_admin", $_POST);
					
				if(count($rows) > 0)
				{
					$_SESSION['validated_admin'] = $_POST['uid'];
					showOptions();
					return;
				} 
				else
					echo "<p style=\"color:red; font-weight:bold;\">Logon failed, try again.</p><br>\n";
			}
			?>
			<div style="margin-left: 3em;">
				<p>Please enter uid and password.</p>
         	<form action="admin_main.php" method="post">
         		User ID:<input name="uid" type="uid" size="10">
            	Password:<input name="password" type="password" size="30">
         		<input type="submit" value="Logon">
         	</form>
         </div>
			<?php
		}
		
		function showOptions()
		{
			?>
			<div style="margin-left: 3em;">
				<h2>What do you want to do today?</h2>
				<ul>
					<li><a href="create_game.php">Create new game.</a></li>
					<li><a href="maintain_components.php">Maintain Buildable Components.</a></li>
					<li><a href="maintain_industry_component_link.php">Maintain Industry Type to Buildable Component Link.</a></li>
				</ul>
				<br />
				<form action="admin_main.php" method="post">
            	<input type="hidden" name="logoff" value="true">
            	<input type="submit" value="Logoff">
            </form>
			</div>
			<?php
		}
      ?>
	</body>
</html>