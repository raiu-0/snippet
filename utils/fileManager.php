<?php

const acceptedMediaFormats = [
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

const acceptedImageFormats = [
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
    'webp'
];

const acceptedVideoFormats = [
    'avi',
    'm2p',
    'm4v',
    'mov',
    'mp4',
    'mpg',
    'ts'
];

const FILEMAXSIZE = 50 * 1024 * 1024;
function getFileExtension($name)
{
    return strtolower(pathinfo($name, PATHINFO_EXTENSION));
}
function appendToFilename($name, $toAppend)
{
    $arr = explode('.', $name);
    $arr[0] .= $toAppend;
    return implode('.', $arr);
}

function uploadFiles($fileArray, $formats = acceptedMediaFormats, $goback=''){
    $postFiles = [];
    if (is_array($fileArray['name'])) {
        foreach ($fileArray['name'] as $index => $fileName) {
            if($fileArray['size'][$index] > FILEMAXSIZE || strlen($fileName) > 255)
                continue;
            if (in_array(getFileExtension($fileName), $formats)) {
                if (file_exists("uploads/$fileName")) {
                    $i = 0;
                    while (file_exists('uploads/' . appendToFilename($fileName, $i)))
                        $i++;
                    $fileName = appendToFilename($fileName, $i);
                }
                move_uploaded_file($fileArray['tmp_name'][$index], $goback."uploads/$fileName");
                $postFiles[] = $fileName;
            }
        }
    } else {
        $fileName = $fileArray['name'];
        if($fileArray['size'] > FILEMAXSIZE || strlen($fileName) > 255)
            return;
        if (in_array(getFileExtension($fileName), $formats)) {
            if (file_exists("uploads/$fileName")) {
                $i = 0;
                while (file_exists('uploads/' . appendToFilename($fileName, $i)))
                    $i++;
                $fileName = appendToFilename($fileName, $i);
            }
            move_uploaded_file($fileArray['tmp_name'], $goback."uploads/$fileName");
            $postFiles[] = $fileName;
        }
    }
    return $postFiles;
}

function removeFile($filename){
    if($filename === 'defaultPFP.png')
        return;
    if(file_exists("uploads/$filename"))
        unlink("uploads/$filename");
}