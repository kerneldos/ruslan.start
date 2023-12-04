<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "portal".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $temp_name
 * @property string|null $name
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Portal extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'portal';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'yii\behaviors\TimestampBehavior',
            [
                'class' => 'yii\behaviors\BlameableBehavior',
                'defaultValue' => null,
            ],
        ];
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection {
        return Yii::$app->get('loginDb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['temp_name'], 'required'],
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
            'user_id' => 'User ID',
            'temp_name' => 'Temp Name',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}
