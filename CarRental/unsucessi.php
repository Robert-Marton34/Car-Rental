<?php
    include_once("services/redirection.php");
    include_once("services/auth.php");
    include_once("services/userstorage.php");

    session_start();

    $user_storage = new UserStorage();
    $auth = new Auth($user_storage);

    $user = $auth->authenticated_user();
    $is_admin = $auth->is_admin();

    if (isset($_GET['logout'])) {
        $auth->logout();
        redirect("index.php");
        exit();
    }

    $booking_data = $_GET; 

    $user_email = isset($booking_data['user_email']) ? htmlspecialchars($booking_data['user_email']) : '';
    $car_id = isset($booking_data['car_id']) ? htmlspecialchars($booking_data['car_id']) : '';
    $start_date = isset($booking_data['start_date']) ? htmlspecialchars($booking_data['start_date']) : '';
    $end_date = isset($booking_data['end_date']) ? htmlspecialchars($booking_data['end_date']) : '';

    $jsonData = file_get_contents('data/cars.json'); 
    $cars = json_decode($jsonData, true);

    $car = null;
    foreach ($cars as $item) {
        if ($item['id'] === $car_id) {
            $car = $item;
            break;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKarRental</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <div class="navbar">
        <div class="logo">iKarRental</div>
            <div class="nav_links">
                <form action="profile.php" method="get">
                    <button class="l_button">Profile</button>
                </form>
                <form action="index.php" method="get">
                    <button name="logout" class="y_button">Logout</button>
                </form>
        </div>
    </div>
    <div class="messsage">
        <h1>Booking Failed!</h1>
        <figure>
            <a href="media/x.jpg"><img src="media/x.jpg"></a>
            <figcaption>The <?= $car['brand']?> <?= $car['model']?> is not availabe in the specified interval <?=$start_date?> to <?=$end_date?><br>
            Try entering a different interval of search for another vehicle </figcaption>
        </figure>
        <form action="index.php" method="">
            <button class="y_button02">Back to vehicle side</button>
        </form>
    </div>


</body>
</html>