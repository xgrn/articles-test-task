<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Exception\ArticleLoadingException;
use App\Services\ArticleLoader;
use App\Tests\AbstractTestCase;

class ArticleLoaderTest extends AbstractTestCase
{

    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE_WITH_TITLE;
        if(file_exists($path)) unlink($path);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE_WITHOUT_TITLE;
        if(file_exists($path)) unlink($path);

        $path = $params->getDataDir().'/articles/'.self::EVIL_FILE;
        if(file_exists($path)) unlink($path);
    }

    const ARTICLE_WITH_TITLE = 'test-article-with-title.md';
    const ARTICLE_WITHOUT_TITLE = 'test-article-without-title.md';

    const EVIL_FILE = 'evil-file.sh';

    public function testLoadArticle()
    {
        $container = $this->createContainer();

        /** @var Params $params */
        $params = $container->get(Params::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE_WITH_TITLE,
            <<<string
Test article content
string
        );

        /** @var ArticleLoader $loader */
        $loader = $container->get(ArticleLoader::class);
        $article = $loader->loadArticle(self::ARTICLE_WITH_TITLE);

        self::assertEquals('Test Article With Title', $article->getTitle());
        self::assertEquals('Test article content', trim($article->getContent()));
    }

    public function testLoadTitle()
    {
        $container = $this->createContainer();

        /** @var Params $params */
        $params = $container->get(Params::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE_WITH_TITLE,
            <<<string
---
title: "Test YAML Title"
---

Test article content
string
        );

        /** @var ArticleLoader $loader */
        $loader = $container->get(ArticleLoader::class);

        $article = $loader->loadTitle(self::ARTICLE_WITH_TITLE);
        self::assertEquals('Test YAML Title', $article->getTitle());

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE_WITHOUT_TITLE,
            <<<string
Test article content
string

        );

        $article = $loader->loadTitle(self::ARTICLE_WITHOUT_TITLE);
        self::assertEquals('Test Article Without Title', $article->getTitle());
    }

    public function testNoFile()
    {
        $container = $this->createContainer();
        /** @var ArticleLoader $loader */
        $loader = $container->get(ArticleLoader::class);

        $this->expectException(ArticleLoadingException::class);
        $loader->loadTitle('this-file-does-not-exists.md');
    }

    public function testNoMarkdown()
    {
        $container = $this->createContainer();

        /** @var Params $params */
        $params = $container->get(Params::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::EVIL_FILE,
            <<<string
sudo evil_script
string

        );

        /** @var ArticleLoader $loader */
        $loader = $container->get(ArticleLoader::class);
        $this->expectException(ArticleLoadingException::class);
        $loader->loadArticle(self::EVIL_FILE);
    }
}
