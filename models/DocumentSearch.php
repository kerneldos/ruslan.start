<?php

namespace app\models;

use stdClass;
use yii\elasticsearch\ActiveDataProvider;

class DocumentSearch extends Document
{
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider {
        $query = Document::find()->highlight([
            'pre_tags' => ['<strong>'],  //default is <em>
            'post_tags' => ['</strong>'],
            'fields' => ['content' => new stdClass()],
        ]);

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
                    'fields' => [
                        'name^2',
                        'attachment.content',
                    ],
                    'query' => $this->content,
                    'default_operator' => 'or',
                    'analyze_wildcard' => true,
                    'minimum_should_match' => '-35%',
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