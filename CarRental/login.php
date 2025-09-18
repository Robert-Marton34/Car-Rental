<?php
    include_once("services/validation.php");
    include_once("services/redirection.php");
    include_once("services/auth.php");
    include_once("services/userstorage.php");

    session_start();
    
    $user_storage = new UserStorage();
    $auth = new Auth($user_storage);

    $data = [];
    $errors = [];

    if (count($_POST) > 0){
        if (isLoginValid($_POST, $errors)){
            $data = [
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];

            $auth_user = $auth->authenticate($data['email'], $data['password']);

            if (!$auth_user){
                $errors['global'] = "Password or Email is inncorrect";
            }
            else{
                $auth->login($auth_user);
                redirect('index.php');
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
            <form action="index.php" method="get">
                <button class="l_button">Homepage</button>
            </form>
            <form action="register.php" method="get">
                <button class="y_button">Registration</button>
            </form>
        </div>
    </div>
    <div class="forming">
        <h1>Login</h1>

        <?php if (isset($errors['global'])) : ?>
            <p><span class="error"><?= $errors['global'] ?></span></p>
        <?php endif; ?>

        <form action="" method="post">
            Email:<br>
            <input type="text" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>"><br>

            <?php if (isset($errors['email'])) : ?>
                <span class="error"><?= $errors['email'] ?></span><br>
            <?php endif; ?>

            Password:<br>
            <input type="password" name="password" id="password"><br>

            <?php if (isset($errors['password'])) : ?>
                <span class="error"><?= $errors['password'] ?></span><br>
            <?php endif; ?>

            <button>Login</button>
        </form>
    </div>
</body>
</html>