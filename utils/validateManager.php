<?php
function validateEmail($email)
{
    return preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\._-])*([a-zA-Z0-9_-])@([a-zA-Z0-9])+(\.([a-zA-Z0-9])+)*$/', $email);
}

function validateName($name)
{
    return preg_match('/[a-zA-Z -_0-9]/', $name);
}

function validateBirthday($birthday)
{
    $birthday = explode('-', $birthday);
    if (count($birthday) !== 3)
        return false;
    return checkdate($birthday[1], $birthday[2], $birthday[0]);
}

function validateAge($birthday)
{
    return (intval(substr(date('Ymd') - date('Ymd', strtotime($birthday)), 0, -4))) > 18;
}

function validateUsername($name)
{
    return preg_match('/[a-zA-Z_0-9]/', $name);
}


function validateData($data, $type)
{
    if ($type === 'email')
        return constructErrorResponse(validateEmail($data), 'Invalid email format.');
    else if ($type === 'name')
        return constructErrorResponse(validateName($data), 'Only letters, numbers, dashes, underscores, and spaces can be used.');
    else if ($type === 'birthday') {
        if (!validateBirthday($data))
            return constructErrorResponse(false, 'Invalid date input.');
        return constructErrorResponse(validateAge($data), 'User must be 18 or above to sign-up.');
    } else if ($type === 'username')
        return constructErrorResponse(validateUsername($data), 'Invalid characters used.');
    else if ($type === 'password') {
        $msg = [];
        if (strlen($data) < 6)
            $msg[] = 'Password must be at least 6 characters long.';
        if (!preg_match('/[a-z]/', $data))
            $msg[] = 'Password must have a lowercase letter.';
        if (!preg_match('/[A-Z]/', $data))
            $msg[] = 'Password must have an uppercase letter.';
        if (!preg_match('/[0-9]/', $data))
            $msg[] = 'Password must have a digit.';
        if (!preg_match('/\W/', $data))
            $msg[] = 'Password must have a symbol.';
        return constructErrorResponse(!count($msg), $msg);
    }
}

function constructErrorResponse($state, $msg)
{
    return ['state' => $state, 'msg' => $state ? '' : $msg];
}

function printError($key)
{
    global $error;
    if (isset($error[$key])) {
        if (!is_array($error[$key])) {
            echo "<span class=\"error-msg\">$error[$key]</span>";
        } else {
            foreach ($error[$key] as $msg) {
                echo "<span class=\"error-msg\">$msg</span>";
            }
        }
        return true;
    } else {
        return false;
    }
}

function printPOSTVal($key)
{
    if (isset($_POST[$key]))
        echo $_POST[$key];
}

function cleanValue($value){
    return htmlspecialchars(strip_tags(trim($value)));
}

function validate($fields, &$error, $rawData, $specificiedError = false, &$userData = array()){
    foreach ($fields as $key) {
        if (!isset($rawData[$key]))
            $error[$key] = 'Missing form parameter.';
        else if (empty(trim($rawData[$key])))
            $error[$key] = 'Input required.';
        else if (strlen($rawData[$key]) > 50)
            $error[$key] = 'Max character limit is 50.';
        else if (cleanValue($rawData[$key]) !== trim($rawData[$key]))
            $error[$key] = "Illegal $key input.";
        else if($specificiedError){
            $userData[$key] = htmlspecialchars(strip_tags(trim($rawData[$key])));
            $validity = validateData($userData[$key], $key);
            if (!$validity['state'])
                $error[$key] = $validity['msg'];
        }
    }
}