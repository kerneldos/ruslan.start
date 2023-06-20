<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property string|null $title
 * @property string $name
 * @property string|null $value
 * @property string $type
 */
class Config extends \yii\db\ActiveRecord
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_PASSWORD = 'password';

    const TYPES_MAP = [
        self::TYPE_TEXT => 'Text',
        self::TYPE_TEXTAREA => 'Text Area',
        self::TYPE_PASSWORD => 'Password',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['value', 'type'], 'string'],
            [['title', 'name'], 'string', 'max' => 150],
            ['type', 'in', 'range' => array_keys(self::TYPES_MAP)],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }
}
