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