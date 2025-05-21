<?php

namespace App\Service;

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfParserService
{
    private Parser $parser;
    private string $uploadDir;

    public function __construct(string $projectDir)
    {
        $this->parser = new Parser();
        $this->uploadDir = $projectDir . '/public/uploads';
    }

    public function parseUploadedPdf(UploadedFile $file): string
    {
        $pdf = $this->parser->parseFile($file->getPathname());
        $text = $pdf->getText();

        // unlink($file->getPathname()); // Optionally delete the temp file
        return $text;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}