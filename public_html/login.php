<?php

	require_once('../private_html/handlers/LoginHandler.php');
	require_once('../private_html/handlers/AuthSessionHandler.php');

	$auth_obj = new AuthSessionHandler();;

	$email = $_POST['email'];
	$password = $_POST['password'];

	$login_obj = new LoginHandler();
	$login_obj->attemptLogin($email, $password);

	unset($login_obj);
	unset($login_obj);

?>