<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Exception\ArticleLoadingException;
use App\Services\ArticleUpdateService;
use App\Tests\AbstractTestCase;

class ArticleUpdateServiceTest extends AbstractTestCase
{

    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE;
        if(file_exists($path)) unlink($path);

        $path = $params->getDataDir().'/articles/'.self::EVIL_SCRIPT;
        if(file_exists($path)) unlink($path);
    }

    const ARTICLE = 'test-article.md';
    const EVIL_SCRIPT = 'evil-script.sh';

    public function testUpdate()
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);
        /** @var ArticleUpdateService $updater */
        $updater = $container->get(ArticleUpdateService::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE;
        file_put_contents(
            $path,
            <<<string
Test article content
string
        );

        $updater->update(self::ARTICLE, <<<string
New test article content
string
);
        $content = file_get_contents($path);
        self::assertEquals('New test article content', $content);
    }

    public function testNoArticle()
    {
        $container = $this->createContainer();
        /** @var ArticleUpdateService $updater */
        $updater = $container->get(ArticleUpdateService::class);

        $this->expectException(ArticleLoadingException::class);
        $updater->update('this-article-does-not-exist.md', 'Some content');
    }

    public function testNotMarkdown()
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);
        /** @var ArticleUpdateService $updater */
        $updater = $container->get(ArticleUpdateService::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::EVIL_SCRIPT,
            'do evil'
        );

        $this->expectException(ArticleLoadingException::class);
        $updater->update(self::EVIL_SCRIPT, 'sudo evil');
    }
}
