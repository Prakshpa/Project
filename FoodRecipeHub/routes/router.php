<?php

$request = $_SERVER['REQUEST_URI'];
//dd($_SERVER);
switch (strToLower($request)) {
    case '/ecommerce/':
        require_view('index.view.php');
        break;
    case '/':
    }