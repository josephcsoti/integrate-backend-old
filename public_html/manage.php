<?php

    require_once('../private_html/handlers/ClassesHandler.php');
    require_once('../private_html/handlers/AuthSessionHandler.php');

    $auth_obj = new AuthSessionHandler();
    $auth_obj->checkAll();

    $user_id = $_SESSION['user_id'];

    $classid_1 = $_POST['classid1'];
    $classid_2 = $_POST['classid2'];
    $classid_3 = $_POST['classid3'];
    $classid_4 = $_POST['classid4'];
    $classid_5 = $_POST['classid5'];
    $classid_6 = $_POST['classid6'];
    $classid_7 = $_POST['classid7'];
    $classid_8 = $_POST['classid8'];

    $classes_obj = new ClassesHandler();
    $classes_obj->updateUserClasses($user_id, $classid_1, $classid_2, $classid_3, $classid_4, $classid_5, $classid_6, $classid_7, $classid_8);

    unset($classes_obj);
    unset($auth_obj);

?>