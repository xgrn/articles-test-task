<?php

namespace App\Kernel;

use App\Controller\Article\FetchController;
use Slim\App;

class RouteAssigner
{

    public static function addRoutes(App $app): void
    {
        $app->get('/', [FetchController::class, 'fetch'])->setName('app_articles_fetch');
    }
}