<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';
date_default_timezone_set('Asia/Hong_Kong');

$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if ($referrer === '')
    exit();

$stringParsed = parse_url($referrer, PHP_URL_QUERY);
$accountParameters = [];
if ($stringParsed)
    parse_str($stringParsed, $accountParameters);
else
    exit();

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

if(isset($data['mode']) && in_array($data['mode'], ['followers', 'following'])){
    $target = cleanValue($accountParameters['user']);
    
    $con = startDBConnection();
    $followData = getFollowData($con, $target, $data['mode']);
    foreach($followData as &$unit){
        $temp = $unit['followed'] ?? $unit['account'];
        $followUserData = getDataByIdentifier($con, 'users', 'username', $temp, false);
        $unit['picture'] = $followUserData['picture'];
        $unit['name'] = $followUserData['name'];
        $unit['followed'] = $_SESSION['user'] === $temp ? 'self' : checkIfFollowed($con, $_SESSION['user'], $temp);
        $unit['username'] = $temp;
    }
    endDBConnection($con);
    echo json_encode($followData, JSON_PRETTY_PRINT);
}
