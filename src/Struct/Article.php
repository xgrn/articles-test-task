<?php

namespace App\Struct;

class Article
{

    public function __construct(
        private string $filename,
        private string $title,
        private string $content
    )
    {
    }

    public function getTitle(): string
    {
        return trim($this->title ?: $this->titleFromFilename());
    }

    private function titleFromFilename(): string
    {
        $title = $this->filename;
        $title = preg_replace('/\.md$/', '', $title);
        $title = str_replace('-', ' ', $title);
        return ucwords($title);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}