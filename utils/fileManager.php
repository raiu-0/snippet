<?php

$acceptedFormats = [
    'apng',
    'avif',
    'gif',
    'jpg',
    'pjp',
    'jfif',
    'jpeg',
    'pjpeg',
    'png',
    'svg',
    'webp',
    'avi',
    'm2p',
    'm4v',
    'mov',
    'mp4',
    'mpg',
    'ts'
];
function getFileExtension($name) {
    $n = strrpos($name, '.');
    return ($n === false) ? '' : substr($name, $n+1);
}
function appendToFilename($name, $toAppend) {
    $arr = explode('.', $name);
    $arr[0] .= $toAppend;
    return implode('.', $arr);
}