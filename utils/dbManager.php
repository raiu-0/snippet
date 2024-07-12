<?php
function startDBConnection()
{
    $host = 'localhost';
    $user = 'root';
    $pass = '';
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

function getPosts($connection, $usernames, $limit = 10)
{
    $usernames = array_map('addQuotes', $usernames);
    $selection = $connection->prepare("SELECT * FROM posts WHERE username IN (" . implode(', ', $usernames) . ") ORDER BY datetime DESC LIMIT $limit");
    $selection->execute();
    $result = $selection->get_result()->fetch_all(MYSQLI_ASSOC);
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
    if($username !== $newData['username']){
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