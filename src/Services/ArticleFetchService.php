<?php

namespace App\Services;

use App\Config\Params;
use App\Struct\Article;

class ArticleFetchService
{

    public function __construct(
        private readonly Params $params,
        private readonly ArticleLoader $loader
    )
    {
    }

    /**
     * @return Article[]
     */
    public function fetchAllTitles(bool $withContent = false): array
    {
        $files = glob($this->params->getDataDir().ArticleLoader::ARTICLE_PATH.'/*.md');
        return array_map(function (string $file) use($withContent) {
            return $withContent ? $this->loader->loadArticle($file) : $this->loader->loadTitle(basename($file));
        }, $files);
    }
}