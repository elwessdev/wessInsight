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
        // Create uploads directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Move the file to the uploads directory temporarily
        $filename = uniqid('cv_') . '.' . $file->guessExtension();
        $file->move($this->uploadDir, $filename);
        $filePath = $this->uploadDir . '/' . $filename;

        // Parse the PDF
        $pdf = $this->parser->parseFile($filePath);
        $text = $pdf->getText();

        // Remove temporary file if needed
        // unlink($filePath);

        return $text;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}