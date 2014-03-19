<?php
session_set_cookie_params(0);
session_start();

if(!(isset($_SESSION['validated_user']) && $_SESSION['validated_user']))
{
	header('Location: index.php');
	exit();
}
?>