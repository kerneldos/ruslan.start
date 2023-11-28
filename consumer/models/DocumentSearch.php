<?php

namespace consumer\models;

use stdClass;
use yii\elasticsearch\ActiveDataProvider;

/**
 *
 */
class DocumentSearch extends Document
{
    public array $tags  = [];
    public array $types = [];
    public int $equalSearch = 0;

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
            'fields' => ['attachment.content' => new stdClass(), 'content' => new stdClass()],
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
            if ($this->equalSearch) {
                $should[] = [
                    'simple_query_string' => [
                        'query' => '"' . $this->content . '"',
                        'fields' => [
                            'name^2',
                            'content',
                        ],
                        'default_operator' => 'AND',
                        'analyze_wildcard' => false,
//                        'analyzer' => 'standard',
                        'minimum_should_match' => '1',
                    ],
                ];
            } else {
                $should[] = [
                    'simple_query_string' => [
                        'fields' => [
                            'name^2',
                            'content',
                        ],
                        'query' => sprintf('*%s*', $this->content),
                        'default_operator' => 'or',
                        'analyze_wildcard' => true,
                        'minimum_should_match' => '-35%',
                    ],
                ];
            }

//            $should[] = [
//                'match' => ['attachment.content' => $this->content],
//            ];
        }

        $filter = [];
        if (!empty($this->tags)) {
//            foreach ($this->tags as $tag) {
//                $should[] = [
//                    'simple_query_string' => [
//                        'fields' => [
//                            'name^2',
//                            'attachment.content',
//                        ],
//                        'query' => sprintf('*%s*', $tag),
//                        'default_operator' => 'or',
//                        'analyze_wildcard' => true,
//                        'minimum_should_match' => '-35%',
//                    ],
//                ];
//            }
            $filter[] = [
                'terms' => [
                    'tags' => $this->tags,
                ],
            ];
        }

        if (!empty($this->types)) {
            $filter[] = [
                'terms' => [
                    'media_type' => $this->types,
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

        if (!empty($this->ai_category)) {
            $filter[] = [
                'term' => [
                    'ai_category' => $this->ai_category,
                ],
            ];
        }

        $query->query([
            'bool' => [
                'must' => $should,
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
            [['tags', 'types', 'category', 'equalSearch'], 'safe'],
        ];
    }
}