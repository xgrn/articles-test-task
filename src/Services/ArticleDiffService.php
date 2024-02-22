<?php

namespace App\Services;

use App\Struct\ArticleDiff;
use Mni\FrontYAML\Parser;

class ArticleDiffService
{

    public function __construct(
        private readonly \Parsedown $parser,
        private readonly ArticleLoader $loader
    )
    {
    }

    public function calcDiff(string $filename, string $newContent)
    {
        $oldArticle = $this->loader->loadArticle($filename);

        $old = $this->parser->parse($oldArticle->getContent());
        $new = $this->parser->parse($newContent);

        return new ArticleDiff(
            $this->calcWordsDiff($old, $new),
            $this->calcTagDiff($old, $new, 'table'),
            $this->calcTagDiff($old, $new, 'img')
        );
    }

    private function calcWordsDiff(string $old, string $new): int
    {
        $calc = function (string $text): int {
            // I don't use str_word_count here as it takes dashes as a word separator
            // I'm trying to make an MS Word-like word counter
            return count(
                preg_split('/[^\p{L}\p{N}\-_]+/u', $text)
            );
        };

        return $calc(strip_tags($new)) - $calc(strip_tags($old));
    }

    private function calcTagDiff(string $old, string $new, string $tag): int
    {
        $calc = function (string $text) use($tag): int {
            return substr_count($text, '<'.$tag);
        };
        return max($calc($new) - $calc($old), 0);
    }
}