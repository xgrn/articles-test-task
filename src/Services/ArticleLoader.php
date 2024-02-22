<?php

namespace App\Services;

use App\Config\Params;
use App\Exception\ArticleLoadingException;
use App\Struct\Article;
use Mni\FrontYAML\Parser;

class ArticleLoader
{

    public function __construct(
        private readonly Params $params,
        private readonly Parser $parser
    )
    {
    }

    const ARTICLE_PATH = '/articles';

    private function loadContent(string $filename): string
    {
        $filename = basename($filename);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if($ext !== 'md') throw new ArticleLoadingException(
            sprintf('The file \'%s\' isn\'t a markdown file', $filename)
        );

        $path = $this->params->getDataDir().self::ARTICLE_PATH.'/'.$filename;
        if(!file_exists($path)) throw new ArticleLoadingException(
            sprintf('File \'%s\' not found', $filename)
        );

        $f = fopen($path, 'r');
        flock($f, LOCK_EX); // Prevent concurrent writing
        $content = stream_get_contents($f);
        fclose($f);
        return $content;
    }

    public function loadTitle(string $filename): Article
    {
        $textContent = $this->loadContent($filename);
        $doc = $this->parser->parse($textContent, false);
        return new Article($filename, $doc->getYAML()['title'] ?? '', '');
    }

    public function loadArticle(string $filename): Article
    {
        $textContent = $this->loadContent($filename);
        $doc = $this->parser->parse($textContent);
        return new Article($filename, $doc->getYAML()['title'] ?? '', $textContent);
    }
}