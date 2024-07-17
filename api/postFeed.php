<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';
require '../utils/dateManager.php';

date_default_timezone_set('Asia/Hong_Kong');

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

function extractFiles($element){
    return $element['filename'];
}

if (!isset($_SESSION['user']))
    exit();

$con = startDBConnection();

if (!isset($data['username'])) {
    $username = $_SESSION['user'];
    $followingData = getDataByIdentifier($con, 'follow', 'account', $username, true);
    $followingList = [];
    foreach ($followingData as $follow)
        $followingList[] = $follow['followed'];
    $followingList[] = $username;
} else
    $followingList = [$data['username']];

$posts = getPosts($con, $followingList);

foreach ($posts as &$post) {
    $postFiles = getDataByIdentifier($con, 'post_files', 'post_id', $post['id'], true);
    $post['postFiles'] = array_map('extractFiles', $postFiles);
    $post['datetime'] = getTimePassed($post['datetime']);

    $acctData = getDataByIdentifier($con, 'users', 'username', $post['username']);

    $post['profilePicture'] = $acctData['picture'];
    $post['name'] = $acctData['name'];
    $post['requesterPicture'] = $_SESSION['picture'];
    $post['hasComments'] = count(getComments($con, $post['id'], 1)) > 0 ? true : false;
    $post['liked'] = checkIfLiked($con, $post['id'], $_SESSION['user']);
}

endDBConnection($con);

echo json_encode($posts, JSON_PRETTY_PRINT);