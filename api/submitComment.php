<?php
date_default_timezone_set('Asia/Hong_Kong');

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

if (isset($data['id'], $data['content'])) {
    session_start();
    require '../utils/dbManager.php';
    require '../utils/validateManager.php';

    $con = startDBConnection();
    
    $id = cleanValue($data['id']);
    $comment = cleanValue($data['content']);

    insertInto($con, 'comments', $id, $_SESSION['user'], $comment, date('Y-m-d H:i:s'));

    endDBConnection($con);
    echo 'Success';
}