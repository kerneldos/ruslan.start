<?php

namespace consumer\modules\dashboard\widgets;

use consumer\models\Document;
use consumer\modules\dashboard\widgets\assets\DocumentsByTypeAsset;
use yii\base\Widget;
use yii\elasticsearch\Exception;

class DocumentsByType extends Widget {
    /**
     * @return string
     * @throws Exception
     */
    public function run(): string {
        $searchResult = Document::find()->addAggregate('documents_by_type', [
            'terms' => [
                'field' => 'media_type',
            ],
        ])->limit(0)->search();

        $documentsByType = [
            'labels' => array_column($searchResult['aggregations']['documents_by_type']['buckets'], 'key'),
            'data'   => array_column($searchResult['aggregations']['documents_by_type']['buckets'], 'doc_count'),
        ];

        DocumentsByTypeAsset::register($this->getView());

        return $this->render('documents-by-type', ['chartData' => $documentsByType]);
    }
}