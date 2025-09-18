<?php
    include_once("services/redirection.php");
    include_once("services/auth.php");
    include_once("services/userstorage.php");
    include_once("services/book.php");
    include_once("services/bookingstorage.php");
    
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



    $jsonData = file_get_contents('data/cars.json'); 
    $cars = json_decode($jsonData, true);

    $carId = isset($_GET['id']) ? $_GET['id'] : null;

    $car = null;
    foreach ($cars as $item) {
        if ($item['id'] === $carId) {
            $car = $item;
            break;
        }
    }


    //Booking
    $data = [];
    $errors = [];
    if (count($_POST) > 0){
        $data = [
            'user_email' => $user['email'],
            'car_id' => $carId,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date']
        ];

        $book = new Book(new BookingStorage());

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $errors[] = 'Please fill in start and end date';
        }
        elseif (strtotime($_POST['end_date']) < strtotime($_POST['start_date'])){
            $errors[] = 'End date should be after start date';
        }
        elseif ($book->isDateRangeConflicting($carId, $_POST['start_date'], $_POST['end_date'])){
            redirect("unsucessi.php?" . http_build_query($data));
        }
        else {
            $book->register($data);
            redirect("sucessu.php?" . http_build_query($data));
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
            <?php if ($user): ?>
                <form action="profile.php" method="get">
                    <button class="l_button">Profile</button>
                </form>
                <form action="index.php" method="get">
                    <button name="logout" class="y_button">Logout</button>
                </form>
            <?php else: ?>
                <form action="login.php">
                    <button class="l_button">Login</button>
                </form>
                <form action="register.php">
                    <button class="y_button">Registration</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="car_details">
        <img src="<?= htmlspecialchars($car['image']); ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
        <h1><?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
        <p><strong>Fuel:</strong> <?= htmlspecialchars($car['fuel_type']); ?></p>
        <p><strong>Shifter:</strong> <?= htmlspecialchars($car['transmission']); ?></p>
        <p><strong>Year of Manufacture:</strong> <?= htmlspecialchars($car['year']); ?></p>
        <p><strong>Number of Seats:</strong> <?= htmlspecialchars($car['passengers']); ?></p>
        <p><strong>Price:</strong> <?= number_format($car['daily_price_huf'], 0, '.', ' '); ?> Ft/day</p>

        <div class="actions">
            <?php if ($user): ?>
                <form action="" method="post">
                    <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['id']); ?>">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date">
                    <button type="submit" class="y_button02">Book Now</button>
                </form>
                <br>
                <?php if (count($errors) > 0): ?>
                    <?php foreach ($errors as $e): ?>
                        <span class="error"><?= htmlspecialchars($e) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <form action="login.php" method="">
                    <button class="y_button02" type="submit">Book</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero">
        <form action="index.php">
            <button>Back to Homepage</button>
        </form>
    </div>
</body>
</html>
