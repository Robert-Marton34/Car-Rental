<?php
    function isUserValid($inputData, &$errors)
    {
        if (!isset($inputData['email']) || !isset($inputData['name']) || !isset($inputData['password']))
            $errors[] = "All input field are required";

        if (!filter_var($inputData['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = "Invalid email address";

        $nameRegex = '/^[A-Za-z]+([\'\-\s][A-Za-z]+)*$/';
        if (!preg_match($nameRegex, $inputData['name']))
            $errors[] = "Invalid name";
        
        $passRegex = '/^(?=.*[0-9])(?=.*[!@#$%&])[A-Za-z0-9@$!&#]{8,}$/';
        if (!preg_match($passRegex, $inputData['password']))
            $errors[] = "Password must be 8 characters long, contain a number and a special character";
        
        return count($errors) === 0;
        
    }

    function isLoginValid($inputData, &$errors)
    {
        if (!isset($inputData['email']) || !isset($inputData['password']))
            $errors['global'] = "Missing email or password";

        if (trim($inputData['email']) === "")
            $errors['email'] = "Email is missing";

        if (trim($inputData['password']) === "")
            $errors['password'] = "Password is missing";

        return count($errors) === 0;
    }