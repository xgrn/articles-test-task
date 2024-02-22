<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Services\ArticleDiffService;
use App\Tests\AbstractTestCase;

class ArticleDiffServiceTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE;
        if(file_exists($path)) unlink($path);
    }

    const ARTICLE = 'test-diff-article.md';

    public function testCalcDiff()
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE,
            <<<string
Word-word word

![Some image](/uploads/imager.jpg "Some image")

| Col1     |      Col2     |
|----------|:-------------:|
| col 1 ct |  col 2 ct     |
string
        );

        $newContent = <<<string
Word-word word word word

![Some image](/uploads/imager.jpg "Some image")

| Col1     |      Col2     |
|----------|:-------------:|
| col 1 ct |  col 2 ct     |

string;

        /** @var ArticleDiffService $diffService */
        $diffService = $container->get(ArticleDiffService::class);

        $diff = $diffService->calcDiff(self::ARTICLE, $newContent);
        self::assertEquals(2, $diff->words);
        self::assertEquals(0, $diff->tables);
        self::assertEquals(0, $diff->images);

        $newContent = <<<string
Word-word word

![Some image](/uploads/imager.jpg "Some image")

![Some image](/uploads/imager.jpg "Some image")

| Col1     |      Col2     |
|----------|:-------------:|
| col 1 ct |  col 2 ct     |

| Col1     |      Col2     |
|----------|:-------------:|
| col 1 ct |  col 2 ct     |
string;

        $diff = $diffService->calcDiff(self::ARTICLE, $newContent);
        self::assertEquals(8, $diff->words);
        self::assertEquals(1, $diff->tables);
        self::assertEquals(1, $diff->images);

        $newContent = <<<string
Word-word
string;

        $diff = $diffService->calcDiff(self::ARTICLE, $newContent);
        self::assertEquals(-10, $diff->words);
        self::assertEquals(0, $diff->tables);
        self::assertEquals(0, $diff->images);
    }
}
