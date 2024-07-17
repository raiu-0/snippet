<!DOCTYPE html>
<html lang="en">

<?php
require 'utils/dateManager.php';
require 'utils/validateManager.php';
require 'utils/dbManager.php';

function extractFiles($element)
{
    return $element['filename'];
}

session_start();
date_default_timezone_set('Asia/Hong_Kong');
if (count($_GET) !== 1 || !isset($_GET['id']))
    exit('Invalid parameters.');

$con = startDBConnection();
$post = getPostByID($con, cleanValue($_GET['id']));
if (is_null($post) || count($post) === 0)
    exit('Invalid post ID.');


$postFiles = getDataByIdentifier($con, 'post_files', 'post_id', $post['id'], true);
$post['postFiles'] = array_map('extractFiles', $postFiles);
$post['datetime'] = getTimePassed($post['datetime']);

$acctData = getDataByIdentifier($con, 'users', 'username', $post['username']);

$post['profilePicture'] = $acctData['picture'];
$post['name'] = $acctData['name'];
$post['requesterPicture'] = $_SESSION['picture'];
$post['hasComments'] = count(getComments($con, $post['id'], 1)) > 0 ? true : false;
$post['liked'] = checkIfLiked($con, $post['id'], $_SESSION['user']);
endDBConnection($con);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>snippet</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/post.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/icons/snippet-icon.png">
</head>

<body>
    <?php require 'utils/menuPanel.php'; ?>
    <div class="content-panel flex-col">
        <div id="post-area" class="post-area flex-col">

        </div>
    </div>
    <?php require 'utils/searchPanel.php' ?>
</body>

<script src="scripts/posts-script.js"></script>
<script>
    document.getElementById('post-area').innerHTML = constructPost(<?php echo "'" . json_encode($post) . "', '" . $_SESSION['user'] . "'" . ', false, false'; ?>);
    document.getElementsByClassName('delete-post-btn')[0].addEventListener('click', () => {
        window.location.replace('home.php');
    })
</script>

</html>