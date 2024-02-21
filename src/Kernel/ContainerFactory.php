<?php

namespace App\Kernel;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;
use \DI;

class ContainerFactory
{

    public static function createContainer(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder
            ->addDefinitions(self::createDefinitions());
        return $builder->build();
    }

    private static function createDefinitions(): array
    {
        return [
            PhpRenderer::class => DI\factory(function () {
                return new PhpRenderer(__DIR__.'/../../templates');
            })
        ];
    }
}