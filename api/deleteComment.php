<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';
require '../utils/dateManager.php';
date_default_timezone_set('Asia/Hong_Kong');

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

if(isset($data['id'])) {
    $con = startDBConnection();
    if(getDataByIdentifier($con, 'comments', 'comment_id', $data['id'])['username'] === $_SESSION['user']);
        deleteByIdentifier($con, 'comments', 'comment_id', $data['id']);
    endDBConnection($con);
    echo 'Success';
}