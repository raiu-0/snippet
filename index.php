<!DOCTYPE html>
<html lang="en">

<?php
require 'utils/validateManager.php';

session_start();
if(isset($_SESSION['user'], $_SESSION['name']))
    header('location: home.php');

$fields = ['identifier', 'password'];
if (isset($_POST['login'])) {
    $error = [];
    validate($fields, $error, $_POST);
    if (count($error) === 0) {
        require 'utils/dbManager.php';
        $con = startDBConnection();
        $identifier = htmlspecialchars(strip_tags(trim($_POST['identifier'])));
        $password = htmlspecialchars(strip_tags(trim($_POST['password'])));
        $getPass = $con->prepare("SELECT username, password, name FROM users WHERE username = ? OR email = ?");
        $getPass->bind_param('ss', $identifier, $identifier);
        $getPass->execute();
        $result = $getPass->get_result();
        $getPass->close();
        if (mysqli_num_rows($result) !== 0) {
            $result = $result->fetch_assoc();
            if (password_verify($password, $result['password'])) { 
                $_SESSION['user'] = $result['username'];
                $_SESSION['name'] = $result['name'];
                header('location: home.php');
            } else
                $error['password'] = 'Wrong password.';
        } else {
            $error['identifier'] = 'Username / email not associated with any account.';
        }
    }

}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>snippet</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="grid col-2 main-frame">
        <div class="flex-row wrapper">
            <h1 class="title">snippet</h1>
        </div>
        <div class="flex-col wrapper">
            <div class="flex-col wrapper card" id="login">
                <form class="flex-col" id="login-form" method="post">
                    <input type="text" placeholder="Email / Username" name="identifier" value="<?php printPOSTVal('identifier') ?>">
                    <?php printError('identifier') ?>
                    <input type="password" placeholder="Password" name="password" value="<?php printPOSTVal('password') ?>">
                    <?php printError('password') ?>
                    <button id="login-button" name="login">Login</button>
                </form>
                <hr>
                <a href="sign-up.php"><button type="button" id="create-account-button">Create an Account</button></a>
            </div>
        </div>
    </div>
</body>

</html>