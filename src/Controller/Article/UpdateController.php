<?php

namespace App\Controller\Article;

use App\Exception\ArticleLoadingException;
use App\Exception\ArticleUpdateException;
use App\Services\ArticleUpdateService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UpdateController
{

    public function update(
        string $filename,
        ResponseInterface $response,
        RequestInterface $request,
        ArticleUpdateService $updateService
    )
    {
        try {
            $updateService->update($filename, $request->getBody()->getContents());
        } catch (ArticleLoadingException|ArticleUpdateException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}