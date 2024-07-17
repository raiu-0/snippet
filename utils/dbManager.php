<?php
function startDBConnection()
{
    $host = 'localhost';
    $user = 'snippet';
    $pass = 'snippetUser@0605';
    $dbName = 'snippet';

    $con = new mysqli($host, $user, $pass, $dbName);
    if ($con->connect_errno) {
        echo "Database connection failure: " . $con->connect_error;
        exit();
    }
    return $con;
}

function endDBConnection($con)
{
    $con->close();
}
function rowExists($connection, $table, $field, $value)
{
    $check = $connection->prepare("SELECT $field FROM $table WHERE $field = ?");
    $check->bind_param('s', $value);
    $check->execute();
    $result = $check->get_result();
    $check->close();
    if (mysqli_num_rows($result) != 0)
        return true;
    return false;
}
function insertInto($connection, $table, ...$values)
{
    $insertion = $connection->prepare("INSERT INTO $table VALUES (" . join(', ', array_fill(0, count($values), '?')) . ')');
    $type = '';
    foreach ($values as $value) {
        switch (gettype($value)) {
            case 'integer':
                $type .= 'i';
                break;
            case 'double':
            case 'float':
                $type .= 'd';
                break;
            default:
                $type .= 's';
                break;
        }
    }
    $insertion->bind_param($type, ...$values);
    $insertion->execute();
    $insertion->close();
}

function getDataByIdentifier($connection, $table, $identifierName, $identifierValue, $multiple = false)
{
    $selection = $connection->prepare("SELECT * FROM $table WHERE $identifierName = ?");
    $selection->bind_param('s', $identifierValue);
    $selection->execute();
    $result = $multiple ? $selection->get_result()->fetch_all(MYSQLI_ASSOC) : $selection->get_result()->fetch_assoc();
    $selection->close();
    return $result;
}

function getDataLikeIdentifier($connection, $table, $identifierName, $identifierValue, $fields = ['*'], $limit = 0)
{
    $sql = "SELECT " . implode(', ', $fields) . " FROM $table WHERE $identifierName LIKE ?";
    if ($limit !== 0)
        $sql .= " LIMIT $limit";

    $identifierValue = '%' . $identifierValue . '%';
    $selection = $connection->prepare($sql);
    $selection->bind_param('s', $identifierValue);
    $selection->execute();
    $result = $selection->get_result()->fetch_all(MYSQLI_ASSOC);
    $selection->close();
    return $result;
}

function deleteByIdentifier($connection, $table, $identifierName, $identifierValue)
{
    $delete = $connection->prepare("DELETE FROM $table WHERE $identifierName = ?");
    $type = '';
    switch (gettype($identifierValue)) {
        case 'integer':
            $type = 'i';
            break;
        case 'double':
        case 'float':
            $type = 'd';
            break;
        default:
            $type = 's';
            break;
    }
    $delete->bind_param($type, $identifierValue);
    $delete->execute();
    $delete->close();
}

function checkIfFollowed($connection, $account, $followed)
{
    $check = $connection->prepare("SELECT * FROM follow WHERE account=? AND followed=?");
    $check->bind_param('ss', $account, $followed);
    $check->execute();
    $result = $check->get_result();
    $check->close();
    if (mysqli_num_rows($result) != 0)
        return true;
    return false;
}

function checkIfFollowBack($connection, $account, $followed){
    $check1 = $connection->prepare("SELECT * FROM follow WHERE account=? AND followed=?");
    $check1->bind_param('ss', $account, $followed);
    $check1->execute();
    $result1 = $check1->get_result()->fetch_assoc();
    $check1->close();
    if(!is_array($result1) || count($result1) === 0)
        return false;
    $check2 = $connection->prepare("SELECT * FROM follow WHERE account=? AND followed=?");
    $check2->bind_param('ss', $followed, $account);
    $check2->execute();
    $result2 = $check2->get_result()->fetch_assoc();
    $check2->close();
    if(strtotime($result1['datetime']) < strtotime($result2['datetime']))
        return true;
    return false;
}

function unfollow($connection, $account, $followed)
{
    $delete = $connection->prepare("DELETE FROM follow where account=? AND followed=?");
    $delete->bind_param('ss', $account, $followed);
    $delete->execute();
    $delete->close();
}

function increaseUserFollow($connection, $username, $field)
{
    $update = $connection->prepare("UPDATE users SET $field = $field + 1 WHERE username=?");
    $update->bind_param('s', $username);
    $update->execute();
    $update->close();
}

function decreaseUserFollow($connection, $username, $field)
{
    $update = $connection->prepare("UPDATE users SET $field = $field - 1 WHERE username=?");
    $update->bind_param('s', $username);
    $update->execute();
    $update->close();
}

