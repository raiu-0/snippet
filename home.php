<!DOCTYPE html>
<html lang="en">

<?php
require 'utils/fileManager.php';
session_start();

if (isset($_POST['publish'])) {
    $postFiles = [];
    foreach ($_FILES['post-media']['name'] as $index => $fileName) {
        if (in_array(getFileExtension($fileName), $acceptedFormats)) {
            $uploadName = $fileName;
            if (file_exists('uploads/' . $fileName)) {
                $i = 0;
                while (file_exists('uploads' . $fileName))
                    $i++;
                $uploadName = appendToFilename($uploadName, $i);
            }
            move_uploaded_file($_FILES['post-media']['tmp_name'][$index], 'uploads/' . $uploadName);
            $postFiles[] = $uploadName;
        }
    }

    $caption = htmlspecialchars(strip_tags(trim($_POST['post-caption'])));
    if (count($postFiles) > 0 || !empty($caption)) {
        require 'utils/dbManager.php';
        $con = startDBConnection();
        if (strlen($caption) === 0)
            insertInto($con, 'posts', null, $_SESSION['user'], null);
        else
            insertInto($con, 'posts', null, $_SESSION['user'], $caption);
        $postID = mysqli_insert_id($con);

        foreach ($postFiles as $files)
            insertInto($con, 'post_files', $postID, $files);
    }
    header('location: home.php');
}
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
    <div class="menu-panel flex-col">
        <div class="account-display flex-row">
            <img src="images/icons/default-profile-icon.png" alt="" class="profile-icon">
            <div class="username-display"><?php echo $_SESSION['name'] ?></div>
        </div>
        <hr>
        <div class="menu-option flex-row">
            <img src="images/icons/home-icon.png" alt="" class="icon-with-label">
            Home
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/notifications-icon.png" alt="" class="icon-with-label">
            Notifications
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/settings-icon.png" alt="" class="icon-with-label">
            Settings
            <a href="home.php"><span class="button-link"></span></a>
        </div>
        <div class="menu-option flex-row">
            <img src="images/icons/account-icon.png" alt="" class="icon-with-label">
            Account
            <a href="home.php"><span class="button-link"></span></a>
        </div>
    </div>

    <div id="content-panel">
        <form id="post-frame" class="flex-row" method="post" enctype="multipart/form-data" onsubmit="clearFileInput()">
            <div id="create-post-frame" class="card flex-col">
                <div class="caption-frame flex-row">
                    <img src="images/icons/default-profile-icon.png" alt="" class="profile-icon">
                    <input type="text" id="post-caption" name="post-caption" placeholder="Enter a post.">
                </div>
                <div class="file-frame flex-row">
                    <img src="images/icons/image-icon.png" class="icon" alt="">
                    <img src="images/icons/video-icon.png" class="icon" alt="">
                    <div class="photo-video-label">Photo/Video</div>
                    <input type="file" name="post-media[]" id="post-media" class="button-link" accept="image/*,video/*"
                        multiple>
                </div>
                <div id="media-preview" class="flex-row"></div>
            </div>
            <button type="submit" id="post-button" class="flex-row" name="publish" value="publish">
                <img src="images/icons/publish-icon.png" id="publish-icon" class="icon">
                Publish
            </button>
        </form>
    </div>
    <div id="right-panel"></div>
</body>

<template id="exit-template">
    <button onclick="removeMedia(this)"><img src="images/icons/x-icon.png"></button>
</template>

<script src="scripts/preview-script.js"></script>

</html>