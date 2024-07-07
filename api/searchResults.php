<?php
require '../utils/dbManager.php';
require '../utils/validateManager.php';

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);
if (isset($data['username'])) {
    $username = cleanValue($data['username']);
    if (strlen($username) === 0)
        exit();
    $con = startDBConnection();
    if (!isset($data['limit']))
        $result = getDataLikeIdentifier($con, 'users', 'username', $username, ['username', 'name', 'picture']);
    else {
        $limit = cleanValue($data['limit']);
        $result = getDataLikeIdentifier($con, 'users', 'username', $username, ['username', 'name', 'picture'], $limit);
    }
    endDBConnection($con);
    echo json_encode($result, JSON_PRETTY_PRINT);
}