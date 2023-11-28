<?php

namespace consumer\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "ai_category".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string|null $description
 * @property AiCategory[] $children
 */
class AiCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ai_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['parent_id', 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getChildren(): ActiveQuery {
        return $this->hasMany('consumer\models\AiCategory', ['parent_id' => 'id']);
    }
}
