<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string|null $description
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
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
     * @return array
     */
    public static function getTree(): array {
        $dataset = Category::find()->indexBy('id')->asArray()->all();
        $tree = [];

        foreach ($dataset as $id => &$node) {
            if (empty($node['parent_id'])) {
                $tree[$id] = &$node;
            } else {
                $dataset[$node['parent_id']]['children'][$id] = &$node;
            }
        }

        return $tree;
    }
}
