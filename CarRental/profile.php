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

    $book = new Book(new BookingStorage());
    $filteredBookings = $book->filterRes($auth->get_user_email());
    $adminBookings = $book->all();

    $jsonData = file_get_contents('data/cars.json'); 
    $cars = json_decode($jsonData, true);
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
            <form action="index.php" method="">
                <button class="l_button">Homepage</button>
            </form>
            <form action="index.php" method="get">
                <button name="logout" class="y_button">Logout</button>
            </form>
        </div>
    </div>
    <div class="hero">
        <h1>Logged in as <?= htmlspecialchars($user['fullname']); ?></h1>
        <form action="index.php">
            <button>Homepage</button>
        </form>
    </div>
    <div class="grid">
        <h2>Reservations</h2>
        <?php if ($is_admin): ?>
            <?php if (empty($adminBookings)): ?>
                    <p>No reservations found.</p>
                <?php else: ?>
                    <div class="car_grid">
                        <?php foreach ($adminBookings as $booking): ?>
                            <?php 
                                $car = null;
                                foreach ($cars as $c) {
                                    if ($c['id'] === $booking['car_id']) {
                                        $car = $c;
                                        break;
                                    }
                                }
                            ?>
                            <?php if ($car): ?>
                                <div class="car_card">
                                    <img src="<?= htmlspecialchars($car['image']); ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                                    <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                                    <p><?= htmlspecialchars($car['passengers']); ?> seats - <?= htmlspecialchars($car['transmission']); ?></p>
                                    <div class="info">
                                        <div class="info-text">
                                            <p>Start date: <?= htmlspecialchars($booking['start_date']); ?></p>
                                            <p>End date: <?= htmlspecialchars($booking['end_date']); ?></p>
                                            <p>User email: <?= htmlspecialchars($booking['user_email']); ?></p>
                                        </div>         
                                        <form action="delete_booking.php" method="get">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($booking['id']); ?>">
                                            <button class="d_button" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
        <?php else: ?>
            <?php if (empty($filteredBookings)): ?>
                <p>No reservations found.</p>
            <?php else: ?>
                <div class="car_grid">
                <?php foreach ($filteredBookings as $booking): ?>
                    <?php 
                        $car = null;
                        foreach ($cars as $c) {
                            if ($c['id'] === $booking['car_id']) {
                                $car = $c;
                                break;
                            }
                        }
                    ?>
                    <?php if ($car): ?>
                        <div class="car_card">
                            <img src="<?= htmlspecialchars($car['image']); ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                            <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                            <p><?= htmlspecialchars($car['passengers']); ?> seats - <?= htmlspecialchars($car['transmission']); ?></p>
                            <div class="info">
                                <div class="info_text">
                                    <p>Start date: <?= htmlspecialchars($booking['start_date']); ?></p>
                                    <p>End date: <?= htmlspecialchars($booking['end_date']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>