<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

function extractFiles($element){
    return $element['filename'];
}

$con = startDBConnection();



$username = $_SESSION['user'];
$followingData = getDataByIdentifier($con, 'follow', 'account', $username, true);
$followingList = [];
foreach($followingData as $follow)
    $followingList[] = $follow['followed'];
$followingList[] = $username;

if(isset($data['username']))
    $followingList = [$data['username']];

$posts = getPosts($con, $followingList);

foreach($posts as &$post){
    $postFiles = getDataByIdentifier($con, 'post_files', 'post_id', $post['id'], true); 
    $post['postFiles'] = array_map('extractFiles', $postFiles);
    $post['datetime'] = date('M j, Y - g:iA', strtotime($post['datetime']));
    
    $acctData = getDataByIdentifier($con, 'users', 'username', $post['username']);

    $post['profilePicture'] = $acctData['picture'];
    $post['name'] = $acctData['name'];
    $post['requesterPicture'] = $_SESSION['picture'];
}

endDBConnection($con);

echo json_encode($posts, JSON_PRETTY_PRINT);