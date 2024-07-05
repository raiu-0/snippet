<!DOCTYPE html>
<html lang="en">

<?php
session_start();

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>snippet</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
</head>

<body>
    <div id="left-panel" class="flex-col">
        <div id="account-display" class="flex-row">
            <img src="images/icons/default-profile-icon.png" alt="" class="profile-icon">
            <div class="username-display"><?php echo $_SESSION['name'] ?></div>
        </div>
        <hr>
        <div class="menu-option flex-row">
            <img src="images/icons/home-icon.png" alt="" class="menu-icon">
            Home
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/notifications-icon.png" alt="" class="menu-icon">
            Notifications
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/settings-icon.png" alt="" class="menu-icon">
            Settings
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/account-icon.png" alt="" class="menu-icon">
            Account
            <a href="home.php"><span class="button-link"></span></a>
        </div>
    </div>

    <div id="middle-panel">
        <div id="create-post-frame" class="card flex-row">
            <img src="images/icons/default-profile-icon.png" alt="" class="profile-icon">
            <form method="POST" enctype="multipart/form-data"></form>
            <input type="text" id="post-caption" name="post-caption" placeholder="Enter a post.">
            <input type="file" name="post-picture" placeholder="Enter a post.">
        </div>
    </div>
    <div id="right-panel"></div>
</body>

</html>