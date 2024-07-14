<div class="menu-panel flex-col">
    <div class="account-display flex-row">
        <img src="uploads/<?php echo $_SESSION['picture'] ?>" alt="" class="profile-icon">
        <div id="user-display" class="flex-col">
            <div id="user-name-display"><?php echo $_SESSION['name'] ?></div>
            <div id="user-username-display">@<?php echo $_SESSION['user'] ?></div>
        </div>
        <a href="account.php?user=<?php echo $_SESSION['user'] ?>"><span class="button-link"></span></a>
    </div>
    <hr>
    <div class="menu-option flex-row">
        <img src="images/icons/home-icon.png" alt="" class="icon-with-label">
        Home
        <a href="home.php"><span class="button-link"></span></a>
    </div>
    <div class="menu-option flex-row">
        <img src="images/icons/interaction-icon.png" alt="" class="icon-with-label">
        Interactions
        <a href="interactions.php"><span class="button-link"></span></a>
    </div>
    <div class="menu-option flex-row">
        <img src="images/icons/settings-icon.png" alt="" class="icon-with-label">
        Settings
        <a href="home.php"><span class="button-link"></span></a>
    </div>
    <div class="menu-option flex-row">
        <img src="images/icons/account-icon.png" alt="" class="icon-with-label">
        Account
        <a href="editProfile.php"><span class="button-link"></span></a>
    </div>
    <form class="menu-option flex-row" method="post" action="utils/menuPanel.php">
        <img src="images/icons/logout-icon.png" alt="" class="icon-with-label">
        Logout
        <a href="logout.php"><span class="button-link"></span></a>
    </form>
</div>