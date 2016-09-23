<?php

	require_once('../private_html/handlers/TestsHandler.php');
	require_once('../private_html/handlers/AuthSessionHandler.php');

	$auth_obj = new AuthSessionHandler();
	$auth_obj->checkAll();

	$classid = $_POST['class_id'];

	$test_obj = new TestsHandler();
	$test_obj->getTestsForClassID($classid);

	unset($test_obj);
	unset($auth_obj);

?>