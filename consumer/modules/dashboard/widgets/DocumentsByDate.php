<?php

namespace consumer\modules\dashboard\widgets;

use consumer\models\Document;
use consumer\modules\dashboard\widgets\assets\DocumentsByDateAsset;
use yii\base\Widget;
use yii\elasticsearch\Exception;

class DocumentsByDate extends Widget {
    /**
     * @return string
     * @throws Exception
     */
    public function run(): string {
        $searchResult = Document::find()->addAggregate('documents_by_date', [
            'date_histogram' => [
                'field' => 'created',
                'calendar_interval' => 'month',
                'min_doc_count' => 1,
            ],
        ])->limit(0)->search();

        $documentsByDate = [
            'labels' => array_map(
                function($date) {return date('d.m.Y', strtotime($date));},
                array_column($searchResult['aggregations']['documents_by_date']['buckets'], 'key_as_string'),
            ),
            'data'   => array_column($searchResult['aggregations']['documents_by_date']['buckets'], 'doc_count'),
        ];

        DocumentsByDateAsset::register($this->getView());

        return $this->render('documents-by-date', ['chartData' => $documentsByDate]);
    }
}