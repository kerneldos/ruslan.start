<?php

use app\models\Document;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Document $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
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
                        'class' => 'btn btn-info',
                    ]);
                },
            ],
            'media_type',
            'content',
        ],
    ]) ?>

</div>
