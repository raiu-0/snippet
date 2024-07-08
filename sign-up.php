<!DOCTYPE html>
<html lang="en">

<?php
require 'utils/validateManager.php';
$fields = ['email', 'name', 'username', 'password', 'birthday'];
if (isset($_POST['sign-up'])) {
    $error = [];
    $userData = [];
    validate($fields, $error, $_POST, true, $userData);
    if (count($error) === 0) {
        require 'utils/dbManager.php';
        $con = startDBConnection();

        $username = $userData['username'];
        $password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $email = $userData['email'];
        $name = $userData['name'];
        $birthday = $userData['birthday'];

        if (rowExists($con, 'users', 'username', $username))
            $error['username'] = 'Username already taken.';

        if (rowExists($con, 'users', 'email', $email))
            $error['email'] = 'Email already in use.';

        if (count($error) === 0)
            insertInto($con, 'users', $username, $password, $email, $name, $birthday, null, 0, 0);

        endDBConnection($con);
        if(count($error) === 0)
            header('location: index.php');
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
            <form method="post" class="flex-col card" id="sign-up">
                <h2 class="card-title">Sign-Up</h2>
                <hr>
                <input type="text" name="email" placeholder="Email" value="<?php printPOSTVal('email') ?>">
                <?php printError('email') ?>
                <input type="text" name="name" placeholder="Name" value="<?php printPOSTVal('name') ?>">
                <?php printError('name') ?>
                <input type="date" name="birthday" id="birthday" placeholder="Birthday"
                    onchange="showBirthdayPlaceholder(this);" value="<?php printPOSTVal('birthday') ?>">
                <?php printError('birthday') ?>
                <input type="text" name="username" placeholder="Username" value="<?php printPOSTVal('username') ?>">
                <?php if (!printError('username')): ?>
                    <span class="input-guide">Usernames may only contain letters, numbers and underscores.</span>
                <?php endif; ?>
                <input type="password" name="password" placeholder="Password" value="<?php printPOSTVal('password') ?>">
                <?php if (!printError('password')): ?>
                    <span class="input-guide">
                        Passwords should contain at least have a length of 6, contain at least one lowercase letter, one
                        uppercase letter, one digit, and one symbol.
                    </span>
                <?php endif; ?>
                <button type="submit" id="sign-up-button" name="sign-up" value="submit">Sign-Up</button>
            </form>
        </div>
    </div>
</body>
<script>
    function showBirthdayPlaceholder(e) {
        e.value != '' ? e.classList.add('has-value') : e.classList.remove('has-value');
    }
    showBirthdayPlaceholder(document.getElementById('birthday'));
</script>

</html>