<?php
    include_once("services/validation.php");
    include_once("services/redirection.php");
    include_once("services/auth.php");
    include_once("services/userstorage.php");

    session_start();

    $data = [];
    $errors = [];

    if(count($_POST) > 0){
        if (isUserValid($_POST, $errors)){
            $data = [
                'fullname' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];

            $auth = new Auth(new UserStorage());

            if ($auth->user_exists($data['email']))
                $errors[] = "User already exists with this email";

            elseif ($data['password'] != $_POST['confirm_password']){
                $errors[] = "Passwords do not match";
            }
            else {
                $auth->register($data);
                redirect("login.php");
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
            <form action="login.php" method="get">
                <button class="l_button">Login</button>
            </form>
            <form action="index.php" method="get">
                <button class="y_button">Homepage</button>
            </form>
        </div>
    </div>
    <div class="forming">
        <h1>Registration</h1>
        <form action="" method="post">
            Full Name:<br>
            <input type="text" id="name" name="name" placeholder="Full Name"> <span></span> <br>
            Email:<br>
            <input type="email" id="email" name="email" placeholder="Email"><br>
            Password:<br>
            <input type="password" id="password" name="password" placeholder="Password"><br>
            Confirm Password:<br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm"><br>

            <button>Register</button> 
        </form>
        <br>
        <?php if (count($errors) > 0): ?>
            <?php foreach ($errors as $e): ?>
                <span class="error"><?= htmlspecialchars($e) ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>