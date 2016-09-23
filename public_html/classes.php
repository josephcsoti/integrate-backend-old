<?php

	require_once('../private_html/handlers/ClassesHandler.php');
	require_once('../private_html/handlers/AuthSessionHandler.php');

	$auth_obj = new AuthSessionHandler();
	$auth_obj->checkAll();

	$userid = $_SESSION['user_id'];

	$classes_obj = new ClassesHandler();
	$classes_obj->getUserClasses($userid);

	unset($classes_obj);
	unset($auth_obj);

?>