<?php

namespace app\models;

use stdClass;
use yii\elasticsearch\ActiveDataProvider;

/**
 *
 */
class DocumentSearch extends Document
{
    public array $tags  = [];
    public array $types = [];

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
            'fields' => ['attachment.content' => new stdClass()],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'attributes' => ['name', 'created'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $should = [];

        if (!empty($this->content)) {
            $should[] = [
                'simple_query_string' => [
                    'fields' => [
                        'name^2',
                        'attachment.content',
                    ],
                    'query' => sprintf('*%s*', $this->content),
                    'default_operator' => 'or',
                    'analyze_wildcard' => true,
                    'minimum_should_match' => '-35%',
                ],
            ];

//            $should[] = [
//                'match' => ['attachment.content' => $this->content],
//            ];
        }

        if (!empty($this->tags)) {
            foreach ($this->tags as $tag) {
                $should[] = [
                    'simple_query_string' => [
                        'fields' => [
                            'name^2',
                            'attachment.content',
                        ],
                        'query' => sprintf('*%s*', $tag),
                        'default_operator' => 'or',
                        'analyze_wildcard' => true,
                        'minimum_should_match' => '-35%',
                    ],
                ];
            }
        }

        $filter = [];
        if (!empty($this->types)) {
            $filter[] = [
                'terms' => [
                    'type' => $this->types,
                ],
            ];
        }

        if (!empty($this->category)) {
            $filter[] = [
                'term' => [
                    'category' => $this->category,
                ],
            ];
        }

        $query->query([
            'bool' => [
                'should' => $should,
                'filter' => $filter,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array {
        return [
            [['name', 'content', 'created', 'mime_type', 'file', 'media_type', 'path', 'sha256', 'md5'], 'string'],
            [['tags', 'types', 'category'], 'safe'],
        ];
    }
}