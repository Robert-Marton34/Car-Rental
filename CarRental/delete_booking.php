<?php

    include_once("services/redirection.php");
    include_once("services/book.php");
    include_once("services/bookingstorage.php");


    session_start();


    $storage = new BookingStorage();
    $book = new Book($storage);

    if (isset($_GET['id'])) {
        $bookIdToDelete = $_GET['id'];
        $book->deleteBooking($bookIdToDelete);
        redirect("profile.php");
    }