<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Exception;

class Customer extends ActiveRecord
{
    // Другие атрибуты и методы класса
    // ...

    /**
     * @return string[]
     */
    public function attributes(): array {
        return ['first_name', 'last_name', 'order_ids', 'email', 'registered_at', 'updated_at', 'status', 'is_active'];
    }

    /**
     * @return array Маппинг этой модели
     */
    public static function mapping(): array {
        return [
            // Типы полей: https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html#field-datatypes
            'properties' => [
                'first_name'     => ['type' => 'text'],
                'last_name'      => ['type' => 'text'],
                'order_ids'      => ['type' => 'keyword'],
                'email'          => ['type' => 'keyword'],
                'registered_at'  => ['type' => 'date'],
                'updated_at'     => ['type' => 'date'],
                'status'         => ['type' => 'keyword'],
                'is_active'      => ['type' => 'boolean'],
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
            //'settings' => [ /* ... */ ],
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