<?php

namespace App\Tests;

use App\Kernel\ContainerFactory;
use App\Kernel\SlimFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\App;

class AbstractTestCase extends TestCase
{

    protected function createApp(array $mockedServices = []): App
    {
        $container = ContainerFactory::createContainer($mockedServices);
        return SlimFactory::createApp($container);
    }

    protected function createContainer(): ContainerInterface
    {
        return ContainerFactory::createContainer();
    }
}