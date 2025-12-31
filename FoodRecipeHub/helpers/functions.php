<?php

function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}

function d($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}
// echo "hello";
function base_path($path)
{
    return BASE_PATH . $path;
}

function require_view($path, $attributes = [])
{
    extract($attributes);
    d($path);
    header("location:views/index.view.php");
}

function require_svg($path, $attributes = [])
{
    extract($attributes);
    require "resources/svg/{$path}";
}

function redirect(string $path, int|null $afterInSeconds = null)
{
    if ($afterInSeconds)
        header("Refresh: {$afterInSeconds}; URL={$path}");
    else {
        header("Location: {$path}");
        exit();
    }
}

