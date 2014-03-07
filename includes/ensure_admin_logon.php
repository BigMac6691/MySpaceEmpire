<?php
session_set_cookie_params(0);
session_start();
        	
if(!(isset($_SESSION['validated_admin']) && $_SESSION['validated_admin']))
{
	header('Location: admin_main.php');
	exit();
}
?>