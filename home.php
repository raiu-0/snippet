<!DOCTYPE html>
<html lang="en">

<?php
require 'utils/fileManager.php';
require 'utils/validateManager.php';
session_start();
date_default_timezone_set('Asia/Hong_Kong');

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
    <link rel="icon" type="image/x-icon" href="images/icons/snippet-icon.png">
</head>

<body>
    <?php require 'utils/menuPanel.php'; ?>

    <div class="content-panel flex-col">
        <form id="post-frame" class="flex-row" method="post" enctype="multipart/form-data">
            <div id="create-post-frame" class="card flex-col">
                <div class="caption-frame flex-row">
                    <img src="uploads/<?php echo $_SESSION['picture'] ?>" alt="" class="profile-icon">
                    <input type="text" id="post-caption" name="post-caption" placeholder="Enter a caption." autocomplete="off">
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
        <div id="post-area" class="post-area flex-col">
        </div>
    </div>
    <?php require 'utils/searchPanel.php' ?>
</body>

<template id="exit-template">
    <button onclick="removeMedia(this)"><img src="images/icons/x-icon.png"></button>
</template>

<script src="scripts/preview-script.js"></script>
<script src="scripts/posts-script.js"></script>
<script>getPosts(<?php echo '"'.$_SESSION['user'].'"'?>);</script>
<script>
    document.getElementById('post-frame').addEventListener('submit', async (event) => {
    event.preventDefault();

    var formData = new FormData(document.getElementById('post-frame'));

    const request = new Request('api/submitPost.php', {
        method: 'POST',
        body: formData
    });

    const response = await fetch(request);
    const text = await response.text();
    console.log(text);
    const data = JSON.parse(text);
    alert(data.msg);
    mediaPreview.innerHTML = '';
    mediaPreview.classList.remove('has-content');
    mediaInput.files = (new DataTransfer()).files;
    document.getElementById('post-caption').value = '';
    document.getElementById('post-area').innerHTML = '';
    getPosts(<?php echo '"'.$_SESSION['user'].'"'?>);
});
</script>
</html>