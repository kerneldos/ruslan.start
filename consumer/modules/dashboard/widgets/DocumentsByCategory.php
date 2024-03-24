<?php

namespace consumer\modules\dashboard\widgets;

use consumer\models\AiCategory;
use consumer\models\Document;
use consumer\modules\dashboard\widgets\assets\DocumentsByCategoryAsset;
use yii\base\Widget;
use yii\elasticsearch\Exception;

class DocumentsByCategory extends Widget {
    /**
     * @return string
     * @throws Exception
     */
    public function run(): string {
        $searchResult = Document::find()->addAggregate('documents_by_category', [
            'terms' => [
                'field' => 'ai_category',
            ],
        ])->limit(0)->search();

        $labels = [];
        foreach ($searchResult['aggregations']['documents_by_category']['buckets'] as $bucket) {
            $labels[] = AiCategory::findOne($bucket['key'])->name;
        }

        $documentsByCategory = [
            'labels' => $labels,
            'data'   => array_column($searchResult['aggregations']['documents_by_category']['buckets'], 'doc_count'),
        ];

        DocumentsByCategoryAsset::register($this->getView());

        return $this->render('documents-by-category', ['chartData' => $documentsByCategory]);
    }
}