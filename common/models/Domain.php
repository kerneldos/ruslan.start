<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "domain".
 *
 * @property int $id
 * @property string $temp_name
 * @property string|null $name
 * @property int|null $user_id
 * @property int $created_at
 * @property int $updated_at
 */
class Domain extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%domain}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['temp_name', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['temp_name'], 'string', 'max' => 6],
            [['name'], 'string', 'max' => 100],
            [['temp_name'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'temp_name' => 'Temp Name',
            'name' => 'Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
