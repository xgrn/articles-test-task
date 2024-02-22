<?php

namespace App\Services;

use App\Config\Params;
use App\Exception\ArticleUpdateException;

class ArticleUpdateService
{

    public function __construct(
        private readonly Params $params,
        private readonly ArticleDiffService $diffService,
        private readonly LoggerService $loggerService
    )
    {
    }

    public function update(string $filename, string $newContent): void
    {
        // If the article doesn't exist an exception will be thrown here from the ArticleLoader
        $diff = $this->diffService->calcDiff($filename, $newContent);
        $this->loggerService->logChanges($filename, $diff);
        $this->saveArticle($filename, $newContent);
    }

    private function saveArticle(string $filename, string $newContent): void
    {
        $filename = basename($filename);
        $path = $this->params->getDataDir().ArticleLoader::ARTICLE_PATH.'/'.$filename;

        // Despite the exception on article loading I prefer double check as the code may be changed
        if(!file_exists($path)) throw new ArticleUpdateException(
            sprintf('Article \'%s\' doesn\'t exist', $filename)
        );

        $tmp = $this->params->getDataDir().ArticleLoader::ARTICLE_PATH.'/'.uniqid().'.md';
        file_put_contents($tmp, $newContent);
        rename($tmp, $path);
    }
}