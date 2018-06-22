<?php
	require_once "config/GoogleLogin.php";
	require_once "config/facebookLogin.php";

	$googleLogin = new GoogleLogin();
	
	unset($_SESSION['access_token']);
	$googleLogin->gClient->revokeToken();
	session_destroy();
	header('Location: index.php');
	exit();
?>