<?php

/**
 * This file is for cPanel deployment ONLY.
 * On the server, rename this to index.php after uploading public/ contents to public_html/
 * and the rest of the app to ~/readiwork-laravel/
 *
 * Directory structure on server:
 *   /home/nnyrjfap/readiwork-laravel/   <- app files (everything except public/)
 *   /home/nnyrjfap/public_html/         <- contents of public/ (this file renamed to index.php)
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../readiwork-laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../readiwork-laravel/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../readiwork-laravel/bootstrap/app.php';

$app->handleRequest(Request::capture());
