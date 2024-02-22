<?php

namespace App\Config;

class Params
{

    public readonly string $rootDir;

    private const DATA_DIR = '/data';

    public function __construct()
    {
        $this->rootDir = __DIR__.'/../../';
    }

    public function getDataDir(): string
    {
        return $this->rootDir.self::DATA_DIR;
    }
}