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