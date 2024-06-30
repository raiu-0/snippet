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

function validateUsername($name)
{
    return preg_match('/[a-zA-Z_0-9]/', $name);
}


function validateData($data, $type)
{
    if ($type === 'email') {
        $validity = validateEmail($data);
        return ['state' => $validity, 'msg' => $validity ? '' : 'Invalid email format.'];
    } else if ($type === 'name') {
        $validity = validateName($data);
        return ['state' => $validity, 'msg' => $validity ? '' : 'Only letters, numbers, dashes, underscores, and spaces can be used.'];
    } else if($type === 'birthday'){
        $validity = validateBirthday($data);
        if($validity === false)
            return ['state' => false, 'msg' => 'Invalid date input.'];
        $validity = (intval(substr(date('Ymd') - date('Ymd', strtotime($data)), 0, -4))) < 18;
        return ['state' => $validity, 'msg' => $validity ? '' : 'User must be 18 or above to sign-up.'];
    } else if ($type === 'username') {
        $validity = validateUsername($data);
        return ['state' => $validity, 'msg' => $validity ? '' : 'Invalid characters used.'];
    } else if ($type === 'password') {
        $msg = [];
        if(strlen($data) < 6)
            $msg[] = 'Password must be at least 6 characters long.';
        if(!preg_match('/[a-z]/', $data))
            $msg[] = 'Password must have a lowercase letter.';
        if(!preg_match('/[A-Z]/', $data))
            $msg[] = 'Password must have an uppercase letter.';
        if(!preg_match('/[0-9]/', $data))
            $msg[] = 'Password must have a digit.';
        if(!preg_match('/\W/', $data))
            $msg[] = 'Password must have a symbol.';
        if(count($msg))
            return ['state' => false, 'msg' => $msg];
        return ['state' => true, 'msg' => ''];
    }
}