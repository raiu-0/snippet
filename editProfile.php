<!DOCTYPE html>
<html lang="en">

<?php
session_start();
require 'utils/validateManager.php';
require 'utils/dbManager.php';
require 'utils/fileManager.php';

$con = startDBConnection();
$initialData = getDataByIdentifier($con, 'users', 'username', $_SESSION['user']);

$fields = ['email', 'name', 'username', 'password', 'birthday'];
if (isset($_POST['save'])) {

    $uploadedFile = uploadFiles($_FILES['picture'], acceptedImageFormats);

    $error = [];
    $userData = [];
    if (strlen($password) === 0)
        validate($fields, $error, $_POST, true, $userData, ['password']);
    else
        validate($fields, $error, $_POST, true, $userData);
    if (count($error) === 0) {
        if (strlen($userData['password']) > 0)
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        if ($userData['username'] !== $_SESSION['user'] && rowExists($con, 'users', 'username', $userData['username']))
            $error['username'] = 'Username already taken.';

        if ($initialData['email'] !== $userData['email'] && rowExists($con, 'users', 'email', $userData['email']))
            $error['email'] = 'Email already in use.';

        if (count($uploadedFile) === 1) {
            $userData['picture'] = $uploadedFile[0];
            removeFile($initialData['picture']);
        }
        if (count($error) === 0) {
            updateProfile($con, $_SESSION['user'], $userData);
            $_SESSION['user'] = $userData['username'];
            $_SESSION['name'] = $userData['name'];
            if (isset($userData['picture']))
                $_SESSION['picture'] = $userData['picture'];
            header('location: editProfile.php');
        }
    }
}

endDBConnection($con);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Edit</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/editProfile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php require 'utils/menuPanel.php'; ?>
    <div id="account-edit" class="content-panel flex-col">
        <h1>Account Settings</h1>
        <form method="post" id="edit-card" enctype="multipart/form-data" class="flex-row">
            <div id="profile-icon">
                <img id="pfp" src="uploads/<?php echo $_SESSION['picture'] ?>">
                <input type="file" id="profile-picture" name="picture" class="button-link" accept="image/*">
            </div>

            <div id="change-details-frame" class="flex-col">
                <label for="email">Email</label>
                <input type="text" name="email" placeholder="Email" value="<?php echo $initialData['email'] ?>">
                <?php printError('email') ?>
                <label for="name">Name</label>
                <input type="text" name="name" placeholder="Name" value="<?php echo $initialData['name'] ?>">
                <?php printError('name') ?>
                <label for="birthday">Birthday</label>
                <input type="date" name="birthday" id="birthday" placeholder="Birthday"
                    onchange="showBirthdayPlaceholder(this);" value="<?php echo $initialData['birthday'] ?>">
                <?php printError('birthday') ?>
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Username"
                    value="<?php echo $initialData['username'] ?>">
                <?php if (!printError('username')): ?>
                    <span class="input-guide">Usernames may only contain letters, numbers and underscores.</span>
                <?php endif; ?>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password" value="">
                <?php if (!printError('password')): ?>
                    <span class="input-guide">
                        Passwords should contain at least have a length of 6, contain at least one lowercase letter, one
                        uppercase letter, one digit, and one symbol.
                    </span>
                <?php endif; ?>

                <button type="submit" id="save-changes-button" name="save" value="save">Save Changes</button>
            </div>
        </form>
    </div>
    <?php require 'utils/searchPanel.php' ?>
</body>
<script>
    function showBirthdayPlaceholder(e) {
        e.value != '' ? e.classList.add('has-value') : e.classList.remove('has-value');
    }
    showBirthdayPlaceholder(document.getElementById('birthday'));
</script>
<script src="scripts/update-pfp.js"></script>

</html>