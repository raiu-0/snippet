<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';
date_default_timezone_set('Asia/Hong_Kong');

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

if (!isset($data['username'])) {
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

    if ($referrer === '')
        exit();

    $stringParsed = parse_url($referrer, PHP_URL_QUERY);
    $accountParameters = [];
    if ($stringParsed)
        parse_str($stringParsed, $accountParameters);
    else
        exit();

    $followed = cleanValue($accountParameters['user']);
} else
    $followed = $data['username'];
$account = $_SESSION['user'];

$con = startDBConnection();
$isFollowed = checkIfFollowed($con, $account, $followed);
$msg = [];
if (!$isFollowed) {
    insertInto($con, 'follow', $account, $followed, date('Y-m-d H:i:s'));
    increaseUserFollow($con, $account, 'following');
    increaseUserFollow($con, $followed, 'followers');
    $followerCount = getDataByIdentifier($con, 'users', 'username', $followed)['followers'];
    $msg['buttonState'] = 'Followed';
} else {
    unfollow($con, $account, $followed);
    decreaseUserFollow($con, $account, 'following');
    decreaseUserFollow($con, $followed, 'followers');
    $msg['buttonState'] = checkIfFollowed($con, $followed, $account) ? 'Follow back' : 'Follow';
}
$follow = getDataByIdentifier($con, 'users', 'username', $followed);
$user = getDataByIdentifier($con, 'users', 'username', $account);
$msg['followerCount'] = $follow['followers'];
$msg['followingCount'] = $user['following'];
endDBConnection($con);

echo json_encode($msg, JSON_PRETTY_PRINT);
