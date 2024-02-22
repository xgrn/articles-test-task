<?php

namespace App\Struct;

class ArticleDiff
{

    public function __construct(
        public readonly int $words,
        public readonly int $tables,
        public readonly int $images
    )
    {
    }
}