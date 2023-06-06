<?php

namespace app\models;

use yii\base\Model;
use yii\elasticsearch\ActiveDataProvider;

class DocumentSearch extends Model
{
    public $name;
    public $content;
    public $created;
    public $mime_type;
    public $file;
    public $media_type;
    public $path;
    public $sha256;
    public $md5;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider {
        $query = Document::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10,]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if (!empty($this->content)) {
            $query->query([
                'simple_query_string' => [
                    'query' => $this->content,
                    'fields' => [
                        'content',
                        'name^2',
                    ],
                ],
            ]);
        }

        return $dataProvider;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'content', 'created', 'mime_type', 'file', 'media_type', 'path', 'sha256', 'md5'], 'string'],
        ];
    }
}