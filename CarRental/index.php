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

    //Filtering
    $filteredCars = $cars;
    $selectedTransmission = $_GET['transmission'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $transmission = $_GET['transmission'] ?? '';
        $minPassengers = (int)($_GET['passengers'] ?? 0);
        $priceMin = (int)($_GET['price_min'] ?? 0);
        $priceMax = (int)($_GET['price_max'] ?? PHP_INT_MAX);
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $filteredCars = [];
        
        foreach ($cars as $car) {
            if ((!$transmission || $car['transmission'] === $transmission) && ($car['passengers'] >= $minPassengers) && ($car['daily_price_huf'] >= $priceMin) && ($car['daily_price_huf'] <= $priceMax)) {
    
                $book = new Book(new BookingStorage());
                $conflictingBookings = $book->filterRange($start_date, $end_date);
    
                $isAvailable = true;
                foreach ($conflictingBookings as $booking) {
                    if ($booking['car_id'] === $car['id']) {
                        $isAvailable = false;
                        break;
                    }
                }
    
                if ($isAvailable) {
                    $filteredCars[] = $car;
                }
            }
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

    <div class="hero">
        <?php if ($user): ?>
            <h1>Welcome, <?= htmlspecialchars($user['fullname']); ?>!</h1>
            <form action="profile.php">
                <button>My Profile</button>
            </form>
        <?php else: ?>
            <h1>Rent cars easily!</h1>
            <form action="register.php">
                <button>Registration</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="options">
        <form method="GET" class="filters">
            <label for="transmission">Transmission:</label>
            <select name="transmission" id="transmission">
                <option value="" <?= $selectedTransmission === '' ? 'selected' : ''; ?>>Any</option>
                <option value="Automatic" <?= $selectedTransmission === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                <option value="Manual" <?= $selectedTransmission === 'Manual' ? 'selected' : ''; ?>>Manual</option>
            </select>

            <label for="passengers">Minimum Passengers:</label>
            <input type="number" name="passengers" id="passengers" value="<?= htmlspecialchars($_GET['passengers'] ?? 0); ?>">

            <label for="price_min">Price Min (Ft):</label>
            <input type="number" name="price_min" id="price_min" value="<?= htmlspecialchars($_GET['price_min'] ?? 0); ?>">

            <label for="price_max">Price Max (Ft):</label>
            <input type="number" name="price_max" id="price_max" value="<?= htmlspecialchars($_GET['price_max'] ?? 75000); ?>">

            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? ''); ?>">

            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? ''); ?>">

            <button type="submit">Filter</button>
        </form>
        <?php if ($is_admin): ?>
            <form action="add_car.php" method="">
                <button class="add_button">Add</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="car_grid">
        <?php if (!empty($filteredCars)): ?>
            <?php foreach ($filteredCars as $car): ?>
                <div class="car_card">
                    <a href="car_details.php?id=<?= $car['id']; ?>">
                        <img src="<?= htmlspecialchars($car['image']); ?>" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                    </a>
                    <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                    <p><?= htmlspecialchars($car['passengers']); ?> seats - <?= htmlspecialchars($car['transmission']); ?></p>
                    <div class="info">
                        <div class="info_text">
                            <p class="price"><?= number_format($car['daily_price_huf'], 0, '.', ' '); ?> Ft</p>
                        </div>
                        <?php if ($is_admin): ?>            
                            <form action="car_details.php" method="get">
                                <input type="hidden" name="id" value="<?= $car['id']; ?>">
                                <button class="y_button02" type="submit">Book</button>
                            </form>

                            <form action="admin_edit.php" method="get">
                                <input type="hidden" name="id" value="<?= $car['id']; ?>">
                                <button class="l_button02" type="submit">Edit</button>
                            </form>  

                            <form action="delete_car.php" method="get">
                                <input type="hidden" name="id" value="<?= $car['id']; ?>">
                                <button class="d_button" type="submit">Delete</button>
                            </form>
                        <?php else: ?>
                            <form action="car_details.php" method="get">
                                <input type="hidden" name="id" value="<?= $car['id']; ?>">
                                <button class="y_button02" type="submit">Book</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No cars found</p>
        <?php endif; ?>
    </div>
</body>
</html>
