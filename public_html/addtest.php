<?php

    require_once('../private_html/handlers/TestsHandler.php');
    require_once('../private_html/handlers/AuthSessionHandler.php');

    $auth_obj = new AuthSessionHandler();
    $auth_obj->checkAll();

    $userid = $_SESSION['user_id'];
    $testname = $_POST['test_name'];
    //$teachername = $_POST['teacher_name'];
    //$testdate = $_POST['test_date'];
    $classid = $_POST['class_id'];

    $test_obj = new TestsHandler();
    $test_obj->addNewTest($userid, $testname, $classid);

    unset($test_obj);
    unset($auth_obj);

?>