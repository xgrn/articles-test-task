<?php

namespace App\Kernel;


use App\Services\LoggerService;
use DI\Bridge\Slim\Bridge;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;

class SlimFactory
{

    public static function createApp(ContainerInterface $container = null): App
    {
        if($container === null) $container = ContainerFactory::createContainer();
        $app = Bridge::create($container);
        RouteAssigner::addRoutes($app);
        self::addErrorMiddleware($app);
        return $app;
    }

    private static function addErrorMiddleware(App $app): void
    {
        $handler = function (
            RequestInterface $request,
            \Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails,
            ?LoggerInterface $logger = null
        ) use ($app) {
            if ($logger) $logger->error($exception->getMessage());

            /** @var LoggerService $fileLogger */
            $fileLogger = $app->getContainer()->get(LoggerService::class);
            $fileLogger->logError($exception);
            $response = $app->getResponseFactory()->createResponse(500, 'Internal Server Error');
            $response->getBody()->write('Internal server error.');
            return $response;
        };
        $mw = $app->addErrorMiddleware(true, true, true);
        $mw->setDefaultErrorHandler($handler);
    }
}