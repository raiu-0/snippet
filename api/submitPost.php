<?php
require '../utils/fileManager.php';
require '../utils/validateManager.php';
session_start();
date_default_timezone_set('Asia/Hong_Kong');

if (isset($_POST['caption']) || !empty($_FILES['post-media'])) {
    $postFiles = uploadFiles($_FILES['post-media'], acceptedMediaFormats, '../');

    $caption = cleanValue($_POST['post-caption']);
    if (count($postFiles) > 0 || !empty($caption)) {
        require '../utils/dbManager.php';
        $con = startDBConnection();
        if (strlen($caption) === 0)
            insertInto($con, 'posts', null, $_SESSION['user'], null, date('Y-m-d H:i:s'), 0);
        else
            insertInto($con, 'posts', null, $_SESSION['user'], $caption, date('Y-m-d H:i:s'), 0);
        $postID = mysqli_insert_id($con);

        foreach ($postFiles as $files)
            insertInto($con, 'post_files', $postID, $files);
        endDBConnection($con);
        echo json_encode(['msg' => 'Post published successfully.']);
    } else
        echo json_encode(['msg' => 'Post must have valid files or caption.']);
}