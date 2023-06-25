<?php

namespace app\helpers;

use app\models\Document;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Yii;
use yii\base\BaseObject;

class FileConverter extends BaseObject
{
    const AVAILABLE_MIME_TYPES = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/pdf',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.presentation',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.graphics',
        'application/rtf',
        'text/rtf',
        'text/plain',
        'text/html',
        'text/css',
        'text/x-php',
        'text/javascript',
        'application/javascript',
        'application/x-httpd-php',
        'application/json',
        'application/xml',
        'image/jpeg',
        'image/png',
        'image/bmp',
    ];

    public Document $document;

    /**
     * @param string $content
     *
     * @return string
     */
    public function stripTags(string $content): string {
        $content = preg_replace('/(<\s*style[^>]*?>.*?<\s*\/style>)|(<[^>\s\/]*[^>]*>)/si', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content);

        return trim($content);
    }

    /**
     * @return string
     */
    public function convertImages(): string {
        return '';
//        try {
//            $inputFileName = Yii::getAlias('@runtime/temp/' . $this->document->md5);
//            file_put_contents($inputFileName, file_get_contents($this->document->path));
//
//            $outFileName = Yii::getAlias('@runtime/temp/out' . $this->document->md5);
//            exec('tesseract ' . $inputFileName . ' ' . $outFileName . ' -l rus+eng');
//
//            $content = file_get_contents($outFileName . '.txt');
//
//            @unlink(\Yii::getAlias($outFileName . '.txt'));
//            @unlink($inputFileName);
//
//            return $content;
//        } catch (\Throwable $exception) {
//            return '';
//        }
    }

    /**
     * @return string
     */
    public function convert(): string {
        switch ($this->document->mime_type) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/bmp':
                $content = $this->convertImages();
                break;

            default:
                $content = file_get_contents($this->document->path);
        }

        return base64_encode($content);
    }
}

