<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Exception;

/**
 * @property string $_id
 * @property string $type
 * @property string $name
 * @property string $content
 * @property string $created
 * @property string $mime_type
 * @property string $file
 * @property string $media_type
 * @property string $path
 * @property string $sha256
 * @property string $md5
 * @property array $attachment
 * @property int $size
 */
class Document extends ActiveRecord
{
    const TYPE_DOCUMENT     = 'Документы';
    const TYPE_PDF          = 'PDF';
    const TYPE_SPREADSHEET  = 'Таблицы';
    const TYPE_PRESENTATION = 'Презентации';
    const TYPE_IMAGE        = 'Изображения';
    const TYPE_ARCHIVE      = 'Архивы';
    const TYPE_GRAPHICS     = 'Графика';
    const TYPE_TXT          = 'Текст';
    const TYPE_HTML         = 'HTML';
    const TYPE_CSS          = 'CSS';
    const TYPE_PHP          = 'PHP';
    const TYPE_JS           = 'JS';
    const TYPE_JSON         = 'JSON';
    const TYPE_XML          = 'XML';
    const TYPE_OTHER        = 'Другой';

    const TYPE_LIST = [
        self::TYPE_DOCUMENT     => self::TYPE_DOCUMENT,
        self::TYPE_PDF          => self::TYPE_PDF,
        self::TYPE_SPREADSHEET  => self::TYPE_SPREADSHEET,
        self::TYPE_PRESENTATION => self::TYPE_PRESENTATION,
        self::TYPE_IMAGE        => self::TYPE_IMAGE,
        self::TYPE_ARCHIVE      => self::TYPE_ARCHIVE,
        self::TYPE_GRAPHICS     => self::TYPE_GRAPHICS,
        self::TYPE_TXT          => self::TYPE_TXT,
        self::TYPE_HTML         => self::TYPE_HTML,
        self::TYPE_CSS          => self::TYPE_CSS,
        self::TYPE_PHP          => self::TYPE_PHP,
        self::TYPE_JS           => self::TYPE_JS,
        self::TYPE_JSON         => self::TYPE_JSON,
        self::TYPE_XML          => self::TYPE_XML,
        self::TYPE_OTHER        => self::TYPE_OTHER,
    ];

    const MIME_TYPES_MAPPING = [
        'application/msword' => self::TYPE_DOCUMENT,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => self::TYPE_DOCUMENT,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document' => self::TYPE_DOCUMENT,
        'application/vnd.oasis.opendocument.text' => self::TYPE_DOCUMENT,
        'application/vnd.ms-powerpoint' => self::TYPE_PRESENTATION,
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => self::TYPE_PRESENTATION,
        'application/vnd.oasis.opendocument.presentation' => self::TYPE_PRESENTATION,
        'application/vnd.oasis.opendocument.spreadsheet' => self::TYPE_SPREADSHEET,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => self::TYPE_SPREADSHEET,
        'application/vnd.oasis.opendocument.graphics' => self::TYPE_GRAPHICS,
        'application/pdf' => self::TYPE_PDF,
        'application/rtf' => self::TYPE_TXT,
        'text/rtf' => self::TYPE_TXT,
        'text/plain' => self::TYPE_TXT,
        'text/html' => self::TYPE_HTML,
        'text/css' => self::TYPE_CSS,
        'text/javascript' => self::TYPE_JS,
        'application/javascript' => self::TYPE_JS,
        'application/x-httpd-php' => self::TYPE_PHP,
        'text/x-php' => self::TYPE_PHP,
        'application/json' => self::TYPE_JSON,
        'application/xml' => self::TYPE_XML,
        'image/jpeg' => self::TYPE_IMAGE,
        'image/png' => self::TYPE_IMAGE,
        'image/bmp' => self::TYPE_IMAGE,
        'application/zip' => self::TYPE_ARCHIVE,
        'application/x-rar' => self::TYPE_ARCHIVE,
        'application/x-7z-compressed' => self::TYPE_ARCHIVE,
    ];

    /**
     * @param string $mimeType
     *
     * @return string
     */
    public static function getType(string $mimeType = ''): string {
        return !empty(self::MIME_TYPES_MAPPING[$mimeType]) ? self::MIME_TYPES_MAPPING[$mimeType] : self::TYPE_OTHER;
    }

    /**
     * @return string[]
     */
    public function attributes(): array {
        return ['name', 'content', 'created', 'mime_type', 'file', 'media_type', 'path', 'sha256', 'md5', 'type', 'attachment', 'size'];
    }

    /**
     * @return array Маппинг этой модели
     */
    public static function mapping(): array {
        return [
            // Типы полей: https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html#field-datatypes
            'properties' => [
                'name'       => ['type' => 'text', 'fielddata' => 'true'],
                'content'    => ['type' => 'text'],
                'created'    => ['type' => 'date'],
                'mime_type'  => ['type' => 'text'],
                'file'       => ['type' => 'text'],
                'media_type' => ['type' => 'text'],
                'path'       => ['type' => 'keyword'],
                'type'       => ['type' => 'keyword'],
                'sha256'     => ['type' => 'text'],
                'md5'        => ['type' => 'text'],
                'size'       => ['type' => 'unsigned_long'],
                'attachment' => [
                    'type' => 'object',
                    'properties' => [
                        'content' => ['type' => 'text', 'fielddata' => 'true'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Создание или обновление маппинга модели
     *
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * @return array[]
     */
    public static function settings(): array {
        return [
            'number_of_replicas' => 0,
            'default_pipeline' => 'attachment',
            'analysis' => [
                'filter' => [
                    'ru_stop' => [
                        'type' => 'stop',
                        'stopwords' => '_russian_'
                    ],
                    'ru_stemmer' => [
                        'type' => 'stemmer',
                        'language' => 'russian',
                    ],
                    'word_delimiter' => [
                        'catenate_all' => true,
                        'type' => 'word_delimiter',
                        'preserve_original' => true,
                    ],
                ],
                'char_filter' => [
                    'yo_filter' => [
                        'type' => 'mapping',
                        'mappings' => [
                            'ё => е',
                            'Ё => Е',
                        ],
                    ],
                ],
                'analyzer' => [
                    'default' => [
                        'char_filter' => [
                            'html_strip',
                            'yo_filter',
                        ],
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'ru_stop',
                            'ru_stemmer',
                            'word_delimiter',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Создание индекса модели
     *
     * @return void
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();

        $command->createIndex(static::index(), [
            //'aliases' => [ /* ... */ ],
            'mappings' => static::mapping(),
            'settings' => static::settings(),
        ]);
    }

    /**
     * Удаление индекса модели
     *
     * @return void
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index());
    }
}