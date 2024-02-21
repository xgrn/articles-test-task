<?php

include __DIR__.'/../vendor/autoload.php';

use App\Kernel\SlimFactory;

$app = SlimFactory::createApp();

$app->run();