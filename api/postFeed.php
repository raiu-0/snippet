<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';

function extractFiles($element){
    return $element['filename'];
}

$username = $_SESSION['user'];

$con = startDBConnection();

$followingData = getDataByIdentifier($con, 'follow', 'account', $username, true);
$followingList = [];
foreach($followingData as $follow)
    $followingList[] = $follow['followed'];
$followingList[] = $username;

$posts = getPosts($con, $followingList);

foreach($posts as &$post){
    $postFiles = getDataByIdentifier($con, 'post_files', 'post_id', $post['id'], true); 
    $post['postFiles'] = array_map('extractFiles', $postFiles);
    $post['datetime'] = date('F j, Y - g:i A', strtotime($post['datetime']));
}

endDBConnection($con);

echo json_encode($posts, JSON_PRETTY_PRINT);