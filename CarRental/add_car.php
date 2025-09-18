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

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $year = intval($_POST['year'] ?? 0);
        $transmission = $_POST['transmission'] ?? '';
        $fuel_type = $_POST['fuel_type'] ?? '';
        $passengers = intval($_POST['passengers'] ?? 0);
        $daily_price_huf = intval($_POST['daily_price_huf'] ?? 0);
        $image = trim($_POST['image'] ?? '');


        if (empty($brand)){
            $errors[] = "Brand cannot be empty";
        }
        if (empty($model)){
            $errors[] = "Model cannot be empty";
        }
        if (empty($year) || $year < 1){
            $errors[] = "Year Invalid";
        }
        if (empty($passengers) || $passengers < 1){
            $errors[] = "Passengers must be at least 1";
        }
        if (empty($daily_price_huf) || $daily_price_huf < 1){
            $errors[] = "Daily price must be greater than 0";
        }
        if (empty($image)){
            $errors[] = "Image URL is required";
        }

        if (empty($errors)) {
            $newCarId = uniqid();

            $newCar = [
                'id' => $newCarId,
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
                'transmission' => $transmission,
                'fuel_type' => $fuel_type,
                'passengers' => $passengers,
                'daily_price_huf' => $daily_price_huf,
                'image' => $image,
            ];

            $cars[] = $newCar;


            file_put_contents('data/cars.json', json_encode($cars, JSON_PRETTY_PRINT));

            redirect("index.php");
            exit();
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
        <h1>Add Car</h1>
        <form action="" method="post">
            <label for="brand">Brand:</label>
            <input type="text" id="brand" name="brand"><br>
            
            <label for="model">Model:</label>
            <input type="text" id="model" name="model"><br>

            <label for="year">Year:</label>
            <input type="number" id="year" name="year"><br>

            <label for="transmission">Transmission:</label>
            <select id="transmission" name="transmission">
                <option value="Automatic">Automatic</option>
                <option value="Manual">Manual</option>
            </select><br>

            <label for="fuel_type">Fuel Type:</label>
            <select id="fuel_type" name="fuel_type">
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
            </select><br>

            <label for="passengers">Passengers:</label>
            <input type="number" id="passengers" name="passengers"><br>

            <label for="daily_price_huf">Daily Price (HUF):</label>
            <input type="number" id="daily_price_huf" name="daily_price_huf"><br>

            <label for="image">Image:</label>
            <input type="text" id="image" name="image"><br>

            <button type="submit">Add</button>
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