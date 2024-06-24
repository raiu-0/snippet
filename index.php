<!DOCTYPE html>
<html lang="en">

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
                <form class="flex-col" id="login-form">
                    <input type="text" placeholder="Email">
                    <span class="error-msg"></span>
                    <input type="password" placeholder="Password">
                    <span class="error-msg"></span>
                    <button id="login-button">Login</button>
                </form>
                <hr>
                <a href="sign-up.php"><button type="button" id="create-account-button">Create an Account</button></a>
            </div>
        </div>
    </div>
</body>
</html>