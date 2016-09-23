<?php

    require_once('../private_html/handlers/SearchHandler.php');

    $searchschoolid = $_POST['schoolid'];
    $searchclassname = $_POST['classname'];

    $search_obj = new SearchHandler();
    $search_obj->searchClasses($searchschoolid, $searchclassname);
    $searchresult = $search_obj->getResult();

    echo json_encode(Array('status' => true, 'classresults' => $searchresult));

    unset($search_obj);

?>