<?php

namespace App\Kernel;


use DI\Bridge\Slim\Bridge;
use Slim\App;

class SlimFactory
{

    public static function createApp(): App
    {
        $container = ContainerFactory::createContainer();
        $app = Bridge::create($container);
        RouteAssigner::addRoutes($app);
        return $app;
    }
}