<?php

	require_once('../private_html/handlers/AuthSessionHandler.php');

	$auth_obj = new AuthSessionHandler();
	$auth_obj->destroySession();

	echo json_encode(array('status' => true, 'message'=> "Logged out"));

	unset($auth_obj);

?>