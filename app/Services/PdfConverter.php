<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfConverter
{
    public function convert($inputPath, $outputPath)
    {
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));

        try {
            switch ($extension) {
                case 'docx':
                case 'doc':
                    return $this->convertWordToPdf($inputPath, $outputPath);
                
                case 'pptx':
                case 'ppt':
                    return $this->convertPptToPdf($inputPath, $outputPath);
                
                default:
                    throw new \Exception("Unsupported file type: $extension");
            }
        } catch (\Exception $e) {
            \Log::error('PDF Conversion Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function convertWordToPdf($inputPath, $outputPath)
    {
        try {
            $phpWord = WordIOFactory::load($inputPath);
            $phpWord->save($outputPath, 'PDF');
            return true;
        } catch (\Exception $e) {
            \Log::error('Word to PDF Conversion Error: ' . $e->getMessage());
            return false;
        }
    }

    private function convertPptToPdf($inputPath, $outputPath)
    {
        try {
            // Untuk PPT, bisa menggunakan library atau command
            $pdf = PDF::loadView('pdf.presentation', [
                'inputPath' => $inputPath
            ]);
            $pdf->save($outputPath);
            return true;
        } catch (\Exception $e) {
            \Log::error('PPT to PDF Conversion Error: ' . $e->getMessage());
            return false;
        }
    }
}