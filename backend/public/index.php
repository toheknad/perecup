<?php

use App\Kernel;

//require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

//return function (array $context) {
//    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
//};
file_put_contents('post.txt',  $_POST);
file_put_contents('request.txt',  $_REQUEST);
file_put_contents('global.txt',  $_GET);
error_log("You messed up!", 3, "/var/tmp/my-errors.log");
//Redirect permanent /payment/yookassa/webhook http://158.160.9.226:8080/payment/yookassa/webhook