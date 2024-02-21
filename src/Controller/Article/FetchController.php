<?php

namespace App\Controller\Article;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

class FetchController
{

    public function fetch(ResponseInterface $response, PhpRenderer $renderer): ResponseInterface
    {
        return $renderer->render($response, 'index.php', [
            'articles' => [
                ['title' => 'Title 1', 'filename' => 'title_1']
            ]
        ]);
    }
}