<?php

namespace consumer\models;

use Yii;

/**
 * This is the model class for table "queue_index".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $data
 * @property string|null $md5
 */
class QueueIndex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_index';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['type'], 'string', 'max' => 50],
            [['md5'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'data' => 'Data',
            'md5' => 'Md5',
        ];
    }
}
