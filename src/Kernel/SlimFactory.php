<?php

namespace App\Kernel;


use DI\Bridge\Slim\Bridge;
use Psr\Container\ContainerInterface;
use Slim\App;

class SlimFactory
{

    public static function createApp(ContainerInterface $container = null): App
    {
        if($container === null) $container = ContainerFactory::createContainer();
        $app = Bridge::create($container);
        RouteAssigner::addRoutes($app);
        return $app;
    }
}