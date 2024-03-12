<?php

namespace consumer\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $type
 * @property int|null $size
 * @property string|null $path
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['size'], 'integer'],
            [['name', 'type', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'size' => 'Size',
            'path' => 'Path',
        ];
    }
}
