<?php

    require_once('../private_html/handlers/SearchHandler.php');

    $searchschool = $_POST['schoolname'];

    $search_obj = new SearchHandler();
    $search_obj->searchSchools($searchschool);
    $searchresult = $search_obj->getResult();

    echo json_encode(Array('status'=> true, 'schoolresults' => $searchresult));

    unset($search_obj);

?>