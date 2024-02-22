<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Services\ArticleFetchService;
use App\Services\ArticleLoader;
use App\Struct\Article;
use App\Tests\AbstractTestCase;

class ArticleFetchServiceTest extends AbstractTestCase
{

    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE_1;
        if(file_exists($path)) unlink($path);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE_2;
        if(file_exists($path)) unlink($path);
    }

    const ARTICLE_1 = 'test-article-1.md';
    const ARTICLE_2 = 'test_article-2.md';

    public function testFetchAllTitles()
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE_1,
            <<<string
---
title: "Test Article 1"
---

Test article 1 content
string
        );

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE_2,
            <<<string
---
title: "Test Article 2"
---

Test article 2 content
string
        );

        /** @var ArticleFetchService $fetcher */
        $fetcher = $container->get(ArticleFetchService::class);

        $articles = array_map(function (Article $article) {
            return [
                'title' => $article->getTitle(),
                'content' => $article->getContent()
            ];
        }, $fetcher->fetchAllTitles());

        self::assertContains(['title' => 'Test Article 1', 'content' => ''], $articles);
        self::assertContains(['title' => 'Test Article 2', 'content' => ''], $articles);
    }
}
