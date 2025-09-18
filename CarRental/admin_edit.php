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

    $jsonData = file_get_contents('data/cars.json'); 
    $cars = json_decode($jsonData, true);

    $carId = isset($_GET['id']) ? ($_GET['id']) : null;

    $car = null;
    foreach ($cars as $item) {
        if ($item['id'] === $carId) {
            $car = $item;
            break;
        }
    }

    $errors = []; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $carId !== null) {
        $brand = trim($_POST['brand']);
        $model = trim($_POST['model']);
        $year = intval($_POST['year']);
        $passengers = intval($_POST['passengers']);
        $transmission = $_POST['transmission'];
        $fuel_type = $_POST['fuel_type'];
        $daily_price_huf = intval($_POST['daily_price_huf']);
        $image = trim($_POST['image']);

    
        if (empty($brand)) {
            $errors['brand'] = "Brand cannot be empty";
        }
        if (empty($model)) {
            $errors['model'] = "Model cannot be empty";
        }
        if (empty($year) || $year < 1) {
            $errors['year'] = "Year invalid";
        }
        if (empty($passengers) || $passengers < 1) {
            $errors['passengers'] = "Passengers must be at least 1";
        }
        if (empty($daily_price_huf) || $daily_price_huf <= 0) {
            $errors['daily_price_huf'] = "Daily price must be greater than 0";
        }
        if (empty($image)){
            $errors[] = "Image URL is required";
        }
    
        if (empty($errors)) {
            foreach ($cars as &$item) {
                if ($item['id'] === $carId) {
                    $item['brand'] = htmlspecialchars($brand);
                    $item['model'] = htmlspecialchars($model);
                    $item['year'] = $year;
                    $item['passengers'] = $passengers;
                    $item['transmission'] = $transmission;
                    $item['fuel_type'] = $fuel_type;
                    $item['daily_price_huf'] = $daily_price_huf;
                    $item['image'] = $image;
                    break;
                }
            }
            file_put_contents('data/cars.json', json_encode($cars, JSON_PRETTY_PRINT));
            redirect("index.php");
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
    <div class="forming">
        <h1>Edit Car Details</h1>
            <form action="" method="post">
            <label for="brand">Brand:</label>
            <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($car['brand']) ?>"><br>

            <label for="model">Model:</label>
            <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>"><br>

            <label for="year">Year:</label>
            <input type="number" id="year" name="year" value="<?= htmlspecialchars($car['year']) ?>"><br>

            <label for="passengers">Passengers:</label>
            <input type="number" id="passengers" name="passengers" value="<?= htmlspecialchars($car['passengers']) ?>"><br>

            <label for="transmission">Transmission:</label>
            <select id="transmission" name="transmission">
                <option value="Automatic" <?= $car['transmission'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                <option value="Manual" <?= $car['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
            </select><br>

            <label for="fuel_type">Fuel Type:</label>
            <select id="fuel_type" name="fuel_type">
                <option value="Petrol" <?= $car['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                <option value="Diesel" <?= $car['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Electric" <?= $car['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
            </select><br>

            <label for="daily_price_huf">Daily Price (HUF):</label>
            <input type="number" id="daily_price_huf" name="daily_price_huf" value="<?= htmlspecialchars($car['daily_price_huf']) ?>"><br>

            <label for="image">Image:</label>
            <input type="text" id="image" name="image" value="<?= htmlspecialchars($car['image']) ?>"><br>

            <button type="submit">Save Changes</button>
        </form>
        <br>
        <?php if (count($errors) > 0): ?>
            <?php foreach ($errors as $e): ?>
                <span class="error"><?= htmlspecialchars($e) ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="hero">
        <form action="index.php">
            <button>Back to Homepage</button>
        </form>
    </div>
</body>
</html>
