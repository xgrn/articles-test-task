<?php

namespace App\Kernel;

use App\Controller\Article\FetchController;
use App\Controller\Article\UpdateController;
use Slim\App;

class RouteAssigner
{

    public static function addRoutes(App $app): void
    {
        $app->get('/', [FetchController::class, 'fetchAll'])->setName('app_articles_fetch_all');
        $app->get('/article/{filename}', [FetchController::class, 'fetchArticle'])->setName('app_article_fetch');
        $app->put('/article/{filename}', [UpdateController::class, 'update'])->setName('app_article_update');
    }
}