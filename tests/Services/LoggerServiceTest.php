<?php

namespace App\Tests\Services;

use App\Config\Params;
use App\Services\LoggerService;
use App\Struct\ArticleDiff;
use App\Tests\AbstractTestCase;

class LoggerServiceTest extends AbstractTestCase
{

    protected function tearDown(): void
    {
        $container = $this->createContainer();
        /** @var Params $params */
        $params = $container->get(Params::class);

        $path = $params->rootDir.'/'.LoggerService::CHANGES_LOG;
        if(file_exists($path)) unlink($path);

        $path = $params->rootDir.'/'.LoggerService::ERROR_LOG;
        if(file_exists($path)) unlink($path);
    }

    public function testLogChanges()
    {
        $container = $this->createContainer();
        /** @var LoggerService $logger */
        $logger = $container->get(LoggerService::class);
        /** @var Params $params */
        $params = $container->get(Params::class);

        $diff = new ArticleDiff(1, 2, 3);
        $logger->logChanges('test-article.md', $diff);

        $log = file_get_contents($params->rootDir.'/'.LoggerService::CHANGES_LOG);
        self::assertStringContainsString('Words added: 1 - Tables added: 2 - Images added: 3', $log);
    }

    public function testLogError()
    {
        $container = $this->createContainer();
        /** @var LoggerService $logger */
        $logger = $container->get(LoggerService::class);
        /** @var Params $params */
        $params = $container->get(Params::class);

        $e = new \Exception('Test exception');
        $logger->logError($e);

        $log = file_get_contents($params->rootDir.'/'.LoggerService::ERROR_LOG);
        self::assertStringContainsString('Test exception - File: '.$e->getFile().', line '.$e->getLine(), $log);
    }
}
