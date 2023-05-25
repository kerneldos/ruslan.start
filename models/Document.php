<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Exception;

/**
 * @property string $_id
 * @property string $name
 * @property string $content
 * @property string $created
 * @property string $mime_type
 * @property string $file
 * @property string $media_type
 * @property string $path
 * @property string $sha256
 * @property string $md5
 */
class Document extends ActiveRecord
{
    /**
     * @return string[]
     */
    public function attributes(): array {
        return ['name', 'content', 'created', 'mime_type', 'file', 'media_type', 'path', 'sha256', 'md5'];
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
                'created'    => ['type' => 'text'],
                'mime_type'  => ['type' => 'text'],
                'file'       => ['type' => 'text'],
                'media_type' => ['type' => 'text'],
                'path'       => ['type' => 'keyword'],
                'sha256'     => ['type' => 'text'],
                'md5'        => ['type' => 'text'],
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
                ],
                'analyzer' => [
                    'default' => [
                        'char_filter' => [
                            'html_strip'
                        ],
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'ru_stop',
                            'ru_stemmer'
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
        $command->deleteIndex(static::index(), static::type());
    }
}