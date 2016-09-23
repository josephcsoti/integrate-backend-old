<?php

    require_once('../private_html/handlers/SignupHandler.php');

    $email = $_POST['email'];
    $password = $_POST['password'];

    $signup_obj = new SignupHandler();
    $signup_obj->createAccount($email, $password);

    unset($signup_obj);

?>