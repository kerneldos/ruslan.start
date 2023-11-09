<?php

use app\models\Category;
use app\models\Document;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Document $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="config-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'created',
            'mime_type',
            [
                'attribute' => 'file',
                'format' => 'raw',
                'value' => function(Document $model) {
                    return Html::a('Скачать', Url::to(['download', 'path' => $model->path, 'name' => $model->name, 'type' => $model->type]), [
                        'target' => '_blank',
                        'class' => 'btn btn-default',
                    ]);
                },
            ],
            [
                'attribute' => 'category',
                'format' => 'text',
                'value' => function(Document $model) {
                    return Category::find()->select('name')->where(['id' => $model->category])->scalar();
                },
            ],
            'ai_category',
            'media_type',
            [
                'attribute' => 'content',
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
