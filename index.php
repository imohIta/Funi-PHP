<?php
    define ("PATH", realpath(__DIR__));

    $parts = explode(DIRECTORY_SEPARATOR, PATH);
    define("PARENT_DIR", $parts[count($parts) - 1]);

    $location = 'http://' . $_SERVER['HTTP_HOST'] . '/' . PARENT_DIR . '/public/';
    header('Location:' . $location);
    exit();
