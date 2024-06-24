<!DOCTYPE html>
<html lang="en">

<?php
require 'utils.php';
$fields = ['email', 'username', 'password', 'birthday'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sign-up'])) {
    foreach ($fields as $key) {
        if (!isset($_POST[$key]))
            $error[$key] = 'Missing form parameters.';
        else if (empty(htmlspecialchars(strip_tags(trim($_POST[$key])))))
            $error[$key] = "Invalid $key input.";
        else {
            $userData[$key] = htmlspecialchars(strip_tags(trim($_POST[$key])));
            $validity = validateData($userData[$key], $key);
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sign-up.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="flex-col wrapper main-frame" id="sign-up-frame">
        <div class="flex-row wrapper">
            <h1 class="title">snippet</h1>
        </div>
        <div class="flex-row wrapper">
            <form method="POST" class="flex-col card" id="sign-up">
                <h2 class="card-title">Sign-Up</h2>
                <hr>
                <input type="text" name="email" placeholder="Email">
                <span class="error-msg"></span>
                <input type="text" name="name" placeholder="Name">
                <span class="error-msg"></span>
                <input type="date" name="birthday" placeholder="Birthday"
                    onchange="this.value!=''?this.classList.add('has-value'):this.classList.remove('has-value') ">
                <span class="error-msg"></span>
                <input type="text" name="username" placeholder="Username">
                <span class="error-msg"></span>
                <input type="password" name="password" placeholder="Password">
                <span class="error-msg"></span>
                <button type="submit" id="sign-up-button" name="sign-up" value="submit">Sign-Up</button>
            </form>
        </div>
    </div>
</body>

</html>