<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ai_text_category".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $hash
 */
class AiTextCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'ai_text_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['name'], 'required'],
            [['description', 'hash'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'hash' => 'Hash',
        ];
    }
}
