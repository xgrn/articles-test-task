<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Services\ImageConverter;
use App\Tests\AbstractTestCase;

class ImageConverterTest extends AbstractTestCase
{

    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->getDataDir().'/articles/'.self::ARTICLE;
        if(file_exists($path)) unlink($path);

        $path = $params->rootDir.'/test_uploads/'.self::IMAGE;
        if(file_exists($path)) unlink($path);

        $path = $params->rootDir.'/test_uploads/test image.webp';
        if(file_exists($path)) unlink($path);

        $path = $params->rootDir.'/test_uploads';
        if(is_dir($path)) rmdir($path);
    }

    const ARTICLE = 'test-article.md';
    const IMAGE = 'test image.png';

    public function testConvertToWebp()
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);
        /** @var ImageConverter $converter */
        $converter = $container->get(ImageConverter::class);

        mkdir($params->rootDir.'/test_uploads');
        file_put_contents(
            $params->rootDir.'/test_uploads/'.self::IMAGE,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFhAJ/wlseKgAAAABJRU5ErkJggg==')
        );

        file_put_contents(
            $params->getDataDir().'/articles/'.self::ARTICLE,
            <<<string
---
images: ["\/test_uploads\/test image.png"]
---
![Image](/test_uploads/test%20image.png "Some image")
![Image](/test_uploads/test+image.png "Some image")
string
        );

        $converter->useUploadFolder('/test_uploads');
        $converter->convertToWebp();

        self::assertTrue(file_exists($params->rootDir.'/test_uploads/test image.webp'));
        self::assertFalse(file_exists($params->rootDir.'/test_uploads/'.self::IMAGE));

        $content = file_get_contents($params->getDataDir().'/articles/'.self::ARTICLE);
        self::assertEquals(<<<string
---
images: ["\/test_uploads\/test image.webp"]
---
![Image](/test_uploads/test%20image.webp "Some image")
![Image](/test_uploads/test%20image.webp "Some image")
string
, $content);
    }
}
