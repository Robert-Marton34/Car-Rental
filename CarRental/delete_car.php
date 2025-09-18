<?php
    include_once("services/redirection.php");

    session_start();

    $cars = json_decode(file_get_contents('data/cars.json'), true);

    if (isset($_GET['id'])) {
        $carIdToDelete = $_GET['id'];
    
        $carFound = false;
        foreach ($cars as $index => $car) {
            if ($car['id'] == $carIdToDelete) {
                unset($cars[$index]);
                $carFound = true;
                break;
            }
        }
    
        if ($carFound) {
            file_put_contents('data/cars.json', json_encode($cars, JSON_PRETTY_PRINT));
            redirect("index.php");
        }
    }