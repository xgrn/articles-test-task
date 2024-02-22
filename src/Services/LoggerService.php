<?php

namespace App\Services;

use App\Config\Params;
use App\Struct\ArticleDiff;

class LoggerService
{

    public function __construct(
        private readonly Params $params
    )
    {
    }

    const CHANGES_LOG = 'article_updates.log';
    const ERROR_LOG = 'errors.log';

    public function logChanges(string $filename, ArticleDiff $diff): void
    {
        $row = sprintf(
            '[UPDATE] [%s] Filename: %s - Words %s: %d - Tables added: %d - Images added: %d',
            date_create()->format('c'), $filename, ($diff->words < 0) ? 'removed' : 'added',
            $diff->words, $diff->tables, $diff->images
        );
        $this->writeLog(self::CHANGES_LOG, $row);
    }

    public function logError(\Exception $exception): void
    {
        $row = sprintf(
            '[ERROR] [%s] %s - File: %s, line %d',
            date_create()->format('c'), $exception->getMessage(), $exception->getFile(), $exception->getLine()
        );
        $this->writeLog(self::ERROR_LOG, $row);
    }

    private function writeLog(string $log, string $data): void
    {
        $f = fopen($this->params->rootDir.'/'.$log, 'a');
        flock($f, LOCK_EX);
        fwrite($f, $data.PHP_EOL);
        fclose($f);
    }
}