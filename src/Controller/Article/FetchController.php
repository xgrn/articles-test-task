<?php

namespace App\Controller\Article;

use App\Exception\ArticleLoadingException;
use App\Services\ArticleFetchService;
use App\Services\ArticleLoader;
use App\Struct\Article;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Stream;
use Slim\Views\PhpRenderer;

class FetchController
{

    public function fetchAll(
        ResponseInterface $response,
        PhpRenderer $renderer,
        ArticleFetchService $fetchService
    ): ResponseInterface
    {
        return $renderer->render($response, 'articles.php', [
            'articles' => $fetchService->fetchAllTitles()
        ]);
    }

    public function fetchArticle(
        string $filename,
        ResponseInterface $response,
        ArticleLoader $loader
    ): ResponseInterface
    {
        try {
            $article = $loader->loadArticle($filename);
        } catch (ArticleLoadingException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
        $response->getBody()->write(json_encode([
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]));
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}