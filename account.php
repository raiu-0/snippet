<!DOCTYPE html>
<html lang="en">

<?php
session_start();
if (count($_GET) !== 1) {
    exit('Invalid parameters.');
}

require 'utils/dbManager.php';
$con = startDBConnection();
$userData = getDataByIdentifier($con, 'users', 'username', $_GET['user']);
date_default_timezone_set('Asia/Hong_Kong');
if (is_null($userData) || count($userData) === 0)
    exit('Invalid username.');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>snippet: <?php echo $userData['username']; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/account.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/icons/snippet-icon.png">
</head>

<body>
    <?php require 'utils/menuPanel.php'; ?>
    <div class="content-panel flex-col">
        <div class="profile-frame flex-row">
            <div class="profile-picture-frame flex-col">
                <img src="uploads/<?php echo $userData['picture'] ?>" alt="" id="profile-picture">
            </div>
            <div class="main-info flex-col">
                <div id="name"><?php echo $userData['name']; ?></div>
                <div id="username">@<?php echo $userData['username']; ?></div>
                <div id="birthday" class="flex-row">
                    <img src="images/icons/calendar-icon.png" class="icon">
                    Born <?php echo $userData['birthday']; ?>
                </div>
                <div class="stats flex-row">
                    <div id="followers">Followers: <?php echo $userData['followers']; ?></div>
                    <div id="following"
                        onclick="followingCountClick(<?php echo $_SESSION['user'] === $userData['username']; ?>)">
                        Following: <?php echo $userData['following']; ?></div>
                </div>
            </div>
            <div class="interact flex-col">
                <?php if ($userData['username'] === $_SESSION['user']): ?>
                    <a href="editProfile.php"><button id="interact">Edit Profile</button></a>
                <?php else:
                    $followedState = checkIfFollowed($con, $_SESSION['user'], $userData['username']);
                    ?>
                    <button id="interact" class="<?php echo $followedState ? 'followed' : 'not-followed'; ?>"
                        onmouseover="followHover(this)" onmouseleave="followNoHover(this)">
                        <?php echo $followedState ? 'Followed' : (checkIfFollowed($con, $userData['username'], $_SESSION['user']) ? 'Follow back' : 'Follow'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div id="post-area" class="post-area flex-col">
        </div>
    </div>
    <?php require 'utils/searchPanel.php' ?>
</body>
<?php endDBConnection($con); ?>
<script src="scripts/interact-script.js"></script>
<script src="scripts/posts-script.js"></script>
<script>getPosts(<?php echo '"' . $_SESSION['user'] . '"' ?>, <?php echo '"' . $userData['username'] . '"' ?>);</script>

</html>