<?php

namespace consumer\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stage_storage".
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
class StageStorage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'stage_storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['key'], 'required'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }
}
