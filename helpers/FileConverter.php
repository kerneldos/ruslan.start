<?php

namespace app\helpers;

use Yii;
use yii\base\BaseObject;
use ZipArchive;

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

    public array $file;

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
        $inputFileName = Yii::getAlias('@runtime/' . $this->file['name']);
        file_put_contents($inputFileName, base64_decode($this->file['content']));

        $outFileName = Yii::getAlias('@runtime/out' . $this->file['name']);
        exec('tesseract ' . $inputFileName . ' ' . $outFileName . ' -l rus+eng');

        $content = file_get_contents($outFileName . '.txt');

        unlink(\Yii::getAlias($outFileName . '.txt'));
        unlink($inputFileName);

        return base64_encode($content);
    }

    /**
     * @param string $mimeType
     *
     * @return mixed|string
     */
    public function convert(string $mimeType = '') {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/bmp':
                return '';

            default:
                return $this->file['content'];
        }
    }
}

