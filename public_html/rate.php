<?php

    require_once('../private_html/handlers/RateHandler.php');
    require_once('../private_html/handlers/AuthSessionHandler.php');

    $auth_obj = new AuthSessionHandler();
    $auth_obj->checkAll();

echo "a20";

    $userid = $_SESSION['user_id'];
    $testid = $_POST['testid'];
    $score = $_POST['score'];

    $rate_obj = new RateHandler();
    $rate_obj->rateTest($userid, $testid, $score);

    unset($rate_obj);
    unset($auth_obj);

?>
