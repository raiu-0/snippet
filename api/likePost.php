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
    $data['id'] = cleanValue($data['id']);
    $liked = checkIfLiked($con, $data['id'], $_SESSION['user']);
    $response = [];
    if(!$liked){
        likePost($con, $data['id'], $_SESSION['user'], date('Y-m-d H:i:s'));
        $response['status'] = 'liked';
    } else {
        unlikePost($con, $data['id'], $_SESSION['user']);
        $response['status'] = 'not liked';
    }
    endDBConnection($con);
    echo json_encode($response, JSON_PRETTY_PRINT);
}