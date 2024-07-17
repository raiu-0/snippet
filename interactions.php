<?php
session_start();
date_default_timezone_set('Asia/Hong_Kong');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>snippet</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/interaction.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/icons/snippet-icon.png">
</head>

<body>
    <?php require 'utils/menuPanel.php'; ?>
    <div class="content-panel flex-col">
        <?php
        require 'utils/dbManager.php';
        require 'utils/dateManager.php';
        $con = startDBConnection();
        $result = getInteractions($con, $_SESSION['user']);
        if (count($result) > 0):
            foreach ($result as $interaction): ?>
                <div class="interaction flex-row">
                    <div class="interaction-profile-picture">
                        <img src="uploads/<?php echo $interaction['picture']; ?>" class="interaction-picture">
                    </div>
                    <div class="interaction-info flex-col">
                        <div class="interaction-identity flex-row">
                            <div class="interaction-username">@<?php echo $interaction['username']; ?></div>
                        </div>
                        <div class="interaction-content">
                            <?php
                            $content = '';
                            $hyperlink = '';
                            if ($interaction['type'] === 'Comment') {
                                $content = '<b>' . $interaction['name'] . '</b> commented on your post.';
                                $content .= '<div class="interaction-comment">"' . $interaction['content'] . '"</div>';
                                $hyperlink = 'post.php?id=' . $interaction['post_id'];
                            } else if ($interaction['type'] === 'Like') {
                                $content = '<b>' . $interaction['name'] . '</b> liked your post.';
                                $hyperlink = 'post.php?id=' . $interaction['post_id'];
                            } else if ($interaction['type'] === 'Follow') {
                                if (checkIfFollowBack($con, $_SESSION['user'], $interaction['username']))
                                    $content = '<b>' . $interaction['name'] . '</b> followed you back.';
                                else
                                    $content = '<b>' . $interaction['name'] . '</b> followed you.';
                                $hyperlink = 'account.php?user=' . $interaction['username'];
                            }
                            echo $content;
                            ?>
                            <div class="interaction-datetime"><?php echo getTimePassed($interaction['datetime']); ?></div>
                        </div>
                    </div>
                    <a href="<?php echo $hyperlink ?>"><span class="button-link"></span></a>
                </div>
            <?php endforeach;
        else: ?>
            <div class="empty-results flex-col">
                <img src="images/icons/sad-icon.png">
                No interactions yet...
            </div>
            <?php
        endif;
        endDBConnection($con);
        ?>
    </div>
    <?php require 'utils/searchPanel.php' ?>
</body>

</html>