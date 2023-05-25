<?php

namespace app\helpers;

use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;
use ZipArchive;

class FileConverter
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
    ];

    private string $filename;

    public function __construct($filePath) {
        $this->filename = $filePath;
    }

    /**
     * @return array|false|string|string[]|null
     */
    private function convertDoc() {
        if(($fh = fopen($this->filename, 'r')) !== false ) {
            $headers = fread($fh, 0xA00);

            // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
            $n1 = ( ord($headers[0x21C]) - 1 );

            // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
            $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

            // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
            $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

            // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
            $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

            // Total length of text in the document
            $textLength = ($n1 + $n2 + $n3 + $n4);

            $extracted_plaintext = fread($fh, $textLength);

            return mb_convert_encoding($extracted_plaintext, 'UTF-8', 'UTF-16LE');
        }

        return 'Error Convert DOC';
    }

    /**
     * @return string
     */
    private function convertDocx(): string {
        $content = [];

        $zip = zip_open($this->filename);

        if (!$zip || is_numeric($zip)) {
            return '';
        }

        while ($zipEntry = zip_read($zip)) {
            if (!zip_entry_open($zip, $zipEntry)) {
                continue;
            }

            if (zip_entry_name($zipEntry) != 'word/document.xml') {
                continue;
            }

            $content[] = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));

            zip_entry_close($zipEntry);
        }

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', join(' ', $content));
        $content = str_replace('</w:r></w:p>', ' ', $content);

        return $this->stripTags($content);
    }

    /**
     * @return string
     */
    function convertXlsx(): string {
        $xmlFileName = 'xl/sharedStrings.xml'; //content file name
        $zipHandle = new ZipArchive;
        $outputText = [];

        if (true === $zipHandle->open($this->filename)) {
            if (($xmlIndex = $zipHandle->locateName($xmlFileName)) !== false) {
                $xmlData = $zipHandle->getFromIndex($xmlIndex);

                $outputText[] = strip_tags($xmlData);
            }

            $zipHandle->close();
        }

        return join(' ', $outputText);
    }

    /**
     * @return string
     */
    function convertPptx(): string {
        $zipArchive = new ZipArchive;
        $outputText = [];

        if(true === $zipArchive->open($this->filename)){
            $slideNumber = 1; //loop through slide files

            while(($xmlIndex = $zipArchive->locateName('ppt/slides/slide'. $slideNumber . '.xml')) !== false) {
                $xmlData = $zipArchive->getFromIndex($xmlIndex);

                $outputText[] = strip_tags($xmlData);
                $slideNumber++;
            }

            $zipArchive->close();
        }

        return join(' ', $outputText);
    }


    /**
     * @param $filename
     * @return string
     */
    public function convertPpt($filename): string {
        // This approach uses detection of the string "chr(0f).Hex_value.chr(0x00).chr(0x00).chr(0x00)" to find text strings, which are then terminated by another NUL chr(0x00). [1] Get text between delimiters [2]
        $fileHandle = fopen($filename, "r");
        $line = fread($fileHandle, filesize($filename));
        $lines = explode(chr(0x0f),$line);
        $outtext = '';

        foreach($lines as $thisline) {
            if (strpos($thisline, chr(0x00).chr(0x00).chr(0x00)) == 1) {
                $text_line = substr($thisline, 4);
                $end_pos   = strpos($text_line, chr(0x00));
                $text_line = substr($text_line, 0, $end_pos);
                $text_line = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/"," ",$text_line);
                if (strlen($text_line) > 1) {
                    $outtext.= substr($text_line, 0, $end_pos)."\n";
                }
            }
        }

        return $outtext;
    }

    /**
     * @return string
     */
    public function convertOpenOffice(): string {
        $zipArchive = new ZipArchive();

        if ($zipArchive->open($this->filename) !== true) {
            return 'Nothing to parse - check that the content.xml file is correctly formed';
        }

        $content = $zipArchive->getFromName('content.xml');

        return $this->stripTags($content);
    }

    /**
     * @return string
     */
    public function convertRtf(): string {
        $rtf = file_get_contents($this->filename);

        return preg_replace("/(\{.*\})|}|(\\\\(?!')\S+)/m",'', $rtf);
    }

    /**
     * @return string
     */
    public function convertPdf(): string {
        $content = [];
        $config = new Config();
        $config->setHorizontalOffset('');
        $config->setFontSpaceLimit(-60);
        $parser = new Parser([], $config);

        try {
            $pdf = $parser->parseFile($this->filename);
            $pages  = $pdf->getPages();

            foreach ($pages as $page) {
                $content[] = $page->getText();
            }
        } catch (\Throwable $exception) {
            return 'Error Convert PDF';
        }

        return join(' ', $content);
    }

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
     * @param string $mimeType
     *
     * @return array|false|string|string[]|null
     */
    public function convert(string $mimeType = '') {

        if(isset($this->filename) && !file_exists($this->filename)) {
            return 'File Not exists';
        }

        if (empty($mimeType)) {
            $mimeType = mime_content_type($this->filename);
        }

        switch ($mimeType) {
            case 'application/msword':
                return $this->convertDoc();

            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return $this->convertDocx();

            case 'application/vnd.ms-powerpoint':
                return $this->convertPpt($this->filename);

            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return $this->convertPptx();

            case 'application/pdf':
                return $this->convertPdf();

            case 'application/vnd.oasis.opendocument.text':
            case 'application/vnd.oasis.opendocument.presentation':
            case 'application/vnd.oasis.opendocument.spreadsheet':
            case 'application/vnd.oasis.opendocument.graphics':
                return $this->convertOpenOffice();

            case 'application/rtf':
            case 'text/rtf':
                return $this->convertRtf();

            case 'text/plain':
            case 'text/html':
            case 'text/css':
            case 'text/x-php':
            case 'text/javascript':
            case 'application/javascript':
            case 'application/x-httpd-php':
            case 'application/json':
            case 'application/xml':
                return file_get_contents($this->filename) ?? '';

            default:
                return 'Invalid File Type';
        }
    }
}

