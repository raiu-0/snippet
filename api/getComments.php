<?php
session_start();
require '../utils/dbManager.php';
require '../utils/validateManager.php';

$post_input = file_get_contents('php://input');
$data = json_decode($post_input, true);

if(isset($data['id'], $data['limit']) && $data['limit'] > 0){
    $con = startDBConnection();
    $comments = getComments($con, $data['id'], $data['limit']);
    foreach($comments as &$comment){
        $commentAuthorData = getDataByIdentifier($con, 'users', 'username', $comment['username']);
        $comment['picture'] = $commentAuthorData['picture'];
        $comment['name'] = $commentAuthorData['name'];
        $comment['datetime'] = date('M j, Y - g:iA', strtotime($comment['datetime']));
    }
    endDBConnection($con);
    
    echo json_encode($comments, JSON_PRETTY_PRINT);
}