<?php

namespace consumer\helpers;

use consumer\models\Document;
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
        try {
            $inputFileName = Yii::getAlias('@runtime/temp/' . $this->document->md5);

            $fp = fopen($inputFileName, 'w');
            fwrite($fp, file_get_contents($this->document->path));
            fclose($fp);

            exec('tesseract ' . $inputFileName . ' stdout -l rus+eng', $text);

            $content = join(PHP_EOL, $text);

            @unlink($inputFileName);

            return $content;
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage());
            return '';
        }
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

