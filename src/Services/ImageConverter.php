<?php

namespace App\Services;

use App\Config\Params;

class ImageConverter
{

    public function __construct(
        private readonly Params $params,
        private readonly ArticleFetchService $fetchService,
        private readonly ArticleUpdateService $updateService,
        private readonly LoggerService $loggerService
    )
    {
    }

    private $uploadFolder = '/public/uploads';

    public function convertToWebp()
    {
        $this->loggerService->disable();
        $images = glob($this->params->rootDir.$this->uploadFolder.'/*');
        foreach ($images as $image) {
            $this->convert(basename($image));
        }
    }

    public function useUploadFolder(string $uploadFolder): void
    {
        $this->uploadFolder = $uploadFolder;
    }

    private $convertables = ['jpg', 'jpeg', 'png'];

    private function convert(string $filename): void
    {
        $filename = basename($filename);
        $path = $this->params->rootDir.$this->uploadFolder.'/'.$filename;
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if(!in_array($ext, $this->convertables)) return;
        $image = $this->createImage($path);
        if(!$image) return;

        $newFilename = preg_replace('/\.[a-z]+$/i', '', $filename).'.webp';
        imagewebp($image, $this->params->rootDir.$this->uploadFolder.'/'.$newFilename);
        imagedestroy($image);
        $this->convertLinks($filename, $newFilename);
        unlink($path);
    }

    private function convertLinks(string $from, string $to): void
    {
        $publicPath = '/'.basename($this->uploadFolder).'/';

        $fromRaw = $publicPath.rawurlencode($from);
        $fromUrl = $publicPath.urlencode($from);
        $toUrl = $publicPath.rawurlencode($to);

        $escapedFrom = str_replace('/', '\\/', $publicPath.$from);
        $escapedTo = str_replace('/', '\\/', $publicPath.$to);

        $articles = $this->fetchService->fetchAllTitles(true);
        foreach ($articles as $article) {
            $countLinks = 0;
            $newContent = str_replace(['('.$fromUrl, '('.$fromRaw], '('.$toUrl, $article->getContent(), $countLinks);

            $countRefs = 0;
            $newContent = str_replace($escapedFrom, $escapedTo, $newContent, $countRefs);

            if($countLinks > 0 or $countRefs > 0) $this->updateService->update($article->getFilename(), $newContent);
        }
    }

    private function createImage(string $path): ?\GdImage
    {
        $mime = mime_content_type($path);
        if($mime === 'image/png') return $this->createFromPng($path);
        if($mime === 'image/jpeg') return $this->createFromJpeg($path);
        return null;
    }

    private function createFromJpeg(string $filename): ?\GdImage
    {
        return imagecreatefromjpeg($filename) ?: null;
    }

    private function createFromPng(string $filename): ?\GdImage
    {
        $image = imagecreatefrompng($filename);
        if(!$image) return null;
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        return $image;
    }
}