function getPosts($connection, $usernames)
{
    $usernames = array_map('addQuotes', $usernames);
    $sql = "SELECT * FROM posts WHERE username IN (" . implode(', ', $usernames) . ") ORDER BY datetime DESC";
    $selection = $connection->prepare($sql);
    $selection->execute();
    $result = $selection->get_result()->fetch_all(MYSQLI_ASSOC);
    $selection->close();
    return $result;
}

function getPostByID($connection, $id)
{
    $selection = $connection->prepare("SELECT * FROM posts WHERE id = ?");
    $selection->bind_param('i', $id);
    $selection->execute();
    $result = $selection->get_result()->fetch_assoc();
    $selection->close();
    return $result;
}

function addQuotes($element)
{
    return "'" . $element . "'";
}

function updateProfile($connection, $username, $newData)
{
    foreach ($newData as $key => $value) {
        if ($key === 'username' || ($key === 'password' && strlen($value) === 0))
            continue;
        $update = $connection->prepare("UPDATE users SET $key = ? WHERE username = ?");
        $update->bind_param('ss', $value, $username);
        $update->execute();
        $update->close();
    }
    if ($username !== $newData['username']) {
        $update = $connection->prepare("UPDATE users SET username = ? WHERE username = ?");
        $update->bind_param('ss', $newData['username'], $username);
        $update->execute();
        $update->close();
    }
}

function getComments($connection, $post_id, $limit = 5)
{
    $selection = $connection->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY datetime DESC LIMIT $limit");
    $selection->bind_param('i', $post_id);
    $selection->execute();
    $result = $selection->get_result()->fetch_all(MYSQLI_ASSOC);
    $selection->close();
    return $result;
}

function checkIfLiked($connection, $post_id, $username)
{
    $check = $connection->prepare("SELECT * FROM likes WHERE post_id = ? AND username = ?");
    $check->bind_param('is', $post_id, $username);
    $check->execute();
    $result = $check->get_result();
    $check->close();
    if (mysqli_num_rows($result) != 0)
        return true;
    return false;
}

function likePost($connection, $post_id, $username, $datetime)
{
    $insertion = $connection->prepare("INSERT INTO likes VALUES (?, ?, ?)");
    $insertion->bind_param('iss', $post_id, $username, $datetime);
    $insertion->execute();
    $insertion->close();

    $update = $connection->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
    $update->bind_param('i', $post_id);
    $update->execute();
    $update->close();
}

function unlikePost($connection, $post_id, $username)
{
    $deletion = $connection->prepare("DELETE FROM likes WHERE post_id = ? AND username = ?");
    $deletion->bind_param('is', $post_id, $username);
    $deletion->execute();
    $deletion->close();

    $update = $connection->prepare("UPDATE posts SET like_count = like_count - 1 WHERE id = ?");
    $update->bind_param('i', $post_id);
    $update->execute();
    $update->close();
}

function getInteractions($connection, $username)
{
    $sql = "SELECT
    'Comment' AS type,
    post_id AS post_id,
    username AS username,
    DATETIME AS datetime,
    COMMENT AS content
FROM
    comments
WHERE
    post_id IN(
    SELECT
        id
    FROM
        posts
    WHERE
        username = ?
) AND username != ?
UNION
SELECT
    'Like' AS type,
    post_id AS post_id,
    username AS username,
    DATETIME AS datetime,
    NULL AS CONTENT
FROM
    likes
WHERE
    post_id IN(
    SELECT
        id
    FROM
        posts
    WHERE
        username = ?
) AND username != ?
UNION
SELECT
    'Follow' AS type,
    NULL AS post_id,
    ACCOUNT AS username,
    DATETIME AS datetime,
    NULL AS content
FROM
    follow
WHERE
    followed = ?
ORDER BY DATETIME
DESC";
    $select = $connection->prepare($sql);
    $select->bind_param('sssss', $username, $username, $username, $username, $username);
    $select->execute();
    $result = $select->get_result()->fetch_all(MYSQLI_ASSOC);
    $select->close();

    foreach ($result as &$row) {
        $interactionInfo = getDataByIdentifier($connection, 'users', 'username', $row['username']);
        $row['name'] = $interactionInfo['name'];
        $row['picture'] = $interactionInfo['picture'];
    }

    return $result;
}

function getFollowData($connection, $username, $mode)
{
    if ($mode === 'followers') {
        $first = 'account';
        $second = 'followed';
    } else if ($mode === 'following') {
        $first = 'followed';
        $second = 'account';
    } else
        return [];
    $select = $connection->prepare("SELECT $first FROM follow WHERE $second = ? ORDER BY datetime DESC");
    $select->bind_param('s', $username);
    $select->execute();
    $result = $select->get_result()->fetch_all(MYSQLI_ASSOC);
    $select->close();
    return $result;
}