<?php

namespace consumer\models;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 */
class Tag extends \yii\db\ActiveRecord {
    /** @var string SCENARIO_IMPORT */
    const SCENARIO_IMPORT = 'import';

    public $importFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            ['importFile', 'file', 'on' => self::SCENARIO_IMPORT],
            ['importFile', 'required', 'on' => self::SCENARIO_IMPORT],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
